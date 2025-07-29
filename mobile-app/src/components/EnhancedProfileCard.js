import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  TouchableOpacity,
  Alert,
} from 'react-native';
import {
  Text,
  Card,
  Avatar,
  Button,
  TextInput,
  Dialog,
  Portal,
  ActivityIndicator,
  Chip,
} from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { useAuth } from '../contexts/AuthContext';
import { theme } from '../styles/theme';

const EnhancedProfileCard = ({ style, onEditSuccess }) => {
  const [editDialogVisible, setEditDialogVisible] = useState(false);
  const [editFormData, setEditFormData] = useState({
    first_name: '',
    last_name: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    pincode: '',
  });
  const [saving, setSaving] = useState(false);

  const { user, updateProfile } = useAuth();

  const handleEditProfile = () => {
    setEditFormData({
      first_name: user?.first_name || '',
      last_name: user?.last_name || '',
      phone: user?.phone || '',
      address: user?.address || '',
      city: user?.city || '',
      state: user?.state || '',
      pincode: user?.pincode || '',
    });
    setEditDialogVisible(true);
  };

  const handleSaveProfile = async () => {
    setSaving(true);
    try {
      const result = await updateProfile(editFormData);
      
      if (result.success) {
        setEditDialogVisible(false);
        Alert.alert('Success', 'Profile updated successfully');
        onEditSuccess?.();
      } else {
        Alert.alert('Error', result.message || 'Failed to update profile');
      }
    } catch (error) {
      Alert.alert('Error', 'Network error. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  const getInitials = () => {
    const firstName = user?.first_name || '';
    const lastName = user?.last_name || '';
    return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
  };

  const getCompletionPercentage = () => {
    const fields = [
      user?.first_name,
      user?.last_name,
      user?.email,
      user?.phone,
      user?.address,
      user?.city,
      user?.state,
      user?.pincode,
    ];
    const filledFields = fields.filter(field => field && field.trim()).length;
    return Math.round((filledFields / fields.length) * 100);
  };

  const completionPercentage = getCompletionPercentage();

  return (
    <View style={[styles.container, style]}>
      <Card style={styles.card}>
        <LinearGradient
          colors={[theme.colors.primary, theme.colors.primaryDark]}
          style={styles.headerGradient}
        >
          <View style={styles.header}>
            <Avatar.Text
              size={80}
              label={getInitials()}
              style={styles.avatar}
              labelStyle={styles.avatarLabel}
            />
            <View style={styles.userInfo}>
              <Text style={styles.userName}>
                {user?.first_name} {user?.last_name}
              </Text>
              <Text style={styles.userEmail}>{user?.email}</Text>
              <View style={styles.completionContainer}>
                <Text style={styles.completionText}>
                  Profile {completionPercentage}% complete
                </Text>
                <View style={styles.progressBar}>
                  <View 
                    style={[
                      styles.progressFill, 
                      { width: `${completionPercentage}%` }
                    ]} 
                  />
                </View>
              </View>
            </View>
          </View>
        </LinearGradient>

        <View style={styles.content}>
          <View style={styles.infoSection}>
            <Text style={styles.sectionTitle}>Personal Information</Text>
            
            <View style={styles.infoRow}>
              <Ionicons name="call-outline" size={20} color={theme.colors.primary} />
              <Text style={styles.infoText}>
                {user?.phone || 'Phone not provided'}
              </Text>
            </View>
            
            <View style={styles.infoRow}>
              <Ionicons name="location-outline" size={20} color={theme.colors.primary} />
              <Text style={styles.infoText}>
                {user?.address ? 
                  `${user.address}, ${user.city}, ${user.state} ${user.pincode}` : 
                  'Address not provided'
                }
              </Text>
            </View>
            
            <View style={styles.infoRow}>
              <Ionicons name="person-outline" size={20} color={theme.colors.primary} />
              <Text style={styles.infoText}>
                Role: {user?.role || 'Customer'}
              </Text>
            </View>
          </View>

          <View style={styles.statusSection}>
            <Text style={styles.sectionTitle}>Account Status</Text>
            <View style={styles.statusRow}>
              <Chip
                mode="outlined"
                selected={user?.is_active === '1'}
                style={[
                  styles.statusChip,
                  user?.is_active === '1' && styles.activeChip
                ]}
                textStyle={[
                  styles.chipText,
                  user?.is_active === '1' && styles.activeChipText
                ]}
              >
                {user?.is_active === '1' ? 'Active' : 'Inactive'}
              </Chip>
              <Text style={styles.memberSince}>
                Member since {new Date(user?.created_at).toLocaleDateString()}
              </Text>
            </View>
          </View>

          <Button
            mode="contained"
            onPress={handleEditProfile}
            style={styles.editButton}
            contentStyle={styles.editButtonContent}
            labelStyle={styles.editButtonText}
            icon="pencil"
          >
            Edit Profile
          </Button>
        </View>
      </Card>

      {/* Edit Profile Dialog */}
      <Portal>
        <Dialog
          visible={editDialogVisible}
          onDismiss={() => setEditDialogVisible(false)}
          style={styles.dialog}
        >
          <Dialog.Title style={styles.dialogTitle}>Edit Profile</Dialog.Title>
          <Dialog.Content>
            <View style={styles.formRow}>
              <TextInput
                label="First Name"
                value={editFormData.first_name}
                onChangeText={(text) => setEditFormData(prev => ({ ...prev, first_name: text }))}
                mode="outlined"
                style={[styles.input, styles.halfInput]}
              />
              <TextInput
                label="Last Name"
                value={editFormData.last_name}
                onChangeText={(text) => setEditFormData(prev => ({ ...prev, last_name: text }))}
                mode="outlined"
                style={[styles.input, styles.halfInput]}
              />
            </View>
            
            <TextInput
              label="Phone"
              value={editFormData.phone}
              onChangeText={(text) => setEditFormData(prev => ({ ...prev, phone: text }))}
              mode="outlined"
              style={styles.input}
              keyboardType="phone-pad"
            />
            
            <TextInput
              label="Address"
              value={editFormData.address}
              onChangeText={(text) => setEditFormData(prev => ({ ...prev, address: text }))}
              mode="outlined"
              style={styles.input}
              multiline
              numberOfLines={2}
            />
            
            <View style={styles.formRow}>
              <TextInput
                label="City"
                value={editFormData.city}
                onChangeText={(text) => setEditFormData(prev => ({ ...prev, city: text }))}
                mode="outlined"
                style={[styles.input, styles.halfInput]}
              />
              <TextInput
                label="Pincode"
                value={editFormData.pincode}
                onChangeText={(text) => setEditFormData(prev => ({ ...prev, pincode: text }))}
                mode="outlined"
                style={[styles.input, styles.halfInput]}
                keyboardType="numeric"
              />
            </View>
            
            <TextInput
              label="State"
              value={editFormData.state}
              onChangeText={(text) => setEditFormData(prev => ({ ...prev, state: text }))}
              mode="outlined"
              style={styles.input}
            />
          </Dialog.Content>
          <Dialog.Actions style={styles.dialogActions}>
            <Button
              onPress={() => setEditDialogVisible(false)}
              disabled={saving}
            >
              Cancel
            </Button>
            <Button
              mode="contained"
              onPress={handleSaveProfile}
              loading={saving}
              disabled={saving}
            >
              Save
            </Button>
          </Dialog.Actions>
        </Dialog>
      </Portal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: theme.spacing.lg,
  },
  card: {
    borderRadius: theme.borderRadius.lg,
    elevation: 4,
    shadowColor: theme.colors.shadowColor,
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
  },
  headerGradient: {
    borderTopLeftRadius: theme.borderRadius.lg,
    borderTopRightRadius: theme.borderRadius.lg,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: theme.spacing.lg,
  },
  avatar: {
    backgroundColor: theme.colors.surface,
  },
  avatarLabel: {
    color: theme.colors.primary,
    fontWeight: 'bold',
    fontSize: 24,
  },
  userInfo: {
    marginLeft: theme.spacing.lg,
    flex: 1,
  },
  userName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.surface,
    marginBottom: 4,
  },
  userEmail: {
    fontSize: 14,
    color: theme.colors.surface,
    opacity: 0.9,
    marginBottom: theme.spacing.sm,
  },
  completionContainer: {
    marginTop: theme.spacing.sm,
  },
  completionText: {
    fontSize: 12,
    color: theme.colors.surface,
    opacity: 0.8,
    marginBottom: 4,
  },
  progressBar: {
    height: 4,
    backgroundColor: 'rgba(255,255,255,0.3)',
    borderRadius: 2,
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    backgroundColor: theme.colors.surface,
    borderRadius: 2,
  },
  content: {
    padding: theme.spacing.lg,
  },
  infoSection: {
    marginBottom: theme.spacing.lg,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.md,
  },
  infoText: {
    fontSize: 14,
    color: theme.colors.text,
    marginLeft: theme.spacing.md,
    flex: 1,
  },
  statusSection: {
    marginBottom: theme.spacing.lg,
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  statusChip: {
    borderColor: theme.colors.textSecondary,
  },
  activeChip: {
    backgroundColor: theme.colors.success,
    borderColor: theme.colors.success,
  },
  chipText: {
    color: theme.colors.textSecondary,
  },
  activeChipText: {
    color: theme.colors.surface,
  },
  memberSince: {
    fontSize: 12,
    color: theme.colors.textSecondary,
  },
  editButton: {
    borderRadius: theme.borderRadius.md,
  },
  editButtonContent: {
    paddingVertical: theme.spacing.sm,
  },
  editButtonText: {
    fontWeight: 'bold',
  },
  dialog: {
    borderRadius: theme.borderRadius.lg,
  },
  dialogTitle: {
    textAlign: 'center',
    color: theme.colors.primary,
  },
  formRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  input: {
    marginBottom: theme.spacing.md,
  },
  halfInput: {
    width: '48%',
  },
  dialogActions: {
    paddingHorizontal: theme.spacing.lg,
    paddingBottom: theme.spacing.lg,
  },
});

export default EnhancedProfileCard;
