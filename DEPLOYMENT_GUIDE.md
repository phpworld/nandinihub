# Nandini Hub - Complete Deployment Guide

This guide covers the deployment of both the API backend and mobile application for the Nandini Hub e-commerce platform.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [API Backend Deployment](#api-backend-deployment)
3. [Mobile App Deployment](#mobile-app-deployment)
4. [Database Setup](#database-setup)
5. [Payment Gateway Configuration](#payment-gateway-configuration)
6. [Push Notifications Setup](#push-notifications-setup)
7. [Testing](#testing)
8. [Monitoring and Maintenance](#monitoring-and-maintenance)

## Prerequisites

### Server Requirements
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 8.1 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **SSL Certificate**: Required for production
- **Domain**: Configured with proper DNS

### Development Tools
- **Node.js**: 16+ for mobile app development
- **Expo CLI**: For React Native development
- **Composer**: For PHP dependency management
- **Git**: For version control

## API Backend Deployment

### 1. Server Setup

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install apache2 mysql-server php8.1 php8.1-mysql php8.1-curl php8.1-json php8.1-mbstring php8.1-xml php8.1-zip php8.1-gd php8.1-intl

# Enable Apache modules
sudo a2enmod rewrite ssl headers

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Deploy Application Files

```bash
# Clone repository
git clone https://github.com/your-repo/nandinihub.git /var/www/nandinihub

# Set permissions
sudo chown -R www-data:www-data /var/www/nandinihub
sudo chmod -R 755 /var/www/nandinihub
sudo chmod -R 777 /var/www/nandinihub/writable

# Install dependencies
cd /var/www/nandinihub
composer install --no-dev --optimize-autoloader
```

### 3. Configure Environment

```bash
# Copy environment file
cp env .env

# Edit configuration
nano .env
```

Update the following in `.env`:

```env
# Database
database.default.hostname = your-db-host
database.default.database = nandinihub_prod
database.default.username = your-db-user
database.default.password = your-db-password

# App settings
app.baseURL = 'https://yourdomain.com/'
app.forceGlobalSecureRequests = true

# JWT settings
jwt.secret = 'your-super-secret-jwt-key-here'
jwt.issuer = 'nandinihub'
jwt.audience = 'nandinihub-users'

# Payment gateway
hdfc.merchant_id = 'your-merchant-id'
hdfc.access_code = 'your-access-code'
hdfc.working_key = 'your-working-key'
hdfc.test_mode = false

# Email settings
email.SMTPHost = 'your-smtp-host'
email.SMTPUser = 'your-smtp-user'
email.SMTPPass = 'your-smtp-password'
```

### 4. Apache Virtual Host

Create `/etc/apache2/sites-available/nandinihub.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/nandinihub/public
    
    <Directory /var/www/nandinihub/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/nandinihub_error.log
    CustomLog ${APACHE_LOG_DIR}/nandinihub_access.log combined
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/nandinihub/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    SSLCertificateChainFile /path/to/your/ca-bundle.crt
    
    <Directory /var/www/nandinihub/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/nandinihub_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/nandinihub_ssl_access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite nandinihub
sudo systemctl reload apache2
```

## Database Setup

### 1. Create Database

```sql
CREATE DATABASE nandinihub_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nandinihub_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON nandinihub_prod.* TO 'nandinihub_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Run Migrations

```bash
cd /var/www/nandinihub
php spark migrate
php spark db:seed DatabaseSeeder
```

### 3. Create Admin User

```bash
php spark make:admin admin@nandinihub.com "Admin User" "secure_admin_password"
```

## Mobile App Deployment

### 1. Prepare for Build

```bash
cd mobile-app

# Install dependencies
npm install

# Update API configuration
nano src/config/api.js
```

Update API base URL:

```javascript
export const API_CONFIG = {
  BASE_URL: 'https://yourdomain.com/api/v1',
  // ... other config
};
```

### 2. Configure App Settings

Update `app.json`:

```json
{
  "expo": {
    "name": "Nandini Hub",
    "slug": "nandinihub",
    "version": "1.0.0",
    "orientation": "portrait",
    "icon": "./assets/icon.png",
    "userInterfaceStyle": "light",
    "splash": {
      "image": "./assets/splash.png",
      "resizeMode": "contain",
      "backgroundColor": "#FF6B35"
    },
    "assetBundlePatterns": ["**/*"],
    "ios": {
      "supportsTablet": true,
      "bundleIdentifier": "com.nandinihub.app"
    },
    "android": {
      "adaptiveIcon": {
        "foregroundImage": "./assets/adaptive-icon.png",
        "backgroundColor": "#FF6B35"
      },
      "package": "com.nandinihub.app"
    },
    "web": {
      "favicon": "./assets/favicon.png"
    }
  }
}
```

### 3. Build for Production

#### Android Build

```bash
# Build APK
expo build:android

# Or build AAB for Play Store
expo build:android -t app-bundle
```

#### iOS Build

```bash
# Build for App Store
expo build:ios
```

### 4. Deploy to App Stores

#### Google Play Store
1. Upload AAB file to Google Play Console
2. Fill in app details and screenshots
3. Set up app signing
4. Submit for review

#### Apple App Store
1. Upload IPA file to App Store Connect
2. Fill in app metadata
3. Submit for review

## Payment Gateway Configuration

### 1. HDFC Payment Gateway

1. Contact HDFC Bank for merchant account
2. Obtain merchant credentials:
   - Merchant ID
   - Access Code
   - Working Key
3. Configure test/production URLs
4. Set up webhook endpoints

### 2. Test Payment Integration

```bash
# Run payment tests
php test_payment_gateway.php
```

## Push Notifications Setup

### 1. Firebase Configuration

1. Create Firebase project
2. Add Android/iOS apps
3. Download configuration files:
   - `google-services.json` (Android)
   - `GoogleService-Info.plist` (iOS)
4. Configure FCM server key

### 2. Expo Push Notifications

1. Configure push notification credentials in Expo
2. Test push notifications:

```bash
# Test push notification
curl -H "Content-Type: application/json" \
     -X POST \
     -d '{"to":"ExponentPushToken[xxx]","title":"Test","body":"Hello World!"}' \
     https://exp.host/--/api/v2/push/send
```

## Testing

### 1. API Testing

```bash
# Run comprehensive API tests
php test_mobile_api.php

# Check test results
cat mobile_api_test_results.json
```

### 2. Mobile App Testing

```bash
# Run on device/simulator
expo start

# Run tests
npm test
```

### 3. Load Testing

```bash
# Install Apache Bench
sudo apt install apache2-utils

# Test API performance
ab -n 1000 -c 10 https://yourdomain.com/api/v1/products
```

## Monitoring and Maintenance

### 1. Log Monitoring

```bash
# API logs
tail -f /var/www/nandinihub/writable/logs/log-$(date +%Y-%m-%d).log

# Apache logs
tail -f /var/log/apache2/nandinihub_error.log
```

### 2. Database Backup

```bash
# Create backup script
cat > /usr/local/bin/backup-nandinihub.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u nandinihub_user -p nandinihub_prod > /backups/nandinihub_$DATE.sql
gzip /backups/nandinihub_$DATE.sql
find /backups -name "nandinihub_*.sql.gz" -mtime +7 -delete
EOF

chmod +x /usr/local/bin/backup-nandinihub.sh

# Add to crontab
echo "0 2 * * * /usr/local/bin/backup-nandinihub.sh" | crontab -
```

### 3. SSL Certificate Renewal

```bash
# Using Let's Encrypt
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
echo "0 12 * * * /usr/bin/certbot renew --quiet" | crontab -
```

### 4. Performance Monitoring

Set up monitoring tools:
- **Uptime monitoring**: UptimeRobot, Pingdom
- **Performance monitoring**: New Relic, DataDog
- **Error tracking**: Sentry, Bugsnag

## Security Checklist

- [ ] SSL certificate installed and configured
- [ ] Database credentials secured
- [ ] JWT secret keys are strong and unique
- [ ] File permissions set correctly
- [ ] Regular security updates applied
- [ ] Firewall configured
- [ ] Rate limiting implemented
- [ ] Input validation in place
- [ ] SQL injection protection enabled
- [ ] XSS protection headers set

## Support and Maintenance

### Regular Tasks
1. **Daily**: Monitor logs and performance
2. **Weekly**: Check for security updates
3. **Monthly**: Review analytics and user feedback
4. **Quarterly**: Performance optimization and feature updates

### Emergency Procedures
1. **Database corruption**: Restore from backup
2. **Server downtime**: Switch to backup server
3. **Security breach**: Follow incident response plan
4. **Payment issues**: Contact payment gateway support

For technical support, contact: support@nandinihub.com
