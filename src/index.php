<?php
header('Content-Type: application/json');

// عرض رسالة بسيطة للتأكد أن الخادم يعمل
echo json_encode([
    "status" => "online",
    "message" => "Liveness server is running",
    "available_endpoints" => [
        "/retrieve_data.php?user_id=...",
        "/update_liveness.php"
    ]
]);
