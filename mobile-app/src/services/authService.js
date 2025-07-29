import AsyncStorage from '@react-native-async-storage/async-storage';
import apiService from './api';
import { API_ENDPOINTS, API_CONFIG } from '../config/api';

class AuthService {
  async register(userData) {
    try {
      const response = await apiService.post(API_ENDPOINTS.AUTH.REGISTER, userData);
      
      if (response.success) {
        await this.saveAuthData(response.data);
      }
      
      return response;
    } catch (error) {
      return error;
    }
  }

  async login(credentials) {
    try {
      console.log('🔐 Attempting login with:', { email: credentials.email });
      console.log('🌐 Login URL:', `${API_CONFIG.BASE_URL}${API_ENDPOINTS.AUTH.LOGIN}`);

      const response = await apiService.post(API_ENDPOINTS.AUTH.LOGIN, credentials);

      console.log('📡 Raw login response:', response);
      console.log('📡 Response type:', typeof response);
      console.log('📡 Response success:', response?.success);
      console.log('📡 Response data:', response?.data);

      if (response && response.success) {
        console.log('✅ Login successful - saving auth data');
        await this.saveAuthData(response.data);
        console.log('✅ Auth data saved successfully');
        return response;
      } else if (response.data && response.data.success) {
        console.log('✅ Login successful (nested structure) - saving auth data');
        await this.saveAuthData(response.data.data);
        console.log('✅ Auth data saved successfully');
        return response.data;
      } else {
        console.log('❌ Login failed - unexpected response structure');
        console.log('❌ Response:', JSON.stringify(response, null, 2));
      }

      return response || { success: false, message: 'Login failed' };
    } catch (error) {
      console.error('❌ Login error:', error);
      console.error('❌ Error details:', {
        message: error.message,
        response: error.response?.data,
        status: error.response?.status,
      });

      // Return a proper error response structure
      return {
        success: false,
        message: error.message || 'Network error occurred',
        error: error
      };
    }
  }

  async logout() {
    try {
      await apiService.post(API_ENDPOINTS.AUTH.LOGOUT);
    } catch (error) {
      // Continue with logout even if API call fails
    } finally {
      await this.clearAuthData();
    }
  }

  async getProfile() {
    try {
      return await apiService.get(API_ENDPOINTS.AUTH.PROFILE);
    } catch (error) {
      return error;
    }
  }

  async updateProfile(profileData) {
    try {
      const response = await apiService.put(API_ENDPOINTS.AUTH.UPDATE_PROFILE, profileData);
      
      if (response.success) {
        // Update stored user data
        const userData = await AsyncStorage.getItem('user_data');
        if (userData) {
          const user = JSON.parse(userData);
          const updatedUser = { ...user, ...response.data };
          await AsyncStorage.setItem('user_data', JSON.stringify(updatedUser));
        }
      }
      
      return response;
    } catch (error) {
      return error;
    }
  }

  async changePassword(passwordData) {
    try {
      return await apiService.post(API_ENDPOINTS.AUTH.CHANGE_PASSWORD, passwordData);
    } catch (error) {
      return error;
    }
  }

  async saveAuthData(authData) {
    const { user, token, refresh_token } = authData;
    
    await AsyncStorage.multiSet([
      ['auth_token', token],
      ['refresh_token', refresh_token],
      ['user_data', JSON.stringify(user)],
    ]);
  }

  async clearAuthData() {
    await AsyncStorage.multiRemove(['auth_token', 'refresh_token', 'user_data']);
  }

  async getStoredAuthData() {
    try {
      const [token, refreshToken, userData] = await AsyncStorage.multiGet([
        'auth_token',
        'refresh_token',
        'user_data',
      ]);

      return {
        token: token[1],
        refreshToken: refreshToken[1],
        user: userData[1] ? JSON.parse(userData[1]) : null,
      };
    } catch (error) {
      return {
        token: null,
        refreshToken: null,
        user: null,
      };
    }
  }

  async isAuthenticated() {
    const { token } = await this.getStoredAuthData();
    return !!token;
  }
}

export default new AuthService();
