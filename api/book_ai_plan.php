<?php
require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to book a trip']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$plan_id = intval($input['plan_id'] ?? 0);
$persons = intval($input['persons'] ?? 1);
$start_date = sanitize($input['start_date'] ?? '');

if (!$plan_id || !$start_date) {
    echo json_encode(['success' => false, 'message' => 'Missing plan ID or date']);
    exit();
}

try {
    // 1. Fetch Plan Details
    $stmt = $pdo->prepare("SELECT * FROM ai_plans WHERE id = ? AND user_id = ?");
    $stmt->execute([$plan_id, $_SESSION['user_id']]);
    $plan = $stmt->fetch();

    if (!$plan) {
        echo json_encode(['success' => false, 'message' => 'Plan not found']);
        exit();
    }

    // 2. Pricing Logic
    $rates = [
        'Economy' => 2500,
        'Balanced' => 5000,
        'Luxury' => 12000
    ];
    $base_rate = $rates[$plan['budget']] ?? 5000;
    $duration = intval($plan['duration']);
    $total_price = $base_rate * $duration * $persons;

    // 3. Insert Booking
    $end_date = date('Y-m-d', strtotime($start_date . " + " . ($duration - 1) . " days"));
    
    $bookingStmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_type, item_id, start_date, end_date, total_price, status) VALUES (?, 'custom', ?, ?, ?, ?, 'pending')");
    $bookingStmt->execute([
        $_SESSION['user_id'],
        $plan_id,
        $start_date,
        $end_date,
        $total_price
    ]);
    
    $booking_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true, 
        'booking_id' => $booking_id,
        'total_price' => $total_price,
        'plan_id' => $plan_id,
        'message' => 'Trip initialized!'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
