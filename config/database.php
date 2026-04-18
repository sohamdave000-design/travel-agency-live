<?php
session_start(); // Start session globally across all files including this

// Robust environment variable lookup
function get_env_var($name, $default = '') {
    return getenv($name) ? getenv($name) : ($_ENV[$name] ?? ($_SERVER[$name] ?? $default));
}

// Railway provides these automatically
$host     = get_env_var('MYSQLHOST',     'localhost');
$dbname   = get_env_var('MYSQLDATABASE', 'travel_agency');
$username = get_env_var('MYSQLUSER',     'root');
$password = get_env_var('MYSQLPASSWORD', '');
$port     = get_env_var('MYSQLPORT',     '3306');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    if (get_env_var('MYSQLHOST')) {
        die("Cloud Database Connection Failed: " . $e->getMessage());
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper functions (kept exactly as before)
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin() { return isset($_SESSION['admin_id']); }
function redirect($url) { header("Location: $url"); exit(); }
function sanitize($data) { return htmlspecialchars(strip_tags(trim($data))); }

function get_image_url($path) {
    if (empty($path)) return '';
    if (preg_match('/^https?:\/\//', $path)) return $path;
    if (strpos($path, '/') === 0 || strpos($path, '../') === 0) return $path;
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (strpos($scriptName, '/admin/') !== false) return '../' . $path;
    return $path;
}
?>
