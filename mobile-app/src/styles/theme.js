import { DefaultTheme } from 'react-native-paper';

export const theme = {
  ...DefaultTheme,
  colors: {
    ...DefaultTheme.colors,
    primary: '#FF6B35',
    primaryDark: '#E55A2B',
    secondary: '#FFA726',
    accent: '#FF8A65',
    background: '#FFFFFF',
    surface: '#FFFFFF',
    text: '#212121',
    textSecondary: '#757575',
    placeholder: '#BDBDBD',
    border: '#E0E0E0',
    success: '#4CAF50',
    warning: '#FF9800',
    error: '#F44336',
    info: '#2196F3',
    
    // Custom colors for the app
    cardBackground: '#FAFAFA',
    shadowColor: '#000000',
    gradientStart: '#FF6B35',
    gradientEnd: '#FF8A65',
    
    // Status colors
    pending: '#FF9800',
    processing: '#2196F3',
    shipped: '#9C27B0',
    delivered: '#4CAF50',
    cancelled: '#F44336',
  },
  
  // Typography
  fonts: {
    ...DefaultTheme.fonts,
    regular: {
      fontFamily: 'System',
      fontWeight: '400',
    },
    medium: {
      fontFamily: 'System',
      fontWeight: '500',
    },
    bold: {
      fontFamily: 'System',
      fontWeight: '700',
    },
  },
  
  // Spacing
  spacing: {
    xs: 4,
    sm: 8,
    md: 16,
    lg: 24,
    xl: 32,
    xxl: 48,
  },
  
  // Border radius
  borderRadius: {
    sm: 4,
    md: 8,
    lg: 12,
    xl: 16,
    round: 50,
  },
  
  // Shadows
  shadows: {
    small: {
      shadowColor: '#000',
      shadowOffset: {
        width: 0,
        height: 1,
      },
      shadowOpacity: 0.18,
      shadowRadius: 1.0,
      elevation: 1,
    },
    medium: {
      shadowColor: '#000',
      shadowOffset: {
        width: 0,
        height: 2,
      },
      shadowOpacity: 0.25,
      shadowRadius: 3.84,
      elevation: 5,
    },
    large: {
      shadowColor: '#000',
      shadowOffset: {
        width: 0,
        height: 4,
      },
      shadowOpacity: 0.30,
      shadowRadius: 4.65,
      elevation: 8,
    },
  },
  
  // Animation durations
  animation: {
    fast: 150,
    normal: 300,
    slow: 500,
  },
};
