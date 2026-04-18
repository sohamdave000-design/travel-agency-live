<?php
session_start(); // Start session globally across all files including this

$host = 'localhost';
$dbname = 'travel_agency';
$username = 'root'; // default XAMPP username
$password = ''; // default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch associations by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if admin is logged in
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper function for sanitizing input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Helper function to handle image paths (especially for admin panel)
function get_image_url($path) {
    if (empty($path)) return '';
    // If it's an absolute URL, return as is
    if (preg_match('/^https?:\/\//', $path)) return $path;
    // If it's root-relative or already adjusted, return as is
    if (strpos($path, '/') === 0 || strpos($path, '../') === 0) return $path;
    
    // Check if we are in admin directory (cross-platform check)
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $isAdmin = (strpos($scriptName, '/admin/') !== false);
    if ($isAdmin) {
        return '../' . $path;
    }
    return $path;
}
?>
