import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_CONFIG, HTTP_STATUS } from '../config/api';

class ApiService {
  constructor() {
    this.client = axios.create({
      baseURL: API_CONFIG.BASE_URL,
      timeout: API_CONFIG.TIMEOUT,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    this.setupInterceptors();
  }

  setupInterceptors() {
    // Request interceptor to add auth token
    this.client.interceptors.request.use(
      async (config) => {
        const token = await AsyncStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor to handle common errors
    this.client.interceptors.response.use(
      (response) => {
        return response.data;
      },
      async (error) => {
        const originalRequest = error.config;

        // Handle token expiration
        if (error.response?.status === HTTP_STATUS.UNAUTHORIZED && !originalRequest._retry) {
          originalRequest._retry = true;
          
          try {
            const refreshToken = await AsyncStorage.getItem('refresh_token');
            if (refreshToken) {
              const response = await this.refreshToken(refreshToken);
              await AsyncStorage.setItem('auth_token', response.data.token);
              await AsyncStorage.setItem('refresh_token', response.data.refresh_token);
              
              // Retry original request
              originalRequest.headers.Authorization = `Bearer ${response.data.token}`;
              return this.client(originalRequest);
            }
          } catch (refreshError) {
            // Refresh failed, redirect to login
            await this.clearAuthData();
            // You might want to emit an event here to redirect to login
          }
        }

        return Promise.reject(this.handleError(error));
      }
    );
  }

  handleError(error) {
    if (error.response) {
      // Server responded with error status
      return {
        success: false,
        message: error.response.data?.message || 'An error occurred',
        status: error.response.status,
        data: error.response.data,
      };
    } else if (error.request) {
      // Network error
      return {
        success: false,
        message: 'Network error. Please check your connection.',
        status: 0,
      };
    } else {
      // Other error
      return {
        success: false,
        message: error.message || 'An unexpected error occurred',
        status: 0,
      };
    }
  }

  async clearAuthData() {
    await AsyncStorage.multiRemove(['auth_token', 'refresh_token', 'user_data']);
  }

  // Generic HTTP methods
  async get(url, params = {}) {
    return this.client.get(url, { params });
  }

  async post(url, data = {}) {
    return this.client.post(url, data);
  }

  async put(url, data = {}) {
    return this.client.put(url, data);
  }

  async delete(url) {
    return this.client.delete(url);
  }

  // Auth methods
  async refreshToken(refreshToken) {
    return this.client.post('/auth/refresh', { refresh_token: refreshToken });
  }
}

export default new ApiService();
