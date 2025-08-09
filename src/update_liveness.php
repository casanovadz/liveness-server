<?php
header('Content-Type: application/json');

// تأكد أن الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // التحقق من البيانات المدخلة
    if (!isset($data['user_id']) || !isset($data['status'])) {
        throw new Exception('Missing required fields');
    }
    
    $db = new PDO('sqlite:/var/www/html/db/data.db');
    $stmt = $db->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->execute([$data['status'], $data['user_id']]);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Data updated',
        'affected_rows' => $stmt->rowCount()
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
