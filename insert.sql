USE sladkostiwzc6552;

INSERT INTO Customer (first_name, last_name, email, phone) VALUES
('Petr', 'Novak', 'petr.novak@gmail.com', '123456789'),
('Jana', 'Svobodova', 'jana.svobodova@seznam.cz', '987654321'),
('Karel', 'Dvorak', 'karel.dvorak@yahoo.com', '456789123'),
('Eva', 'Prochazkova', 'eva.prochazkova@seznam.cz', '654321987'),
('Marek', 'Kral', 'marek.kral@gmail.com', '789123456'),
('Lucie', 'Pospisilova', 'lucie.pospisilova@seznam.cz', '321987654'),
('Tomas', 'Horak', 'tomas.horak@hotmail.com', '147258369');

INSERT INTO Address (customer_id, street, city, zip_code) VALUES
(1, 'Cukrovarska 12', 'Praha', '11000'),
(2, 'Sladka 5', 'Brno', '60200'),
(3, 'Cokoladova 7', 'Ostrava', '70030'),
(4, 'Bonbonova 22', 'Plzen', '30100'),
(5, 'Karamelova 9', 'Olomouc', '77900'),
(6, 'Pernikova 3', 'Liberec', '46001'),
(7, 'Marcipanova 15', 'Hradec Kralove', '50002');

INSERT INTO Category (name, description, parent_category, image_url) VALUES
('Chocolate', 'Chocolate-based sweets', NULL, 'chocolate.jpg'),
('Candy', 'Various candies', NULL, 'candy.jpg'),
('Caramel', 'Caramel sweets', NULL, 'caramel.jpg'),
('Biscuits', 'Sweet biscuits', NULL, 'biscuits.jpg'),
('Seasonal', 'Seasonal sweets', NULL, 'seasonal.jpg'),
('Gummies', 'Gummy bears and jellies', 2, 'gummies.jpg'),
('Special Edition', 'Limited edition sweets', NULL, 'special.jpg');

INSERT INTO Product (category_id, name, description, price, stock) VALUES
(1, 'Milk Chocolate Bar', 'Classic milk chocolate bar 100g', 29.90, 500),
(1, 'Dark Chocolate 70%', 'Dark chocolate with 70% cocoa', 34.90, 300),
(2, 'Fruit Candy Mix', 'Assorted fruit-flavored candies', 59.90, 200),
(3, 'Salted Caramel Toffee', 'Soft caramel with sea salt', 79.90, 150),
(4, 'Chocolate Chip Cookies', 'Box of 12 cookies', 89.90, 100),
(6, 'Gummy Bears', 'Fruit-flavored gummy bears 200g', 49.90, 250),
(5, 'Christmas Gingerbread', 'Traditional gingerbread pack', 99.90, 80);

INSERT INTO OrderTable (customer_id, order_date, status, total_price) VALUES
(1, '2025-01-05', 'Completed', 64.80),
(2, '2025-01-15', 'Pending', 59.90),
(3, '2025-02-01', 'Completed', 79.90),
(4, '2025-02-10', 'Completed', 89.90),
(5, '2025-02-15', 'Pending', 34.90),
(6, '2025-03-01', 'Completed', 99.90),
(7, '2025-03-05', 'Completed', 49.90);

INSERT INTO Order_Product (order_id, product_id, quantity, unit_price) VALUES
(1, 1, 1, 29.90),
(1, 2, 1, 34.90),
(2, 3, 1, 59.90),
(3, 4, 1, 79.90),
(4, 5, 1, 89.90),
(5, 2, 1, 34.90),
(6, 7, 1, 99.90),
(7, 6, 1, 49.90);

INSERT INTO Payment (order_id, payment_type, payment_date, amount) VALUES
(1, 'Card', '2025-01-05', 64.80),
(2, 'Bank Transfer', '2025-01-15', 59.90),
(3, 'Card', '2025-02-01', 79.90),
(4, 'Cash', '2025-02-10', 89.90),
(5, 'Card', '2025-02-15', 34.90),
(6, 'Card', '2025-03-01', 99.90),
(7, 'Card', '2025-03-05', 49.90);
