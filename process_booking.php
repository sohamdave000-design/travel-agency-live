<?php
require_once 'config/database.php';

if (!isLoggedIn()) {
    redirect('login.html');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_type'])) {
    $booking_type = sanitize($_POST['booking_type']);
    $item_id = (int)$_POST['item_id'];
    $price = (float)$_POST['price'];
    $start_date = sanitize($_POST['start_date']);
    $end_date = !empty($_POST['end_date']) ? sanitize($_POST['end_date']) : null;
    $persons = isset($_POST['persons']) ? (int)$_POST['persons'] : 1;
    
    // Calculate total
    $total_price = $price * $persons;
    if (in_array($booking_type, ['hotel', 'rental']) && $end_date) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $diff = $start->diff($end)->days;
        if ($diff == 0) $diff = 1;
        $total_price = $price * $diff;
    }

    try {
        $pdo->beginTransaction();

        // Insert Booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_type, item_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
        $stmt->execute([$_SESSION['user_id'], $booking_type, $item_id, $start_date, $end_date, $total_price]);
        $booking_id = $pdo->lastInsertId();
        
        // Update Available Seats if bus
        if ($booking_type == 'bus') {
            $updateBus = $pdo->prepare("UPDATE buses SET available_seats = available_seats - ? WHERE id = ? AND available_seats >= ?");
            $updateBus->execute([$persons, $item_id, $persons]);
        }

        $pdo->commit();
        redirect("dashboard.html?booking_success=1&booking_id=$booking_id");
    } catch (Exception $e) {
        $pdo->rollBack();
        redirect("dashboard.html?error=Booking failed");
    }
} else {
    redirect("index.html");
}

