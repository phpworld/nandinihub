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
import { useAuth } from '../../contexts/AuthContext';
import { theme } from '../../styles/theme';

const RegisterScreen = ({ navigation }) => {
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
  });
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [snackbarVisible, setSnackbarVisible] = useState(false);
  const [snackbarMessage, setSnackbarMessage] = useState('');

  const { register, isLoading } = useAuth();

  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value,
    }));
  };

  const handleRegister = async () => {
    // Validation
    const errors = validateForm();
    if (errors.length > 0) {
      showSnackbar(errors[0]);
      return;
    }

    // Prepare data for API
    const userData = {
      first_name: formData.first_name.trim(),
      last_name: formData.last_name.trim(),
      email: formData.email.trim().toLowerCase(),
      phone: formData.phone.trim(),
      password: formData.password,
    };

    const result = await register(userData);
    
    if (!result.success) {
      showSnackbar(result.message || 'Registration failed');
    }
  };

  const validateForm = () => {
    const errors = [];

    if (!formData.first_name.trim()) {
      errors.push('First name is required');
    }

    if (!formData.last_name.trim()) {
      errors.push('Last name is required');
    }

    if (!formData.email.trim()) {
      errors.push('Email is required');
    } else if (!isValidEmail(formData.email)) {
      errors.push('Please enter a valid email address');
    }

    if (!formData.phone.trim()) {
      errors.push('Phone number is required');
    } else if (!isValidPhone(formData.phone)) {
      errors.push('Please enter a valid phone number');
    }

    if (!formData.password) {
      errors.push('Password is required');
    } else if (formData.password.length < 6) {
      errors.push('Password must be at least 6 characters long');
    }

    if (formData.password !== formData.confirmPassword) {
      errors.push('Passwords do not match');
    }

    return errors;
  };

  const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const isValidPhone = (phone) => {
    const phoneRegex = /^[6-9]\d{9}$/;
    return phoneRegex.test(phone.replace(/\s+/g, ''));
  };

  const showSnackbar = (message) => {
    setSnackbarMessage(message);
    setSnackbarVisible(true);
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        <Card style={styles.card}>
          <Card.Content style={styles.cardContent}>
            <Text style={styles.title}>Create Account</Text>
            <Text style={styles.subtitle}>Join Nandini Hub today</Text>

            <View style={styles.nameRow}>
              <TextInput
                label="First Name"
                value={formData.first_name}
                onChangeText={(value) => handleInputChange('first_name', value)}
                mode="outlined"
                autoCapitalize="words"
                style={[styles.input, styles.nameInput]}
                theme={{ colors: { primary: theme.colors.primary } }}
              />
              <TextInput
                label="Last Name"
                value={formData.last_name}
                onChangeText={(value) => handleInputChange('last_name', value)}
                mode="outlined"
                autoCapitalize="words"
                style={[styles.input, styles.nameInput]}
                theme={{ colors: { primary: theme.colors.primary } }}
              />
            </View>

            <TextInput
              label="Email"
              value={formData.email}
              onChangeText={(value) => handleInputChange('email', value)}
              mode="outlined"
              keyboardType="email-address"
              autoCapitalize="none"
              autoComplete="email"
              style={styles.input}
              theme={{ colors: { primary: theme.colors.primary } }}
            />

            <TextInput
              label="Phone Number"
              value={formData.phone}
              onChangeText={(value) => handleInputChange('phone', value)}
              mode="outlined"
              keyboardType="phone-pad"
              autoComplete="tel"
              style={styles.input}
              theme={{ colors: { primary: theme.colors.primary } }}
            />

            <TextInput
              label="Password"
              value={formData.password}
              onChangeText={(value) => handleInputChange('password', value)}
              mode="outlined"
              secureTextEntry={!showPassword}
              autoCapitalize="none"
              style={styles.input}
              theme={{ colors: { primary: theme.colors.primary } }}
              right={
                <TextInput.Icon
                  icon={showPassword ? 'eye-off' : 'eye'}
                  onPress={() => setShowPassword(!showPassword)}
                />
              }
            />

            <TextInput
              label="Confirm Password"
              value={formData.confirmPassword}
              onChangeText={(value) => handleInputChange('confirmPassword', value)}
              mode="outlined"
              secureTextEntry={!showConfirmPassword}
              autoCapitalize="none"
              style={styles.input}
              theme={{ colors: { primary: theme.colors.primary } }}
              right={
                <TextInput.Icon
                  icon={showConfirmPassword ? 'eye-off' : 'eye'}
                  onPress={() => setShowConfirmPassword(!showConfirmPassword)}
                />
              }
            />

            <Button
              mode="contained"
              onPress={handleRegister}
              loading={isLoading}
              disabled={isLoading}
              style={styles.registerButton}
              contentStyle={styles.buttonContent}
            >
              Create Account
            </Button>

            <View style={styles.footer}>
              <Text style={styles.footerText}>Already have an account? </Text>
              <Button
                mode="text"
                onPress={() => navigation.goBack()}
                compact
                labelStyle={styles.linkText}
              >
                Sign In
              </Button>
            </View>
          </Card.Content>
        </Card>
      </ScrollView>

      <Snackbar
        visible={snackbarVisible}
        onDismiss={() => setSnackbarVisible(false)}
        duration={4000}
        style={styles.snackbar}
      >
        {snackbarMessage}
      </Snackbar>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  scrollContent: {
    flexGrow: 1,
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.xl,
  },
  card: {
    borderRadius: theme.borderRadius.lg,
    elevation: 4,
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
  nameRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  nameInput: {
    flex: 1,
    marginHorizontal: theme.spacing.xs,
  },
  input: {
    marginBottom: theme.spacing.md,
  },
  registerButton: {
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

export default RegisterScreen;
