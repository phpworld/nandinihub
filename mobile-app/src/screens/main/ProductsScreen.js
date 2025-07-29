import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  Image,
  RefreshControl,
} from 'react-native';
import {
  Text,
  Card,
  Button,
  Searchbar,
  Chip,
  ActivityIndicator,
  Menu,
  Divider,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import { useCart } from '../../contexts/CartContext';
import apiService from '../../services/api';
import { API_ENDPOINTS } from '../../config/api';

const ProductsScreen = ({ navigation, route }) => {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [sortBy, setSortBy] = useState('created_at');
  const [sortOrder, setSortOrder] = useState('desc');
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [sortMenuVisible, setSortMenuVisible] = useState(false);

  const { addToCart } = useCart();

  // Get initial category from navigation params
  const initialCategoryId = route.params?.categoryId;
  const initialCategoryName = route.params?.categoryName;

  useEffect(() => {
    loadCategories();
    if (initialCategoryId) {
      setSelectedCategory({ id: initialCategoryId, name: initialCategoryName });
    }
  }, []);

  useEffect(() => {
    loadProducts(true);
  }, [selectedCategory, sortBy, sortOrder]);

  const loadCategories = async () => {
    try {
      const response = await apiService.get(API_ENDPOINTS.CATEGORIES.LIST);
      if (response.success) {
        setCategories(response.data);
      }
    } catch (error) {
      console.error('Error loading categories:', error);
    }
  };

  const loadProducts = async (reset = false) => {
    try {
      if (reset) {
        setIsLoading(true);
        setPage(1);
      }

      const params = {
        page: reset ? 1 : page,
        per_page: 20,
        sort_by: sortBy,
        sort_order: sortOrder,
      };

      if (selectedCategory) {
        params.category_id = selectedCategory.id;
      }

      if (searchQuery.trim()) {
        params.search = searchQuery.trim();
      }

      const response = await apiService.get(API_ENDPOINTS.PRODUCTS.LIST, params);

      if (response.success) {
        const newProducts = response.data;
        setProducts(reset ? newProducts : [...products, ...newProducts]);
        setHasMore(response.pagination?.has_next || false);
        
        if (!reset) {
          setPage(prev => prev + 1);
        }
      }
    } catch (error) {
      console.error('Error loading products:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadProducts(true);
  };

  const handleSearch = () => {
    loadProducts(true);
  };

  const handleCategorySelect = (category) => {
    setSelectedCategory(category);
  };

  const handleSortChange = (newSortBy, newSortOrder) => {
    setSortBy(newSortBy);
    setSortOrder(newSortOrder);
    setSortMenuVisible(false);
  };

  const handleAddToCart = async (productId) => {
    const result = await addToCart(productId, 1);
    // You might want to show a toast or snackbar here
  };

  const renderProduct = ({ item }) => (
    <TouchableOpacity
      style={styles.productCard}
      onPress={() => navigation.navigate('ProductDetail', { productId: item.id })}
    >
      <Card style={styles.card}>
        <Image
          source={{ uri: item.image_url || 'https://via.placeholder.com/200' }}
          style={styles.productImage}
          resizeMode="cover"
        />
        <Card.Content style={styles.productContent}>
          <Text style={styles.productName} numberOfLines={2}>
            {item.name}
          </Text>
          <Text style={styles.productDescription} numberOfLines={2}>
            {item.short_description}
          </Text>
          
          <View style={styles.priceContainer}>
            <Text style={styles.price}>₹{item.sale_price || item.price}</Text>
            {item.sale_price && (
              <Text style={styles.originalPrice}>₹{item.price}</Text>
            )}
          </View>

          <View style={styles.productActions}>
            <Button
              mode="contained"
              onPress={() => handleAddToCart(item.id)}
              style={styles.addToCartButton}
              contentStyle={styles.buttonContent}
              compact
            >
              Add to Cart
            </Button>
          </View>
        </Card.Content>
      </Card>
    </TouchableOpacity>
  );

  const renderCategory = ({ item }) => (
    <Chip
      selected={selectedCategory?.id === item.id}
      onPress={() => handleCategorySelect(item)}
      style={[
        styles.categoryChip,
        selectedCategory?.id === item.id && styles.selectedCategoryChip
      ]}
      textStyle={[
        styles.categoryChipText,
        selectedCategory?.id === item.id && styles.selectedCategoryChipText
      ]}
    >
      {item.name}
    </Chip>
  );

  const renderHeader = () => (
    <View style={styles.header}>
      {/* Search Bar */}
      <Searchbar
        placeholder="Search products..."
        onChangeText={setSearchQuery}
        value={searchQuery}
        onSubmitEditing={handleSearch}
        style={styles.searchBar}
      />

      {/* Categories */}
      <View style={styles.categoriesSection}>
        <FlatList
          data={[{ id: null, name: 'All' }, ...categories]}
          renderItem={renderCategory}
          keyExtractor={(item) => item.id?.toString() || 'all'}
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.categoriesList}
        />
      </View>

      {/* Sort and Filter */}
      <View style={styles.filterSection}>
        <Text style={styles.resultsText}>
          {products.length} products found
        </Text>
        
        <Menu
          visible={sortMenuVisible}
          onDismiss={() => setSortMenuVisible(false)}
          anchor={
            <Button
              mode="outlined"
              onPress={() => setSortMenuVisible(true)}
              icon="sort"
              compact
            >
              Sort
            </Button>
          }
        >
          <Menu.Item
            onPress={() => handleSortChange('name', 'asc')}
            title="Name (A-Z)"
          />
          <Menu.Item
            onPress={() => handleSortChange('name', 'desc')}
            title="Name (Z-A)"
          />
          <Menu.Item
            onPress={() => handleSortChange('price', 'asc')}
            title="Price (Low to High)"
          />
          <Menu.Item
            onPress={() => handleSortChange('price', 'desc')}
            title="Price (High to Low)"
          />
          <Menu.Item
            onPress={() => handleSortChange('created_at', 'desc')}
            title="Newest First"
          />
        </Menu>
      </View>
    </View>
  );

  const renderFooter = () => {
    if (!hasMore) return null;
    
    return (
      <View style={styles.footer}>
        <ActivityIndicator size="small" color={theme.colors.primary} />
      </View>
    );
  };

  if (isLoading && products.length === 0) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <FlatList
        data={products}
        renderItem={renderProduct}
        keyExtractor={(item) => item.id.toString()}
        numColumns={2}
        ListHeaderComponent={renderHeader}
        ListFooterComponent={renderFooter}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        onEndReached={() => hasMore && loadProducts()}
        onEndReachedThreshold={0.1}
        contentContainerStyle={styles.listContent}
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
  header: {
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
  },
  searchBar: {
    marginBottom: theme.spacing.md,
  },
  categoriesSection: {
    marginBottom: theme.spacing.md,
  },
  categoriesList: {
    paddingRight: theme.spacing.lg,
  },
  categoryChip: {
    marginRight: theme.spacing.sm,
    backgroundColor: theme.colors.surface,
  },
  selectedCategoryChip: {
    backgroundColor: theme.colors.primary,
  },
  categoryChipText: {
    color: theme.colors.text,
  },
  selectedCategoryChipText: {
    color: theme.colors.surface,
  },
  filterSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  resultsText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  listContent: {
    paddingBottom: theme.spacing.xl,
  },
  productCard: {
    flex: 1,
    margin: theme.spacing.sm,
    maxWidth: '48%',
  },
  card: {
    elevation: 2,
  },
  productImage: {
    width: '100%',
    height: 150,
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
  productDescription: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.sm,
  },
  priceContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
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
  productActions: {
    alignItems: 'center',
  },
  addToCartButton: {
    backgroundColor: theme.colors.primary,
    width: '100%',
  },
  buttonContent: {
    paddingVertical: theme.spacing.xs,
  },
  footer: {
    padding: theme.spacing.lg,
    alignItems: 'center',
  },
});

export default ProductsScreen;
