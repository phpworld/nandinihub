import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import apiService from './api';

// Configure notification behavior
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

class NotificationService {
  constructor() {
    this.expoPushToken = null;
    this.notificationListener = null;
    this.responseListener = null;
  }

  async initialize() {
    try {
      // Register for push notifications
      const token = await this.registerForPushNotifications();
      
      if (token) {
        this.expoPushToken = token;
        await this.saveTokenToStorage(token);
        await this.sendTokenToServer(token);
      }

      // Set up notification listeners
      this.setupNotificationListeners();

      return token;
    } catch (error) {
      console.error('Failed to initialize notifications:', error);
      return null;
    }
  }

  async registerForPushNotifications() {
    let token;

    if (Platform.OS === 'android') {
      await Notifications.setNotificationChannelAsync('default', {
        name: 'default',
        importance: Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250],
        lightColor: '#FF231F7C',
      });
    }

    if (Device.isDevice) {
      const { status: existingStatus } = await Notifications.getPermissionsAsync();
      let finalStatus = existingStatus;
      
      if (existingStatus !== 'granted') {
        const { status } = await Notifications.requestPermissionsAsync();
        finalStatus = status;
      }
      
      if (finalStatus !== 'granted') {
        console.log('Failed to get push token for push notification!');
        return null;
      }
      
      token = (await Notifications.getExpoPushTokenAsync()).data;
    } else {
      console.log('Must use physical device for Push Notifications');
    }

    return token;
  }

  setupNotificationListeners() {
    // Listener for notifications received while app is foregrounded
    this.notificationListener = Notifications.addNotificationReceivedListener(
      this.handleNotificationReceived.bind(this)
    );

    // Listener for when user taps on notification
    this.responseListener = Notifications.addNotificationResponseReceivedListener(
      this.handleNotificationResponse.bind(this)
    );
  }

  handleNotificationReceived(notification) {
    console.log('Notification received:', notification);
    
    // Handle different notification types
    const { data } = notification.request.content;
    
    switch (data?.type) {
      case 'order_update':
        this.handleOrderUpdateNotification(data);
        break;
      case 'promotion':
        this.handlePromotionNotification(data);
        break;
      case 'general':
        this.handleGeneralNotification(data);
        break;
      default:
        console.log('Unknown notification type:', data?.type);
    }
  }

  handleNotificationResponse(response) {
    console.log('Notification response:', response);
    
    const { data } = response.notification.request.content;
    
    // Navigate based on notification type
    switch (data?.type) {
      case 'order_update':
        if (data.order_id) {
          // Navigate to order details
          // This would need to be implemented with navigation service
          console.log('Navigate to order:', data.order_id);
        }
        break;
      case 'promotion':
        if (data.product_id) {
          // Navigate to product details
          console.log('Navigate to product:', data.product_id);
        } else if (data.category_id) {
          // Navigate to category
          console.log('Navigate to category:', data.category_id);
        }
        break;
      default:
        // Navigate to home or relevant screen
        console.log('Navigate to home');
    }
  }

  handleOrderUpdateNotification(data) {
    // Handle order status updates
    console.log('Order update notification:', data);
    
    // You could update local state, show in-app notification, etc.
    // For example, update order status in context or local storage
  }

  handlePromotionNotification(data) {
    // Handle promotional notifications
    console.log('Promotion notification:', data);
    
    // Could show special offers, discounts, etc.
  }

  handleGeneralNotification(data) {
    // Handle general notifications
    console.log('General notification:', data);
  }

  async saveTokenToStorage(token) {
    try {
      await AsyncStorage.setItem('expo_push_token', token);
    } catch (error) {
      console.error('Failed to save push token:', error);
    }
  }

  async getTokenFromStorage() {
    try {
      return await AsyncStorage.getItem('expo_push_token');
    } catch (error) {
      console.error('Failed to get push token:', error);
      return null;
    }
  }

  async sendTokenToServer(token) {
    try {
      // Send token to your backend server
      // This endpoint would need to be implemented in your API
      const response = await apiService.post('/notifications/register-token', {
        token: token,
        platform: Platform.OS,
        device_info: {
          brand: Device.brand,
          modelName: Device.modelName,
          osName: Device.osName,
          osVersion: Device.osVersion,
        },
      });

      if (response.success) {
        console.log('Push token registered successfully');
      } else {
        console.error('Failed to register push token:', response.message);
      }
    } catch (error) {
      console.error('Error sending token to server:', error);
    }
  }

  async scheduleLocalNotification(title, body, data = {}, trigger = null) {
    try {
      const notificationId = await Notifications.scheduleNotificationAsync({
        content: {
          title,
          body,
          data,
          sound: 'default',
        },
        trigger: trigger || null, // null means immediate
      });

      return notificationId;
    } catch (error) {
      console.error('Failed to schedule notification:', error);
      return null;
    }
  }

  async cancelNotification(notificationId) {
    try {
      await Notifications.cancelScheduledNotificationAsync(notificationId);
    } catch (error) {
      console.error('Failed to cancel notification:', error);
    }
  }

  async cancelAllNotifications() {
    try {
      await Notifications.cancelAllScheduledNotificationsAsync();
    } catch (error) {
      console.error('Failed to cancel all notifications:', error);
    }
  }

  async getBadgeCount() {
    try {
      return await Notifications.getBadgeCountAsync();
    } catch (error) {
      console.error('Failed to get badge count:', error);
      return 0;
    }
  }

  async setBadgeCount(count) {
    try {
      await Notifications.setBadgeCountAsync(count);
    } catch (error) {
      console.error('Failed to set badge count:', error);
    }
  }

  async clearBadge() {
    try {
      await Notifications.setBadgeCountAsync(0);
    } catch (error) {
      console.error('Failed to clear badge:', error);
    }
  }

  // Clean up listeners
  cleanup() {
    if (this.notificationListener) {
      Notifications.removeNotificationSubscription(this.notificationListener);
    }
    
    if (this.responseListener) {
      Notifications.removeNotificationSubscription(this.responseListener);
    }
  }

  // Helper method to create notification for order updates
  async notifyOrderUpdate(orderId, status, message) {
    const title = 'Order Update';
    const body = message || `Your order #${orderId} status has been updated to ${status}`;
    
    return await this.scheduleLocalNotification(title, body, {
      type: 'order_update',
      order_id: orderId,
      status: status,
    });
  }

  // Helper method to create notification for promotions
  async notifyPromotion(title, message, productId = null, categoryId = null) {
    return await this.scheduleLocalNotification(title, message, {
      type: 'promotion',
      product_id: productId,
      category_id: categoryId,
    });
  }

  // Helper method to create general notifications
  async notifyGeneral(title, message, data = {}) {
    return await this.scheduleLocalNotification(title, message, {
      type: 'general',
      ...data,
    });
  }
}

export default new NotificationService();
