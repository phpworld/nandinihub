import React, { createContext, useContext, useReducer, useEffect } from 'react';
import authService from '../services/authService';

// Initial state
const initialState = {
  user: null,
  isAuthenticated: false,
  isLoading: true,
  error: null,
};

// Action types
const AUTH_ACTIONS = {
  SET_LOADING: 'SET_LOADING',
  LOGIN_SUCCESS: 'LOGIN_SUCCESS',
  LOGOUT: 'LOGOUT',
  UPDATE_USER: 'UPDATE_USER',
  SET_ERROR: 'SET_ERROR',
  CLEAR_ERROR: 'CLEAR_ERROR',
};

// Reducer
const authReducer = (state, action) => {
  switch (action.type) {
    case AUTH_ACTIONS.SET_LOADING:
      return {
        ...state,
        isLoading: action.payload,
      };
    case AUTH_ACTIONS.LOGIN_SUCCESS:
      return {
        ...state,
        user: action.payload,
        isAuthenticated: true,
        isLoading: false,
        error: null,
      };
    case AUTH_ACTIONS.LOGOUT:
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        isLoading: false,
        error: null,
      };
    case AUTH_ACTIONS.UPDATE_USER:
      return {
        ...state,
        user: { ...state.user, ...action.payload },
      };
    case AUTH_ACTIONS.SET_ERROR:
      return {
        ...state,
        error: action.payload,
        isLoading: false,
      };
    case AUTH_ACTIONS.CLEAR_ERROR:
      return {
        ...state,
        error: null,
      };
    default:
      return state;
  }
};

// Create context
const AuthContext = createContext();

// Provider component
export const AuthProvider = ({ children }) => {
  const [state, dispatch] = useReducer(authReducer, initialState);

  // Check if user is already authenticated on app start
  useEffect(() => {
    checkAuthStatus();
  }, []);

  const checkAuthStatus = async () => {
    try {
      const { user, token } = await authService.getStoredAuthData();
      
      if (user && token) {
        dispatch({ type: AUTH_ACTIONS.LOGIN_SUCCESS, payload: user });
      } else {
        dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: false });
      }
    } catch (error) {
      dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: false });
    }
  };

  const login = async (credentials) => {
    dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: true });
    dispatch({ type: AUTH_ACTIONS.CLEAR_ERROR });

    try {
      const response = await authService.login(credentials);

      console.log('🔄 AuthContext received login response:', response);

      // Check for the correct response structure from our API
      if (response && response.success && response.data && response.data.user && response.data.token) {
        console.log('✅ Login successful, updating auth state with user:', response.data.user.first_name);
        dispatch({ type: AUTH_ACTIONS.LOGIN_SUCCESS, payload: response.data.user });
        return { success: true };
      } else if (response && response.user && response.token) {
        // Fallback for direct user/token structure
        console.log('✅ Login successful (direct structure), updating auth state with user:', response.user.first_name);
        dispatch({ type: AUTH_ACTIONS.LOGIN_SUCCESS, payload: response.user });
        return { success: true };
      } else if (response && response.success === false) {
        console.log('❌ Login failed - API returned error:', response.message);
        const errorMessage = response.message || 'Login failed';
        dispatch({ type: AUTH_ACTIONS.SET_ERROR, payload: errorMessage });
        return { success: false, message: errorMessage };
      } else {
        console.log('❌ Login failed - unexpected response structure:', response);
        const errorMessage = response?.message || 'Login failed - invalid response';
        dispatch({ type: AUTH_ACTIONS.SET_ERROR, payload: errorMessage });
        return { success: false, message: errorMessage };
      }
    } catch (error) {
      console.error('❌ Login error in AuthContext:', error);
      const errorMessage = error.message || 'Login failed';
      dispatch({ type: AUTH_ACTIONS.SET_ERROR, payload: errorMessage });
      return { success: false, message: errorMessage };
    } finally {
      dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: false });
    }
  };

  const register = async (userData) => {
    dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: true });
    dispatch({ type: AUTH_ACTIONS.CLEAR_ERROR });

    try {
      const response = await authService.register(userData);

      console.log('🔄 AuthContext received register response:', response);

      // The authService returns the response.data directly, which contains user, token, etc.
      if (response && response.user && response.token) {
        console.log('✅ Registration successful, updating auth state with user:', response.user.first_name);
        dispatch({ type: AUTH_ACTIONS.LOGIN_SUCCESS, payload: response.user });
        return { success: true };
      } else if (response && response.success && response.data && response.data.user) {
        // Fallback for different response structure
        console.log('✅ Registration successful (fallback structure), updating auth state with user:', response.data.user.first_name);
        dispatch({ type: AUTH_ACTIONS.LOGIN_SUCCESS, payload: response.data.user });
        return { success: true };
      } else {
        console.log('❌ Registration failed - invalid response structure:', response);
        const errorMessage = response?.message || 'Registration failed';
        dispatch({ type: AUTH_ACTIONS.SET_ERROR, payload: errorMessage });
        return { success: false, message: errorMessage };
      }
    } catch (error) {
      console.error('❌ Registration error in AuthContext:', error);
      const errorMessage = error.message || 'Registration failed';
      dispatch({ type: AUTH_ACTIONS.SET_ERROR, payload: errorMessage });
      return { success: false, message: errorMessage };
    } finally {
      dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: false });
    }
  };

  const logout = async () => {
    dispatch({ type: AUTH_ACTIONS.SET_LOADING, payload: true });
    
    try {
      await authService.logout();
    } catch (error) {
      // Continue with logout even if API call fails
    } finally {
      dispatch({ type: AUTH_ACTIONS.LOGOUT });
    }
  };

  const updateProfile = async (profileData) => {
    try {
      const response = await authService.updateProfile(profileData);
      
      if (response.success) {
        dispatch({ type: AUTH_ACTIONS.UPDATE_USER, payload: response.data });
        return { success: true };
      } else {
        return { success: false, message: response.message };
      }
    } catch (error) {
      return { success: false, message: error.message || 'Update failed' };
    }
  };

  const changePassword = async (passwordData) => {
    try {
      const response = await authService.changePassword(passwordData);
      return response;
    } catch (error) {
      return { success: false, message: error.message || 'Password change failed' };
    }
  };

  const clearError = () => {
    dispatch({ type: AUTH_ACTIONS.CLEAR_ERROR });
  };

  const value = {
    ...state,
    login,
    register,
    logout,
    updateProfile,
    changePassword,
    clearError,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

// Hook to use auth context
export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
