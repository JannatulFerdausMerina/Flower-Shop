-- ============================================================
-- Bloom & Petal Flower Shop - Database Schema & Seed Data
-- ============================================================

CREATE DATABASE IF NOT EXISTS flower_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE flower_shop;

-- ------------------------------------------------------------
-- Table: categories
-- ------------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: products
-- ------------------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(170) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: users (customer accounts)
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: admins
-- ------------------------------------------------------------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: orders
-- ------------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    notes VARCHAR(255) DEFAULT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: order_items
-- ------------------------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Seed: default admin (username: admin / password: admin123)
-- ------------------------------------------------------------
INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$eFJB99r9Y8SPCSuB3NdRVOexo373qnlCC14RSOKT7BdPJ2hvrlcVK', 'Shop Administrator');
-- NOTE: the hash above corresponds to the plaintext password: admin123

-- ------------------------------------------------------------
-- Seed: categories
-- ------------------------------------------------------------
INSERT INTO categories (name, slug, image) VALUES
('Bouquets', 'bouquets', 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=600'),
('Roses', 'roses', 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=600'),
('Wedding Flowers', 'wedding-flowers', 'https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?w=600'),
('Potted Plants', 'potted-plants', 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600'),
('Gift Boxes', 'gift-boxes', 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=600');

-- ------------------------------------------------------------
-- Seed: products
-- ------------------------------------------------------------
INSERT INTO products (category_id, name, slug, description, price, stock, image, featured) VALUES
(1, 'Pastel Garden Bouquet', 'pastel-garden-bouquet', 'A soft, romantic mix of pastel peonies, ranunculus, and eucalyptus, hand-tied with trailing ribbon.', 42.00, 25, 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=700', 1),
(1, 'Sunrise Wildflower Bunch', 'sunrise-wildflower-bunch', 'A cheerful gathering of seasonal wildflowers in warm sunrise tones, perfect for brightening any room.', 34.00, 30, 'https://images.unsplash.com/photo-1457089328109-e5d9bd499191?w=700', 1),
(1, 'Blush & Cream Hand-Tied', 'blush-cream-hand-tied', 'Delicate blush roses and cream lisianthus gathered into an elegant hand-tied bouquet.', 48.00, 20, 'https://images.unsplash.com/photo-1462275646964-a0e3386b89fa?w=700', 0),
(2, 'Classic Red Rose Dozen', 'classic-red-rose-dozen', 'A timeless dozen of long-stem red roses, the perfect way to say "I love you."', 55.00, 40, 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=700', 1),
(2, 'Pink Rose Romance', 'pink-rose-romance', 'Soft pink roses arranged with baby\'s breath, expressing admiration and gentle affection.', 49.00, 35, 'https://images.unsplash.com/photo-1455659817273-f96807779a8a?w=700', 0),
(2, 'White Rose Elegance', 'white-rose-elegance', 'Pure white roses symbolizing new beginnings and quiet elegance, beautifully wrapped.', 52.00, 18, 'https://images.unsplash.com/photo-1487070183336-b863922373d4?w=700', 0),
(3, 'Bridal Cascade Bouquet', 'bridal-cascade-bouquet', 'A flowing cascade bouquet of white orchids, garden roses, and trailing greenery for the modern bride.', 120.00, 8, 'https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?w=700', 1),
(3, 'Ceremony Centerpiece Set', 'ceremony-centerpiece-set', 'A set of 4 lush centerpieces in ivory and sage, designed to elevate any wedding table.', 180.00, 5, 'https://images.unsplash.com/photo-1519378058457-4c29a0a2efac?w=700', 0),
(4, 'Potted Peace Lily', 'potted-peace-lily', 'An easy-care peace lily in a ceramic pot, known for its glossy leaves and graceful white blooms.', 28.00, 22, 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=700', 0),
(4, 'Mini Succulent Trio', 'mini-succulent-trio', 'Three charming succulents in pastel ceramic pots, ideal for desks and windowsills.', 24.00, 50, 'https://images.unsplash.com/photo-1459411552884-841db9b3cc2a?w=700', 1),
(5, 'Luxury Flower & Chocolate Box', 'luxury-flower-chocolate-box', 'A curated hat box of garden roses paired with a box of artisan chocolates.', 65.00, 15, 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=700', 1),
(5, 'Birthday Surprise Box', 'birthday-surprise-box', 'A vibrant mixed bloom box with a handwritten card slot, ready to surprise on any birthday.', 45.00, 20, 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?w=700', 0);
