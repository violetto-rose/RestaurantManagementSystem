-- Disable foreign key checks to allow inserting data more easily
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data from the tables
TRUNCATE TABLE `Order`;
TRUNCATE TABLE OrderItem;
TRUNCATE TABLE Bill;
TRUNCATE TABLE Reservation;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert demo data for reservations
INSERT INTO Reservation (CustomerID, TableID, ReservationDate, ReservationTime, NumberOfGuests, TableType, Price)
VALUES 
(1, 1, '2024-07-18', '18:00:00', 2, 'normal', 100),
(1, 2, '2024-07-18', '19:00:00', 4, 'normal', 100),
(1, 3, '2024-07-19', '19:30:00', 3, 'normal', 100),
(1, 4, '2024-07-19', '18:30:00', 2, 'normal', 100),
(1, 5, '2024-07-20', '19:00:00', 4, 'normal', 100),
(1, 6, '2024-07-20', '20:00:00', 2, 'normal', 100),
(1, 7, '2024-07-21', '17:30:00', 3, 'normal', 100),
(1, 8, '2024-07-21', '19:30:00', 4, 'normal', 100),
(1, 9, '2024-07-22', '20:00:00', 2, 'normal', 100),
(1, 10, '2024-07-22', '18:00:00', 3, 'normal', 100),
(1, 11, '2024-07-23', '18:30:00', 4, 'normal', 100),
(1, 12, '2024-07-23', '20:30:00', 2, 'normal', 100),
(1, 13, '2024-07-24', '18:00:00', 3, 'normal', 100),
(1, 14, '2024-07-24', '19:00:00', 4, 'normal', 100),
(1, 15, '2024-07-25', '19:30:00', 2, 'normal', 100),
(1, 16, '2024-07-25', '18:30:00', 3, 'normal', 100),
(1, 17, '2024-07-26', '19:00:00', 4, 'normal', 100),
(1, 18, '2024-07-26', '20:00:00', 2, 'normal', 100),
(1, 19, '2024-07-27', '17:30:00', 3, 'normal', 100),
(1, 20, '2024-07-27', '19:30:00', 4, 'normal', 100);

-- Insert demo data for orders
INSERT INTO `Order` (ReservationID, OrderDate, OrderTime)
SELECT ReservationID, ReservationDate, ReservationTime
FROM Reservation;

