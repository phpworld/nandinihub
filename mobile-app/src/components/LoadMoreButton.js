import React from 'react';
import {
  View,
  StyleSheet,
  TouchableOpacity,
} from 'react-native';
import {
  Text,
  Button,
  ActivityIndicator,
} from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { theme } from '../styles/theme';

const LoadMoreButton = ({
  onPress,
  loading = false,
  disabled = false,
  hasMore = true,
  variant = 'outlined',
  style,
  text = 'Load More',
  loadingText = 'Loading...',
  noMoreText = 'No more items',
}) => {
  if (!hasMore) {
    return (
      <View style={[styles.container, style]}>
        <Text style={styles.noMoreText}>{noMoreText}</Text>
      </View>
    );
  }

  if (variant === 'gradient') {
    return (
      <TouchableOpacity
        style={[styles.gradientContainer, style]}
        onPress={onPress}
        disabled={loading || disabled}
        activeOpacity={0.8}
      >
        <LinearGradient
          colors={[theme.colors.primary, theme.colors.primaryDark]}
          style={styles.gradientButton}
          start={{ x: 0, y: 0 }}
          end={{ x: 1, y: 0 }}
        >
          {loading ? (
            <View style={styles.loadingContainer}>
              <ActivityIndicator size="small" color={theme.colors.surface} />
              <Text style={styles.gradientButtonText}>{loadingText}</Text>
            </View>
          ) : (
            <View style={styles.buttonContent}>
              <Text style={styles.gradientButtonText}>{text}</Text>
              <Ionicons
                name="chevron-down"
                size={20}
                color={theme.colors.surface}
                style={styles.icon}
              />
            </View>
          )}
        </LinearGradient>
      </TouchableOpacity>
    );
  }

  if (variant === 'minimal') {
    return (
      <TouchableOpacity
        style={[styles.minimalContainer, style]}
        onPress={onPress}
        disabled={loading || disabled}
        activeOpacity={0.7}
      >
        {loading ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="small" color={theme.colors.primary} />
            <Text style={styles.minimalText}>{loadingText}</Text>
          </View>
        ) : (
          <View style={styles.buttonContent}>
            <Text style={styles.minimalText}>{text}</Text>
            <Ionicons
              name="chevron-down"
              size={18}
              color={theme.colors.primary}
              style={styles.icon}
            />
          </View>
        )}
      </TouchableOpacity>
    );
  }

  // Default outlined variant
  return (
    <View style={[styles.container, style]}>
      <Button
        mode="outlined"
        onPress={onPress}
        loading={loading}
        disabled={loading || disabled}
        style={styles.outlinedButton}
        contentStyle={styles.buttonContentStyle}
        labelStyle={styles.outlinedButtonText}
        icon={loading ? undefined : "chevron-down"}
      >
        {loading ? loadingText : text}
      </Button>
    </View>
  );
};

// Enhanced Load More with pagination info
export const LoadMoreWithInfo = ({
  currentPage,
  totalPages,
  totalItems,
  itemsPerPage,
  onPress,
  loading,
  hasMore,
  style,
}) => {
  const currentItems = Math.min(currentPage * itemsPerPage, totalItems);
  
  return (
    <View style={[styles.infoContainer, style]}>
      <Text style={styles.infoText}>
        Showing {currentItems} of {totalItems} items
      </Text>
      
      {hasMore && (
        <LoadMoreButton
          onPress={onPress}
          loading={loading}
          hasMore={hasMore}
          variant="gradient"
          style={styles.infoButton}
        />
      )}
      
      <Text style={styles.pageInfo}>
        Page {currentPage} of {totalPages}
      </Text>
    </View>
  );
};

// Infinite scroll trigger component
export const InfiniteScrollTrigger = ({
  onLoadMore,
  loading,
  hasMore,
  threshold = 100,
}) => {
  if (!hasMore) return null;

  return (
    <View style={styles.infiniteContainer}>
      {loading ? (
        <View style={styles.infiniteLoading}>
          <ActivityIndicator size="large" color={theme.colors.primary} />
          <Text style={styles.infiniteText}>Loading more items...</Text>
        </View>
      ) : (
        <TouchableOpacity
          style={styles.infiniteTrigger}
          onPress={onLoadMore}
        >
          <Ionicons
            name="refresh"
            size={24}
            color={theme.colors.primary}
          />
          <Text style={styles.infiniteText}>Tap to load more</Text>
        </TouchableOpacity>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    paddingVertical: theme.spacing.lg,
  },
  outlinedButton: {
    borderColor: theme.colors.primary,
    borderWidth: 2,
    borderRadius: theme.borderRadius.lg,
    paddingHorizontal: theme.spacing.lg,
  },
  buttonContentStyle: {
    paddingVertical: theme.spacing.sm,
  },
  outlinedButtonText: {
    color: theme.colors.primary,
    fontWeight: 'bold',
    fontSize: 16,
  },
  gradientContainer: {
    borderRadius: theme.borderRadius.lg,
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
    borderRadius: theme.borderRadius.lg,
    paddingVertical: theme.spacing.md,
    paddingHorizontal: theme.spacing.xl,
    minWidth: 150,
  },
  gradientButtonText: {
    color: theme.colors.surface,
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  minimalContainer: {
    paddingVertical: theme.spacing.md,
    paddingHorizontal: theme.spacing.lg,
    borderRadius: theme.borderRadius.md,
    backgroundColor: theme.colors.surface,
    elevation: 1,
  },
  minimalText: {
    color: theme.colors.primary,
    fontSize: 14,
    fontWeight: '600',
  },
  buttonContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  icon: {
    marginLeft: theme.spacing.sm,
  },
  noMoreText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    fontStyle: 'italic',
    textAlign: 'center',
  },
  infoContainer: {
    alignItems: 'center',
    paddingVertical: theme.spacing.lg,
    backgroundColor: theme.colors.background,
  },
  infoText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    marginBottom: theme.spacing.md,
  },
  infoButton: {
    marginVertical: theme.spacing.md,
  },
  pageInfo: {
    color: theme.colors.textSecondary,
    fontSize: 12,
    marginTop: theme.spacing.sm,
  },
  infiniteContainer: {
    paddingVertical: theme.spacing.xl,
    alignItems: 'center',
  },
  infiniteLoading: {
    alignItems: 'center',
  },
  infiniteTrigger: {
    alignItems: 'center',
    paddingVertical: theme.spacing.md,
    paddingHorizontal: theme.spacing.lg,
    borderRadius: theme.borderRadius.md,
    backgroundColor: theme.colors.surface,
    elevation: 2,
  },
  infiniteText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    marginTop: theme.spacing.sm,
  },
});

export default LoadMoreButton;
