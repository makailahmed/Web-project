<?php
include 'config.php';

$conn = new mysqli($server, $username, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$tableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($tableSql);


$tableSql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255),
    brand VARCHAR(100),
    price DECIMAL(10,2),
    stock INT,
    description TEXT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

$conn->query($tableSql);

$tableSql = "CREATE TABLE IF NOT EXISTS cart (
    userId INT NOT NULL,
    productID INT NOT NULL,
    id INT AUTO_INCREMENT PRIMARY KEY
);";

$conn->query($tableSql);


$tableSql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id TEXT NOT NULL,
    product_ids TEXT NOT NULL,
    quantities TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

$conn->query($tableSql);

$tableSql = "CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rate INT CHECK (rate BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

$conn->query($tableSql);
?>
