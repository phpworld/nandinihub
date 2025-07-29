import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
} from 'react-native';
import {
  Text,
  Card,
  Button,
  Avatar,
  List,
  Divider,
  Dialog,
  Portal,
  TextInput,
} from 'react-native-paper';
import { Ionicons } from '@expo/vector-icons';

import { theme } from '../../styles/theme';
import { useAuth } from '../../contexts/AuthContext';

const ProfileScreen = ({ navigation }) => {
  const [editDialogVisible, setEditDialogVisible] = useState(false);
  const [editFormData, setEditFormData] = useState({
    first_name: '',
    last_name: '',
    phone: '',
  });

  const { user, logout, updateProfile, isLoading } = useAuth();

  const handleEditProfile = () => {
    setEditFormData({
      first_name: user?.first_name || '',
      last_name: user?.last_name || '',
      phone: user?.phone || '',
    });
    setEditDialogVisible(true);
  };

  const handleSaveProfile = async () => {
    const result = await updateProfile(editFormData);
    
    if (result.success) {
      setEditDialogVisible(false);
      Alert.alert('Success', 'Profile updated successfully');
    } else {
      Alert.alert('Error', result.message || 'Failed to update profile');
    }
  };

  const handleLogout = () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout?',
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Logout', onPress: logout, style: 'destructive' },
      ]
    );
  };

  const menuItems = [
    {
      title: 'My Orders',
      icon: 'receipt-outline',
      onPress: () => navigation.navigate('Orders'),
    },
    {
      title: 'My Addresses',
      icon: 'location-outline',
      onPress: () => navigation.navigate('Addresses'),
    },
    {
      title: 'Change Password',
      icon: 'lock-closed-outline',
      onPress: () => navigation.navigate('ChangePassword'),
    },
    {
      title: 'Help & Support',
      icon: 'help-circle-outline',
      onPress: () => Alert.alert('Help', 'Contact support at support@nandinihub.com'),
    },
    {
      title: 'About',
      icon: 'information-circle-outline',
      onPress: () => Alert.alert('About', 'Nandini Hub v1.0.0\nPremium Puja Samagri'),
    },
  ];

  return (
    <ScrollView style={styles.container}>
      {/* Profile Header */}
      <Card style={styles.profileCard}>
        <Card.Content style={styles.profileContent}>
          <View style={styles.profileHeader}>
            <Avatar.Text
              size={80}
              label={user ? `${user.first_name[0]}${user.last_name[0]}` : 'U'}
              style={styles.avatar}
            />
            <View style={styles.profileInfo}>
              <Text style={styles.userName}>
                {user ? `${user.first_name} ${user.last_name}` : 'User'}
              </Text>
              <Text style={styles.userEmail}>{user?.email}</Text>
              <Text style={styles.userPhone}>{user?.phone}</Text>
            </View>
          </View>
          
          <Button
            mode="outlined"
            onPress={handleEditProfile}
            style={styles.editButton}
            icon="pencil"
          >
            Edit Profile
          </Button>
        </Card.Content>
      </Card>

      {/* Menu Items */}
      <Card style={styles.menuCard}>
        {menuItems.map((item, index) => (
          <View key={item.title}>
            <List.Item
              title={item.title}
              left={(props) => (
                <List.Icon {...props} icon={item.icon} color={theme.colors.primary} />
              )}
              right={(props) => <List.Icon {...props} icon="chevron-right" />}
              onPress={item.onPress}
              style={styles.menuItem}
            />
            {index < menuItems.length - 1 && <Divider />}
          </View>
        ))}
      </Card>

      {/* Logout Button */}
      <Button
        mode="contained"
        onPress={handleLogout}
        style={styles.logoutButton}
        buttonColor={theme.colors.error}
        icon="logout"
      >
        Logout
      </Button>

      {/* Edit Profile Dialog */}
      <Portal>
        <Dialog visible={editDialogVisible} onDismiss={() => setEditDialogVisible(false)}>
          <Dialog.Title>Edit Profile</Dialog.Title>
          <Dialog.Content>
            <TextInput
              label="First Name"
              value={editFormData.first_name}
              onChangeText={(text) => setEditFormData(prev => ({ ...prev, first_name: text }))}
              mode="outlined"
              style={styles.dialogInput}
            />
            <TextInput
              label="Last Name"
              value={editFormData.last_name}
              onChangeText={(text) => setEditFormData(prev => ({ ...prev, last_name: text }))}
              mode="outlined"
              style={styles.dialogInput}
            />
            <TextInput
              label="Phone"
              value={editFormData.phone}
              onChangeText={(text) => setEditFormData(prev => ({ ...prev, phone: text }))}
              mode="outlined"
              keyboardType="phone-pad"
              style={styles.dialogInput}
            />
          </Dialog.Content>
          <Dialog.Actions>
            <Button onPress={() => setEditDialogVisible(false)}>Cancel</Button>
            <Button onPress={handleSaveProfile} loading={isLoading}>Save</Button>
          </Dialog.Actions>
        </Dialog>
      </Portal>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  profileCard: {
    margin: theme.spacing.lg,
    elevation: 2,
  },
  profileContent: {
    padding: theme.spacing.lg,
  },
  profileHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.lg,
  },
  avatar: {
    backgroundColor: theme.colors.primary,
  },
  profileInfo: {
    flex: 1,
    marginLeft: theme.spacing.lg,
  },
  userName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  userEmail: {
    fontSize: 14,
    color: theme.colors.textSecondary,
    marginBottom: theme.spacing.xs,
  },
  userPhone: {
    fontSize: 14,
    color: theme.colors.textSecondary,
  },
  editButton: {
    borderColor: theme.colors.primary,
  },
  menuCard: {
    margin: theme.spacing.lg,
    marginTop: 0,
    elevation: 2,
  },
  menuItem: {
    paddingVertical: theme.spacing.sm,
  },
  logoutButton: {
    margin: theme.spacing.lg,
    marginTop: theme.spacing.xl,
  },
  dialogInput: {
    marginBottom: theme.spacing.md,
  },
});

export default ProfileScreen;
