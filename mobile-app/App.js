import React, {useEffect, useState} from 'react';
import {StatusBar, LogBox} from 'react-native';
import {NavigationContainer} from '@react-navigation/native';
import {Provider as PaperProvider} from 'react-native-paper';
import * as SplashScreen from 'expo-splash-screen';

import {AuthProvider} from './src/contexts/AuthContext';
import {CartProvider} from './src/contexts/CartContext';
import {NotificationProvider} from './src/contexts/NotificationContext';
import AppNavigator from './src/navigation/AppNavigator';
import {theme} from './src/styles/theme';
import LoadingScreen from './src/components/LoadingScreen';

// Ignore specific warnings
LogBox.ignoreLogs([
  'Non-serializable values were found in the navigation state',
  'VirtualizedLists should never be nested',
  'Warning: Cannot update a component',
]);

// Keep the splash screen visible while we fetch resources
SplashScreen.preventAutoHideAsync();

const App = () => {
  const [appIsReady, setAppIsReady] = useState(false);

  useEffect(() => {
    async function prepare() {
      try {
        // Artificially delay for demo purposes
        await new Promise(resolve => setTimeout(resolve, 2000));
      } catch (e) {
        console.warn(e);
      } finally {
        // Tell the application to render
        setAppIsReady(true);
      }
    }

    prepare();
  }, []);

  const onLayoutRootView = React.useCallback(async () => {
    if (appIsReady) {
      // This tells the splash screen to hide immediately
      await SplashScreen.hideAsync();
    }
  }, [appIsReady]);

  if (!appIsReady) {
    return <LoadingScreen />;
  }

  return (
    <PaperProvider theme={theme}>
      <AuthProvider>
        <CartProvider>
          <NotificationProvider>
            <NavigationContainer onReady={onLayoutRootView}>
              <StatusBar
                barStyle="light-content"
                backgroundColor={theme.colors.primary}
                translucent={false}
              />
              <AppNavigator />
            </NavigationContainer>
          </NotificationProvider>
        </CartProvider>
      </AuthProvider>
    </PaperProvider>
  );
};

export default App;
