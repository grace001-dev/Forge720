-- Database setup for Forge 720
-- Run this in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS forge720;
USE forge720;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table (enhanced)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    category_id INT,
    material VARCHAR(100),
    dimensions VARCHAR(100),
    finish VARCHAR(100),
    stock_quantity INT DEFAULT 100,
    is_customizable BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Cart Items table (for session-based cart storage)
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    customization_options JSON,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),

    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),

    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(50) NOT NULL,
    shipping_state VARCHAR(50),
    shipping_zip VARCHAR(10) NOT NULL,
    shipping_country VARCHAR(50) NOT NULL,

    billing_address TEXT,
    billing_city VARCHAR(50),
    billing_state VARCHAR(50),
    billing_zip VARCHAR(10),
    billing_country VARCHAR(50),

    shipping_method VARCHAR(50),
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    tracking_number VARCHAR(100),

    special_instructions TEXT,
    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    customization_options JSON,
    subtotal DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Quotes table
CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),

    description TEXT NOT NULL,
    estimated_cost DECIMAL(10,2),
    status ENUM('pending', 'reviewing', 'quoted', 'accepted', 'rejected', 'expired') DEFAULT 'pending',

    attachment_path VARCHAR(255),
    attachment_filename VARCHAR(255),

    required_by_date DATE,
    quoted_date TIMESTAMP NULL,
    quote_valid_until DATE,

    notes TEXT,
    admin_notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Product Customization Options table
CREATE TABLE IF NOT EXISTS customization_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    option_name VARCHAR(100) NOT NULL,
    option_type ENUM('text', 'select', 'multiselect', 'number') NOT NULL,
    option_values JSON,
    is_required BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Categories
INSERT INTO categories (name, description, image) VALUES
('Gates', 'Custom metal gates with intricate designs', 'gates-cat.jpg'),
('Stairs', 'Modern and elegant staircases', 'stairs-cat.jpg'),
('Fencing', 'Durable fencing solutions', 'fencing-cat.jpg'),
('Railings', 'Indoor and outdoor railings', 'railings-cat.jpg'),
('Balconies', 'Custom balcony designs', 'balconies-cat.jpg');

-- Insert some sample products (enhanced)
INSERT INTO products (name, description, price, image, category_id, material, dimensions, finish) VALUES
('Custom Metal Gate', 'Handcrafted wrought iron gate with intricate designs', 1200.00, 'gate.jpg', 1, 'Wrought Iron', '6ft x 4ft', 'Black Powder Coat'),
('Steel Staircase', 'Modern steel staircase with glass railings', 2500.00, 'staircase.jpg', 2, 'Steel', '10 steps', 'Stainless Steel'),
('Aluminum Fence', 'Durable aluminum fencing for residential properties', 800.00, 'aluminum-fence.jpg', 3, 'Aluminum', '100ft x 6ft', 'Bronze'),
('Brass Handrail', 'Elegant brass handrail for indoor use', 450.00, 'handrail.jpg', 4, 'Brass', '20ft', 'Polished Brass'),
('Iron Balcony', 'Custom iron balcony with decorative elements', 1800.00, 'balcony.jpg', 5, 'Cast Iron', '8ft x 6ft', 'Black');

-- Insert sample customization options
INSERT INTO customization_options (product_id, option_name, option_type, option_values, is_required, display_order) VALUES
(1, 'Material', 'select', '["Wrought Iron", "Cast Iron", "Steel"]', FALSE, 1),
(1, 'Finish Color', 'select', '["Black", "Bronze", "Gold", "Silver"]', FALSE, 2),
(1, 'Width (feet)', 'number', NULL, FALSE, 3),
(1, 'Height (feet)', 'number', NULL, FALSE, 4),
(2, 'Material', 'select', '["Steel", "Stainless Steel"]', FALSE, 1),
(2, 'Glass Type', 'select', '["Tempered", "Frosted", "Clear"]', FALSE, 2),
(2, 'Number of Steps', 'number', NULL, FALSE, 3);