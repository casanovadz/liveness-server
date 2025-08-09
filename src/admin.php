<?php
// ابدأ بتهيئة متغير البيئية ADMIN_TOKEN على Render أو سيستم التشغيل
$ADMIN_TOKEN = getenv('ADMIN_TOKEN') ?: 'changeme';

$token = $_GET['token'] ?? ($_SERVER['HTTP_X_ADMIN_TOKEN'] ?? null);
if ($token !== $ADMIN_TOKEN) {
    http_response_code(401);
    echo "Unauthorized\n";
    exit;
}

$dbfile = __DIR__ . '/db/data.db';
if (!file_exists($dbfile)) {
    echo "No DB found\n";
    exit;
}

$db = new PDO('sqlite:' . $dbfile);
$stmt = $db->query('SELECT user_id, transaction_id, spoof_ip, liveness_id, selfie_path, meta, updated_at FROM liveness ORDER BY updated_at DESC');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<!doctype html><html><head><meta charset='utf-8'><title>Admin</title></head><body>";
echo "<h1>Entries</h1><table border=1 cellpadding=6><tr><th>user_id</th><th>txn</th><th>spoof_ip</th><th>liveness_id</th><th>selfie</th><th>updated_at</th></tr>";
foreach($rows as $r){
    echo "<tr>";
    echo "<td>{$r['user_id']}</td>";
    echo "<td>{$r['transaction_id']}</td>";
    echo "<td>{$r['spoof_ip']}</td>";
    echo "<td>{$r['liveness_id']}</td>";
    $sp = $r['selfie_path'] ? "<a href='".basename($r['selfie_path'])."'>file</a>" : "";
    echo "<td>$sp</td>";
    echo "<td>".($r['updated_at']?date('Y-m-d H:i:s',$r['updated_at']):'')."</td>";
    echo "</tr>";
}
echo "</table></body></html>";
