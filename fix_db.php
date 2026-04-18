<?php
require_once 'config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id INT NOT NULL,
        transaction_id VARCHAR(50) NOT NULL,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50),
        card_last4 VARCHAR(4),
        payer_email VARCHAR(100),
        payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'
    )";
    $pdo->exec($sql);
    echo "Payments table created/verified successfully!";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
