# Nandini Hub Mobile App

A React Native mobile application for Nandini Hub - Premium Puja Samagri shopping platform.

## Features

### Authentication
- User registration and login
- JWT token-based authentication
- Profile management
- Password change functionality

### Product Management
- Browse products with pagination
- Search products
- Filter by categories
- Product details with images and descriptions
- Featured products section

### Shopping Cart
- Add/remove items from cart
- Update item quantities
- Cart summary with totals
- Persistent cart across sessions

### Order Management
- Order placement
- Order history
- Order status tracking
- Order details view

### Address Management
- Save multiple shipping addresses
- Set default address
- Add/edit/delete addresses

### User Interface
- Modern Material Design UI
- Responsive design for all screen sizes
- Dark/light theme support
- Smooth animations and transitions

## Technology Stack

- **Framework**: React Native with Expo
- **Navigation**: React Navigation v6
- **UI Library**: React Native Paper
- **State Management**: React Context API
- **HTTP Client**: Axios
- **Storage**: AsyncStorage
- **Authentication**: JWT tokens

## Project Structure

```
src/
├── components/          # Reusable UI components
├── config/             # Configuration files
├── contexts/           # React Context providers
├── navigation/         # Navigation configuration
├── screens/           # Screen components
│   ├── auth/          # Authentication screens
│   └── main/          # Main app screens
├── services/          # API services
└── styles/            # Theme and styling
```

## Setup Instructions

### Prerequisites
- Node.js (v16 or higher)
- npm or yarn
- Expo CLI
- Android Studio (for Android development)
- Xcode (for iOS development, macOS only)

### Installation

1. **Install dependencies**
   ```bash
   cd mobile-app
   npm install
   ```

2. **Configure API endpoint**
   Update the `BASE_URL` in `src/config/api.js` to point to your API server:
   ```javascript
   export const API_CONFIG = {
     BASE_URL: 'http://your-api-server.com/api/v1',
     // ...
   };
   ```

3. **Add required assets**
   Place the following assets in the `assets/` directory:
   - `icon.png` (1024x1024) - App icon
   - `adaptive-icon.png` (1024x1024) - Android adaptive icon
   - `splash.png` (1242x2436) - Splash screen
   - `favicon.png` (32x32) - Web favicon

### Running the App

1. **Start the development server**
   ```bash
   npm start
   ```

2. **Run on Android**
   ```bash
   npm run android
   ```

3. **Run on iOS**
   ```bash
   npm run ios
   ```

4. **Run on Web**
   ```bash
   npm run web
   ```

## API Integration

The app integrates with the Nandini Hub API with the following endpoints:

### Authentication
- `POST /auth/register` - User registration
- `POST /auth/login` - User login
- `POST /auth/refresh` - Token refresh
- `GET /auth/profile` - Get user profile
- `PUT /auth/profile` - Update profile
- `POST /auth/change-password` - Change password
- `POST /auth/logout` - Logout

### Products
- `GET /products` - Get products with pagination and filters
- `GET /products/{id}` - Get product details
- `GET /products/featured` - Get featured products
- `GET /products/search` - Search products

### Categories
- `GET /categories` - Get all categories
- `GET /categories/{id}` - Get category details
- `GET /categories/tree` - Get category hierarchy
- `GET /categories/popular` - Get popular categories

### Cart
- `GET /cart` - Get cart items
- `POST /cart/add` - Add item to cart
- `PUT /cart/{id}` - Update cart item
- `DELETE /cart/{id}` - Remove cart item
- `DELETE /cart` - Clear cart

### Orders
- `GET /orders` - Get user orders
- `GET /orders/{id}` - Get order details
- `POST /orders` - Create new order
- `PUT /orders/{id}/cancel` - Cancel order

### Addresses
- `GET /addresses` - Get user addresses
- `POST /addresses` - Create new address
- `PUT /addresses/{id}` - Update address
- `DELETE /addresses/{id}` - Delete address

## Building for Production

### Android
```bash
npm run build:android
```

### iOS
```bash
npm run build:ios
```

### Configuration for Production

1. **Update app.json**
   - Set correct bundle identifiers
   - Configure app store metadata
   - Set up proper permissions

2. **Environment Variables**
   - Set production API URLs
   - Configure analytics keys
   - Set up crash reporting

3. **Assets**
   - Replace placeholder images with production assets
   - Optimize images for mobile devices
   - Add proper app icons for all sizes

## Testing

Run tests with:
```bash
npm test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, contact the development team or create an issue in the repository.
