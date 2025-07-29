import React from 'react';
import {
  View,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Image,
} from 'react-native';
import {
  Text,
  Avatar,
  Divider,
  List,
  Button,
} from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { theme } from '../styles/theme';

const DrawerContent = ({ navigation }) => {
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    await logout();
    navigation.closeDrawer();
  };

  const navigateToScreen = (screenName) => {
    navigation.navigate(screenName);
    navigation.closeDrawer();
  };

  const menuItems = [
    {
      title: 'Home',
      icon: 'home-outline',
      onPress: () => navigateToScreen('Home'),
    },
    {
      title: 'Categories',
      icon: 'grid-outline',
      onPress: () => navigateToScreen('Categories'),
    },
    {
      title: 'Products',
      icon: 'bag-outline',
      onPress: () => navigateToScreen('Products'),
    },
    {
      title: 'My Orders',
      icon: 'receipt-outline',
      onPress: () => navigateToScreen('Orders'),
    },
    {
      title: 'My Cart',
      icon: 'cart-outline',
      onPress: () => navigateToScreen('Cart'),
    },
    {
      title: 'Addresses',
      icon: 'location-outline',
      onPress: () => navigateToScreen('Addresses'),
    },
    {
      title: 'Profile',
      icon: 'person-outline',
      onPress: () => navigateToScreen('Profile'),
    },
  ];

  return (
    <View style={styles.container}>
      {/* Header */}
      <LinearGradient
        colors={[theme.colors.primary, theme.colors.primaryDark]}
        style={styles.header}
      >
        <View style={styles.userInfo}>
          <Avatar.Text
            size={60}
            label={user?.first_name?.charAt(0) || 'U'}
            style={styles.avatar}
            labelStyle={styles.avatarLabel}
          />
          <View style={styles.userDetails}>
            <Text style={styles.userName}>
              {user?.first_name} {user?.last_name}
            </Text>
            <Text style={styles.userEmail}>{user?.email}</Text>
            <Text style={styles.userLocation}>
              {user?.city}, {user?.state}
            </Text>
          </View>
        </View>
      </LinearGradient>

      {/* Menu Items */}
      <ScrollView style={styles.menuContainer}>
        {menuItems.map((item, index) => (
          <TouchableOpacity
            key={index}
            style={styles.menuItem}
            onPress={item.onPress}
          >
            <View style={styles.menuItemContent}>
              <Ionicons
                name={item.icon}
                size={24}
                color={theme.colors.primary}
                style={styles.menuIcon}
              />
              <Text style={styles.menuText}>{item.title}</Text>
            </View>
            <Ionicons
              name="chevron-forward-outline"
              size={20}
              color={theme.colors.textSecondary}
            />
          </TouchableOpacity>
        ))}

        <Divider style={styles.divider} />

        {/* Additional Options */}

        <TouchableOpacity
          style={styles.menuItem}
          onPress={() => navigateToScreen('Help')}
        >
          <View style={styles.menuItemContent}>
            <Ionicons
              name="help-circle-outline"
              size={24}
              color={theme.colors.textSecondary}
              style={styles.menuIcon}
            />
            <Text style={styles.menuText}>Help & Support</Text>
          </View>
          <Ionicons
            name="chevron-forward-outline"
            size={20}
            color={theme.colors.textSecondary}
          />
        </TouchableOpacity>
      </ScrollView>

      {/* Footer */}
      <View style={styles.footer}>
        <Button
          mode="outlined"
          onPress={handleLogout}
          style={styles.logoutButton}
          labelStyle={styles.logoutButtonText}
          icon="logout"
        >
          Logout
        </Button>
        <Text style={styles.appVersion}>Nandini Hub v1.0.0</Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  header: {
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: 20,
  },
  userInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  avatar: {
    backgroundColor: theme.colors.surface,
  },
  avatarLabel: {
    color: theme.colors.primary,
    fontWeight: 'bold',
  },
  userDetails: {
    marginLeft: 15,
    flex: 1,
  },
  userName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: theme.colors.surface,
    marginBottom: 2,
  },
  userEmail: {
    fontSize: 14,
    color: theme.colors.surface,
    opacity: 0.9,
    marginBottom: 2,
  },
  userLocation: {
    fontSize: 12,
    color: theme.colors.surface,
    opacity: 0.8,
  },
  menuContainer: {
    flex: 1,
    paddingTop: 10,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 15,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.border,
  },
  menuItemContent: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  menuIcon: {
    marginRight: 15,
  },
  menuText: {
    fontSize: 16,
    color: theme.colors.text,
    fontWeight: '500',
  },
  divider: {
    marginVertical: 10,
  },
  footer: {
    padding: 20,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
  },
  logoutButton: {
    borderColor: theme.colors.error,
    marginBottom: 10,
  },
  logoutButtonText: {
    color: theme.colors.error,
  },
  appVersion: {
    textAlign: 'center',
    fontSize: 12,
    color: theme.colors.textSecondary,
  },
});

export default DrawerContent;
