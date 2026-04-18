<?php
require_once 'config/database.php';
try {
    $q = $pdo->query("DESCRIBE rentals");
    $columns = $q->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
