// API Configuration
// For development: Use your computer's IP address so mobile devices can connect
// For production: Replace with your actual server URL

// Try multiple possible URLs for better connectivity
const DEV_IP = '193.186.4.186'; // Your computer's IP address
const LOCALHOST_URL = 'http://localhost/nandinihub/api/v1'; // For web testing
const DEV_BASE_URL = `http://${DEV_IP}/nandinihub/api/v1`;
const FALLBACK_URL = 'http://127.0.0.1/nandinihub/api/v1'; // Fallback localhost
const PROD_BASE_URL = 'https://your-production-domain.com/api/v1';

// Detect if running on web or mobile and use appropriate URL
const isWeb = typeof window !== 'undefined' && window.location;
const BASE_URL = isWeb ? LOCALHOST_URL : DEV_BASE_URL;

export const API_CONFIG = {
  BASE_URL: BASE_URL,
  TIMEOUT: 30000, // Increased timeout to 30 seconds
  RETRY_ATTEMPTS: 3,
};

// API Endpoints
export const API_ENDPOINTS = {
  // Authentication
  AUTH: {
    REGISTER: '/auth/register',
    LOGIN: '/auth/login',
    REFRESH: '/auth/refresh',
    PROFILE: '/auth/profile',
    UPDATE_PROFILE: '/auth/profile',
    CHANGE_PASSWORD: '/auth/change-password',
    LOGOUT: '/auth/logout',
  },
  
  // Products
  PRODUCTS: {
    LIST: '/products',
    DETAIL: '/products',
    FEATURED: '/products/featured',
    SEARCH: '/products/search',
    BY_CATEGORY: '/products/category',
  },
  
  // Categories
  CATEGORIES: {
    LIST: '/categories',
    DETAIL: '/categories',
    TREE: '/categories/tree',
    POPULAR: '/categories/popular',
    SEARCH: '/categories/search',
  },
  
  // Cart
  CART: {
    LIST: '/cart',
    ADD: '/cart/add',
    UPDATE: '/cart',
    REMOVE: '/cart',
    CLEAR: '/cart',
    COUNT: '/cart/count',
  },
  
  // Orders
  ORDERS: {
    LIST: '/orders',
    DETAIL: '/orders',
    CREATE: '/orders',
    CANCEL: '/orders',
  },
  
  // Addresses
  ADDRESSES: {
    LIST: '/addresses',
    DETAIL: '/addresses',
    CREATE: '/addresses',
    UPDATE: '/addresses',
    DELETE: '/addresses',
    SET_DEFAULT: '/addresses',
  },

  // Payment
  PAYMENT: {
    INITIATE: '/payment/initiate',
    VERIFY: '/payment/verify',
    METHODS: '/payment/methods',
    CALLBACK: '/payment/callback',
  },
};

// HTTP Status Codes
export const HTTP_STATUS = {
  OK: 200,
  CREATED: 201,
  BAD_REQUEST: 400,
  UNAUTHORIZED: 401,
  FORBIDDEN: 403,
  NOT_FOUND: 404,
  VALIDATION_ERROR: 422,
  INTERNAL_SERVER_ERROR: 500,
};
