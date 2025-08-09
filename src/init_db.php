<?php
$dbfile = __DIR__ . '/db/data.db';
if (!file_exists(dirname($dbfile))) mkdir(dirname($dbfile), 0755, true);

$db = new PDO('sqlite:' . $dbfile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS liveness (
    user_id TEXT PRIMARY KEY,
    transaction_id TEXT,
    spoof_ip TEXT,
    liveness_id TEXT,
    selfie_path TEXT,
    meta TEXT,
    updated_at INTEGER
);
");

echo "DB initialized at $dbfile\n";
