<?php
// Mock input for a 3-day trip
$_SERVER['REQUEST_METHOD'] = 'POST';
$input = [
    'destination' => 'TestCity',
    'duration' => 3,
    'vibe' => 'Adventure',
    'budget' => 'Balanced'
];

// Helper to capture output
ob_start();
require_once 'c:\XAMPP\htdocs\travel_agency\api\ai_engine.php';
$output = ob_get_clean();

$result = json_code($output, true); // Wait, I can't easily capture only the JSON since the file echoes it.
// I'll just write a script that replicates the logic or includes it and parses.

// Alternative: Just check if the file works via CLI if I can mock the input stream.
?>
