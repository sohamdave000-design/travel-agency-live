<?php
require_once dirname(__DIR__) . '/config/database.php';

$updates = [
    146 => [15.2993, 74.1240], // Goa
    147 => [32.2432, 77.1892], // Manali
    148 => [34.0837, 74.7973], // Kashmir
    149 => [26.9124, 75.7873], // Rajasthan (Jaipur)
    150 => [9.4981, 76.3329],  // Kerala (Alleppey)
    151 => [34.1526, 77.5771], // Ladakh
    152 => [11.6231, 92.7412], // Andaman (Port Blair)
];

try {
    $stmt = $pdo->prepare("UPDATE packages SET latitude = ?, longitude = ? WHERE id = ?");
    foreach ($updates as $id => $coords) {
        $stmt->execute([$coords[0], $coords[1], $id]);
        echo "Updated Package ID $id with coordinates: $coords[0], $coords[1]\n";
    }
    echo "All maps added successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
