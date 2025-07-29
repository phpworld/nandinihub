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
  Searchbar,
  ActivityIndicator,
} from 'react-native-paper';

import { theme } from '../../styles/theme';
import apiService from '../../services/api';
import { API_ENDPOINTS } from '../../config/api';

const CategoriesScreen = ({ navigation }) => {
  const [categories, setCategories] = useState([]);
  const [filteredCategories, setFilteredCategories] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadCategories();
  }, []);

  useEffect(() => {
    filterCategories();
  }, [searchQuery, categories]);

  const loadCategories = async () => {
    try {
      setIsLoading(true);
      const response = await apiService.get(API_ENDPOINTS.CATEGORIES.LIST);
      
      if (response.success) {
        setCategories(response.data);
      }
    } catch (error) {
      console.error('Error loading categories:', error);
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const filterCategories = () => {
    if (!searchQuery.trim()) {
      setFilteredCategories(categories);
    } else {
      const filtered = categories.filter(category =>
        category.name.toLowerCase().includes(searchQuery.toLowerCase())
      );
      setFilteredCategories(filtered);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadCategories();
  };

  const handleCategoryPress = (category) => {
    navigation.navigate('ProductsMain', {
      categoryId: category.id,
      categoryName: category.name,
    });
  };

  const renderCategory = ({ item }) => (
    <TouchableOpacity
      style={styles.categoryCard}
      onPress={() => handleCategoryPress(item)}
    >
      <Card style={styles.card}>
        <Image
          source={{ uri: item.image_url || 'https://via.placeholder.com/150' }}
          style={styles.categoryImage}
          resizeMode="cover"
        />
        <Card.Content style={styles.cardContent}>
          <Text style={styles.categoryName} numberOfLines={2}>
            {item.name}
          </Text>
          <Text style={styles.productCount}>
            {item.product_count} products
          </Text>
        </Card.Content>
      </Card>
    </TouchableOpacity>
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
      <View style={styles.header}>
        <Searchbar
          placeholder="Search categories..."
          onChangeText={setSearchQuery}
          value={searchQuery}
          style={styles.searchBar}
        />
      </View>

      <FlatList
        data={filteredCategories}
        renderItem={renderCategory}
        keyExtractor={(item) => item.id.toString()}
        numColumns={2}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>No categories found</Text>
          </View>
        }
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
    elevation: 2,
  },
  listContent: {
    padding: theme.spacing.md,
  },
  categoryCard: {
    flex: 1,
    margin: theme.spacing.sm,
    maxWidth: '48%',
  },
  card: {
    elevation: 2,
  },
  categoryImage: {
    width: '100%',
    height: 120,
  },
  cardContent: {
    padding: theme.spacing.md,
    alignItems: 'center',
  },
  categoryName: {
    fontSize: 16,
    fontWeight: '500',
    color: theme.colors.text,
    textAlign: 'center',
    marginBottom: theme.spacing.xs,
  },
  productCount: {
    fontSize: 12,
    color: theme.colors.textSecondary,
    textAlign: 'center',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingTop: theme.spacing.xxl,
  },
  emptyText: {
    fontSize: 16,
    color: theme.colors.textSecondary,
  },
});

export default CategoriesScreen;
