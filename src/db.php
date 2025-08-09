<?php
$host = "localhost";      // اسم السيرفر
$dbname = "liveness_db";  // اسم قاعدة البيانات
$username = "root";       // اسم المستخدم
$password = "";           // كلمة المرور

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "DB Connection failed: " . $e->getMessage()]));
}
