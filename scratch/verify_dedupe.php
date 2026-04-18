<?php
// Function to sanitize (copied from config/database.php mock)
function sanitize($data) { return $data; }
function isLoggedIn() { return false; }

// Destination and duration
$destination = "Goa";
$duration = 3;
$vibe = "Adventure";

// Define the expanded list (same as in ai_engine.html)
$vibe_activities = [
    'Adventure'  => [
        'Trekking', 'River Rafting', 'Wildlife Safari', 'Ziplining', 'Rock Climbing',
        'Scuba Diving', 'Paragliding', 'Bungee Jumping', 'ATV Ride', 'Caving Explorations',
        'Night Trekking', 'Hot Air Ballooning', 'Kayaking', 'Surfing Lessons', 'Mountain Biking'
    ]
];

$all_activities = $vibe_activities['Adventure']; // simplified for test
shuffle($all_activities);

$pool = $vibe_activities['Adventure'];
shuffle($pool);

$used = [];
$duplicates = 0;

for ($day = 1; $day <= $duration; $day++) {
    echo "Day $day:\n";
    $times = ['Morning', 'Afternoon', 'Evening'];
    foreach ($times as $time) {
        $act_name = array_shift($pool);
        if (in_array($act_name, $used)) {
            $duplicates++;
            echo "  REPEAT: $act_name\n";
        } else {
            $used[] = $act_name;
            echo "  NEW: $act_name\n";
        }
    }
}

echo "\nTotal duplicates: $duplicates\n";
if ($duplicates === 0) echo "SUCCESS: No repetitions in 3-day trip.\n";
else echo "FAILURE: Repeated activities found.\n";
?>
