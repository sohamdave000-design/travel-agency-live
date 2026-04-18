<?php
require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$destination = sanitize($input['destination'] ?? 'Goa');
$duration = intval($input['duration'] ?? 3);
$vibe = sanitize($input['vibe'] ?? 'Balanced');
$budget = sanitize($input['budget'] ?? 'Economy');

// --- AI SIMULATION ENGINE ---
// In a real scenario, this would call Gemini or OpenAI API
$itinerary = [];

// Base activities by vibe (Expanded for multi-day variety)
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

// Combine all activities for a master fallback pool
$all_activities = [];
foreach($vibe_activities as $v => $list) {
    $all_activities = array_merge($all_activities, $list);
}
$all_activities = array_unique($all_activities);
shuffle($all_activities);

// Select the primary pool based on vibe
$pool = $vibe_activities[$vibe] ?? $vibe_activities['Balanced'];
shuffle($pool);

for ($day = 1; $day <= $duration; $day++) {
    $daily_activities = [];
    $times = ['Morning', 'Afternoon', 'Evening'];
    
    foreach ($times as $time) {
        // Try to get activity from Vibe pool first, then fallback to global pool
        if (!empty($pool)) {
            $act_name = array_shift($pool);
        } else {
            // Pick from all activities, but make sure it wasn't used in this trip yet
            // (In a simulation, we just pick from the shuffled global pool)
            $act_name = array_shift($all_activities);
        }

        // Avoid description repetition by using templates
        $templates = [
            'Experience the unique ' . strtolower($act_name) . ' in the heart of ' . $destination . '.',
            'A perfect time for ' . strtolower($act_name) . ' to discover the local charm.',
            'Enjoy a curated ' . strtolower($act_name) . ' session tailored for your trip.',
            'Immerse yourself in ' . strtolower($act_name) . ' and soak in the atmosphere.'
        ];
        $desc = $templates[array_rand($templates)];

        $daily_activities[] = [
            'time' => $time,
            'activity' => $act_name . ($time == 'Morning' ? ' at ' . $destination : ''),
            'description' => $desc
        ];
    }

    $itinerary[] = [
        'day' => $day,
        'activities' => $daily_activities
    ];
}

$response_data = [
    'success' => true,
    'destination' => $destination,
    'duration' => $duration,
    'vibe' => $vibe,
    'budget' => $budget,
    'itinerary' => $itinerary,
    'summary' => "A customized $duration-day $vibe trip to $destination tailored for a $budget budget."
];

// Save to DB if logged in
if (isLoggedIn()) {
    try {
        $stmt = $pdo->prepare("INSERT INTO ai_plans (user_id, destination, duration, vibe, budget, itinerary_data) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $destination,
            $duration,
            $vibe,
            $budget,
            json_encode($itinerary)
        ]);
        $response_data['plan_id'] = $pdo->lastInsertId();
    } catch (PDOException $e) {
        $response_data['db_error'] = $e->getMessage();
    }
}

echo json_encode($response_data);
?>
