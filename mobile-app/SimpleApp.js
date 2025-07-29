import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Provider as PaperProvider } from 'react-native-paper';

const SimpleApp = () => {
  return (
    <PaperProvider>
      <View style={styles.container}>
        <Text style={styles.title}>🎉 Nandini Hub</Text>
        <Text style={styles.subtitle}>Mobile App is Working!</Text>
        <Text style={styles.info}>✅ Expo SDK 53</Text>
        <Text style={styles.info}>✅ React Native 0.79.5</Text>
        <Text style={styles.info}>✅ React 19.0.0</Text>
      </View>
    </PaperProvider>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
    padding: 20,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#FF6B35',
    marginBottom: 10,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 18,
    color: '#333',
    marginBottom: 30,
    textAlign: 'center',
  },
  info: {
    fontSize: 16,
    color: '#666',
    marginBottom: 10,
    textAlign: 'center',
  },
});

export default SimpleApp;
