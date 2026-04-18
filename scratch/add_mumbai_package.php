<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $stmt = $pdo->prepare("INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $pkg_name = 'Mumbai Heritage & Elephanta Caves Tour';
    $destination = 'Maharashtra';
    $price = 12000.00;
    $duration = 2;
    $description = 'Explore the historic heart of Mumbai and the mystical Elephanta Caves. Visit the Gateway of India, Chhatrapati Shivaji Maharaj Terminus (UNESCO site), Mani Bhavan, and take a ferry to the Gharapuri island to witness the ancient rock-cut temples dedicated to Lord Shiva.';
    $image = 'assets/images/mumbai_heritage.png';
    $latitude = 18.9220;
    $longitude = 72.8347;

    $stmt->execute([$pkg_name, $destination, $price, $duration, $description, $image, $latitude, $longitude]);
    
    echo "Successfully added: " . $pkg_name . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
