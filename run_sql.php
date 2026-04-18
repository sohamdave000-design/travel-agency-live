<?php
require_once 'config/database.php';

$sql = file_get_contents('new_packages.sql');

try {
    // Remove USE travel_agency; if present since database is already selected in config/database.php
    $sql = str_replace('USE travel_agency;', '', $sql);
    
    // Run new packages SQL
    try {
        $pdo->exec($sql);
        echo "Packages SQL executed successfully!<br>";
    } catch (PDOException $e) {
        echo "Packages SQL skipped/failed: " . $e->getMessage() . "<br>";
    }
    
    // Run admin responses SQL
    if (file_exists('admin_responses.sql')) {
        try {
            $resp_sql = file_get_contents('admin_responses.sql');
            $pdo->exec($resp_sql);
            echo "Admin Responses SQL executed successfully!<br>";
        } catch (PDOException $e) {
            echo "Admin Responses SQL skipped: " . $e->getMessage() . "<br>";
        }
    }
    
    // Run enhanced payments SQL
    if (file_exists('enhance_payments.sql')) {
        try {
            $pay_sql = file_get_contents('enhance_payments.sql');
            $pdo->exec($pay_sql);
            echo "Payments Enhancement SQL executed successfully!<br>";
        } catch (PDOException $e) {
            echo "Payments Enhancement SQL skipped: " . $e->getMessage() . "<br>";
        }
    }
} catch (PDOException $e) {
    echo "Fatal connection error: " . $e->getMessage();
}
?>

