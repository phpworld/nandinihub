# Nandini Hub Mobile API Documentation

## Overview
This document provides comprehensive documentation for the Nandini Hub Mobile API, which includes JWT authentication and RESTful endpoints for mobile app development.

## Base URL
```
http://localhost/nandinihub/api/v1
```

## Authentication
The API uses JWT (JSON Web Token) for authentication. Include the token in the Authorization header:
```
Authorization: Bearer <your-jwt-token>
```

## Response Format
All API responses follow this standard format:
```json
{
    "success": true|false,
    "message": "Response message",
    "data": {...} // Response data (optional)
}
```

For paginated responses:
```json
{
    "success": true,
    "message": "Success message",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 100,
        "total_pages": 5,
        "has_next": true,
        "has_prev": false
    }
}
```

## Authentication Endpoints

### 1. Register User
**POST** `/auth/register`

**Request Body:**
```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password123",
    "phone": "9876543210"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {...},
        "token": "jwt-token",
        "refresh_token": "refresh-token",
        "token_type": "Bearer"
    }
}
```

### 2. Login User
**POST** `/auth/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

### 3. Refresh Token
**POST** `/auth/refresh`

**Request Body:**
```json
{
    "refresh_token": "your-refresh-token"
}
```

### 4. Get User Profile
**GET** `/auth/profile`
*Requires Authentication*

### 5. Update Profile
**PUT** `/auth/profile`
*Requires Authentication*

**Request Body:**
```json
{
    "first_name": "John",
    "last_name": "Doe",
    "phone": "9876543210"
}
```

### 6. Change Password
**POST** `/auth/change-password`
*Requires Authentication*

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword"
}
```

### 7. Logout
**POST** `/auth/logout`
*Requires Authentication*

## Product Endpoints

### 1. Get All Products
**GET** `/products`

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `per_page` (int): Items per page (default: 20, max: 100)
- `category_id` (int): Filter by category
- `search` (string): Search in product name/description
- `featured` (boolean): Filter featured products
- `sort_by` (string): Sort field (name, price, created_at)
- `sort_order` (string): Sort order (asc, desc)

### 2. Get Single Product
**GET** `/products/{id-or-slug}`

### 3. Get Featured Products
**GET** `/products/featured`

**Query Parameters:**
- `limit` (int): Number of products (default: 10, max: 50)

### 4. Search Products
**GET** `/products/search`

**Query Parameters:**
- `q` (string): Search query (required)
- `page` (int): Page number
- `per_page` (int): Items per page

### 5. Get Products by Category
**GET** `/products/category/{category-id}`

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page

## Category Endpoints

### 1. Get All Categories
**GET** `/categories`

### 2. Get Single Category
**GET** `/categories/{id-or-slug}`

### 3. Get Category Tree
**GET** `/categories/tree`

### 4. Get Popular Categories
**GET** `/categories/popular`

**Query Parameters:**
- `limit` (int): Number of categories (default: 10, max: 50)

### 5. Search Categories
**GET** `/categories/search`

**Query Parameters:**
- `q` (string): Search query (required)

## Cart Endpoints
*All cart endpoints require authentication*

### 1. Get Cart Items
**GET** `/cart`

### 2. Add Item to Cart
**POST** `/cart/add`

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 2
}
```

### 3. Update Cart Item
**PUT** `/cart/{item-id}`

**Request Body:**
```json
{
    "quantity": 3
}
```

### 4. Remove Cart Item
**DELETE** `/cart/{item-id}`

### 5. Clear Cart
**DELETE** `/cart`

### 6. Get Cart Count
**GET** `/cart/count`

## Order Endpoints
*All order endpoints require authentication*

### 1. Get User Orders
**GET** `/orders`

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `status` (string): Filter by order status

### 2. Get Single Order
**GET** `/orders/{order-id}`

### 3. Create Order
**POST** `/orders`

**Request Body:**
```json
{
    "shipping_address_id": 1,
    "notes": "Optional delivery notes"
}
```

### 4. Cancel Order
**PUT** `/orders/{order-id}/cancel`

## Address Endpoints
*All address endpoints require authentication*

### 1. Get User Addresses
**GET** `/addresses`

### 2. Get Single Address
**GET** `/addresses/{address-id}`

### 3. Create Address
**POST** `/addresses`

**Request Body:**
```json
{
    "name": "John Doe",
    "phone": "9876543210",
    "address_line_1": "123 Main Street",
    "address_line_2": "Apt 4B",
    "city": "Mumbai",
    "state": "Maharashtra",
    "postal_code": "400001",
    "country": "India",
    "is_default": false
}
```

### 4. Update Address
**PUT** `/addresses/{address-id}`

### 5. Delete Address
**DELETE** `/addresses/{address-id}`

### 6. Set Default Address
**PUT** `/addresses/{address-id}/default`

## Error Codes
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting
Currently no rate limiting is implemented, but it's recommended for production use.

## Testing
Use tools like Postman or curl to test the API endpoints. Make sure to include the JWT token in the Authorization header for protected endpoints.

Example curl request:
```bash
curl -X GET "http://localhost/nandinihub/api/v1/auth/profile" \
  -H "Authorization: Bearer your-jwt-token" \
  -H "Content-Type: application/json"
```
