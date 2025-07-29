import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  Image,
} from 'react-native';
import {
  Text,
  Card,
  Button,
  Searchbar,
  ActivityIndicator,
  Chip,
} from 'react-native-paper';

import { theme } from '../../styles/theme';
import { useCart } from '../../contexts/CartContext';
import apiService from '../../services/api';
import { API_ENDPOINTS } from '../../config/api';

const SearchScreen = ({ navigation, route }) => {
  const [searchQuery, setSearchQuery] = useState(route.params?.query || '');
  const [searchResults, setSearchResults] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [hasSearched, setHasSearched] = useState(false);

  const { addToCart } = useCart();

  useEffect(() => {
    if (route.params?.query) {
      handleSearch();
    }
  }, []);

  const handleSearch = async () => {
    if (!searchQuery.trim()) return;

    try {
      setIsLoading(true);
      setHasSearched(true);
      
      const response = await apiService.get(API_ENDPOINTS.PRODUCTS.SEARCH, {
        q: searchQuery.trim(),
        per_page: 50,
      });

      if (response.success) {
        setSearchResults(response.data);
      } else {
        setSearchResults([]);
      }
    } catch (error) {
      console.error('Error searching products:', error);
      setSearchResults([]);
    } finally {
      setIsLoading(false);
    }
  };

  const handleAddToCart = async (productId) => {
    await addToCart(productId, 1);
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

          <Button
            mode="contained"
            onPress={() => handleAddToCart(item.id)}
            style={styles.addToCartButton}
            contentStyle={styles.buttonContent}
            compact
          >
            Add to Cart
          </Button>
        </Card.Content>
      </Card>
    </TouchableOpacity>
  );

  const renderEmptyState = () => {
    if (isLoading) return null;

    if (!hasSearched) {
      return (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyTitle}>Search Products</Text>
          <Text style={styles.emptySubtitle}>
            Enter a product name or keyword to search
          </Text>
        </View>
      );
    }

    if (searchResults.length === 0) {
      return (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyTitle}>No results found</Text>
          <Text style={styles.emptySubtitle}>
            Try searching with different keywords
          </Text>
        </View>
      );
    }

    return null;
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Searchbar
          placeholder="Search products..."
          onChangeText={setSearchQuery}
          value={searchQuery}
          onSubmitEditing={handleSearch}
          style={styles.searchBar}
          autoFocus
        />
      </View>

      {isLoading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={theme.colors.primary} />
          <Text style={styles.loadingText}>Searching...</Text>
        </View>
      ) : (
        <FlatList
          data={searchResults}
          renderItem={renderProduct}
          keyExtractor={(item) => item.id.toString()}
          numColumns={2}
          contentContainerStyle={styles.listContent}
          ListEmptyComponent={renderEmptyState}
          ListHeaderComponent={
            hasSearched && searchResults.length > 0 ? (
              <View style={styles.resultsHeader}>
                <Text style={styles.resultsText}>
                  {searchResults.length} results for "{searchQuery}"
                </Text>
              </View>
            ) : null
          }
        />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  header: {
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
  },
  searchBar: {
    elevation: 2,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: theme.spacing.md,
    color: theme.colors.textSecondary,
  },
  resultsHeader: {
    padding: theme.spacing.lg,
    paddingBottom: theme.spacing.md,
  },
  resultsText: {
    fontSize: 16,
    fontWeight: '500',
    color: theme.colors.text,
  },
  listContent: {
    padding: theme.spacing.md,
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
  addToCartButton: {
    backgroundColor: theme.colors.primary,
  },
  buttonContent: {
    paddingVertical: theme.spacing.xs,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: theme.spacing.xl,
    paddingTop: theme.spacing.xxl,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.sm,
    textAlign: 'center',
  },
  emptySubtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
  },
});

export default SearchScreen;
