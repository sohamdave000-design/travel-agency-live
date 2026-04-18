<?php
require_once 'config/database.php';

try {
    // 1. Update existing prices (multiply by 80 to approximate INR)
    $pdo->exec("UPDATE packages SET price = price * 80;");
    $pdo->exec("UPDATE hotels SET price_per_night = price_per_night * 80;");
    $pdo->exec("UPDATE buses SET price = price * 80;");
    echo "Existing prices updated to INR.<br>";

    // 2. Add New Packages
    $stmt = $pdo->prepare("INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Varanasi
    $stmt->execute([
        'Varanasi - The Spiritual Soul', 'Uttar Pradesh', 12000.00, 3,
        'Experience the ancient rituals on the Holy Ganges. Attend the Ganga Aarti at Dashashwamedh Ghat, explore ancient temples, and walk the historic narrow alleys of the spiritual capital of India.',
        'https://images.unsplash.com/photo-1561361513-2d000a50f0dc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        25.3176, 82.9739
    ]);

    // Munnar
    $stmt->execute([
        'Munnar Tea Garden Retreat', 'Kerala', 15000.00, 4,
        'Escape to the lush green hills of Kerala. Visit tea plantations, Eravikulam National Park to see the Nilgiri Tahr, and enjoy the misty mountains of the Western Ghats.',
        'https://images.unsplash.com/photo-1593693397690-ca6b3301d239?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        10.0889, 77.0595
    ]);

    // Hampi
    $stmt->execute([
        'Hampi Heritage Tour', 'Karnataka', 14000.00, 3,
        'Explore the UNESCO World Heritage site and ruins of the Vijayanagara Empire. Marvel at the Virupaksha Temple, stone chariot, and the unique boulder-strewn landscapes of Hampi.',
        'https://images.unsplash.com/photo-1590050752117-23a9d7fc2107?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        15.3350, 76.4600
    ]);

    // Amritsar
    $stmt->execute([
        'Amritsar & Golden Temple', 'Punjab', 11000.00, 2,
        'Visit the holiest shrine of Sikhism - the Golden Temple. Experience the patriotic vibe at the Wagah Border ceremony and pay respects at Jallianwala Bagh.',
        'https://images.unsplash.com/photo-1548013146-72479768bada?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        31.6200, 74.8723
    ]);

    // Spiti Valley
    $stmt->execute([
        'Spiti Valley Adventure', 'Himachal Pradesh', 35000.00, 8,
        'A rugged road trip to the "Middle Land". Visit the Key Monastery, Tabo, Kaza, and the highest post office in the world in the stunning high-altitude cold desert.',
        'https://images.unsplash.com/photo-1605649440419-586bc61048fc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        32.2461, 78.0349
    ]);

    // Coorg
    $stmt->execute([
        'Coorg - Scotland of India', 'Karnataka', 13000.00, 3,
        'Relax in coffee plantations. Visit Abbey Falls, Raja\'s Seat, and the Talakaveri. Enjoy the pleasant climate and spicy Kodava cuisine.',
        'https://images.unsplash.com/photo-1585116968120-3cd4cf5fb8c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        12.4244, 75.7382
    ]);

    echo "New packages added successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
