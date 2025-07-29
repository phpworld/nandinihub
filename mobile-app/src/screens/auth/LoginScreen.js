import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import {
  Text,
  TextInput,
  Button,
  Card,
  Snackbar,
} from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { useAuth } from '../../contexts/AuthContext';
import { theme } from '../../styles/theme';

const LoginScreen = ({ navigation }) => {
  const [email, setEmail] = useState('vinaysingh43@gmail.com'); // Pre-filled for testing
  const [password, setPassword] = useState('12345678'); // Pre-filled for testing
  const [showPassword, setShowPassword] = useState(false);
  const [snackbarVisible, setSnackbarVisible] = useState(false);
  const [snackbarMessage, setSnackbarMessage] = useState('');

  const { login, isLoading, error } = useAuth();

  // Test function to verify API connectivity
  const testApiLogin = async () => {
    try {
      console.log('🧪 Testing direct API call...');
      const response = await fetch('http://192.168.1.7/nandinihub/api/v1/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: 'vinaysingh43@gmail.com',
          password: '12345678'
        })
      });

      console.log('🧪 Direct API response status:', response.status);
      const data = await response.json();
      console.log('🧪 Direct API response data:', data);

      if (data.success) {
        showSnackbar('✅ Direct API test successful!');
      } else {
        showSnackbar('❌ Direct API test failed: ' + data.message);
      }
    } catch (error) {
      console.error('🧪 Direct API test error:', error);
      showSnackbar('❌ Direct API test error: ' + error.message);
    }
  };

  const handleLogin = async () => {
    if (!email.trim() || !password.trim()) {
      showSnackbar('Please fill in all fields');
      return;
    }

    if (!isValidEmail(email)) {
      showSnackbar('Please enter a valid email address');
      return;
    }

    try {
      console.log('🔐 LoginScreen: Starting login process');
      console.log('🔐 LoginScreen: Email:', email.trim());

      // Test multiple API endpoints for better connectivity
      console.log('🧪 Testing API connectivity...');

      const apiUrls = [
        'http://192.168.1.7/nandinihub/api/v1/auth/login',
        'http://localhost/nandinihub/api/v1/auth/login',
        'http://127.0.0.1/nandinihub/api/v1/auth/login'
      ];

      let directResponse = null;
      let workingUrl = null;

      for (const url of apiUrls) {
        try {
          console.log(`🧪 Trying URL: ${url}`);
          directResponse = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              email: email.trim(),
              password: password
            })
          });

          if (directResponse.ok) {
            workingUrl = url;
            console.log(`✅ Working URL found: ${url}`);
            break;
          }
        } catch (error) {
          console.log(`❌ Failed URL: ${url} - ${error.message}`);
          continue;
        }
      }

      if (directResponse && directResponse.ok && workingUrl) {
        console.log('🧪 Direct API response status:', directResponse.status);
        const directData = await directResponse.json();
        console.log('🧪 Direct API response data:', directData);

        if (directData.success) {
          console.log('✅ Direct API login successful!');
          showSnackbar('✅ API connection successful!');

          // Update API config to use working URL
          const baseUrl = workingUrl.replace('/auth/login', '');
          console.log('🔧 Updating API base URL to:', baseUrl);

          // Now try the normal login flow
          const result = await login({ email: email.trim(), password });
          console.log('🔐 LoginScreen: Login result:', result);

          if (result.success) {
            console.log('✅ LoginScreen: Login successful, navigation should happen automatically');
            showSnackbar('Login successful! Welcome back.');
          } else {
            console.log('❌ LoginScreen: Login failed:', result.message);
            showSnackbar(result.message || 'Login failed. Please try again.');
          }
        } else {
          showSnackbar('❌ Login failed: ' + directData.message);
        }
      } else {
        console.log('❌ All API URLs failed');
        showSnackbar('❌ Cannot connect to server. Please check your network connection.');
      }
    } catch (error) {
      console.error('❌ LoginScreen: Login error:', error);
      showSnackbar('❌ Login error: ' + error.message);
    }
  };

  const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const showSnackbar = (message) => {
    setSnackbarMessage(message);
    setSnackbarVisible(true);
  };

  return (
    <LinearGradient
      colors={[theme.colors.gradientStart, theme.colors.gradientEnd]}
      style={styles.container}
    >
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardAvoid}
      >
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
        >
          <View style={styles.header}>
            <View style={styles.logoContainer}>
              <Text style={styles.logoText}>🕉️</Text>
            </View>
            <Text style={styles.appName}>Nandini Hub</Text>
            <Text style={styles.tagline}>Premium Puja Samagri</Text>
          </View>

          <Card style={styles.card}>
            <Card.Content style={styles.cardContent}>
              <Text style={styles.title}>Welcome Back</Text>
              <Text style={styles.subtitle}>Sign in to your account</Text>

              <TextInput
                label="Email"
                value={email}
                onChangeText={setEmail}
                mode="outlined"
                keyboardType="email-address"
                autoCapitalize="none"
                autoComplete="email"
                style={styles.input}
                theme={{ colors: { primary: theme.colors.primary } }}
              />

              <TextInput
                label="Password"
                value={password}
                onChangeText={setPassword}
                mode="outlined"
                secureTextEntry={!showPassword}
                autoCapitalize="none"
                autoComplete="password"
                style={styles.input}
                theme={{ colors: { primary: theme.colors.primary } }}
                right={
                  <TextInput.Icon
                    icon={showPassword ? 'eye-off' : 'eye'}
                    onPress={() => setShowPassword(!showPassword)}
                  />
                }
              />

              <Button
                mode="contained"
                onPress={handleLogin}
                loading={isLoading}
                disabled={isLoading}
                style={styles.loginButton}
                contentStyle={styles.buttonContent}
              >
                Sign In
              </Button>

              {/* Temporary test button */}
              <Button
                mode="outlined"
                onPress={testApiLogin}
                style={[styles.loginButton, { marginTop: 10 }]}
                contentStyle={styles.buttonContent}
              >
                Test API Direct
              </Button>

              <View style={styles.footer}>
                <Text style={styles.footerText}>Don't have an account? </Text>
                <Button
                  mode="text"
                  onPress={() => navigation.navigate('Register')}
                  compact
                  labelStyle={styles.linkText}
                >
                  Sign Up
                </Button>
              </View>
            </Card.Content>
          </Card>
        </ScrollView>
      </KeyboardAvoidingView>

      <Snackbar
        visible={snackbarVisible}
        onDismiss={() => setSnackbarVisible(false)}
        duration={4000}
        style={styles.snackbar}
      >
        {snackbarMessage}
      </Snackbar>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  keyboardAvoid: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.xl,
  },
  header: {
    alignItems: 'center',
    marginBottom: theme.spacing.xl,
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: theme.spacing.md,
  },
  logoText: {
    fontSize: 48,
    marginBottom: theme.spacing.sm,
  },
  appName: {
    fontSize: 28,
    fontWeight: 'bold',
    color: theme.colors.surface,
    marginBottom: theme.spacing.xs,
  },
  tagline: {
    fontSize: 14,
    color: theme.colors.surface,
    opacity: 0.9,
  },
  card: {
    borderRadius: theme.borderRadius.lg,
    elevation: 8,
  },
  cardContent: {
    paddingVertical: theme.spacing.xl,
    paddingHorizontal: theme.spacing.lg,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.text,
    textAlign: 'center',
    marginBottom: theme.spacing.xs,
  },
  subtitle: {
    fontSize: 16,
    color: theme.colors.textSecondary,
    textAlign: 'center',
    marginBottom: theme.spacing.xl,
  },
  input: {
    marginBottom: theme.spacing.md,
  },
  loginButton: {
    marginTop: theme.spacing.lg,
    marginBottom: theme.spacing.lg,
    backgroundColor: theme.colors.primary,
  },
  buttonContent: {
    paddingVertical: theme.spacing.sm,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  footerText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  linkText: {
    color: theme.colors.primary,
    fontSize: 14,
    fontWeight: 'bold',
  },
  snackbar: {
    backgroundColor: theme.colors.error,
  },
});

export default LoginScreen;
