<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $stmt = $pdo->prepare("INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $pkg_name = 'Gir National Park & Wildlife Safari';
    $destination = 'Gujarat';
    $price = 18000.00;
    $duration = 3;
    $description = 'Experience the thrill of the jungle! Gir National Park is the only place in the world where you can see Asiatic lions in their natural habitat. This package includes a guided jeep safari, nature trails, and a visit to the Kamleshwar Dam.';
    $image = 'assets/images/gir_lion.png';
    $latitude = 21.1243;
    $longitude = 70.8242;

    $stmt->execute([$pkg_name, $destination, $price, $duration, $description, $image, $latitude, $longitude]);
    
    echo "Successfully added: " . $pkg_name . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
