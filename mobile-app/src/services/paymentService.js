import apiService from './api';
import { API_ENDPOINTS } from '../config/api';

class PaymentService {
  async initiatePayment(orderId) {
    try {
      const response = await apiService.post(API_ENDPOINTS.PAYMENT.INITIATE, {
        order_id: orderId,
      });
      
      return response;
    } catch (error) {
      return error;
    }
  }

  async verifyPayment(transactionId) {
    try {
      const response = await apiService.get(`${API_ENDPOINTS.PAYMENT.VERIFY}/${transactionId}`);
      
      return response;
    } catch (error) {
      return error;
    }
  }

  async getPaymentMethods() {
    try {
      const response = await apiService.get(API_ENDPOINTS.PAYMENT.METHODS);
      
      return response;
    } catch (error) {
      return error;
    }
  }

  async processPaymentCallback(callbackData) {
    try {
      const response = await apiService.post(API_ENDPOINTS.PAYMENT.CALLBACK, callbackData);
      
      return response;
    } catch (error) {
      return error;
    }
  }

  // Helper method to open payment gateway in WebView
  openPaymentGateway(paymentUrl, onSuccess, onFailure, onCancel) {
    // This would typically open a WebView or redirect to payment gateway
    // For now, we'll simulate the payment process
    
    return new Promise((resolve, reject) => {
      // In a real implementation, this would:
      // 1. Open WebView with payment URL
      // 2. Handle success/failure callbacks
      // 3. Return to app with payment result
      
      // Simulated payment flow
      setTimeout(() => {
        // Simulate random success/failure for demo
        const isSuccess = Math.random() > 0.3; // 70% success rate
        
        if (isSuccess) {
          const mockResult = {
            success: true,
            transaction_id: 'TXN' + Date.now(),
            status: 'success',
            payment_method: 'card',
            gateway_transaction_id: 'GTW' + Date.now(),
          };
          
          if (onSuccess) onSuccess(mockResult);
          resolve(mockResult);
        } else {
          const mockError = {
            success: false,
            status: 'failed',
            failure_reason: 'Payment declined by bank',
          };
          
          if (onFailure) onFailure(mockError);
          reject(mockError);
        }
      }, 3000); // Simulate 3 second payment process
    });
  }

  // Helper method to format payment amount
  formatAmount(amount, currency = 'INR') {
    const formatter = new Intl.NumberFormat('en-IN', {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 2,
    });
    
    return formatter.format(amount);
  }

  // Helper method to get payment status color
  getStatusColor(status) {
    switch (status?.toLowerCase()) {
      case 'success':
      case 'completed':
        return '#4CAF50'; // Green
      case 'pending':
      case 'processing':
        return '#FF9800'; // Orange
      case 'failed':
      case 'declined':
        return '#F44336'; // Red
      case 'cancelled':
        return '#9E9E9E'; // Grey
      default:
        return '#757575'; // Default grey
    }
  }

  // Helper method to get payment status text
  getStatusText(status) {
    switch (status?.toLowerCase()) {
      case 'success':
        return 'Payment Successful';
      case 'pending':
        return 'Payment Pending';
      case 'processing':
        return 'Processing Payment';
      case 'failed':
        return 'Payment Failed';
      case 'cancelled':
        return 'Payment Cancelled';
      case 'refunded':
        return 'Payment Refunded';
      default:
        return 'Unknown Status';
    }
  }

  // Helper method to validate payment data
  validatePaymentData(paymentData) {
    const errors = [];

    if (!paymentData.order_id) {
      errors.push('Order ID is required');
    }

    if (!paymentData.amount || paymentData.amount <= 0) {
      errors.push('Valid amount is required');
    }

    if (!paymentData.currency) {
      errors.push('Currency is required');
    }

    return {
      isValid: errors.length === 0,
      errors,
    };
  }

  // Helper method to handle payment errors
  handlePaymentError(error) {
    let message = 'Payment failed. Please try again.';
    
    if (error.message) {
      message = error.message;
    } else if (error.data?.message) {
      message = error.data.message;
    }

    // Log error for debugging
    console.error('Payment Error:', error);

    return {
      success: false,
      message,
      error: error,
    };
  }

  // Helper method to track payment events (for analytics)
  trackPaymentEvent(eventName, eventData) {
    // This would integrate with analytics service
    console.log('Payment Event:', eventName, eventData);
    
    // Example events:
    // - payment_initiated
    // - payment_method_selected
    // - payment_success
    // - payment_failed
    // - payment_cancelled
  }
}

export default new PaymentService();
