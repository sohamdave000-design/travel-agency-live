<?php
require_once dirname(__DIR__) . '/config/database.php';
$count = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
echo "Total packages: $count\n";
$latest = $pdo->query("SELECT * FROM packages ORDER BY id DESC LIMIT 1")->fetch();
print_r($latest);
?>
