import React, { useEffect } from 'react';
import {
  View,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  Image,
  Alert,
} from 'react-native';
import {
  Text,
  Card,
  Button,
  IconButton,
  Divider,
  ActivityIndicator,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import { useCart } from '../../contexts/CartContext';

const CartScreen = ({ navigation }) => {
  const {
    items,
    totalItems,
    subtotal,
    total,
    isLoading,
    updateCartItem,
    removeFromCart,
    clearCart,
    loadCart,
  } = useCart();

  useEffect(() => {
    loadCart();
  }, []);

  const handleQuantityChange = async (itemId, currentQuantity, change) => {
    const newQuantity = currentQuantity + change;
    if (newQuantity <= 0) {
      handleRemoveItem(itemId);
      return;
    }

    await updateCartItem(itemId, newQuantity);
  };

  const handleRemoveItem = (itemId) => {
    Alert.alert(
      'Remove Item',
      'Are you sure you want to remove this item from your cart?',
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Remove', onPress: () => removeFromCart(itemId), style: 'destructive' },
      ]
    );
  };

  const handleClearCart = () => {
    Alert.alert(
      'Clear Cart',
      'Are you sure you want to remove all items from your cart?',
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Clear All', onPress: () => clearCart(), style: 'destructive' },
      ]
    );
  };

  const handleCheckout = () => {
    if (items.length === 0) {
      Alert.alert('Empty Cart', 'Please add some items to your cart before checkout.');
      return;
    }
    navigation.navigate('Checkout');
  };

  const renderCartItem = ({ item }) => (
    <Card style={styles.itemCard}>
      <View style={styles.itemContainer}>
        <TouchableOpacity
          onPress={() => navigation.navigate('ProductDetail', { productId: item.product_id })}
        >
          <Image
            source={{ uri: item.product?.image_url || 'https://via.placeholder.com/80' }}
            style={styles.itemImage}
            resizeMode="cover"
          />
        </TouchableOpacity>

        <View style={styles.itemDetails}>
          <TouchableOpacity
            onPress={() => navigation.navigate('ProductDetail', { productId: item.product_id })}
          >
            <Text style={styles.itemName} numberOfLines={2}>
              {item.product?.name || 'Product'}
            </Text>
          </TouchableOpacity>

          <View style={styles.priceContainer}>
            <Text style={styles.itemPrice}>₹{item.price}</Text>
            {item.product?.price && item.price < item.product.price && (
              <Text style={styles.originalPrice}>₹{item.product.price}</Text>
            )}
          </View>

          <View style={styles.quantityContainer}>
            <IconButton
              icon="minus"
              size={20}
              onPress={() => handleQuantityChange(item.id, item.quantity, -1)}
              style={styles.quantityButton}
            />
            <Text style={styles.quantityText}>{item.quantity}</Text>
            <IconButton
              icon="plus"
              size={20}
              onPress={() => handleQuantityChange(item.id, item.quantity, 1)}
              style={styles.quantityButton}
            />
          </View>
        </View>

        <View style={styles.itemActions}>
          <Text style={styles.itemTotal}>₹{(item.price * item.quantity).toFixed(2)}</Text>
          <IconButton
            icon="delete"
            size={20}
            onPress={() => handleRemoveItem(item.id)}
            iconColor={theme.colors.error}
          />
        </View>
      </View>
    </Card>
  );

  const renderEmptyCart = () => (
    <View style={styles.emptyContainer}>
      <Ionicons name="bag-outline" size={80} color={theme.colors.textSecondary} />
      <Text style={styles.emptyTitle}>Your cart is empty</Text>
      <Text style={styles.emptySubtitle}>Add some products to get started</Text>
      <Button
        mode="contained"
        onPress={() => navigation.navigate('Products')}
        style={styles.shopButton}
      >
        Start Shopping
      </Button>
    </View>
  );

  const renderCartSummary = () => (
    <Card style={styles.summaryCard}>
      <Card.Content>
        <Text style={styles.summaryTitle}>Order Summary</Text>
        
        <View style={styles.summaryRow}>
          <Text style={styles.summaryLabel}>Items ({totalItems})</Text>
          <Text style={styles.summaryValue}>₹{subtotal.toFixed(2)}</Text>
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
  );

  if (isLoading && items.length === 0) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  if (items.length === 0) {
    return (
      <View style={styles.container}>
        {renderEmptyCart()}
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <FlatList
        data={items}
        renderItem={renderCartItem}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        ListFooterComponent={renderCartSummary}
      />

      <View style={styles.bottomActions}>
        <Button
          mode="outlined"
          onPress={handleClearCart}
          style={styles.clearButton}
          textColor={theme.colors.error}
        >
          Clear Cart
        </Button>
        
        <Button
          mode="contained"
          onPress={handleCheckout}
          style={styles.checkoutButton}
          loading={isLoading}
        >
          Checkout
        </Button>
      </View>
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
  itemCard: {
    marginBottom: theme.spacing.md,
    elevation: 2,
  },
  itemContainer: {
    flexDirection: 'row',
    padding: theme.spacing.md,
  },
  itemImage: {
    width: 80,
    height: 80,
    borderRadius: theme.borderRadius.sm,
  },
  itemDetails: {
    flex: 1,
    marginLeft: theme.spacing.md,
    justifyContent: 'space-between',
  },
  itemName: {
    fontSize: 16,
    fontWeight: '500',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  priceContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
  },
  itemPrice: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  originalPrice: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    textDecorationLine: 'line-through',
    marginLeft: theme.spacing.sm,
  },
  quantityContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  quantityButton: {
    backgroundColor: theme.colors.surface,
    borderWidth: 1,
    borderColor: theme.colors.border,
    margin: 0,
  },
  quantityText: {
    fontSize: 16,
    fontWeight: 'bold',
    marginHorizontal: theme.spacing.md,
    minWidth: 20,
    textAlign: 'center',
  },
  itemActions: {
    alignItems: 'flex-end',
    justifyContent: 'space-between',
  },
  itemTotal: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: theme.spacing.xl,
  },
  emptyTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginTop: theme.spacing.lg,
    marginBottom: theme.spacing.sm,
  },
  emptySubtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginBottom: theme.spacing.xl,
  },
  shopButton: {
    backgroundColor: theme.colors.primary,
  },
  summaryCard: {
    marginTop: theme.spacing.lg,
    elevation: 2,
  },
  summaryTitle: {
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
  bottomActions: {
    flexDirection: 'row',
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
  },
  clearButton: {
    flex: 1,
    marginRight: theme.spacing.sm,
    borderColor: theme.colors.error,
  },
  checkoutButton: {
    flex: 2,
    marginLeft: theme.spacing.sm,
    backgroundColor: theme.colors.primary,
  },
});

export default CartScreen;
