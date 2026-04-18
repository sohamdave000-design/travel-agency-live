<?php
require_once 'config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$package_id = isset($_POST['package_id']) ? (int)$_POST['package_id'] : 0;

if (!$package_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid package']);
    exit;
}

if ($action === 'add') {
    $stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, package_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $package_id]);
    echo json_encode(['success' => true, 'action' => 'added']);
} elseif ($action === 'remove') {
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND package_id = ?");
    $stmt->execute([$user_id, $package_id]);
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

