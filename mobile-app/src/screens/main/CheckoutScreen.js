import React, { useState, useEffect } from 'react';
import { View, StyleSheet, ScrollView, Alert } from 'react-native';
import { Text, Card, Button, RadioButton, Divider, ActivityIndicator } from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import { useCart } from '../../contexts/CartContext';
import apiService from '../../services/api';
import { API_ENDPOINTS } from '../../config/api';

const CheckoutScreen = ({ navigation }) => {
  const [addresses, setAddresses] = useState([]);
  const [selectedAddress, setSelectedAddress] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [placingOrder, setPlacingOrder] = useState(false);

  const { items, total, clearCart } = useCart();

  useEffect(() => {
    loadAddresses();
  }, []);

  const loadAddresses = async () => {
    try {
      setIsLoading(true);
      const response = await apiService.get(API_ENDPOINTS.ADDRESSES.LIST);

      if (response.success) {
        setAddresses(response.data);
        // Select default address if available
        const defaultAddress = response.data.find(addr => addr.is_default);
        if (defaultAddress) {
          setSelectedAddress(defaultAddress.id);
        } else if (response.data.length > 0) {
          setSelectedAddress(response.data[0].id);
        }
      }
    } catch (error) {
      console.error('Error loading addresses:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const handlePlaceOrder = async () => {
    if (!selectedAddress) {
      Alert.alert('Error', 'Please select a delivery address');
      return;
    }

    if (items.length === 0) {
      Alert.alert('Error', 'Your cart is empty');
      return;
    }

    try {
      setPlacingOrder(true);

      const orderData = {
        shipping_address_id: selectedAddress,
        notes: 'Order placed from mobile app',
      };

      const response = await apiService.post(API_ENDPOINTS.ORDERS.CREATE, orderData);

      if (response.success) {
        const orderId = response.data.id;

        // Navigate to payment screen
        navigation.navigate('Payment', {
          orderId: orderId,
          orderTotal: total,
          orderDetails: response.data,
        });

        // Clear cart after successful order placement
        await clearCart();
      } else {
        Alert.alert('Error', response.message || 'Failed to place order');
      }
    } catch (error) {
      Alert.alert('Error', 'Failed to place order. Please try again.');
    } finally {
      setPlacingOrder(false);
    }
  };

  const renderAddress = (address) => (
    <Card key={address.id} style={styles.addressCard}>
      <Card.Content>
        <View style={styles.addressContent}>
          <View style={styles.addressInfo}>
            <Text style={styles.addressName}>{address.name}</Text>
            <Text style={styles.addressText}>
              {address.address_line_1}
              {address.address_line_2 ? `, ${address.address_line_2}` : ''}
            </Text>
            <Text style={styles.addressText}>
              {address.city}, {address.state} - {address.postal_code}
            </Text>
            <Text style={styles.addressPhone}>{address.phone}</Text>
            {address.is_default && (
              <Text style={styles.defaultBadge}>Default Address</Text>
            )}
          </View>
          <RadioButton
            value={address.id}
            status={selectedAddress === address.id ? 'checked' : 'unchecked'}
            onPress={() => setSelectedAddress(address.id)}
            color={theme.colors.primary}
          />
        </View>
      </Card.Content>
    </Card>
  );

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
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
            <Text style={styles.summaryLabel}>Items ({items.length})</Text>
            <Text style={styles.summaryValue}>₹{total.toFixed(2)}</Text>
          </View>
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Delivery</Text>
            <Text style={styles.summaryValue}>Free</Text>
          </View>
          <Divider style={styles.divider} />
          <View style={styles.summaryRow}>
            <Text style={styles.totalLabel}>Total</Text>
            <Text style={styles.totalValue}>₹{total.toFixed(2)}</Text>
          </View>
        </Card.Content>
      </Card>

      {/* Delivery Address */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Delivery Address</Text>
          <Button
            mode="text"
            onPress={() => navigation.navigate('Profile', { screen: 'AddEditAddress' })}
            compact
          >
            Add New
          </Button>
        </View>

        {addresses.length === 0 ? (
          <Card style={styles.emptyCard}>
            <Card.Content>
              <Text style={styles.emptyText}>No addresses found</Text>
              <Button
                mode="outlined"
                onPress={() => navigation.navigate('Profile', { screen: 'AddEditAddress' })}
                style={styles.addAddressButton}
              >
                Add Address
              </Button>
            </Card.Content>
          </Card>
        ) : (
          addresses.map(renderAddress)
        )}
      </View>

      {/* Place Order Button */}
      <View style={styles.buttonContainer}>
        <Button
          mode="contained"
          onPress={handlePlaceOrder}
          loading={placingOrder}
          disabled={placingOrder || !selectedAddress || items.length === 0}
          style={styles.placeOrderButton}
          contentStyle={styles.buttonContent}
        >
          Place Order - ₹{total.toFixed(2)}
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
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
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
  divider: {
    marginVertical: theme.spacing.md,
  },
  totalLabel: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  totalValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  section: {
    paddingHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.lg,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: theme.spacing.md,
  },
  addressCard: {
    marginBottom: theme.spacing.md,
    elevation: 1,
  },
  addressContent: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
  },
  addressInfo: {
    flex: 1,
  },
  addressName: {
    fontSize: 16,
    fontWeight: '500',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  addressText: {
    fontSize: 14,
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  addressPhone: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.xs,
  },
  defaultBadge: {
    fontSize: 12,
    color: theme.colors.primary,
    fontWeight: 'bold',
  },
  emptyCard: {
    elevation: 1,
  },
  emptyText: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginBottom: theme.spacing.lg,
  },
  addAddressButton: {
    borderColor: theme.colors.primary,
  },
  buttonContainer: {
    padding: theme.spacing.lg,
  },
  placeOrderButton: {
    backgroundColor: theme.colors.primary,
  },
  buttonContent: {
    paddingVertical: theme.spacing.md,
  },
});

export default CheckoutScreen;
