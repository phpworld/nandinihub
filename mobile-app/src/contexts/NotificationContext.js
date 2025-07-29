import React, { createContext, useContext, useEffect, useState } from 'react';
import notificationService from '../services/notificationService';
import { useAuth } from './AuthContext';

// Initial state
const initialState = {
  expoPushToken: null,
  notifications: [],
  badgeCount: 0,
  isInitialized: false,
};

// Action types
const NOTIFICATION_ACTIONS = {
  SET_TOKEN: 'SET_TOKEN',
  ADD_NOTIFICATION: 'ADD_NOTIFICATION',
  REMOVE_NOTIFICATION: 'REMOVE_NOTIFICATION',
  CLEAR_NOTIFICATIONS: 'CLEAR_NOTIFICATIONS',
  SET_BADGE_COUNT: 'SET_BADGE_COUNT',
  SET_INITIALIZED: 'SET_INITIALIZED',
};

// Reducer
const notificationReducer = (state, action) => {
  switch (action.type) {
    case NOTIFICATION_ACTIONS.SET_TOKEN:
      return {
        ...state,
        expoPushToken: action.payload,
      };
    case NOTIFICATION_ACTIONS.ADD_NOTIFICATION:
      return {
        ...state,
        notifications: [action.payload, ...state.notifications],
        badgeCount: state.badgeCount + 1,
      };
    case NOTIFICATION_ACTIONS.REMOVE_NOTIFICATION:
      return {
        ...state,
        notifications: state.notifications.filter(
          notification => notification.id !== action.payload
        ),
      };
    case NOTIFICATION_ACTIONS.CLEAR_NOTIFICATIONS:
      return {
        ...state,
        notifications: [],
        badgeCount: 0,
      };
    case NOTIFICATION_ACTIONS.SET_BADGE_COUNT:
      return {
        ...state,
        badgeCount: action.payload,
      };
    case NOTIFICATION_ACTIONS.SET_INITIALIZED:
      return {
        ...state,
        isInitialized: action.payload,
      };
    default:
      return state;
  }
};

// Create context
const NotificationContext = createContext();

// Provider component
export const NotificationProvider = ({ children }) => {
  const [state, dispatch] = React.useReducer(notificationReducer, initialState);
  const { isAuthenticated } = useAuth();

  useEffect(() => {
    if (isAuthenticated) {
      initializeNotifications();
    } else {
      cleanup();
    }

    return () => {
      cleanup();
    };
  }, [isAuthenticated]);

  const initializeNotifications = async () => {
    try {
      const token = await notificationService.initialize();
      
      if (token) {
        dispatch({ type: NOTIFICATION_ACTIONS.SET_TOKEN, payload: token });
      }

      // Get current badge count
      const badgeCount = await notificationService.getBadgeCount();
      dispatch({ type: NOTIFICATION_ACTIONS.SET_BADGE_COUNT, payload: badgeCount });

      dispatch({ type: NOTIFICATION_ACTIONS.SET_INITIALIZED, payload: true });
    } catch (error) {
      console.error('Failed to initialize notifications:', error);
    }
  };

  const cleanup = () => {
    notificationService.cleanup();
    dispatch({ type: NOTIFICATION_ACTIONS.SET_INITIALIZED, payload: false });
  };

  const addNotification = (notification) => {
    const notificationWithId = {
      ...notification,
      id: Date.now().toString(),
      timestamp: new Date().toISOString(),
    };
    
    dispatch({ type: NOTIFICATION_ACTIONS.ADD_NOTIFICATION, payload: notificationWithId });
  };

  const removeNotification = (notificationId) => {
    dispatch({ type: NOTIFICATION_ACTIONS.REMOVE_NOTIFICATION, payload: notificationId });
  };

  const clearAllNotifications = async () => {
    dispatch({ type: NOTIFICATION_ACTIONS.CLEAR_NOTIFICATIONS });
    await notificationService.clearBadge();
  };

  const updateBadgeCount = async (count) => {
    dispatch({ type: NOTIFICATION_ACTIONS.SET_BADGE_COUNT, payload: count });
    await notificationService.setBadgeCount(count);
  };

  const scheduleOrderUpdateNotification = async (orderId, status, message) => {
    return await notificationService.notifyOrderUpdate(orderId, status, message);
  };

  const schedulePromotionNotification = async (title, message, productId, categoryId) => {
    return await notificationService.notifyPromotion(title, message, productId, categoryId);
  };

  const scheduleGeneralNotification = async (title, message, data) => {
    return await notificationService.notifyGeneral(title, message, data);
  };

  const requestPermissions = async () => {
    return await notificationService.registerForPushNotifications();
  };

  const getNotificationSettings = () => {
    // This would typically get settings from AsyncStorage or API
    return {
      orderUpdates: true,
      promotions: true,
      general: true,
      sound: true,
      vibration: true,
    };
  };

  const updateNotificationSettings = async (settings) => {
    // This would typically save settings to AsyncStorage or API
    console.log('Updating notification settings:', settings);
    
    // You could implement logic to enable/disable certain types of notifications
    // based on user preferences
  };

  // Helper function to format notification for display
  const formatNotificationForDisplay = (notification) => {
    return {
      id: notification.id,
      title: notification.title,
      body: notification.body,
      timestamp: notification.timestamp,
      type: notification.data?.type || 'general',
      isRead: notification.isRead || false,
      data: notification.data || {},
    };
  };

  // Helper function to get unread notification count
  const getUnreadCount = () => {
    return state.notifications.filter(notification => !notification.isRead).length;
  };

  // Helper function to mark notification as read
  const markAsRead = (notificationId) => {
    // Update notification read status
    // This would typically also sync with backend
    console.log('Marking notification as read:', notificationId);
  };

  // Helper function to get notifications by type
  const getNotificationsByType = (type) => {
    return state.notifications.filter(notification => 
      notification.data?.type === type
    );
  };

  const value = {
    ...state,
    addNotification,
    removeNotification,
    clearAllNotifications,
    updateBadgeCount,
    scheduleOrderUpdateNotification,
    schedulePromotionNotification,
    scheduleGeneralNotification,
    requestPermissions,
    getNotificationSettings,
    updateNotificationSettings,
    formatNotificationForDisplay,
    getUnreadCount,
    markAsRead,
    getNotificationsByType,
  };

  return (
    <NotificationContext.Provider value={value}>
      {children}
    </NotificationContext.Provider>
  );
};

// Hook to use notification context
export const useNotifications = () => {
  const context = useContext(NotificationContext);
  if (!context) {
    throw new Error('useNotifications must be used within a NotificationProvider');
  }
  return context;
};
