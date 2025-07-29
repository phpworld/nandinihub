import React, { useState } from 'react';
import {
  View,
  Image,
  StyleSheet,
  ActivityIndicator,
} from 'react-native';
import { Text, Card } from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../styles/theme';
import { API_CONFIG } from '../config/api';
import AddToCartButton from './AddToCartButton';

const ProductImage = ({
  source,
  style,
  resizeMode = 'cover',
  showPlaceholder = true,
  placeholderIcon = 'image-outline',
  placeholderText = 'No Image',
}) => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  // Function to get the full image URL
  const getImageUrl = (imagePath) => {
    if (!imagePath) return null;
    
    // If it's already a full URL, return as is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }
    
    // If it's a relative path, construct the full URL
    const baseUrl = API_CONFIG.BASE_URL.replace('/api/v1', '');
    return `${baseUrl}/${imagePath}`;
  };

  const imageUrl = getImageUrl(source);

  const handleLoadStart = () => {
    setLoading(true);
    setError(false);
  };

  const handleLoadEnd = () => {
    setLoading(false);
  };

  const handleError = () => {
    setLoading(false);
    setError(true);
  };

  const renderPlaceholder = () => (
    <View style={[styles.placeholder, style]}>
      <Ionicons
        name={placeholderIcon}
        size={40}
        color={theme.colors.textSecondary}
      />
      {placeholderText && (
        <Text style={styles.placeholderText}>{placeholderText}</Text>
      )}
    </View>
  );

  const renderLoading = () => (
    <View style={[styles.loading, style]}>
      <ActivityIndicator size="small" color={theme.colors.primary} />
    </View>
  );

  // If no image URL or error occurred, show placeholder
  if (!imageUrl || error) {
    return showPlaceholder ? renderPlaceholder() : null;
  }

  return (
    <View style={style}>
      {loading && renderLoading()}
      <Image
        source={{ uri: imageUrl }}
        style={[style, loading && styles.hidden]}
        resizeMode={resizeMode}
        onLoadStart={handleLoadStart}
        onLoadEnd={handleLoadEnd}
        onError={handleError}
      />
    </View>
  );
};

// Category Image Component
export const CategoryImage = ({ category, style, onPress }) => {
  const categoryImages = {
    'incense': 'https://via.placeholder.com/150x150/FF6B35/FFFFFF?text=Incense',
    'oils': 'https://via.placeholder.com/150x150/4CAF50/FFFFFF?text=Oils',
    'flowers': 'https://via.placeholder.com/150x150/9C27B0/FFFFFF?text=Flowers',
    'accessories': 'https://via.placeholder.com/150x150/2196F3/FFFFFF?text=Accessories',
    'default': 'https://via.placeholder.com/150x150/757575/FFFFFF?text=Category',
  };

  const getImageSource = () => {
    if (category?.image) {
      return category.image;
    }
    
    const categoryName = category?.name?.toLowerCase() || 'default';
    return categoryImages[categoryName] || categoryImages.default;
  };

  return (
    <Card style={[styles.categoryCard, style]} onPress={onPress}>
      <ProductImage
        source={getImageSource()}
        style={styles.categoryImage}
        placeholderIcon="grid-outline"
        placeholderText="Category"
      />
      <View style={styles.categoryOverlay}>
        <Text style={styles.categoryName}>
          {category?.name || 'Category'}
        </Text>
      </View>
    </Card>
  );
};

// Product Card Image Component
export const ProductCardImage = ({ product, style, onPress }) => {
  const getProductImage = () => {
    if (product?.images && product.images.length > 0) {
      return product.images[0];
    }
    if (product?.image) {
      return product.image;
    }
    return null;
  };

  return (
    <Card style={[styles.productCard, style]} onPress={onPress}>
      <ProductImage
        source={getProductImage()}
        style={styles.productImage}
        placeholderIcon="bag-outline"
        placeholderText="Product"
      />
      <View style={styles.productInfo}>
        <Text style={styles.productName} numberOfLines={2}>
          {product?.name || 'Product Name'}
        </Text>
        <View style={styles.priceRow}>
          <View style={styles.priceContainer}>
            <Text style={styles.productPrice}>
              ₹{product?.price || '0.00'}
            </Text>
            {product?.original_price && product.original_price > product.price && (
              <Text style={styles.originalPrice}>
                ₹{product.original_price}
              </Text>
            )}
          </View>
          <AddToCartButton
            product={product}
            variant="icon"
            size="small"
            style={styles.addToCartButton}
          />
        </View>
      </View>
    </Card>
  );
};

const styles = StyleSheet.create({
  placeholder: {
    backgroundColor: theme.colors.background,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderStyle: 'dashed',
    borderRadius: theme.borderRadius.md,
  },
  placeholderText: {
    marginTop: theme.spacing.sm,
    fontSize: 12,
    color: theme.colors.textSecondary,
    textAlign: 'center',
  },
  loading: {
    position: 'absolute',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: theme.colors.background,
    zIndex: 1,
  },
  hidden: {
    opacity: 0,
  },
  categoryCard: {
    borderRadius: theme.borderRadius.lg,
    elevation: 3,
    margin: theme.spacing.sm,
  },
  categoryImage: {
    width: '100%',
    height: 120,
    borderTopLeftRadius: theme.borderRadius.lg,
    borderTopRightRadius: theme.borderRadius.lg,
  },
  categoryOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: 'rgba(0,0,0,0.6)',
    padding: theme.spacing.sm,
    borderBottomLeftRadius: theme.borderRadius.lg,
    borderBottomRightRadius: theme.borderRadius.lg,
  },
  categoryName: {
    color: theme.colors.surface,
    fontSize: 14,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  productCard: {
    borderRadius: theme.borderRadius.lg,
    elevation: 3,
    margin: theme.spacing.sm,
  },
  productImage: {
    width: '100%',
    height: 150,
    borderTopLeftRadius: theme.borderRadius.lg,
    borderTopRightRadius: theme.borderRadius.lg,
  },
  productInfo: {
    padding: theme.spacing.md,
  },
  productName: {
    fontSize: 14,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.sm,
  },
  priceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  priceContainer: {
    flex: 1,
  },
  productPrice: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  originalPrice: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    textDecorationLine: 'line-through',
    marginTop: 2,
  },
  addToCartButton: {
    marginLeft: theme.spacing.sm,
  },
});

export default ProductImage;
