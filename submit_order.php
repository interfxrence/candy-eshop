<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_log("Order script started");

try {
    require_once 'config.php';
    error_log("Database connection successful");
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $raw_input = file_get_contents('php://input');
    error_log("Raw input: " . $raw_input);
    error_log("JSON decode error: " . json_last_error_msg());
    
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}

error_log("Input data received: " . print_r($input, true));

$required_fields = ['firstName', 'lastName', 'email', 'phone', 'street', 'city', 'zipCode', 'paymentType', 'items', 'total'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (!preg_match('/^\d{5}$/', $input['zipCode'])) { // изменить на /^\d{5}(-\d{4})?$/  
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ZIP code must be 5 digits']);
    exit;
}


if (!is_array($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

try {
    $pdo->beginTransaction();
    error_log("Transaction started");

    $stmt = $pdo->prepare("SELECT customer_id FROM Customer WHERE email = ?");
    $stmt->execute([$input['email']]);
    $existing_customer = $stmt->fetch();
    
    if ($existing_customer) {
        $customer_id = $existing_customer['customer_id'];
        error_log("Existing customer found: " . $customer_id);
        

        $stmt = $pdo->prepare("UPDATE Customer SET first_name = ?, last_name = ?, phone = ? WHERE customer_id = ?");
        $stmt->execute([
            $input['firstName'],
            $input['lastName'],
            $input['phone'],
            $customer_id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO Customer (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $input['firstName'],
            $input['lastName'],
            $input['email'],
            $input['phone']
        ]);
        $customer_id = $pdo->lastInsertId();
        error_log("New customer created: " . $customer_id);
    }
    
    $stmt = $pdo->prepare("SELECT address_id FROM Address WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $existing_address = $stmt->fetch();
    
    if ($existing_address) {
        $stmt = $pdo->prepare("UPDATE Address SET street = ?, city = ?, zip_code = ? WHERE customer_id = ?");
        $stmt->execute([
            $input['street'],
            $input['city'],
            $input['zipCode'],
            $customer_id
        ]);
        $address_id = $existing_address['address_id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO Address (customer_id, street, city, zip_code) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $customer_id,
            $input['street'],
            $input['city'],
            $input['zipCode']
        ]);
        $address_id = $pdo->lastInsertId();
    }
    
    error_log("Address handled: " . $address_id);
    
    $order_date = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT INTO OrderTable (customer_id, order_date, status, total_price) VALUES (?, ?, 'Pending', ?)");
    $stmt->execute([
        $customer_id,
        $order_date,
        $input['total']
    ]);
    $order_id = $pdo->lastInsertId();
    error_log("Order created: " . $order_id);
    
    $stmt = $pdo->prepare("INSERT INTO Order_Product (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    
    foreach ($input['items'] as $item) {
        if (!isset($item['id']) || !isset($item['quantity']) || !isset($item['price'])) {
            throw new Exception('Invalid item in cart');
        }
        
        $check_stmt = $pdo->prepare("SELECT product_id, stock FROM Product WHERE product_id = ?");
        $check_stmt->execute([$item['id']]);
        $product = $check_stmt->fetch();
        
        if (!$product) {
            throw new Exception("Product with ID {$item['id']} does not exist");
        }
        
        if ($product['stock'] < $item['quantity']) {
            throw new Exception("Insufficient stock for product ID {$item['id']}");
        }
        
        $stmt->execute([
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
        
        $update_stock = $pdo->prepare("UPDATE Product SET stock = stock - ? WHERE product_id = ?");
        $update_stock->execute([$item['quantity'], $item['id']]);
        
        error_log("Item processed: Product {$item['id']}, Quantity {$item['quantity']}");
    }
    
    $stmt = $pdo->prepare("INSERT INTO Payment (order_id, payment_type, payment_date, amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $order_id,
        $input['paymentType'],
        $order_date,
        $input['total']
    ]);
    $payment_id = $pdo->lastInsertId();
    
    error_log("Payment created: " . $payment_id);
    
    $pdo->commit();
    error_log("Transaction committed successfully");

    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $order_id,
        'customer_id' => $customer_id,
        'payment_id' => $payment_id,
        'total' => $input['total']
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Transaction rolled back due to error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Order processing error: ' . $e->getMessage()
    ]);
}
?>