# Forge 720 - Custom Fabrication Website

A complete website for Forge 720, a custom metal fabrication company, built with HTML, CSS, JavaScript, and PHP.

## Features

- **Homepage**: Showcases featured products and company information
- **Product Catalog**: Displays all available fabrication products
- **User Registration & Login**: Secure user authentication system
- **About Page**: Company information and services
- **Contact Form**: Customer inquiry system
- **Responsive Design**: Jungle green and gold theme

## Setup Instructions

### Prerequisites
- XAMPP (Apache + MySQL)
- PHP 7.0 or higher
- MySQL 5.6 or higher

### Installation Steps

1. **Start XAMPP**
   - Launch XAMPP Control Panel
   - Start Apache and MySQL services

2. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `forge720`
   - Import the `db_setup.sql` file:
     - Go to the Import tab
     - Select the `db_setup.sql` file
     - Click Go

3. **Website Files**
   - Place all website files in `C:\xampp\htdocs\forge720\`
   - Ensure the following files are present:
     - `index.php`
     - `login.php`
     - `register.php`
     - `products.php`
     - `about.php`
     - `contact.php`
     - `logout.php`
     - `config.php`
     - `functions.php`
     - `style.css`
     - `script.js`
     - `db_setup.sql`

4. **Update Product Images** (if database already exists)
   - Run `http://localhost/forge720/update_images_local.php` in your browser
   - This will update existing product records to use the local image filenames

5. **Images Folder** (Optional)
   - Save your real product images in `images/`
   - Use these filenames for the current products:
     - `aluminum-fence.jpg`
     - `handrail.jpg`
     - `balcony.jpg`
     - `gate.jpg`
     - `staircase.jpg`
   - You can replace these with your own photos later; just keep the filename consistent in the database

### Accessing the Website

Open your web browser and go to: `http://localhost/forge720/`

## File Structure

```
forge720/
├── index.php          # Homepage
├── login.php          # User login
├── register.php       # User registration
├── products.php       # Product catalog
├── about.php          # About page
├── contact.php        # Contact form
├── logout.php         # User logout
├── config.php         # Database configuration
├── functions.php      # Helper functions
├── style.css          # CSS styles
├── script.js          # JavaScript functionality
├── db_setup.sql       # Database schema
├── images/            # Product images (create this folder)
└── README.md          # This file
```

## Database Schema

### Users Table
- `id` (INT, Primary Key)
- `username` (VARCHAR)
- `email` (VARCHAR)
- `password` (VARCHAR, hashed)
- `created_at` (TIMESTAMP)

### Products Table
- `id` (INT, Primary Key)
- `name` (VARCHAR)
- `description` (TEXT)
- `price` (DECIMAL)
- `image` (VARCHAR)
- `created_at` (TIMESTAMP)

## Customization

### Colors
The theme uses jungle green (#4B8B3E) and gold (#FFD700). To change colors:
- Edit the CSS variables in `style.css` under `:root`

### Products
To add more products:
- Insert new records into the `products` table via phpMyAdmin
- Add corresponding images to the `images/` folder

### Company Information
Update contact details in:
- `contact.php` (address, phone, email)
- `about.php` (company description)

## Security Notes

- Passwords are hashed using PHP's `password_hash()` function
- User input is sanitized to prevent SQL injection
- Session management is used for user authentication

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running in XAMPP
- Check database credentials in `config.php`
- Verify database name matches the one created

### Page Not Loading
- Ensure Apache is running
- Check file permissions
- Verify all required PHP extensions are enabled

### Login/Register Not Working
- Confirm database tables were created correctly
- Check for PHP errors in error logs
- Ensure session handling is working

## Support

For issues or questions, please contact the development team.