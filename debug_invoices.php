<?php
require_once 'config/database.php';
echo "--- Last 5 Bookings ---\n";
$q = $pdo->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5");
while($row = $q->fetch()) {
    print_r($row);
}
echo "\n--- Last 5 Payments ---\n";
$q = $pdo->query("SELECT * FROM payments ORDER BY id DESC LIMIT 5");
while($row = $q->fetch()) {
    print_r($row);
}
?>
