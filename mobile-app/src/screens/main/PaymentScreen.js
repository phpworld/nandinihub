import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  ScrollView,
  Alert,
  Linking,
} from 'react-native';
import {
  Text,
  Card,
  Button,
  RadioButton,
  ActivityIndicator,
  Divider,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';
import { WebView } from 'react-native-webview';

import { theme } from '../../styles/theme';
import paymentService from '../../services/paymentService';

const PaymentScreen = ({ navigation, route }) => {
  const [paymentMethods, setPaymentMethods] = useState([]);
  const [selectedMethod, setSelectedMethod] = useState('card');
  const [isLoading, setIsLoading] = useState(false);
  const [paymentData, setPaymentData] = useState(null);
  const [showWebView, setShowWebView] = useState(false);
  const [paymentUrl, setPaymentUrl] = useState('');

  const { orderId, orderTotal, orderDetails } = route.params;

  useEffect(() => {
    loadPaymentMethods();
  }, []);

  const loadPaymentMethods = async () => {
    try {
      const response = await paymentService.getPaymentMethods();
      
      if (response.success) {
        setPaymentMethods(response.data);
      }
    } catch (error) {
      console.error('Error loading payment methods:', error);
    }
  };

  const handlePayment = async () => {
    try {
      setIsLoading(true);
      
      // Initiate payment
      const response = await paymentService.initiatePayment(orderId);
      
      if (response.success) {
        setPaymentData(response.data);
        setPaymentUrl(response.data.payment_url);
        setShowWebView(true);
      } else {
        Alert.alert('Error', response.message || 'Failed to initiate payment');
      }
    } catch (error) {
      Alert.alert('Error', 'Failed to process payment. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleWebViewNavigation = (navState) => {
    const { url } = navState;
    
    // Check for success/failure URLs
    if (url.includes('/payment/success')) {
      setShowWebView(false);
      handlePaymentSuccess();
    } else if (url.includes('/payment/failure')) {
      setShowWebView(false);
      handlePaymentFailure();
    } else if (url.includes('/payment/cancel')) {
      setShowWebView(false);
      handlePaymentCancel();
    }
  };

  const handlePaymentSuccess = async () => {
    if (paymentData?.transaction_id) {
      // Verify payment status
      const verifyResponse = await paymentService.verifyPayment(paymentData.transaction_id);
      
      if (verifyResponse.success && verifyResponse.data.status === 'success') {
        Alert.alert(
          'Payment Successful!',
          'Your order has been placed successfully.',
          [
            {
              text: 'View Order',
              onPress: () => navigation.navigate('Profile', { 
                screen: 'Orders' 
              }),
            },
          ]
        );
      } else {
        handlePaymentFailure();
      }
    }
  };

  const handlePaymentFailure = () => {
    Alert.alert(
      'Payment Failed',
      'Your payment could not be processed. Please try again.',
      [
        { text: 'Retry', onPress: () => handlePayment() },
        { text: 'Cancel', style: 'cancel' },
      ]
    );
  };

  const handlePaymentCancel = () => {
    Alert.alert(
      'Payment Cancelled',
      'You have cancelled the payment process.',
      [{ text: 'OK' }]
    );
  };

  const renderPaymentMethod = (method) => (
    <Card key={method.id} style={styles.methodCard}>
      <Card.Content>
        <View style={styles.methodContent}>
          <View style={styles.methodInfo}>
            <Ionicons 
              name={method.icon} 
              size={24} 
              color={theme.colors.primary} 
              style={styles.methodIcon}
            />
            <View style={styles.methodDetails}>
              <Text style={styles.methodName}>{method.name}</Text>
              <Text style={styles.methodDescription}>{method.description}</Text>
            </View>
          </View>
          <RadioButton
            value={method.id}
            status={selectedMethod === method.id ? 'checked' : 'unchecked'}
            onPress={() => setSelectedMethod(method.id)}
            color={theme.colors.primary}
          />
        </View>
      </Card.Content>
    </Card>
  );

  if (showWebView) {
    return (
      <View style={styles.webViewContainer}>
        <View style={styles.webViewHeader}>
          <Button
            mode="text"
            onPress={() => setShowWebView(false)}
            icon="arrow-left"
          >
            Back
          </Button>
          <Text style={styles.webViewTitle}>Payment Gateway</Text>
        </View>
        <WebView
          source={{ uri: paymentUrl }}
          onNavigationStateChange={handleWebViewNavigation}
          startInLoadingState={true}
          renderLoading={() => (
            <View style={styles.webViewLoading}>
              <ActivityIndicator size="large" color={theme.colors.primary} />
              <Text>Loading payment gateway...</Text>
            </View>
          )}
        />
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      {/* Order Summary */}
      <Card style={styles.summaryCard}>
        <Card.Content>
          <Text style={styles.sectionTitle}>Order Summary</Text>
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Order ID</Text>
            <Text style={styles.summaryValue}>#{orderId}</Text>
          </View>
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Total Amount</Text>
            <Text style={styles.totalAmount}>
              {paymentService.formatAmount(orderTotal)}
            </Text>
          </View>
        </Card.Content>
      </Card>

      {/* Payment Methods */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Select Payment Method</Text>
        {paymentMethods.map(renderPaymentMethod)}
      </View>

      {/* Security Info */}
      <Card style={styles.securityCard}>
        <Card.Content>
          <View style={styles.securityHeader}>
            <Ionicons name="shield-checkmark" size={24} color={theme.colors.success} />
            <Text style={styles.securityTitle}>Secure Payment</Text>
          </View>
          <Text style={styles.securityText}>
            Your payment information is encrypted and secure. We use industry-standard 
            security measures to protect your data.
          </Text>
        </Card.Content>
      </Card>

      {/* Payment Button */}
      <View style={styles.buttonContainer}>
        <Button
          mode="contained"
          onPress={handlePayment}
          loading={isLoading}
          disabled={isLoading || !selectedMethod}
          style={styles.payButton}
          contentStyle={styles.payButtonContent}
        >
          Pay {paymentService.formatAmount(orderTotal)}
        </Button>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  summaryCard: {
    margin: theme.spacing.lg,
    elevation: 2,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
  },
  summaryLabel: {
    fontSize: 16,
    color: theme.colors.text,
  },
  summaryValue: {
    fontSize: 16,
    color: theme.colors.text,
  },
  totalAmount: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  section: {
    paddingHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.lg,
  },
  methodCard: {
    marginBottom: theme.spacing.md,
    elevation: 1,
  },
  methodContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  methodInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  methodIcon: {
    marginRight: theme.spacing.md,
  },
  methodDetails: {
    flex: 1,
  },
  methodName: {
    fontSize: 16,
    fontWeight: '500',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  methodDescription: {
    fontSize: 14,
    color: theme.colors.textSecondary,
  },
  securityCard: {
    margin: theme.spacing.lg,
    marginTop: 0,
    elevation: 1,
  },
  securityHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
  },
  securityTitle: {
    fontSize: 16,
    fontWeight: '500',
    color: theme.colors.text,
    marginLeft: theme.spacing.sm,
  },
  securityText: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    lineHeight: 20,
  },
  buttonContainer: {
    padding: theme.spacing.lg,
  },
  payButton: {
    backgroundColor: theme.colors.primary,
  },
  payButtonContent: {
    paddingVertical: theme.spacing.md,
  },
  webViewContainer: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  webViewHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: theme.spacing.md,
    backgroundColor: theme.colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.border,
  },
  webViewTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginLeft: theme.spacing.md,
  },
  webViewLoading: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
});

export default PaymentScreen;
