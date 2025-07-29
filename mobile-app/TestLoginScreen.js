import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Alert,
  ScrollView,
  ActivityIndicator,
} from 'react-native';
import authService from './src/services/authService';
import { API_CONFIG } from './src/config/api';

const TestLoginScreen = () => {
  const [email, setEmail] = useState('vinaysingh43@gmail.com');
  const [password, setPassword] = useState('12345678');
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [autoLoginDone, setAutoLoginDone] = useState(false);

  const handleLogin = async () => {
    setLoading(true);
    setResult(null);

    try {
      console.log('🔗 API Base URL:', API_CONFIG.BASE_URL);
      
      const response = await authService.login({ email, password });
      
      console.log('📱 Login response:', response);
      
      if (response.success) {
        setResult({
          success: true,
          message: 'Login successful!',
          user: response.data.user,
          token: response.data.token ? 'Token received' : 'No token',
        });
        Alert.alert('Success', 'Login successful!');
      } else {
        setResult({
          success: false,
          message: response.message || 'Login failed',
          error: response.errors || response.error,
        });
        Alert.alert('Error', response.message || 'Login failed');
      }
    } catch (error) {
      console.error('❌ Login error:', error);
      setResult({
        success: false,
        message: 'Network error or server unreachable',
        error: error.message,
      });
      Alert.alert('Error', 'Network error. Check your connection.');
    } finally {
      setLoading(false);
    }
  };

  const testApiConnection = async () => {
    setLoading(true);
    try {
      const response = await fetch(API_CONFIG.BASE_URL.replace('/api/v1', ''));
      const text = await response.text();
      Alert.alert('API Test', `Status: ${response.status}\nResponse: ${text.substring(0, 100)}...`);
    } catch (error) {
      Alert.alert('API Test Failed', error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.title}>🔐 Login Test</Text>
      
      <Text style={styles.label}>API URL:</Text>
      <Text style={styles.apiUrl}>{API_CONFIG.BASE_URL}</Text>
      
      <Text style={styles.label}>Email:</Text>
      <TextInput
        style={styles.input}
        value={email}
        onChangeText={setEmail}
        placeholder="Enter email"
        keyboardType="email-address"
        autoCapitalize="none"
      />
      
      <Text style={styles.label}>Password:</Text>
      <TextInput
        style={styles.input}
        value={password}
        onChangeText={setPassword}
        placeholder="Enter password"
        secureTextEntry
      />
      
      <TouchableOpacity
        style={[styles.button, styles.loginButton]}
        onPress={handleLogin}
        disabled={loading}
      >
        {loading ? (
          <ActivityIndicator color="white" />
        ) : (
          <Text style={styles.buttonText}>Login</Text>
        )}
      </TouchableOpacity>
      
      <TouchableOpacity
        style={[styles.button, styles.testButton]}
        onPress={testApiConnection}
        disabled={loading}
      >
        <Text style={styles.buttonText}>Test API Connection</Text>
      </TouchableOpacity>
      
      {result && (
        <View style={[styles.result, result.success ? styles.success : styles.error]}>
          <Text style={styles.resultTitle}>
            {result.success ? '✅ Success' : '❌ Error'}
          </Text>
          <Text style={styles.resultText}>Message: {result.message}</Text>
          {result.user && (
            <Text style={styles.resultText}>
              User: {result.user.first_name} {result.user.last_name}
            </Text>
          )}
          {result.token && (
            <Text style={styles.resultText}>Token: {result.token}</Text>
          )}
          {result.error && (
            <Text style={styles.resultText}>Error: {JSON.stringify(result.error)}</Text>
          )}
        </View>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    backgroundColor: '#f5f5f5',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 30,
    marginTop: 50,
  },
  label: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
    marginTop: 15,
  },
  apiUrl: {
    fontSize: 12,
    color: '#666',
    backgroundColor: '#eee',
    padding: 10,
    borderRadius: 5,
    fontFamily: 'monospace',
  },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: 'white',
  },
  button: {
    borderRadius: 8,
    padding: 15,
    alignItems: 'center',
    marginTop: 20,
  },
  loginButton: {
    backgroundColor: '#007AFF',
  },
  testButton: {
    backgroundColor: '#34C759',
  },
  buttonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  result: {
    marginTop: 20,
    padding: 15,
    borderRadius: 8,
    borderWidth: 1,
  },
  success: {
    backgroundColor: '#d4edda',
    borderColor: '#c3e6cb',
  },
  error: {
    backgroundColor: '#f8d7da',
    borderColor: '#f5c6cb',
  },
  resultTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  resultText: {
    fontSize: 14,
    marginBottom: 5,
  },
});

export default TestLoginScreen;
