<?php
require_once 'config/database.php';

echo "<h1>Fixing 0-Price Bookings</h1>";

try {
    $pdo->beginTransaction();

    // 1. Packages
    $stmt = $pdo->prepare("
        UPDATE bookings b
        JOIN packages p ON b.item_id = p.id
        SET b.total_price = p.price * COALESCE(ABS(CAST(JSON_EXTRACT(b.extra_details, '$.persons') AS UNSIGNED)), 1)
        WHERE b.booking_type = 'package' AND b.total_price = 0
    ");
    // Note: If persons is not in JSON, fallback to 1. 
    // Wait, package_details.html might not even store persons in extra_details.
    
    // Let's try a simpler approach for all types
    $bookings = $pdo->query("SELECT * FROM bookings WHERE total_price = 0")->fetchAll();
    $count = 0;

    foreach ($bookings as $b) {
        $id = $b['id'];
        $type = $b['booking_type'];
        $item_id = $b['item_id'];
        $new_price = 0;

        if ($type == 'package') {
            $p = $pdo->prepare("SELECT price FROM packages WHERE id = ?");
            $p->execute([$item_id]);
            $base = $p->fetchColumn();
            $new_price = (float)$base; // Default to 1 person if unknown
        } elseif ($type == 'bus') {
            $p = $pdo->prepare("SELECT price FROM buses WHERE id = ?");
            $p->execute([$item_id]);
            $base = $p->fetchColumn();
            $new_price = (float)$base;
        } elseif ($type == 'hotel') {
            $p = $pdo->prepare("SELECT price_per_night FROM hotels WHERE id = ?");
            $p->execute([$item_id]);
            $base = $p->fetchColumn();
            $s = new DateTime($b['start_date']);
            $e = new DateTime($b['end_date'] ?: $b['start_date']);
            $nights = $s->diff($e)->days ?: 1;
            $new_price = (float)$base * $nights;
        }

        if ($new_price > 0) {
            $pdo->prepare("UPDATE bookings SET total_price = ? WHERE id = ?")->execute([$new_price, $id]);
            $pdo->prepare("UPDATE payments SET amount = ? WHERE booking_id = ? AND amount = 0")->execute([$new_price, $id]);
            echo "Fixed Booking #$id ($type): set to ₹$new_price<br>";
            $count++;
        }
    }

    $pdo->commit();
    echo "<h2>Successfully fixed $count bookings!</h2>";
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
<a href="admin/index.html">Back to Dashboard</a>
