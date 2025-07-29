import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  TouchableOpacity,
  FlatList,
  Modal,
} from 'react-native';
import {
  Searchbar,
  Text,
  Chip,
  Card,
  IconButton,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../styles/theme';

const EnhancedSearchBar = ({
  value,
  onChangeText,
  onSubmitEditing,
  placeholder = 'Search products...',
  suggestions = [],
  recentSearches = [],
  onSuggestionPress,
  onFilterPress,
  showFilters = true,
}) => {
  const [showSuggestions, setShowSuggestions] = useState(false);
  const [showFiltersModal, setShowFiltersModal] = useState(false);

  const popularSearches = [
    'Incense Sticks',
    'Camphor',
    'Diya',
    'Kumkum',
    'Turmeric',
    'Sandalwood',
    'Puja Thali',
    'Flowers',
  ];

  const handleSearchFocus = () => {
    setShowSuggestions(true);
  };

  const handleSearchBlur = () => {
    // Delay hiding suggestions to allow for suggestion selection
    setTimeout(() => setShowSuggestions(false), 200);
  };

  const handleSuggestionSelect = (suggestion) => {
    onChangeText(suggestion);
    setShowSuggestions(false);
    onSuggestionPress?.(suggestion);
  };

  const renderSuggestionItem = ({ item }) => (
    <TouchableOpacity
      style={styles.suggestionItem}
      onPress={() => handleSuggestionSelect(item)}
    >
      <Ionicons
        name="search-outline"
        size={16}
        color={theme.colors.textSecondary}
        style={styles.suggestionIcon}
      />
      <Text style={styles.suggestionText}>{item}</Text>
    </TouchableOpacity>
  );

  const renderRecentSearchItem = ({ item }) => (
    <TouchableOpacity
      style={styles.suggestionItem}
      onPress={() => handleSuggestionSelect(item)}
    >
      <Ionicons
        name="time-outline"
        size={16}
        color={theme.colors.textSecondary}
        style={styles.suggestionIcon}
      />
      <Text style={styles.suggestionText}>{item}</Text>
    </TouchableOpacity>
  );

  const renderPopularChip = (search, index) => (
    <Chip
      key={index}
      mode="outlined"
      onPress={() => handleSuggestionSelect(search)}
      style={styles.popularChip}
      textStyle={styles.popularChipText}
    >
      {search}
    </Chip>
  );

  return (
    <View style={styles.container}>
      <View style={styles.searchContainer}>
        <Searchbar
          placeholder={placeholder}
          value={value}
          onChangeText={onChangeText}
          onSubmitEditing={onSubmitEditing}
          onFocus={handleSearchFocus}
          onBlur={handleSearchBlur}
          style={styles.searchBar}
          inputStyle={styles.searchInput}
          iconColor={theme.colors.primary}
          placeholderTextColor={theme.colors.textSecondary}
        />
        {showFilters && (
          <TouchableOpacity
            style={styles.filterButton}
            onPress={() => setShowFiltersModal(true)}
          >
            <Ionicons
              name="options-outline"
              size={24}
              color={theme.colors.primary}
            />
          </TouchableOpacity>
        )}
      </View>

      {/* Suggestions Dropdown */}
      {showSuggestions && (
        <Card style={styles.suggestionsCard}>
          <View style={styles.suggestionsContainer}>
            {/* Current suggestions based on input */}
            {suggestions.length > 0 && (
              <View style={styles.suggestionSection}>
                <Text style={styles.sectionTitle}>Suggestions</Text>
                <FlatList
                  data={suggestions}
                  renderItem={renderSuggestionItem}
                  keyExtractor={(item, index) => `suggestion-${index}`}
                  showsVerticalScrollIndicator={false}
                />
              </View>
            )}

            {/* Recent searches */}
            {recentSearches.length > 0 && (
              <View style={styles.suggestionSection}>
                <Text style={styles.sectionTitle}>Recent Searches</Text>
                <FlatList
                  data={recentSearches.slice(0, 5)}
                  renderItem={renderRecentSearchItem}
                  keyExtractor={(item, index) => `recent-${index}`}
                  showsVerticalScrollIndicator={false}
                />
              </View>
            )}

            {/* Popular searches */}
            <View style={styles.suggestionSection}>
              <Text style={styles.sectionTitle}>Popular Searches</Text>
              <View style={styles.popularSearches}>
                {popularSearches.map((search, index) =>
                  renderPopularChip(search, index)
                )}
              </View>
            </View>
          </View>
        </Card>
      )}

      {/* Filters Modal */}
      <Modal
        visible={showFiltersModal}
        transparent
        animationType="slide"
        onRequestClose={() => setShowFiltersModal(false)}
      >
        <View style={styles.modalOverlay}>
          <Card style={styles.filtersModal}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Search Filters</Text>
              <IconButton
                icon="close"
                size={24}
                onPress={() => setShowFiltersModal(false)}
              />
            </View>
            <View style={styles.modalContent}>
              <Text style={styles.filterLabel}>Categories</Text>
              <View style={styles.filterChips}>
                {['All', 'Incense', 'Oils', 'Flowers', 'Accessories'].map(
                  (category, index) => (
                    <Chip
                      key={index}
                      mode="outlined"
                      selected={index === 0}
                      onPress={() => {}}
                      style={styles.filterChip}
                    >
                      {category}
                    </Chip>
                  )
                )}
              </View>
              
              <Text style={styles.filterLabel}>Price Range</Text>
              <View style={styles.filterChips}>
                {['All', '₹0-₹100', '₹100-₹500', '₹500+'].map(
                  (range, index) => (
                    <Chip
                      key={index}
                      mode="outlined"
                      selected={index === 0}
                      onPress={() => {}}
                      style={styles.filterChip}
                    >
                      {range}
                    </Chip>
                  )
                )}
              </View>
            </View>
          </Card>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: theme.spacing.md,
    zIndex: 1000,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: theme.spacing.md,
  },
  searchBar: {
    flex: 1,
    elevation: 2,
    backgroundColor: theme.colors.surface,
  },
  searchInput: {
    fontSize: 16,
  },
  filterButton: {
    marginLeft: theme.spacing.sm,
    padding: theme.spacing.sm,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    elevation: 2,
  },
  suggestionsCard: {
    position: 'absolute',
    top: 60,
    left: theme.spacing.md,
    right: theme.spacing.md,
    maxHeight: 300,
    elevation: 8,
    zIndex: 1001,
  },
  suggestionsContainer: {
    padding: theme.spacing.md,
  },
  suggestionSection: {
    marginBottom: theme.spacing.md,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.sm,
  },
  suggestionItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: theme.spacing.sm,
  },
  suggestionIcon: {
    marginRight: theme.spacing.sm,
  },
  suggestionText: {
    fontSize: 16,
    color: theme.colors.text,
  },
  popularSearches: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  popularChip: {
    marginRight: theme.spacing.sm,
    marginBottom: theme.spacing.sm,
  },
  popularChipText: {
    fontSize: 12,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  filtersModal: {
    margin: theme.spacing.md,
    borderRadius: theme.borderRadius.lg,
    maxHeight: '70%',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: theme.spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.border,
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.text,
  },
  modalContent: {
    padding: theme.spacing.lg,
  },
  filterLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.sm,
    marginTop: theme.spacing.md,
  },
  filterChips: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  filterChip: {
    marginRight: theme.spacing.sm,
    marginBottom: theme.spacing.sm,
  },
});

export default EnhancedSearchBar;
