<?php
header('Content-Type: application/json');

try {
    // مسار قاعدة البيانات - تأكد أنه صحيح
    $dbPath = __DIR__ . '/database.db';
    
    // 1. التحقق من وجود الملف
    if (!file_exists($dbPath)) {
        throw new Exception("Database file not found at: " . $dbPath);
    }
    
    // 2. التحقق من أذونات الملف
    if (!is_readable($dbPath)) {
        throw new Exception("Database file is not readable");
    }

    // 3. الاتصال بقاعدة البيانات
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. جلب قائمة الجداول الموجودة
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
    
    echo json_encode([
        'database_path' => $dbPath,
        'tables_exist' => !empty($tables),
        'tables' => $tables,
        'file_permissions' => substr(sprintf('%o', fileperms($dbPath)), -4)
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
