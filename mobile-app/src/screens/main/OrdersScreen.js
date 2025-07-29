import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
} from 'react-native';
import {
  Text,
  Card,
  Chip,
  ActivityIndicator,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import apiService from '../../services/api';
import { API_ENDPOINTS } from '../../config/api';

const OrdersScreen = ({ navigation }) => {
  const [orders, setOrders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadOrders();
  }, []);

  const loadOrders = async () => {
    try {
      setIsLoading(true);
      const response = await apiService.get(API_ENDPOINTS.ORDERS.LIST);
      
      if (response.success) {
        setOrders(response.data);
      }
    } catch (error) {
      console.error('Error loading orders:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadOrders();
  };

  const getStatusColor = (status) => {
    switch (status?.toLowerCase()) {
      case 'pending':
        return theme.colors.pending;
      case 'processing':
        return theme.colors.processing;
      case 'shipped':
        return theme.colors.shipped;
      case 'delivered':
        return theme.colors.delivered;
      case 'cancelled':
        return theme.colors.cancelled;
      default:
        return theme.colors.textSecondary;
    }
  };

  const renderOrder = ({ item }) => (
    <TouchableOpacity
      onPress={() => navigation.navigate('OrderDetail', { orderId: item.id })}
    >
      <Card style={styles.orderCard}>
        <Card.Content>
          <View style={styles.orderHeader}>
            <Text style={styles.orderId}>Order #{item.id}</Text>
            <Chip
              style={[styles.statusChip, { backgroundColor: getStatusColor(item.status) }]}
              textStyle={styles.statusText}
            >
              {item.status?.toUpperCase()}
            </Chip>
          </View>

          <Text style={styles.orderDate}>
            {new Date(item.created_at).toLocaleDateString()}
          </Text>

          <View style={styles.orderDetails}>
            <Text style={styles.itemCount}>
              {item.total_items} item{item.total_items !== 1 ? 's' : ''}
            </Text>
            <Text style={styles.orderTotal}>₹{item.total}</Text>
          </View>
        </Card.Content>
      </Card>
    </TouchableOpacity>
  );

  const renderEmptyState = () => (
    <View style={styles.emptyContainer}>
      <Ionicons name="receipt-outline" size={80} color={theme.colors.textSecondary} />
      <Text style={styles.emptyTitle}>No orders yet</Text>
      <Text style={styles.emptySubtitle}>
        Your order history will appear here
      </Text>
    </View>
  );

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <FlatList
        data={orders}
        renderItem={renderOrder}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        ListEmptyComponent={renderEmptyState}
      />
    </View>
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
  listContent: {
    padding: theme.spacing.lg,
  },
  orderCard: {
    marginBottom: theme.spacing.md,
    elevation: 2,
  },
  orderHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
  },
  orderId: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  statusChip: {
    height: 28,
  },
  statusText: {
    color: theme.colors.surface,
    fontSize: 12,
    fontWeight: 'bold',
  },
  orderDate: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.md,
  },
  orderDetails: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  itemCount: {
    fontSize: 14,
    color: theme.colors.textSecondary,
  },
  orderTotal: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingTop: theme.spacing.xxl,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginTop: theme.spacing.lg,
    marginBottom: theme.spacing.sm,
  },
  emptySubtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
  },
});

export default OrdersScreen;
