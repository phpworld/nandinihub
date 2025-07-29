import React, { useState, useRef, useEffect } from 'react';
import {
  View,
  StyleSheet,
  Dimensions,
  ScrollView,
  TouchableOpacity,
  Image,
} from 'react-native';
import { Text, Card } from 'react-native-paper';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../styles/theme';

const { width: screenWidth } = Dimensions.get('window');

const BannerSlider = ({ banners = [], onBannerPress }) => {
  const [currentIndex, setCurrentIndex] = useState(0);
  const scrollViewRef = useRef(null);

  // Default banners if none provided
  const defaultBanners = [
    {
      id: 1,
      title: 'Premium Puja Samagri',
      subtitle: 'Authentic & Pure Products',
      image: 'https://via.placeholder.com/400x200/FF6B35/FFFFFF?text=Puja+Samagri',
      backgroundColor: ['#FF6B35', '#FF8A65'],
    },
    {
      id: 2,
      title: 'Special Offers',
      subtitle: 'Up to 30% Off on Selected Items',
      image: 'https://via.placeholder.com/400x200/4CAF50/FFFFFF?text=Special+Offers',
      backgroundColor: ['#4CAF50', '#66BB6A'],
    },
    {
      id: 3,
      title: 'Festival Collection',
      subtitle: 'Complete Puja Essentials',
      image: 'https://via.placeholder.com/400x200/9C27B0/FFFFFF?text=Festival+Collection',
      backgroundColor: ['#9C27B0', '#BA68C8'],
    },
  ];

  const slideBanners = banners.length > 0 ? banners : defaultBanners;

  useEffect(() => {
    const timer = setInterval(() => {
      const nextIndex = (currentIndex + 1) % slideBanners.length;
      setCurrentIndex(nextIndex);
      scrollViewRef.current?.scrollTo({
        x: nextIndex * screenWidth,
        animated: true,
      });
    }, 4000); // Auto-slide every 4 seconds

    return () => clearInterval(timer);
  }, [currentIndex, slideBanners.length]);

  const handleScroll = (event) => {
    const contentOffset = event.nativeEvent.contentOffset;
    const index = Math.round(contentOffset.x / screenWidth);
    setCurrentIndex(index);
  };

  const renderBanner = (banner, index) => (
    <TouchableOpacity
      key={banner.id}
      style={styles.bannerContainer}
      onPress={() => onBannerPress?.(banner)}
      activeOpacity={0.9}
    >
      <Card style={styles.bannerCard}>
        <LinearGradient
          colors={banner.backgroundColor || ['#FF6B35', '#FF8A65']}
          style={styles.bannerGradient}
        >
          <View style={styles.bannerContent}>
            <View style={styles.bannerTextContainer}>
              <Text style={styles.bannerTitle}>{banner.title}</Text>
              <Text style={styles.bannerSubtitle}>{banner.subtitle}</Text>
            </View>
            {banner.image && (
              <Image
                source={{ uri: banner.image }}
                style={styles.bannerImage}
                resizeMode="contain"
              />
            )}
          </View>
        </LinearGradient>
      </Card>
    </TouchableOpacity>
  );

  const renderDots = () => (
    <View style={styles.dotsContainer}>
      {slideBanners.map((_, index) => (
        <TouchableOpacity
          key={index}
          style={[
            styles.dot,
            index === currentIndex ? styles.activeDot : styles.inactiveDot,
          ]}
          onPress={() => {
            setCurrentIndex(index);
            scrollViewRef.current?.scrollTo({
              x: index * screenWidth,
              animated: true,
            });
          }}
        />
      ))}
    </View>
  );

  return (
    <View style={styles.container}>
      <ScrollView
        ref={scrollViewRef}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onScroll={handleScroll}
        scrollEventThrottle={16}
        style={styles.scrollView}
      >
        {slideBanners.map((banner, index) => renderBanner(banner, index))}
      </ScrollView>
      {slideBanners.length > 1 && renderDots()}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: theme.spacing.lg,
  },
  scrollView: {
    height: 180,
  },
  bannerContainer: {
    width: screenWidth,
    paddingHorizontal: 0,
  },
  bannerCard: {
    borderRadius: 0,
    elevation: 0,
    margin: 0,
  },
  bannerGradient: {
    borderRadius: 0,
    height: 250,
    justifyContent: 'center',
  },
  bannerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: theme.spacing.lg,
  },
  bannerTextContainer: {
    flex: 1,
  },
  bannerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.surface,
    marginBottom: theme.spacing.sm,
    textShadowColor: 'rgba(0,0,0,0.3)',
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 2,
  },
  bannerSubtitle: {
    fontSize: 16,
    color: theme.colors.surface,
    opacity: 0.95,
    textShadowColor: 'rgba(0,0,0,0.2)',
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 1,
  },
  bannerImage: {
    width: 80,
    height: 80,
    marginLeft: theme.spacing.md,
  },
  dotsContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: theme.spacing.md,
  },
  dot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginHorizontal: 4,
  },
  activeDot: {
    backgroundColor: theme.colors.primary,
  },
  inactiveDot: {
    backgroundColor: theme.colors.border,
  },
});

export default BannerSlider;
