<?php
header('Content-Type: application/json');

try {
    $dbPath = getenv('DB_PATH') ?: __DIR__.'/database.db';
    
    if (!file_exists($dbPath)) {
        throw new Exception("Database file not found at: ".$dbPath);
    }

    $db = new PDO('sqlite:'.$dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء الجدول إذا لم يكن موجوداً
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY,
        name TEXT,
        status TEXT)");
    
    // إضافة بيانات اختبارية إذا كانت الجداول فارغة
    $count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO users (name, status) VALUES ('Test User', 'active')");
    }

    // جلب البيانات
    $stmt = $db->query("SELECT * FROM users");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results ?: ['message' => 'No data found']);

} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
