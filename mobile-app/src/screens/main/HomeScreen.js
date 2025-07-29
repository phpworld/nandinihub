import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  ScrollView,
  RefreshControl,
  TouchableOpacity,
  FlatList,
} from 'react-native';
import {
  Text,
  Button,
  ActivityIndicator,
} from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import { useAuth } from '../../contexts/AuthContext';
import { useCart } from '../../contexts/CartContext';
import apiService from '../../services/api';
import { API_ENDPOINTS, API_CONFIG } from '../../config/api';
import BannerSlider from '../../components/BannerSlider';

import { ProductCardImage } from '../../components/ProductImage';
import CircularCategoryItem from '../../components/CircularCategoryItem';

const HomeScreen = ({ navigation }) => {

  const [featuredProducts, setFeaturedProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [categoryProducts, setCategoryProducts] = useState({});
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const { user } = useAuth();
  const { totalItems } = useCart();

  useEffect(() => {
    testApiConnectivity();
    loadHomeData();
  }, []);

  const testApiConnectivity = async () => {
    try {
      console.log('🔍 Testing API connectivity...');
      console.log('🌐 Testing URL:', `${API_CONFIG.BASE_URL}/categories`);

      const response = await fetch(`${API_CONFIG.BASE_URL}/categories`);
      console.log('📡 Response status:', response.status);
      console.log('📡 Response ok:', response.ok);

      if (response.ok) {
        const data = await response.json();
        console.log('✅ API connectivity test successful');
        console.log('📊 Sample data:', data.success ? 'API working' : 'API error');
      } else {
        console.log('❌ API connectivity test failed - status:', response.status);
      }
    } catch (error) {
      console.log('❌ API connectivity test error:', error.message);
    }
  };

  const loadCategoryProducts = async (categoryId, limit = 6) => {
    try {
      console.log(`🔄 Loading products for category ${categoryId}...`);
      const response = await apiService.get(
        `${API_ENDPOINTS.PRODUCTS.LIST}?category_id=${categoryId}&limit=${limit}`
      );
      console.log(`📦 Category ${categoryId} products response:`, response);

      if (response && response.success) {
        setCategoryProducts(prev => ({
          ...prev,
          [categoryId]: response.data || []
        }));
        console.log(`✅ Category ${categoryId} products loaded:`, response.data?.length || 0);
      } else {
        console.log(`❌ Category ${categoryId} products failed:`, response);
      }
    } catch (error) {
      console.error(`❌ Error loading category ${categoryId} products:`, error);
    }
  };

  const loadHomeData = async () => {
    try {
      setIsLoading(true);
      console.log('🔄 Loading home data...');
      console.log('🌐 API Base URL:', API_CONFIG.BASE_URL);

      console.log('📡 Making API calls...');
      const [featuredResponse, categoriesResponse] = await Promise.all([
        apiService.get(API_ENDPOINTS.PRODUCTS.FEATURED, { limit: 10 }),
        apiService.get(API_ENDPOINTS.CATEGORIES.LIST, { limit: 8, with_counts: true }),
      ]);

      console.log('📦 Featured products response:', featuredResponse);
      console.log('🏷️ Categories response:', categoriesResponse);

      if (featuredResponse && featuredResponse.success) {
        setFeaturedProducts(featuredResponse.data || []);
        console.log('✅ Featured products loaded:', featuredResponse.data?.length || 0);
      } else {
        console.log('❌ Featured products failed:', featuredResponse);
        // Set fallback featured products for testing
        const fallbackProducts = [
          { id: 1, name: 'Premium Incense Sticks', price: 150, image_url: null },
          { id: 2, name: 'Sacred Puja Oil', price: 200, image_url: null },
          { id: 3, name: 'Fresh Marigold Flowers', price: 50, image_url: null },
          { id: 4, name: 'Brass Diya Set', price: 300, image_url: null },
        ];
        setFeaturedProducts(fallbackProducts);
        console.log('🔄 Using fallback featured products');
      }

      if (categoriesResponse && categoriesResponse.success) {
        setCategories(categoriesResponse.data || []);
        console.log('✅ Categories loaded:', categoriesResponse.data?.length || 0);

        // Load products for first 3 categories
        const topCategories = (categoriesResponse.data || []).slice(0, 3);
        topCategories.forEach(category => {
          loadCategoryProducts(category.id);
        });
      } else {
        console.log('❌ Categories failed:', categoriesResponse);
        // Set fallback categories for testing
        const fallbackCategories = [
          { id: 1, name: 'Incense Sticks', image_url: null, product_count: 25 },
          { id: 2, name: 'Puja Oils', image_url: null, product_count: 15 },
          { id: 3, name: 'Sacred Flowers', image_url: null, product_count: 30 },
          { id: 4, name: 'Prayer Items', image_url: null, product_count: 20 },
          { id: 5, name: 'Decorative Items', image_url: null, product_count: 18 },
          { id: 6, name: 'Spiritual Books', image_url: null, product_count: 12 },
        ];
        setCategories(fallbackCategories);
        console.log('🔄 Using fallback categories');
      }
    } catch (error) {
      console.error('❌ Error loading home data:', error);
      console.error('❌ Error details:', {
        message: error.message,
        code: error.code,
        response: error.response?.data,
        status: error.response?.status,
      });

      // Check if it's a network error
      if (error.code === 'NETWORK_ERROR' || error.message.includes('Network Error')) {
        console.log('🌐 Network error detected - check if backend is running');
      }

      // Set fallback data for testing
      const fallbackCategories = [
        { id: 1, name: 'Incense Sticks', image_url: null, product_count: 25 },
        { id: 2, name: 'Puja Oils', image_url: null, product_count: 15 },
        { id: 3, name: 'Sacred Flowers', image_url: null, product_count: 30 },
        { id: 4, name: 'Prayer Items', image_url: null, product_count: 20 },
      ];
      const fallbackProducts = [
        { id: 1, name: 'Premium Incense Sticks', price: 150, image_url: null },
        { id: 2, name: 'Sacred Puja Oil', price: 200, image_url: null },
        { id: 3, name: 'Fresh Marigold Flowers', price: 50, image_url: null },
        { id: 4, name: 'Brass Diya Set', price: 300, image_url: null },
      ];
      setCategories(fallbackCategories);
      setFeaturedProducts(fallbackProducts);
      console.log('🔄 Using fallback data due to error');
    } finally {
      setIsLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadHomeData();
    setRefreshing(false);
  };



  const renderFeaturedProduct = ({ item }) => (
    <ProductCardImage
      product={{
        ...item,
        image: item.image_url,
        price: item.sale_price || item.price,
        original_price: item.sale_price ? item.price : null,
      }}
      style={styles.productCard}
      onPress={() => navigation.navigate('ProductDetail', { productId: item.id })}
    />
  );

  const renderCategory = ({ item }) => (
    <CircularCategoryItem
      category={{
        ...item,
        image_url: item.image_url,
      }}
      size="medium"
      onPress={() => navigation.navigate('Products', {
        screen: 'ProductsMain',
        params: { categoryId: item.id, categoryName: item.name }
      })}
    />
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
      <ScrollView
        style={styles.scrollView}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        showsVerticalScrollIndicator={false}
      >
        {/* Full Width Banner Slider */}
        <BannerSlider
          onBannerPress={(banner) => {
            // Handle banner press - navigate to specific category or product
            console.log('Banner pressed:', banner);
          }}
        />

      {/* Categories Section */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Categories ({categories.length})</Text>
          <Button
            mode="text"
            onPress={() => navigation.navigate('Products', { screen: 'Categories' })}
            labelStyle={styles.seeAllText}
          >
            See All
          </Button>
        </View>
        
        {categories.length > 0 ? (
          <FlatList
            data={categories}
            renderItem={renderCategory}
            keyExtractor={(item) => item.id.toString()}
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.categoriesList}
          />
        ) : (
          <Text style={styles.noDataText}>No categories available</Text>
        )}
      </View>

      {/* Featured Products Section */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>✨ Featured Products ({featuredProducts.length})</Text>
          <Button
            mode="text"
            onPress={() => navigation.navigate('Products')}
            labelStyle={styles.seeAllText}
          >
            See All
          </Button>
        </View>
        
        {featuredProducts.length > 0 ? (
          <FlatList
            data={featuredProducts}
            renderItem={renderFeaturedProduct}
            keyExtractor={(item) => item.id.toString()}
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.productsList}
          />
        ) : (
          <Text style={styles.noDataText}>No featured products available</Text>
        )}
      </View>

      {/* Category-based Product Sections */}
      {categories.slice(0, 3).map((category) => {
        const products = categoryProducts[category.id] || [];
        if (products.length === 0) return null;

        return (
          <View key={category.id} style={styles.section}>
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>{category.name}</Text>
              <Button
                mode="text"
                onPress={() => navigation.navigate('Products', {
                  screen: 'ProductsMain',
                  params: { categoryId: category.id, categoryName: category.name }
                })}
                labelStyle={styles.seeAllText}
              >
                See All
              </Button>
            </View>

            <FlatList
              data={products}
              renderItem={renderFeaturedProduct}
              keyExtractor={(item) => item.id.toString()}
              horizontal
              showsHorizontalScrollIndicator={false}
              contentContainerStyle={styles.productsList}
            />
          </View>
        );
      })}
      </ScrollView>

      {/* Floating Cart Icon */}
      <TouchableOpacity
        style={styles.floatingCartButton}
        onPress={() => navigation.navigate('Cart')}
      >
        <Ionicons name="bag-outline" size={24} color={theme.colors.surface} />
        {totalItems > 0 && (
          <View style={styles.cartBadge}>
            <Text style={styles.cartBadgeText}>{totalItems}</Text>
          </View>
        )}
      </TouchableOpacity>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  scrollView: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },

  floatingCartButton: {
    position: 'absolute',
    top: 40,
    right: 20,
    backgroundColor: theme.colors.primary,
    borderRadius: 25,
    width: 50,
    height: 50,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 8,
    shadowColor: theme.colors.shadowColor,
    shadowOffset: {
      width: 0,
      height: 4,
    },
    shadowOpacity: 0.3,
    shadowRadius: 4.65,
    zIndex: 1000,
  },
  cartBadge: {
    position: 'absolute',
    top: -5,
    right: -5,
    backgroundColor: theme.colors.error,
    borderRadius: 12,
    minWidth: 24,
    height: 24,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: theme.colors.surface,
  },
  cartBadgeText: {
    color: theme.colors.surface,
    fontSize: 12,
    fontWeight: 'bold',
  },
  searchBar: {
    backgroundColor: theme.colors.surface,
    elevation: 2,
  },
  searchInput: {
    fontSize: 16,
  },
  section: {
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.lg,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: theme.spacing.md,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  seeAllText: {
    color: theme.colors.primary,
    fontSize: 14,
  },
  noDataText: {
    textAlign: 'center',
    color: theme.colors.textSecondary,
    fontSize: 14,
    fontStyle: 'italic',
    paddingVertical: theme.spacing.lg,
  },
  categoriesList: {
    paddingRight: theme.spacing.lg,
  },
  categoryItem: {
    marginRight: theme.spacing.md,
  },
  categoryCard: {
    alignItems: 'center',
    width: 80,
  },
  categoryImage: {
    width: 60,
    height: 60,
    borderRadius: 30,
    marginBottom: theme.spacing.sm,
  },
  categoryName: {
    fontSize: 12,
    textAlign: 'center',
    color: theme.colors.text,
  },
  productsList: {
    paddingRight: theme.spacing.lg,
  },
  productCard: {
    marginRight: theme.spacing.md,
    width: 160,
  },
  card: {
    elevation: 2,
  },
  productImage: {
    width: '100%',
    height: 120,
  },
  productContent: {
    padding: theme.spacing.sm,
  },
  productName: {
    fontSize: 14,
    fontWeight: '500',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  priceContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  price: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
  originalPrice: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    textDecorationLine: 'line-through',
    marginLeft: theme.spacing.xs,
  },

});

export default HomeScreen;
