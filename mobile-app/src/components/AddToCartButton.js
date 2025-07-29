import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  TouchableOpacity,
  Animated,
  Alert,
} from 'react-native';
import {
  Text,
  Button,
  IconButton,
  ActivityIndicator,
  Snackbar,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useCart } from '../contexts/CartContext';
import { theme } from '../styles/theme';

const AddToCartButton = ({
  product,
  style,
  size = 'medium',
  variant = 'contained',
  showQuantityControls = false,
  onAddSuccess,
  onAddError,
}) => {
  const [quantity, setQuantity] = useState(1);
  const [loading, setLoading] = useState(false);
  const [showSuccess, setShowSuccess] = useState(false);
  const { addToCart, items } = useCart();

  // Check if product is already in cart
  const cartItem = items.find(item => item.product_id === product.id);
  const isInCart = !!cartItem;

  const handleAddToCart = async () => {
    if (!product || !product.id) {
      Alert.alert('Error', 'Product information is missing');
      return;
    }

    setLoading(true);
    try {
      const result = await addToCart(product.id, quantity);
      
      if (result.success) {
        setShowSuccess(true);
        onAddSuccess?.(product, quantity);
        
        // Auto-hide success message
        setTimeout(() => setShowSuccess(false), 2000);
      } else {
        Alert.alert('Error', result.message || 'Failed to add item to cart');
        onAddError?.(result.message);
      }
    } catch (error) {
      Alert.alert('Error', 'Network error. Please try again.');
      onAddError?.(error.message);
    } finally {
      setLoading(false);
    }
  };

  const incrementQuantity = () => {
    setQuantity(prev => prev + 1);
  };

  const decrementQuantity = () => {
    setQuantity(prev => Math.max(1, prev - 1));
  };

  const getButtonSize = () => {
    switch (size) {
      case 'small':
        return { height: 36, fontSize: 12, paddingHorizontal: 12 };
      case 'large':
        return { height: 56, fontSize: 18, paddingHorizontal: 24 };
      default:
        return { height: 44, fontSize: 14, paddingHorizontal: 16 };
    }
  };

  const buttonSize = getButtonSize();

  if (variant === 'icon') {
    return (
      <View style={[styles.container, style]}>
        <TouchableOpacity
          style={[
            styles.iconButton,
            { width: buttonSize.height, height: buttonSize.height },
            isInCart && styles.iconButtonInCart,
          ]}
          onPress={handleAddToCart}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator size="small" color={theme.colors.surface} />
          ) : (
            <Ionicons
              name={isInCart ? "checkmark" : "add"}
              size={20}
              color={theme.colors.surface}
            />
          )}
        </TouchableOpacity>
        
        <Snackbar
          visible={showSuccess}
          onDismiss={() => setShowSuccess(false)}
          duration={2000}
          style={styles.snackbar}
        >
          Added to cart!
        </Snackbar>
      </View>
    );
  }

  if (variant === 'gradient') {
    return (
      <View style={[styles.container, style]}>
        {showQuantityControls && (
          <View style={styles.quantityControls}>
            <IconButton
              icon="minus"
              size={20}
              onPress={decrementQuantity}
              disabled={quantity <= 1}
              style={styles.quantityButton}
            />
            <Text style={styles.quantityText}>{quantity}</Text>
            <IconButton
              icon="plus"
              size={20}
              onPress={incrementQuantity}
              style={styles.quantityButton}
            />
          </View>
        )}
        
        <TouchableOpacity
          style={[styles.gradientButtonContainer, { height: buttonSize.height }]}
          onPress={handleAddToCart}
          disabled={loading}
          activeOpacity={0.8}
        >
          <LinearGradient
            colors={
              isInCart
                ? [theme.colors.success, theme.colors.successDark]
                : [theme.colors.primary, theme.colors.primaryDark]
            }
            style={[styles.gradientButton, { height: buttonSize.height }]}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 0 }}
          >
            {loading ? (
              <ActivityIndicator size="small" color={theme.colors.surface} />
            ) : (
              <View style={styles.buttonContent}>
                <Ionicons
                  name={isInCart ? "checkmark-circle" : "cart"}
                  size={18}
                  color={theme.colors.surface}
                  style={styles.buttonIcon}
                />
                <Text style={[styles.buttonText, { fontSize: buttonSize.fontSize }]}>
                  {isInCart ? 'In Cart' : 'Add to Cart'}
                </Text>
              </View>
            )}
          </LinearGradient>
        </TouchableOpacity>
        
        <Snackbar
          visible={showSuccess}
          onDismiss={() => setShowSuccess(false)}
          duration={2000}
          style={styles.snackbar}
        >
          Added to cart successfully!
        </Snackbar>
      </View>
    );
  }

  // Default contained variant
  return (
    <View style={[styles.container, style]}>
      {showQuantityControls && (
        <View style={styles.quantityControls}>
          <IconButton
            icon="minus"
            size={20}
            onPress={decrementQuantity}
            disabled={quantity <= 1}
            style={styles.quantityButton}
          />
          <Text style={styles.quantityText}>{quantity}</Text>
          <IconButton
            icon="plus"
            size={20}
            onPress={incrementQuantity}
            style={styles.quantityButton}
          />
        </View>
      )}
      
      <Button
        mode="contained"
        onPress={handleAddToCart}
        loading={loading}
        disabled={loading}
        style={[
          styles.button,
          { height: buttonSize.height },
          isInCart && styles.buttonInCart,
        ]}
        contentStyle={[styles.buttonContentStyle, { height: buttonSize.height }]}
        labelStyle={[styles.buttonLabel, { fontSize: buttonSize.fontSize }]}
        icon={isInCart ? "check" : "cart-plus"}
      >
        {isInCart ? 'In Cart' : 'Add to Cart'}
      </Button>
      
      <Snackbar
        visible={showSuccess}
        onDismiss={() => setShowSuccess(false)}
        duration={2000}
        style={styles.snackbar}
      >
        Added to cart successfully!
      </Snackbar>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    position: 'relative',
  },
  button: {
    borderRadius: theme.borderRadius.md,
  },
  buttonInCart: {
    backgroundColor: theme.colors.success,
  },
  buttonContentStyle: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  buttonLabel: {
    fontWeight: 'bold',
    color: theme.colors.surface,
  },
  iconButton: {
    backgroundColor: theme.colors.primary,
    borderRadius: 22,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 3,
    shadowColor: theme.colors.shadowColor,
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
  },
  iconButtonInCart: {
    backgroundColor: theme.colors.success,
  },
  gradientButtonContainer: {
    borderRadius: theme.borderRadius.md,
    elevation: 3,
    shadowColor: theme.colors.shadowColor,
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
  },
  gradientButton: {
    borderRadius: theme.borderRadius.md,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 16,
  },
  buttonContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  buttonIcon: {
    marginRight: 8,
  },
  buttonText: {
    color: theme.colors.surface,
    fontWeight: 'bold',
  },
  quantityControls: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: theme.spacing.sm,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    elevation: 2,
    paddingHorizontal: theme.spacing.sm,
  },
  quantityButton: {
    margin: 0,
  },
  quantityText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginHorizontal: theme.spacing.md,
    minWidth: 30,
    textAlign: 'center',
  },
  snackbar: {
    backgroundColor: theme.colors.success,
  },
});

export default AddToCartButton;