-- Insert OrderItems with variations
-- Note: MenuID references are now replaced with actual item names for clarity
INSERT INTO OrderItem (OrderID, MenuID, Quantity)
VALUES
-- July 18
(1, (SELECT MenuID FROM Menu WHERE ItemName = 'Samosa'), 2),
(1, (SELECT MenuID FROM Menu WHERE ItemName = 'Butter Chicken'), 1),
(1, (SELECT MenuID FROM Menu WHERE ItemName = 'Naan'), 2),
(1, (SELECT MenuID FROM Menu WHERE ItemName = 'Masala Chai'), 2),
(2, (SELECT MenuID FROM Menu WHERE ItemName = 'Paneer Tikka'), 1),
(2, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (vegetable)'), 2),
(2, (SELECT MenuID FROM Menu WHERE ItemName = 'Raita'), 1),
(2, (SELECT MenuID FROM Menu WHERE ItemName = 'Mango Lassi'), 2),

-- July 19
(3, (SELECT MenuID FROM Menu WHERE ItemName = 'Chicken Tikka'), 1),
(3, (SELECT MenuID FROM Menu WHERE ItemName = 'Rogan Josh'), 1),
(3, (SELECT MenuID FROM Menu WHERE ItemName = 'Paratha'), 3),
(3, (SELECT MenuID FROM Menu WHERE ItemName = 'Indian Filter Coffee'), 2),
(4, (SELECT MenuID FROM Menu WHERE ItemName = 'Aloo Tikki'), 1),
(4, (SELECT MenuID FROM Menu WHERE ItemName = 'Palak Paneer'), 1),
(4, (SELECT MenuID FROM Menu WHERE ItemName = 'Jeera Rice'), 1),
(4, (SELECT MenuID FROM Menu WHERE ItemName = 'Lassi'), 2),

-- July 20
(5, (SELECT MenuID FROM Menu WHERE ItemName = 'Paneer Butter Masala'), 2),
(5, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (chicken)'), 2),
(5, (SELECT MenuID FROM Menu WHERE ItemName = 'Naan'), 4),
(5, (SELECT MenuID FROM Menu WHERE ItemName = 'Gulab Jamun'), 2),
(6, (SELECT MenuID FROM Menu WHERE ItemName = 'Fish Curry'), 1),
(6, (SELECT MenuID FROM Menu WHERE ItemName = 'Lemon Rice'), 1),
(6, (SELECT MenuID FROM Menu WHERE ItemName = 'Papadum'), 2),
(6, (SELECT MenuID FROM Menu WHERE ItemName = 'Masala Chai'), 1),

-- July 21
(7, (SELECT MenuID FROM Menu WHERE ItemName = 'Chana Masala'), 1),
(7, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (mutton)'), 1),
(7, (SELECT MenuID FROM Menu WHERE ItemName = 'Roti'), 4),
(7, (SELECT MenuID FROM Menu WHERE ItemName = 'Rasgulla'), 1),
(8, (SELECT MenuID FROM Menu WHERE ItemName = 'Baingan Bharta'), 1),
(8, (SELECT MenuID FROM Menu WHERE ItemName = 'Butter Chicken'), 1),
(8, (SELECT MenuID FROM Menu WHERE ItemName = 'Pulao'), 1),
(8, (SELECT MenuID FROM Menu WHERE ItemName = 'Mango Lassi'), 2),

-- July 22
(9, (SELECT MenuID FROM Menu WHERE ItemName = 'Samosa'), 1),
(9, (SELECT MenuID FROM Menu WHERE ItemName = 'Paneer Butter Masala'), 1),
(9, (SELECT MenuID FROM Menu WHERE ItemName = 'Naan'), 2),
(9, (SELECT MenuID FROM Menu WHERE ItemName = 'Kheer'), 1),
(10, (SELECT MenuID FROM Menu WHERE ItemName = 'Chicken Tikka'), 1),
(10, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (chicken)'), 1),
(10, (SELECT MenuID FROM Menu WHERE ItemName = 'Raita'), 1),
(10, (SELECT MenuID FROM Menu WHERE ItemName = 'Indian Filter Coffee'), 2),

-- July 23 - July 27: Add similar variations for the remaining days

-- July 23
(11, (SELECT MenuID FROM Menu WHERE ItemName = 'Paneer Tikka'), 2),
(11, (SELECT MenuID FROM Menu WHERE ItemName = 'Rogan Josh'), 2),
(11, (SELECT MenuID FROM Menu WHERE ItemName = 'Paratha'), 4),
(11, (SELECT MenuID FROM Menu WHERE ItemName = 'Lassi'), 3),
(12, (SELECT MenuID FROM Menu WHERE ItemName = 'Aloo Tikki'), 1),
(12, (SELECT MenuID FROM Menu WHERE ItemName = 'Fish Curry'), 1),
(12, (SELECT MenuID FROM Menu WHERE ItemName = 'Jeera Rice'), 1),
(12, (SELECT MenuID FROM Menu WHERE ItemName = 'Jalebi'), 1),

-- July 24
(13, (SELECT MenuID FROM Menu WHERE ItemName = 'Chana Masala'), 1),
(13, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (vegetable)'), 1),
(13, (SELECT MenuID FROM Menu WHERE ItemName = 'Naan'), 2),
(13, (SELECT MenuID FROM Menu WHERE ItemName = 'Mango Lassi'), 2),
(14, (SELECT MenuID FROM Menu WHERE ItemName = 'Baingan Bharta'), 1),
(14, (SELECT MenuID FROM Menu WHERE ItemName = 'Butter Chicken'), 2),
(14, (SELECT MenuID FROM Menu WHERE ItemName = 'Pulao'), 1),
(14, (SELECT MenuID FROM Menu WHERE ItemName = 'Gulab Jamun'), 2),

-- July 25
(15, (SELECT MenuID FROM Menu WHERE ItemName = 'Samosa'), 2),
(15, (SELECT MenuID FROM Menu WHERE ItemName = 'Palak Paneer'), 1),
(15, (SELECT MenuID FROM Menu WHERE ItemName = 'Roti'), 3),
(15, (SELECT MenuID FROM Menu WHERE ItemName = 'Masala Chai'), 2),
(16, (SELECT MenuID FROM Menu WHERE ItemName = 'Chicken Tikka'), 1),
(16, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (mutton)'), 1),
(16, (SELECT MenuID FROM Menu WHERE ItemName = 'Raita'), 1),
(16, (SELECT MenuID FROM Menu WHERE ItemName = 'Indian Filter Coffee'), 2),

-- July 26
(17, (SELECT MenuID FROM Menu WHERE ItemName = 'Paneer Butter Masala'), 2),
(17, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (chicken)'), 2),
(17, (SELECT MenuID FROM Menu WHERE ItemName = 'Naan'), 4),
(17, (SELECT MenuID FROM Menu WHERE ItemName = 'Rasgulla'), 2),
(18, (SELECT MenuID FROM Menu WHERE ItemName = 'Fish Curry'), 1),
(18, (SELECT MenuID FROM Menu WHERE ItemName = 'Lemon Rice'), 1),
(18, (SELECT MenuID FROM Menu WHERE ItemName = 'Papadum'), 2),
(18, (SELECT MenuID FROM Menu WHERE ItemName = 'Kheer'), 1),

-- July 27
(19, (SELECT MenuID FROM Menu WHERE ItemName = 'Aloo Tikki'), 2),
(19, (SELECT MenuID FROM Menu WHERE ItemName = 'Rogan Josh'), 1),
(19, (SELECT MenuID FROM Menu WHERE ItemName = 'Paratha'), 3),
(19, (SELECT MenuID FROM Menu WHERE ItemName = 'Lassi'), 2),
(20, (SELECT MenuID FROM Menu WHERE ItemName = 'Paneer Tikka'), 1),
(20, (SELECT MenuID FROM Menu WHERE ItemName = 'Biryani (vegetable)'), 2),
(20, (SELECT MenuID FROM Menu WHERE ItemName = 'Raita'), 1),
(20, (SELECT MenuID FROM Menu WHERE ItemName = 'Jalebi'), 2);

-- Insert Bills (with calculated total amounts based on order items)
INSERT INTO Bill (OrderID, TotalAmount, PaymentStatus)
SELECT 
    oi.OrderID,
    SUM(m.Price * oi.Quantity) AS TotalAmount,
    'Paid' AS PaymentStatus
FROM 
    OrderItem oi
JOIN 
    Menu m ON oi.MenuID = m.MenuID
GROUP BY 
    oi.OrderID;