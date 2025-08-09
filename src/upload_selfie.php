<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$user_id = $_POST['user_id'] ?? ($_GET['user_id'] ?? null);

if (!$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$upload_dir = __DIR__ . '/uploads';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// حالة multipart/form-data
if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['file']['tmp_name'];
    $name = basename($_FILES['file']['name']);
    $dest = $upload_dir . '/' . $user_id . '_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', $name);
    move_uploaded_file($tmp, $dest);

    // تحديث قاعدة البيانات بمسار الصورة
    $db = new PDO('sqlite:' . __DIR__ . '/db/data.db');
    $stmt = $db->prepare('UPDATE liveness SET selfie_path = :path, updated_at = :upd WHERE user_id = :uid');
    $stmt->execute([':path' => $dest, ':upd' => time(), ':uid' => $user_id]);

    echo json_encode(['status'=>'ok','path'=>$dest]);
    exit;
}

// حالة JSON مع base64
$raw = file_get_contents('php://input');
$j = json_decode($raw, true);
if ($j && isset($j['image_base64'])) {
    $img = $j['image_base64'];
    if (preg_match('/^data:(image\/\w+);base64,/', $img, $m)) {
        $data = substr($img, strpos($img, ',') + 1);
        $data = base64_decode($data);
        $ext = explode('/', $m[1])[1];
    } else {
        $data = base64_decode($img);
        $ext = 'jpg';
    }
    $dest = $upload_dir . '/' . $user_id . '_' . time() . '.' . $ext;
    file_put_contents($dest, $data);

    $db = new PDO('sqlite:' . __DIR__ . '/db/data.db');
    $stmt = $db->prepare('UPDATE liveness SET selfie_path = :path, updated_at = :upd WHERE user_id = :uid');
    $stmt->execute([':path' => $dest, ':upd' => time(), ':uid' => $user_id]);

    echo json_encode(['status'=>'ok','path'=>$dest]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'No file provided']);
