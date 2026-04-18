<?php
require_once 'config/database.php';
$stmt = $pdo->query("SELECT name, image FROM hotels WHERE name LIKE '%Taj Mahal%'");
while($row = $stmt->fetch()) {
    echo "Hotel: " . $row['name'] . "\n";
    echo "Image URL: " . $row['image'] . "\n\n";
}
?>
