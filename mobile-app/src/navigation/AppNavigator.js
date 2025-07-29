import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { Ionicons } from '@expo/vector-icons';
import { TouchableOpacity } from 'react-native';

import { useAuth } from '../contexts/AuthContext';
import { theme } from '../styles/theme';
import DrawerContent from '../components/DrawerContent';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';

// Main Screens
import HomeScreen from '../screens/main/HomeScreen';
import ProductsScreen from '../screens/main/ProductsScreen';
import ProductDetailScreen from '../screens/main/ProductDetailScreen';
import CartScreen from '../screens/main/CartScreen';
import ProfileScreen from '../screens/main/ProfileScreen';
import CategoriesScreen from '../screens/main/CategoriesScreen';
import SearchScreen from '../screens/main/SearchScreen';
import OrdersScreen from '../screens/main/OrdersScreen';
import OrderDetailScreen from '../screens/main/OrderDetailScreen';
import AddressesScreen from '../screens/main/AddressesScreen';
import AddEditAddressScreen from '../screens/main/AddEditAddressScreen';
import CheckoutScreen from '../screens/main/CheckoutScreen';
import PaymentScreen from '../screens/main/PaymentScreen';

const Stack = createStackNavigator();
const Tab = createBottomTabNavigator();
const Drawer = createDrawerNavigator();

// Helper function to get screen options with drawer button
const getScreenOptions = (navigation) => ({
  headerStyle: {
    backgroundColor: theme.colors.primary,
  },
  headerTintColor: theme.colors.surface,
  headerTitleStyle: {
    fontWeight: 'bold',
  },
  headerLeft: () => (
    <TouchableOpacity
      onPress={() => navigation.openDrawer()}
      style={{ marginLeft: 15 }}
    >
      <Ionicons name="menu" size={24} color={theme.colors.surface} />
    </TouchableOpacity>
  ),
});

// Auth Stack Navigator
const AuthStack = () => (
  <Stack.Navigator
    screenOptions={{
      headerStyle: {
        backgroundColor: theme.colors.primary,
      },
      headerTintColor: theme.colors.surface,
      headerTitleStyle: {
        fontWeight: 'bold',
      },
    }}
  >
    <Stack.Screen 
      name="Login" 
      component={LoginScreen}
      options={{ headerShown: false }}
    />
    <Stack.Screen 
      name="Register" 
      component={RegisterScreen}
      options={{ title: 'Create Account' }}
    />
  </Stack.Navigator>
);

// Home Stack Navigator
const HomeStack = ({ navigation }) => (
  <Stack.Navigator
    screenOptions={getScreenOptions(navigation)}
  >
    <Stack.Screen 
      name="HomeMain" 
      component={HomeScreen}
      options={{ title: 'Nandini Hub' }}
    />
    <Stack.Screen 
      name="ProductDetail" 
      component={ProductDetailScreen}
      options={{ title: 'Product Details' }}
    />
    <Stack.Screen 
      name="Search" 
      component={SearchScreen}
      options={{ title: 'Search Products' }}
    />
  </Stack.Navigator>
);

// Products Stack Navigator
const ProductsStack = ({ navigation }) => (
  <Stack.Navigator
    screenOptions={getScreenOptions(navigation)}
  >
    <Stack.Screen 
      name="ProductsMain" 
      component={ProductsScreen}
      options={{ title: 'Products' }}
    />
    <Stack.Screen 
      name="Categories" 
      component={CategoriesScreen}
      options={{ title: 'Categories' }}
    />
    <Stack.Screen 
      name="ProductDetail" 
      component={ProductDetailScreen}
      options={{ title: 'Product Details' }}
    />
  </Stack.Navigator>
);

// Cart Stack Navigator
const CartStack = ({ navigation }) => (
  <Stack.Navigator
    screenOptions={getScreenOptions(navigation)}
  >
    <Stack.Screen 
      name="CartMain" 
      component={CartScreen}
      options={{ title: 'Shopping Cart' }}
    />
    <Stack.Screen
      name="Checkout"
      component={CheckoutScreen}
      options={{ title: 'Checkout' }}
    />
    <Stack.Screen
      name="Payment"
      component={PaymentScreen}
      options={{ title: 'Payment' }}
    />
  </Stack.Navigator>
);

// Profile Stack Navigator
const ProfileStack = ({ navigation }) => (
  <Stack.Navigator
    screenOptions={getScreenOptions(navigation)}
  >
    <Stack.Screen 
      name="ProfileMain" 
      component={ProfileScreen}
      options={{ title: 'Profile' }}
    />
    <Stack.Screen 
      name="Orders" 
      component={OrdersScreen}
      options={{ title: 'My Orders' }}
    />
    <Stack.Screen 
      name="OrderDetail" 
      component={OrderDetailScreen}
      options={{ title: 'Order Details' }}
    />
    <Stack.Screen 
      name="Addresses" 
      component={AddressesScreen}
      options={{ title: 'My Addresses' }}
    />
    <Stack.Screen 
      name="AddEditAddress" 
      component={AddEditAddressScreen}
      options={({ route }) => ({
        title: route.params?.address ? 'Edit Address' : 'Add Address'
      })}
    />
  </Stack.Navigator>
);

// Main Tab Navigator
const MainTabs = () => (
  <Tab.Navigator
    screenOptions={({ route }) => ({
      tabBarIcon: ({ focused, color, size }) => {
        let iconName;

        if (route.name === 'Home') {
          iconName = focused ? 'home' : 'home-outline';
        } else if (route.name === 'Products') {
          iconName = focused ? 'grid' : 'grid-outline';
        } else if (route.name === 'Cart') {
          iconName = focused ? 'bag' : 'bag-outline';
        } else if (route.name === 'Profile') {
          iconName = focused ? 'person' : 'person-outline';
        }

        return <Ionicons name={iconName} size={size} color={color} />;
      },
      tabBarActiveTintColor: theme.colors.primary,
      tabBarInactiveTintColor: theme.colors.textSecondary,
      tabBarStyle: {
        backgroundColor: theme.colors.surface,
        borderTopColor: theme.colors.border,
      },
      headerShown: false,
    })}
  >
    <Tab.Screen name="Home" component={HomeStack} />
    <Tab.Screen name="Products" component={ProductsStack} />
    <Tab.Screen name="Cart" component={CartStack} />
    <Tab.Screen name="Profile" component={ProfileStack} />
  </Tab.Navigator>
);

// Main Drawer Navigator
const MainDrawer = () => (
  <Drawer.Navigator
    drawerContent={(props) => <DrawerContent {...props} />}
    screenOptions={{
      headerShown: false,
      drawerStyle: {
        width: 280,
      },
      drawerType: 'front',
      overlayColor: 'rgba(0,0,0,0.5)',
    }}
  >
    <Drawer.Screen name="MainTabs" component={MainTabs} />
  </Drawer.Navigator>
);

// Main App Navigator
const AppNavigator = () => {
  const { isAuthenticated, isLoading, user } = useAuth();

  console.log('🧭 AppNavigator state:', { isAuthenticated, isLoading, hasUser: !!user });

  if (isLoading) {
    console.log('⏳ App is loading...');
    return null; // LoadingScreen is shown in App.js
  }

  if (isAuthenticated) {
    console.log('✅ User is authenticated, showing MainDrawer');
    return <MainDrawer />;
  } else {
    console.log('❌ User not authenticated, showing AuthStack');
    return <AuthStack />;
  }
};

export default AppNavigator;
