<?php
header('Content-Type: application/json; charset=utf-8');

// CORS للسماح بالوصول من المتصفح (يمكن تقييده لاحقًا)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';

if (!$user_id) {
    echo json_encode([]);
    exit;
}

$dbfile = __DIR__ . '/db/data.db';
if (!file_exists($dbfile)) {
    echo json_encode([]);
    exit;
}

try {
    $db = new PDO('sqlite:' . $dbfile);
    $stmt = $db->prepare('SELECT user_id, transaction_id, spoof_ip, liveness_id, selfie_path, meta, updated_at FROM liveness WHERE user_id = :uid');
    $stmt->execute([':uid' => $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // إرجاع مصفوفة (كما يتوقع السكربت)
        echo json_encode([$row]);
    } else {
        // إذا لم توجد بيانات نرجع مصفوفة فارغة
        echo json_encode([]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([]);
}
