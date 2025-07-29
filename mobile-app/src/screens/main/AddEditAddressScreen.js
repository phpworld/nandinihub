import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Text, Card } from 'react-native-paper';
import { theme } from '../../styles/theme';

const AddEditAddressScreen = ({ route }) => {
  const address = route.params?.address;
  const isEditing = !!address;

  return (
    <View style={styles.container}>
      <Card style={styles.card}>
        <Card.Content>
          <Text style={styles.title}>
            {isEditing ? 'Edit Address' : 'Add New Address'}
          </Text>
          <Text style={styles.message}>
            This screen will contain a form to add or edit shipping addresses 
            with fields for name, phone, address lines, city, state, postal code, etc.
          </Text>
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
  },
});

export default AddEditAddressScreen;
