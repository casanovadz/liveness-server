<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$user_id = $data['user_id'];
$transaction_id = $data['transaction_id'] ?? null;
$liveness_id = $data['liveness_id'] ?? null;
$spoof_ip = $data['spoof_ip'] ?? null;
$meta = $data['meta'] ?? null;
$now = time();

$dbfile = __DIR__ . '/db/data.db';
if (!file_exists($dbfile)) {
    // محاولة إنشاء DB إذا لم يوجد
    require __DIR__ . '/init_db.php';
}

try {
    $db = new PDO('sqlite:' . $dbfile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إدراج أو تحديث
    $stmt = $db->prepare('INSERT INTO liveness (user_id, transaction_id, spoof_ip, liveness_id, meta, updated_at)
        VALUES (:user_id, :txn, :spoof, :lid, :meta, :upd)
        ON CONFLICT(user_id) DO UPDATE SET
        transaction_id = excluded.transaction_id,
        spoof_ip = excluded.spoof_ip,
        liveness_id = excluded.liveness_id,
        meta = excluded.meta,
        updated_at = excluded.updated_at
    ');
    $stmt->execute([
        ':user_id' => $user_id,
        ':txn' => $transaction_id,
        ':spoof' => $spoof_ip,
        ':lid' => $liveness_id,
        ':meta' => $meta ? json_encode($meta) : null,
        ':upd' => $now
    ]);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
