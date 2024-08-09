-- Drop existing database if it exists
DROP DATABASE IF EXISTS SwaadSanchalan;
-- Create a new database
CREATE DATABASE SwaadSanchalan;
USE SwaadSanchalan;
-- Create Customer Table
CREATE TABLE Customer (
    CustomerID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Phone VARCHAR(15),
    Email VARCHAR(100)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Create RestaurantTable Table
CREATE TABLE RestaurantTable (
    TableID INT PRIMARY KEY AUTO_INCREMENT,
    TableNumber INT NOT NULL,
    Capacity INT NOT NULL,
    IsAvailable BOOLEAN DEFAULT TRUE,
    TableType ENUM('normal', 'premium', 'vip') NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Create Reservation Table
CREATE TABLE Reservation (
    ReservationID INT PRIMARY KEY AUTO_INCREMENT,
    CustomerID INT,
    TableID INT,
    ReservationDate DATE NOT NULL,
    ReservationTime TIME NOT NULL,
    NumberOfGuests INT NOT NULL CHECK (NumberOfGuests > 0),
    TableType ENUM('normal', 'premium', 'vip') NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID) ON DELETE
    SET NULL,
        FOREIGN KEY (TableID) REFERENCES RestaurantTable(TableID) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Create Menu Table
CREATE TABLE Menu (
    MenuID INT PRIMARY KEY AUTO_INCREMENT,
    ItemName VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10, 2) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Create Order Table
CREATE TABLE `Order` (
    OrderID INT PRIMARY KEY AUTO_INCREMENT,
    ReservationID INT DEFAULT NULL,
    OrderDate DATE NOT NULL,
    OrderTime TIME NOT NULL,
    FOREIGN KEY (ReservationID) REFERENCES Reservation(ReservationID) ON DELETE
    SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Create OrderItem Table
CREATE TABLE OrderItem (
    OrderItemID INT PRIMARY KEY AUTO_INCREMENT,
    OrderID INT,
    MenuID INT,
    Quantity INT NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (MenuID) REFERENCES Menu(MenuID) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Create Bill Table
CREATE TABLE Bill (
    BillID INT PRIMARY KEY AUTO_INCREMENT,
    OrderID INT,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    PaymentStatus VARCHAR(50),
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- Insert into Customer Table
INSERT INTO Customer (Name, Phone, Email)
VALUES (
        'John Doe',
        '123-456-7890',
        'john.doe@example.com'
    );
-- Insert into RestaurantTable Table
-- Normal tables
INSERT INTO RestaurantTable (TableNumber, Capacity, IsAvailable, TableType)
VALUES (1, 4, TRUE, 'normal'),
    (2, 4, TRUE, 'normal'),
    (3, 4, TRUE, 'normal'),
    (4, 4, TRUE, 'normal'),
    (5, 4, TRUE, 'normal'),
    (6, 4, TRUE, 'normal'),
    (7, 4, TRUE, 'normal'),
    (8, 4, TRUE, 'normal'),
    (9, 4, TRUE, 'normal'),
    (10, 4, TRUE, 'normal'),
    (11, 4, TRUE, 'normal'),
    (12, 4, TRUE, 'normal'),
    (13, 4, TRUE, 'normal'),
    (14, 4, TRUE, 'normal'),
    (15, 4, TRUE, 'normal'),
    (16, 4, TRUE, 'normal'),
    (17, 4, TRUE, 'normal'),
    (18, 4, TRUE, 'normal'),
    (19, 4, TRUE, 'normal'),
    (20, 4, TRUE, 'normal'),
    -- Premium tables
    (21, 6, TRUE, 'premium'),
    (22, 6, TRUE, 'premium'),
    (23, 6, TRUE, 'premium'),
    (24, 6, TRUE, 'premium'),
    (25, 6, TRUE, 'premium'),
    (26, 6, TRUE, 'premium'),
    (27, 6, TRUE, 'premium'),
    (28, 6, TRUE, 'premium'),
    (29, 6, TRUE, 'premium'),
    (30, 6, TRUE, 'premium'),
    -- VIP tables
    (31, 8, TRUE, 'vip'),
    (32, 8, TRUE, 'vip'),
    (33, 8, TRUE, 'vip'),
    (34, 8, TRUE, 'vip'),
    (35, 8, TRUE, 'vip'),
    (36, 8, TRUE, 'vip'),
    (37, 8, TRUE, 'vip'),
    (38, 8, TRUE, 'vip'),
    (39, 8, TRUE, 'vip'),
    (40, 8, TRUE, 'vip');
-- Insert into Menu Table
-- Insert Starters
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Samosa', 'For 2 pieces', 50),
    ('Paneer Tikka', '', 200),
    ('Chicken Tikka', '', 250),
    ('Aloo Tikki', 'For 2 pieces', 60);
-- Insert Main Course - Vegetarian
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Paneer Butter Masala', '', 250),
    ('Chana Masala', '', 180),
    ('Baingan Bharta', '', 220),
    ('Biryani (vegetable)', '', 280),
    ('Palak Paneer', '', 250);
-- Insert Main Course - Non-Vegetarian
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Butter Chicken', '', 300),
    ('Rogan Josh', '', 350),
    ('Fish Curry', '', 300),
    ('Biryani (chicken)', '', 350),
    ('Biryani (mutton)', '', 400);
-- Insert Bread
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Naan', '', 40),
    ('Roti', '', 20),
    ('Paratha', '', 50),
    ('Puri', 'For 3 pieces', 40);
-- Insert Rice
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Jeera Rice', '', 120),
    ('Pulao', '', 150),
    ('Lemon Rice', '', 130);
-- Insert Side Dishes
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Raita', '', 70),
    ('Papadum', '', 30),
    ('Pickles and Chutneys', '', 20);
-- Insert Desserts
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Gulab Jamun', 'For 2 pieces', 50),
    ('Rasgulla', 'For 2 pieces', 60),
    ('Kheer', '', 80),
    ('Jalebi', 'Per plate', 100);
-- Insert Beverages
INSERT INTO Menu (ItemName, Description, Price)
VALUES ('Masala Chai', '', 30),
    ('Lassi', '', 60),
    ('Mango Lassi', '', 80),
    ('Indian Filter Coffee', '', 50);