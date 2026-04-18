<?php
require_once 'config/database.php';

echo "Checking schema...<br>";

// Check payments table
$columns = $pdo->query("DESCRIBE payments")->fetchAll(PDO::FETCH_COLUMN);
$needed_pay = ['transaction_id', 'card_last4', 'payer_email'];
$missing_pay = array_diff($needed_pay, $columns);

if (!empty($missing_pay)) {
    echo "Adding missing columns to payments table...<br>";
    if (file_exists('enhance_payments.sql')) {
        $sql = file_get_contents('enhance_payments.sql');
        try {
            $pdo->exec($sql);
            echo "Payments migration completed.<br>";
        } catch (PDOException $e) {
            echo "Payments migration error: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "Payments table already updated.<br>";
}

// Check admin_responses
$contact_cols = $pdo->query("DESCRIBE contact_messages")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('response', $contact_cols)) {
    echo "Adding response columns to contact_messages...<br>";
    if (file_exists('admin_responses.sql')) {
        $sql = file_get_contents('admin_responses.sql');
        try {
            $pdo->exec($sql);
            echo "Admin Responses migration completed.<br>";
        } catch (PDOException $e) {
            echo "Admin Responses error: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "Contact messages/Reviews already updated.<br>";
}

echo "Done.";
?>

