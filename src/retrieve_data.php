<?php
header('Content-Type: application/json');

try {
    // 1. تحقق من وجود الملف
    if(!file_exists('database.db')) {
        throw new Exception("Database file not found");
    }
    
    // 2. تحقق من أذونات الملف
    if(!is_writable('database.db')) {
        throw new Exception("Database is not writable");
    }
    
    // 3. حاول الاتصال
    $db = new PDO('sqlite:database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 4. استعلام اختباري
    $test = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $test->fetchAll();
    
    if(empty($tables)) {
        echo json_encode(["error" => "No tables found in database"]);
    } else {
        echo json_encode(["tables" => $tables]);
    }
    
} catch(Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}