import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Text, Card } from 'react-native-paper';
import { theme } from '../../styles/theme';

const OrderDetailScreen = ({ route }) => {
  const { orderId } = route.params;

  return (
    <View style={styles.container}>
      <Card style={styles.card}>
        <Card.Content>
          <Text style={styles.title}>Order Details</Text>
          <Text style={styles.subtitle}>Order ID: {orderId}</Text>
          <Text style={styles.message}>
            This screen will show detailed order information including items, 
            shipping address, payment details, and order status tracking.
          </Text>
        </Card.Content>
      </Card>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
    padding: theme.spacing.lg,
  },
  card: {
    elevation: 2,
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: theme.spacing.sm,
  },
  subtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.lg,
  },
  message: {
    fontSize: 14,
    lineHeight: 20,
    color: theme.colors.text,
  },
});

export default OrderDetailScreen;
