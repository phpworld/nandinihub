import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  TouchableOpacity,
  Image,
} from 'react-native';
import { Text, ActivityIndicator } from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../styles/theme';
import { API_CONFIG } from '../config/api';

const CircularCategoryItem = ({ category, onPress, size = 'medium' }) => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  const getSizeConfig = () => {
    switch (size) {
      case 'small':
        return { imageSize: 50, fontSize: 11, spacing: 8 };
      case 'large':
        return { imageSize: 90, fontSize: 16, spacing: 12 };
      default:
        return { imageSize: 70, fontSize: 13, spacing: 10 };
    }
  };

  const sizeConfig = getSizeConfig();

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

  const getDefaultImage = () => {
    const categoryName = category?.name?.toLowerCase() || '';
    const defaultImages = {
      'incense': 'https://via.placeholder.com/150x150/FF6B35/FFFFFF?text=🕯️',
      'oils': 'https://via.placeholder.com/150x150/4CAF50/FFFFFF?text=🛢️',
      'flowers': 'https://via.placeholder.com/150x150/E91E63/FFFFFF?text=🌸',
      'accessories': 'https://via.placeholder.com/150x150/2196F3/FFFFFF?text=📿',
      'puja': 'https://via.placeholder.com/150x150/9C27B0/FFFFFF?text=🙏',
      'default': 'https://via.placeholder.com/150x150/757575/FFFFFF?text=📦',
    };

    for (const key in defaultImages) {
      if (categoryName.includes(key)) {
        return defaultImages[key];
      }
    }
    return defaultImages.default;
  };

  const imageUrl = getImageUrl(category?.image_url) || getDefaultImage();

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

  const renderImage = () => {
    if (error || !imageUrl) {
      return (
        <View style={[styles.placeholder, { 
          width: sizeConfig.imageSize, 
          height: sizeConfig.imageSize,
          borderRadius: sizeConfig.imageSize / 2 
        }]}>
          <Ionicons
            name="grid-outline"
            size={sizeConfig.imageSize * 0.4}
            color={theme.colors.textSecondary}
          />
        </View>
      );
    }

    return (
      <View style={[styles.imageContainer, { 
        width: sizeConfig.imageSize, 
        height: sizeConfig.imageSize,
        borderRadius: sizeConfig.imageSize / 2 
      }]}>
        {loading && (
          <View style={[styles.loadingOverlay, { 
            width: sizeConfig.imageSize, 
            height: sizeConfig.imageSize,
            borderRadius: sizeConfig.imageSize / 2 
          }]}>
            <ActivityIndicator size="small" color={theme.colors.primary} />
          </View>
        )}
        <Image
          source={{ uri: imageUrl }}
          style={[styles.image, { 
            width: sizeConfig.imageSize, 
            height: sizeConfig.imageSize,
            borderRadius: sizeConfig.imageSize / 2 
          }]}
          onLoadStart={handleLoadStart}
          onLoadEnd={handleLoadEnd}
          onError={handleError}
          resizeMode="cover"
        />
        <LinearGradient
          colors={['transparent', 'rgba(0,0,0,0.3)']}
          style={[styles.gradient, { 
            width: sizeConfig.imageSize, 
            height: sizeConfig.imageSize,
            borderRadius: sizeConfig.imageSize / 2 
          }]}
        />
      </View>
    );
  };

  return (
    <TouchableOpacity
      style={[styles.container, { marginBottom: sizeConfig.spacing }]}
      onPress={() => onPress?.(category)}
      activeOpacity={0.7}
    >
      {renderImage()}
      <Text
        style={[styles.categoryName, {
          fontSize: sizeConfig.fontSize,
          marginTop: sizeConfig.spacing
        }]}
        numberOfLines={2}
      >
        {category?.name || 'Category'}
      </Text>
      {category?.product_count !== undefined && (
        <Text style={[styles.productCount, { fontSize: sizeConfig.fontSize - 2 }]}>
          {category.product_count} items
        </Text>
      )}
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: theme.spacing.sm,
  },
  imageContainer: {
    position: 'relative',
    elevation: 3,
    shadowColor: theme.colors.shadowColor,
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
  },
  image: {
    backgroundColor: theme.colors.background,
  },
  loadingOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    backgroundColor: theme.colors.background,
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 1,
  },
  gradient: {
    position: 'absolute',
    top: 0,
    left: 0,
  },
  placeholder: {
    backgroundColor: theme.colors.background,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: theme.colors.border,
    borderStyle: 'dashed',
    elevation: 1,
  },
  categoryName: {
    textAlign: 'center',
    color: theme.colors.text,
    fontWeight: '600',
    lineHeight: 16,
    maxWidth: 80,
  },
  productCount: {
    textAlign: 'center',
    color: theme.colors.textSecondary,
    fontWeight: '400',
    marginTop: 2,
    maxWidth: 80,
  },
});

export default CircularCategoryItem;
