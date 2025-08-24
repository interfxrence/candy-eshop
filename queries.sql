SELECT * FROM Customer
WHERE email LIKE '%@seznam.cz';

SELECT * FROM Product
WHERE price > 1000;

SELECT * FROM OrderTable
WHERE MONTH(order_date) = 1 AND YEAR(order_date) = 2025;

SELECT c.first_name, c.last_name, o.order_id, o.total_price
FROM Customer c
JOIN OrderTable o ON c.customer_id = o.customer_id;

SELECT c.first_name, c.last_name, a.street, a.city
FROM Customer c
LEFT JOIN Address a ON c.customer_id = a.customer_id;

SELECT p.name AS product, k.name AS category
FROM Product p
LEFT JOIN Category k ON p.category_id = k.category_id;

SELECT o.order_id, o.total_price, p.payment_type, p.amount
FROM OrderTable o
RIGHT JOIN Payment p ON o.order_id = p.order_id;

SELECT name, price
FROM Product
ORDER BY price DESC
LIMIT 1;

SELECT AVG(price) AS average_price
FROM Product;

SELECT c.customer_id, c.first_name, COUNT(o.order_id) AS order_count
FROM Customer c
JOIN OrderTable o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.first_name;

SELECT c.customer_id, c.first_name, COUNT(o.order_id) AS order_count
FROM Customer c
JOIN OrderTable o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.first_name
HAVING COUNT(o.order_id) > 3;

SELECT DISTINCT p.name
FROM Product p
JOIN Order_Product op ON p.product_id = op.product_id;

SELECT DISTINCT c.first_name, c.last_name
FROM Customer c
JOIN OrderTable o ON c.customer_id = o.customer_id
JOIN Payment p ON o.order_id = p.order_id
WHERE p.payment_type = 'Card';

SELECT o.order_id, SUM(op.quantity * op.unit_price) AS calculated_price
FROM OrderTable o
JOIN Order_Product op ON o.order_id = op.order_id
GROUP BY o.order_id;

SELECT c.first_name, c.last_name
FROM Customer c
LEFT JOIN OrderTable o ON c.customer_id = o.customer_id
WHERE o.order_id IS NULL;
