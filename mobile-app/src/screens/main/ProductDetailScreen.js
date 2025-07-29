import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  ScrollView,
  Image,
  TouchableOpacity,
  Alert,
} from 'react-native';
import {
  Text,
  Button,
  Card,
  Chip,
  ActivityIndicator,
  Divider,
  IconButton,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import { useCart } from '../../contexts/CartContext';
import apiService from '../../services/api';
import { API_ENDPOINTS } from '../../config/api';

const ProductDetailScreen = ({ navigation, route }) => {
  const [product, setProduct] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const [isLoading, setIsLoading] = useState(true);
  const [addingToCart, setAddingToCart] = useState(false);

  const { addToCart } = useCart();
  const { productId } = route.params;

  useEffect(() => {
    loadProduct();
  }, [productId]);

  const loadProduct = async () => {
    try {
      setIsLoading(true);
      const response = await apiService.get(`${API_ENDPOINTS.PRODUCTS.DETAIL}/${productId}`);
      
      if (response.success) {
        setProduct(response.data);
      } else {
        Alert.alert('Error', 'Product not found');
        navigation.goBack();
      }
    } catch (error) {
      console.error('Error loading product:', error);
      Alert.alert('Error', 'Failed to load product details');
      navigation.goBack();
    } finally {
      setIsLoading(false);
    }
  };

  const handleQuantityChange = (change) => {
    const newQuantity = quantity + change;
    if (newQuantity >= 1 && newQuantity <= (product?.stock_quantity || 999)) {
      setQuantity(newQuantity);
    }
  };

  const handleAddToCart = async () => {
    try {
      setAddingToCart(true);
      const result = await addToCart(productId, quantity);
      
      if (result.success) {
        Alert.alert(
          'Success',
          'Product added to cart successfully!',
          [
            { text: 'Continue Shopping', style: 'cancel' },
            { text: 'View Cart', onPress: () => navigation.navigate('Cart') },
          ]
        );
      } else {
        Alert.alert('Error', result.message || 'Failed to add product to cart');
      }
    } catch (error) {
      Alert.alert('Error', 'Failed to add product to cart');
    } finally {
      setAddingToCart(false);
    }
  };

  const handleBuyNow = async () => {
    await handleAddToCart();
    navigation.navigate('Cart');
  };

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  if (!product) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Product not found</Text>
      </View>
    );
  }

  const isOutOfStock = product.stock_quantity === 0;
  const hasDiscount = product.sale_price && product.sale_price < product.price;
  const discountPercentage = hasDiscount 
    ? Math.round(((product.price - product.sale_price) / product.price) * 100)
    : 0;

  return (
    <View style={styles.container}>
      <ScrollView style={styles.scrollView}>
        {/* Product Image */}
        <View style={styles.imageContainer}>
          <Image
            source={{ uri: product.image_url || 'https://via.placeholder.com/400' }}
            style={styles.productImage}
            resizeMode="cover"
          />
          {hasDiscount && (
            <View style={styles.discountBadge}>
              <Text style={styles.discountText}>{discountPercentage}% OFF</Text>
            </View>
          )}
        </View>

        {/* Product Info */}
        <Card style={styles.infoCard}>
          <Card.Content style={styles.cardContent}>
            <Text style={styles.productName}>{product.name}</Text>
            
            {product.category && (
              <Chip
                style={styles.categoryChip}
                textStyle={styles.categoryChipText}
              >
                {product.category.name}
              </Chip>
            )}

            <View style={styles.priceContainer}>
              <Text style={styles.currentPrice}>
                ₹{product.sale_price || product.price}
              </Text>
              {hasDiscount && (
                <Text style={styles.originalPrice}>₹{product.price}</Text>
              )}
            </View>

            <Text style={styles.stockText}>
              {isOutOfStock ? (
                <Text style={styles.outOfStock}>Out of Stock</Text>
              ) : (
                `${product.stock_quantity} items available`
              )}
            </Text>

            <Divider style={styles.divider} />

            <Text style={styles.sectionTitle}>Description</Text>
            <Text style={styles.description}>
              {product.description || product.short_description}
            </Text>

            {product.features && (
              <>
                <Divider style={styles.divider} />
                <Text style={styles.sectionTitle}>Features</Text>
                <Text style={styles.features}>{product.features}</Text>
              </>
            )}

            {/* Quantity Selector */}
            {!isOutOfStock && (
              <>
                <Divider style={styles.divider} />
                <Text style={styles.sectionTitle}>Quantity</Text>
                <View style={styles.quantityContainer}>
                  <IconButton
                    icon="minus"
                    size={20}
                    onPress={() => handleQuantityChange(-1)}
                    disabled={quantity <= 1}
                    style={styles.quantityButton}
                  />
                  <Text style={styles.quantityText}>{quantity}</Text>
                  <IconButton
                    icon="plus"
                    size={20}
                    onPress={() => handleQuantityChange(1)}
                    disabled={quantity >= product.stock_quantity}
                    style={styles.quantityButton}
                  />
                </View>
              </>
            )}

            {/* Reviews Section */}
            {product.reviews && product.reviews.length > 0 && (
              <>
                <Divider style={styles.divider} />
                <Text style={styles.sectionTitle}>
                  Reviews ({product.total_reviews})
                </Text>
                <View style={styles.ratingContainer}>
                  <Text style={styles.averageRating}>
                    ⭐ {product.average_rating?.toFixed(1) || 'No ratings'}
                  </Text>
                </View>
              </>
            )}
          </Card.Content>
        </Card>
      </ScrollView>

      {/* Bottom Actions */}
      <View style={styles.bottomActions}>
        <Button
          mode="outlined"
          onPress={handleAddToCart}
          loading={addingToCart}
          disabled={isOutOfStock || addingToCart}
          style={[styles.actionButton, styles.addToCartButton]}
          contentStyle={styles.buttonContent}
        >
          Add to Cart
        </Button>
        
        <Button
          mode="contained"
          onPress={handleBuyNow}
          loading={addingToCart}
          disabled={isOutOfStock || addingToCart}
          style={[styles.actionButton, styles.buyNowButton]}
          contentStyle={styles.buttonContent}
        >
          Buy Now
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
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorText: {
    fontSize: 16,
    color: theme.colors.textSecondary,
  },
  scrollView: {
    flex: 1,
  },
  imageContainer: {
    position: 'relative',
  },
  productImage: {
    width: '100%',
    height: 300,
  },
  discountBadge: {
    position: 'absolute',
    top: theme.spacing.md,
    right: theme.spacing.md,
    backgroundColor: theme.colors.error,
    paddingHorizontal: theme.spacing.sm,
    paddingVertical: theme.spacing.xs,
    borderRadius: theme.borderRadius.sm,
  },
  discountText: {
    color: theme.colors.surface,
    fontSize: 12,
    fontWeight: 'bold',
  },
  infoCard: {
    margin: theme.spacing.lg,
    elevation: 2,
  },
  cardContent: {
    padding: theme.spacing.lg,
  },
  productName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
  },
  categoryChip: {
    alignSelf: 'flex-start',
    marginBottom: theme.spacing.md,
    backgroundColor: theme.colors.primary,
  },
  categoryChipText: {
    color: theme.colors.surface,
  },
  priceContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
  },
  currentPrice: {
    fontSize: 28,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  originalPrice: {
    fontSize: 18,
    color: theme.colors.textSecondary,
    textDecorationLine: 'line-through',
    marginLeft: theme.spacing.md,
  },
  stockText: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.lg,
  },
  outOfStock: {
    color: theme.colors.error,
    fontWeight: 'bold',
  },
  divider: {
    marginVertical: theme.spacing.lg,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.sm,
  },
  description: {
    fontSize: 16,
    color: theme.colors.text,
    lineHeight: 24,
  },
  features: {
    fontSize: 16,
    color: theme.colors.text,
    lineHeight: 24,
  },
  quantityContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-start',
  },
  quantityButton: {
    backgroundColor: theme.colors.surface,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  quantityText: {
    fontSize: 18,
    fontWeight: 'bold',
    marginHorizontal: theme.spacing.lg,
    minWidth: 30,
    textAlign: 'center',
  },
  ratingContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  averageRating: {
    fontSize: 16,
    color: theme.colors.text,
  },
  bottomActions: {
    flexDirection: 'row',
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
  },
  actionButton: {
    flex: 1,
    marginHorizontal: theme.spacing.xs,
  },
  addToCartButton: {
    borderColor: theme.colors.primary,
  },
  buyNowButton: {
    backgroundColor: theme.colors.primary,
  },
  buttonContent: {
    paddingVertical: theme.spacing.sm,
  },
});

export default ProductDetailScreen;
