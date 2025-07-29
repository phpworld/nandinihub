import React from 'react';
import { View, StyleSheet } from 'react-native';
import { ActivityIndicator, Text } from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../styles/theme';

const LoadingScreen = ({ message = 'Loading...' }) => {
  return (
    <LinearGradient
      colors={[theme.colors.gradientStart, theme.colors.gradientEnd]}
      style={styles.container}
    >
      <View style={styles.content}>
        <View style={styles.logoContainer}>
          <Text style={styles.logoText}>🕉️</Text>
          <Text style={styles.appName}>Nandini Hub</Text>
        </View>
        <Text style={styles.appName}>Nandini Hub</Text>
        <Text style={styles.tagline}>Premium Puja Samagri</Text>
        
        <View style={styles.loadingContainer}>
          <ActivityIndicator 
            size="large" 
            color={theme.colors.surface}
            style={styles.loader}
          />
          <Text style={styles.loadingText}>{message}</Text>
        </View>
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  content: {
    alignItems: 'center',
    paddingHorizontal: theme.spacing.xl,
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: theme.spacing.xl,
  },
  logoText: {
    fontSize: 60,
    marginBottom: theme.spacing.sm,
  },
  appName: {
    fontSize: 32,
    fontWeight: 'bold',
    color: theme.colors.surface,
    marginBottom: theme.spacing.sm,
    textAlign: 'center',
  },
  tagline: {
    fontSize: 16,
    color: theme.colors.surface,
    opacity: 0.9,
    marginBottom: theme.spacing.xxl,
    textAlign: 'center',
  },
  loadingContainer: {
    alignItems: 'center',
    marginTop: theme.spacing.xl,
  },
  loader: {
    marginBottom: theme.spacing.md,
  },
  loadingText: {
    fontSize: 16,
    color: theme.colors.surface,
    opacity: 0.8,
  },
});

export default LoadingScreen;
