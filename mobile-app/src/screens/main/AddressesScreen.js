import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Text, Card, Button } from 'react-native-paper';
import { theme } from '../../styles/theme';

const AddressesScreen = ({ navigation }) => {
  return (
    <View style={styles.container}>
      <Card style={styles.card}>
        <Card.Content>
          <Text style={styles.title}>My Addresses</Text>
          <Text style={styles.message}>
            This screen will show saved addresses and allow users to add, 
            edit, or delete shipping addresses.
          </Text>
          <Button
            mode="contained"
            onPress={() => navigation.navigate('AddEditAddress')}
            style={styles.button}
          >
            Add New Address
          </Button>
        </Card.Content>
      </Card>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
    padding: theme.spacing.lg,
  },
  card: {
    elevation: 2,
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: theme.spacing.lg,
  },
  message: {
    fontSize: 14,
    lineHeight: 20,
    color: theme.colors.text,
    marginBottom: theme.spacing.xl,
  },
  button: {
    backgroundColor: theme.colors.primary,
  },
});

export default AddressesScreen;
