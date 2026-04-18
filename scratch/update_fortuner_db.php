<?php
require_once 'config/database.php';
try {
    $stmt = $pdo->prepare("UPDATE rentals SET image = 'assets/img/rentals/toyota_fortuner.png' WHERE name LIKE '%Fortuner%'");
    $stmt->execute();
    echo "Database updated successfully. Rows affected: " . $stmt->rowCount();
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
