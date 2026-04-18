<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    $stmt = $pdo->prepare("INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Meghalaya
    $stmt->execute([
        'Meghalaya - The Cloud Sanctuary', 'Meghalaya', 18000.00, 5,
        'Explore the wettest place on Earth! Visit the stunning Double Decker Living Root Bridges, the crystal-clear waters of Umngot River in Dawki, and the majestic Nohkalikai Falls. A journey into the heart of the Khasi hills.',
        'assets/images/meghalaya.png', 25.4670, 91.3662
    ]);
    echo "Added Meghalaya package.\n";

    // Varanasi
    $stmt->execute([
        'Varanasi - The Eternal City', 'Uttar Pradesh', 12000.00, 3,
        'Experience the oldest living city in the world. Witness the magical Ganga Aarti at Dashashwamedh Ghat, explore ancient temples, and take a sunrise boat ride on the holy Ganges. A soul-stirring journey into Indian spirituality.',
        'assets/images/varanasi.png', 25.3176, 82.9739
    ]);
    echo "Added Varanasi package.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
