CREATE DATABASE IF NOT EXISTS sladkostiwzc6552;
USE sladkostiwzc6552;

CREATE TABLE Customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE Address (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    street VARCHAR(100),
    city VARCHAR(50),
    zip_code VARCHAR(10),
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE CASCADE
);

CREATE TABLE Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    parent_category INT,
    image_url VARCHAR(255),
    FOREIGN KEY (parent_category) REFERENCES Category(category_id)
);

CREATE TABLE Product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES Category(category_id)
);

CREATE TABLE OrderTable (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_date DATE NOT NULL,
    status VARCHAR(30),
    total_price DECIMAL(12,2),
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
);

CREATE TABLE Order_Product (
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id) REFERENCES OrderTable(order_id)
        ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE Payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNIQUE,
    payment_type VARCHAR(50),
    payment_date DATE,
    amount DECIMAL(12,2),
    FOREIGN KEY (order_id) REFERENCES OrderTable(order_id)
        ON DELETE CASCADE
);
