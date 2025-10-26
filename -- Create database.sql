-- Create database
CREATE DATABASE IF NOT EXISTS store_db;
USE store_db;

-- Inventory table
CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    srp DECIMAL(10,2) NOT NULL
);

-- Sales table
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    quantity_sold INT NOT NULL,
    date_of_sale DATE NOT NULL,
    FOREIGN KEY (item_id) REFERENCES inventory(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Sample data (optional)
INSERT INTO inventory (item_code, item_name, quantity, unit_cost, srp) VALUES
('A001', 'Shampoo 200ml', 50, 40.00, 60.00),
('A002', 'Toothpaste 100g', 30, 25.00, 40.00),
('A003', 'Soap Bar', 100, 15.00, 25.00),
('A004', 'Conditioner 200ml', 40, 45.00, 65.00);
