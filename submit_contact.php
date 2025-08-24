<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Povolena pouze POST metoda']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Neplatná data']);
    exit;
}

/
$required_fields = ['name', 'email', 'message'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Chybí povinné pole: $field"]);
        exit;
    }
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Neplatný formát emailu']);
    exit;
}

try {
    $create_table = "
        CREATE TABLE IF NOT EXISTS Contact_Messages (
            message_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(100),
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('new', 'read', 'replied') DEFAULT 'new'
        )
    ";
    $pdo->exec($create_table);
    
    $stmt = $pdo->prepare("
        INSERT INTO Contact_Messages (name, email, phone, subject, message) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $input['name'],
        $input['email'],
        $input['phone'] ?? null,
        $input['subject'] ?? 'Obecný dotaz',
        $input['message']
    ]);
    
    $message_id = $pdo->lastInsertId();
    
    $admin_email = 'admin@sladkacukrarna.cz'; // измените на реальный email администратора
    $subject_email = 'Nová zpráva z kontaktního formuláře - Sladká cukrárna';
    $message_email = "
        Nová zpráva z kontaktního formuláře:
        
        Jméno: {$input['name']}
        Email: {$input['email']}
        Telefon: " . ($input['phone'] ?? 'Nezadán') . "
        Předmět: " . ($input['subject'] ?? 'Obecný dotaz') . "
        
        Zpráva:
        {$input['message']}
        
        ---
        Tato zpráva byla automaticky odeslána z webu Sladká cukrárna.
    ";
    
    $headers = [
        'From: noreply@sladkacukrarna.cz',
        'Reply-To: ' . $input['email'],
        'Content-Type: text/plain; charset=UTF-8'
    ];

    // mail($admin_email, $subject_email, $message_email, implode("\r\n", $headers));
    // откоментить в случае экстренной необходимости (не рекомендуется)
    echo json_encode([
        'success' => true,
        'message' => 'Vaša zpráva byla úspěšně odeslána. Odpovíme vám do 24 hodin.',
        'message_id' => $message_id
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Chyba při odesílání zprávy: ' . $e->getMessage()
    ]);
}
?>