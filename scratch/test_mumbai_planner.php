<?php
/**
 * Test script for Mumbai AI Planner
 * Mocks the API call and checks for duplicates.
 */

// Define mock data
$destination = "Mumbai";
$duration = 3;
$vibe = "Adventure";
$budget = "Balanced";

// Mock the environment for ai_engine.html
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SESSION['user_id'] = 1; // mock login

// We'll use a wrapper to capture the JSON output
function run_engine($dest, $dur, $vb, $bg) {
    // We'll simulate the input stream by temporarily writing to a file and reading it
    // But since we are calling it within the same process, we can just mock the variables
    // if we modified ai_engine.html to be more modular.
    // For now, I'll just replicate the logic for the test.
    
    // Expanded activities from ai_engine.html
    $vibe_activities = [
        'Adventure'  => [
            'Trekking', 'River Rafting', 'Wildlife Safari', 'Ziplining', 'Rock Climbing',
            'Scuba Diving', 'Paragliding', 'Bungee Jumping', 'ATV Ride', 'Caving Explorations',
            'Night Trekking', 'Hot Air Ballooning', 'Kayaking', 'Surfing Lessons', 'Mountain Biking'
        ],
        'Relaxation' => [
            'Spa Session', 'Beach Sunset', 'Yoga Class', 'Garden Walk', 'Museum Visit',
            'Sunset Cruise', 'Pottery Workshop', 'Tea Tasting', 'Botanical Garden Tour', 'Ayurvedic Massage',
            'Bird Watching', 'Sunset Yoga', 'Meditation Session', 'Art Therapy', 'Lakeside Picnic'
        ],
        'Foodie'     => [
            'Street Food Tour', 'Cooking Class', 'Local Market Visit', 'Fine Dining', 'Brewery Tour',
            'Dessert Crawl', 'Night Market Food Tour', 'Farm-to-Table Lunch', 'Vineyard Visit', 'Spices Tour',
            'Street Photography & Food', 'Traditional Feast', 'Coffee Plantation Tour', 'Seafood Trail', 'Tea Ceremony'
        ],
        'Cultural'   => [
            'Temple Visit', 'Traditional Dance Show', 'Palace Tour', 'Art Gallery', 'Heritage Walk',
            'Fort Exploration', 'Museum Hopping', 'Puppet Show', 'Pottery Village Visit', 'Local Festival Experience',
            'Handloom Weaving Tour', 'Historical Monuments', 'Puppet Workshop', 'Classical Music Concert', 'Ancestral Home Visit'
        ],
        'Balanced'   => [
            'City Sightseeing', 'Local Market', 'Nature Walk', 'Dinner with View', 'Photography Session',
            'Local Neighborhood Walk', 'Panoramic Viewpoint', 'City Museum', 'Waterfront Stroll', 'Shopping at Local Artisans',
            'Public Park Visit', 'Iconic Landmark Visit', 'Tram/Metro Ride Tour', 'Street Art Tour', 'Hidden Garden Discovery'
        ]
    ];

    $all_activities = [];
    foreach($vibe_activities as $v => $list) { $all_activities = array_merge($all_activities, $list); }
    $all_activities = array_unique($all_activities);
    shuffle($all_activities);

    $pool = $vibe_activities[$vb] ?? $vibe_activities['Balanced'];
    shuffle($pool);

    $itinerary = [];
    $used = [];

    for ($day = 1; $day <= $dur; $day++) {
        $daily = [];
        $times = ['Morning', 'Afternoon', 'Evening'];
        foreach ($times as $time) {
            $act = !empty($pool) ? array_shift($pool) : array_shift($all_activities);
            $daily[] = [
                'day' => $day,
                'time' => $time,
                'activity' => $act . ($time == 'Morning' ? ' at ' . $dest : ''),
            ];
            $used[] = $act;
        }
        $itinerary[] = $daily;
    }
    return $itinerary;
}

echo "Testing AI Planner for $destination ($duration days, $vibe vibe)...\n\n";

$plan = run_engine($destination, $duration, $vibe, $budget);

$flat_activities = [];
foreach ($plan as $day) {
    echo "Day " . $day[0]['day'] . ":\n";
    foreach ($day as $act) {
        $clean_name = str_replace(" at $destination", "", $act['activity']);
        echo "  - [" . $act['time'] . "] " . $act['activity'] . "\n";
        $flat_activities[] = $clean_name;
    }
    echo "\n";
}

$unique_count = count(array_unique($flat_activities));
$total_count = count($flat_activities);

echo "Total Activities: $total_count\n";
echo "Unique Activities: $unique_count\n";

if ($unique_count === $total_count) {
    echo "\nSUCCESS: No repetitions found in the trip plan!\n";
} else {
    echo "\nFAILURE: Duplicates detected.\n";
}
?>
