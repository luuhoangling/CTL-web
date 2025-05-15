# Simple PHP Shoe Store

A basic e-commerce website for selling shoes, built with PHP, MySQL, and Bootstrap.

## Features

1. Display product list
2. View product details
3. Search for products
4. Shopping cart functionality
5. Simple order placement (no real payment)
6. Admin product management
7. Admin login/logout
8. CRUD operations for products (admin)

## Requirements

- PHP 7.0+
- MySQL 5.6+
- Web server (Apache, Nginx, etc.)

## Installation

1. Clone or download this repository to your web server directory
2. Create a MySQL database named `shoe_store`
3. Import the `database.sql` file into your MySQL database
4. Configure the database connection in `includes/config.php` if needed
5. Make sure the `assets/images` directory is writable

```bash
# Sample commands to set up on a local XAMPP server
# 1. Clone repository to htdocs folder
git clone https://github.com/yourusername/shoe-store.git

# 2. Create database and import SQL
mysql -u root -p -e "CREATE DATABASE shoe_store;"
mysql -u root -p shoe_store < database.sql

# 3. Set permissions for images directory
chmod 777 assets/images
```

## Configuration

Edit the database configuration in `includes/config.php`:

```php
define('DB_SERVER', 'localhost');    // Database host
define('DB_USERNAME', 'root');       // Database username
define('DB_PASSWORD', '');           // Database password
define('DB_NAME', 'shoe_store');     // Database name
```

## Usage

### Customer Site

- Browse products on the homepage
- View product details 
- Add products to cart
- Place orders

### Admin Panel

- Access the admin panel at `/admin`
- Login with the default credentials:
  - Username: `admin`
  - Password: `admin123`
- Manage products (add, edit, delete)
- View orders and sales statistics

## File Structure

```
/ShoeStore
  /admin               # Admin panel files
    /auth              # Authentication files
    /products          # Product management
    /includes          # Admin includes
  /assets              # Assets directory
    /images            # Product images
  /css                 # CSS stylesheets
  /includes            # Main site includes
  /js                  # JavaScript files
  database.sql         # Database structure
  index.php            # Homepage
  products.php         # Products page
  product.php          # Product detail page
  cart.php             # Shopping cart
  checkout.php         # Checkout page
  search.php           # Search results
```

## Security Notes

1. The project uses prepared statements to prevent SQL injection
2. Passwords are hashed using PHP's password_hash() function
3. Input validation is implemented on all forms
4. File upload security measures are in place

## Project Extensions (Future Improvements)

1. User registration and login
2. User order history
3. Multiple product images
4. Product reviews and ratings
5. Advanced filtering and sorting
6. Payment gateway integration
7. Stock management
8. Order status tracking
9. More admin statistics and reports
10. Product categories and subcategories management
