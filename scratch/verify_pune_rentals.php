<?php
require_once dirname(__DIR__) . '/config/database.php';
$stmt = $pdo->prepare("SELECT * FROM rentals WHERE city = 'Pune'");
$stmt->execute();
$pune_vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Pune Vehicles found: " . count($pune_vehicles) . "\n";
foreach ($pune_vehicles as $v) {
    echo "ID: " . $v['id'] . " | Name: " . $v['name'] . " | Image: " . $v['image'] . "\n";
    if (file_exists(dirname(__DIR__) . '/' . $v['image'])) {
        echo "Check: File EXISTS\n";
    } else {
        echo "Check: File MISSING at " . dirname(__DIR__) . '/' . $v['image'] . "\n";
    }
}
?>
