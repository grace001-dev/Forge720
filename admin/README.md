# Forge 720 Admin Panel

## Overview
The Forge 720 admin panel provides comprehensive content management capabilities for the e-commerce website, allowing administrators to manage products, orders, users, quotes, and website content.

## Features

### Dashboard
- Overview statistics (total products, orders, users, quotes)
- Quick access to pending orders and quotes
- Recent activity summary

### Products Management
- Add, edit, and delete products
- Manage product categories
- Set pricing, descriptions, images, and inventory
- Configure customization options

### Orders Management
- View all customer orders
- Update order status (pending → processing → shipped → delivered)
- View detailed order information including customer details and items

### Users Management
- View all registered users
- Change user roles (user/admin)
- User statistics and management

### Quotes Management
- Review and respond to quote requests
- Update quote status and pricing
- Add admin notes and track quote lifecycle

### Content Management
- Edit website content (about page, contact info, hero section)
- Update footer information
- HTML support for rich content

## Access

### Admin Login
- URL: `http://localhost/forge720/admin/login.php`
- Username: `admin`
- Password: `admin123`

### Navigation
After logging in, use the sidebar to navigate between different management sections.

## Database Schema

The admin panel uses the following database tables:
- `users` - User accounts with role management
- `products` - Product catalog
- `categories` - Product categories
- `cart_items` - Shopping cart items
- `orders` & `order_items` - Order management
- `quotes` - Quote request system
- `wishlists` - Customer wishlists
- `customization_options` - Product customization settings

## Security Features

- Role-based access control
- Session-based authentication
- Input sanitization and validation
- SQL injection protection
- Admin-only access to management functions

## File Structure

```
admin/
├── index.php          # Dashboard
├── login.php          # Admin login
├── logout.php         # Admin logout
├── products.php       # Product management
├── categories.php     # Category management
├── orders.php         # Order management
├── users.php          # User management
├── quotes.php         # Quote management
└── content.php        # Content management
```

## Usage Tips

1. **Regular Maintenance**: Check pending orders and quotes daily
2. **Product Updates**: Keep product inventory and pricing current
3. **User Management**: Monitor user accounts and assign admin roles as needed
4. **Content Updates**: Regularly update website content to keep it fresh
5. **Backup**: Regularly backup the database before making major changes

## Support

For technical issues or questions about the admin panel functionality, refer to the main application documentation or contact the development team.