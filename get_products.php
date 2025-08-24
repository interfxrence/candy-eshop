<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

try {
    require_once 'config.php';
    
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            p.product_id,
            p.name,
            p.description,
            p.price,
            p.stock,
            c.name as category_name,
            c.category_id
        FROM Product p
        LEFT JOIN Category c ON p.category_id = c.category_id
        WHERE p.stock > 0
        ORDER BY c.name, p.name
    ");
    
    $stmt->execute();
    $products = $stmt->fetchAll();

    $categories = [];
    foreach ($products as $product) {
        $category_name = $product['category_name'] ?? 'Other';
        if (!isset($categories[$category_name])) {
            $categories[$category_name] = [];
        }
        $categories[$category_name][] = $product;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'categories' => $categories
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Error in get_products.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error loading products: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>