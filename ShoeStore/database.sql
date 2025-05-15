-- SQL commands to create database and tables for the shoe store

-- Create database
CREATE DATABASE IF NOT EXISTS shoe_store;

-- Use the database
USE shoe_store;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    stock INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address TEXT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO users (username, password) VALUES 
('admin', 'admin123');

-- Insert sample products
INSERT INTO products (name, description, price, image, category, stock) VALUES
('Nike Air Max', 'Classic Nike Air Max running shoes with air cushion technology', 129.99, 'nike_air_max.jpg', 'Running', 25),
('Adidas Superstar', 'Iconic shell toe sneakers from Adidas', 89.99, 'adidas_superstar.jpg', 'Casual', 30),
('Puma RS-X', 'Bold chunky sneakers with retro design', 99.99, 'puma_rsx.jpg', 'Casual', 15),
('New Balance 990', 'Premium running shoes made in USA', 179.99, 'new_balance_990.jpg', 'Running', 10),
('Converse Chuck Taylor', 'Classic high top canvas sneakers', 59.99, 'converse_chuck.jpg', 'Casual', 40),
('Vans Old Skool', 'Popular skate shoes with side stripe', 69.99, 'vans_old_skool.jpg', 'Skate', 35),
('Reebok Club C', 'Vintage tennis-inspired casual shoes', 79.99, 'reebok_club_c.jpg', 'Casual', 20),
('ASICS Gel-Kayano', 'Premium stability running shoes', 159.99, 'asics_gel_kayano.jpg', 'Running', 12);
