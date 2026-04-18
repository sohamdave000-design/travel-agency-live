<?php
$files = [
    'c:/XAMPP/htdocs/travel_agency/schema.sql',
    'c:/XAMPP/htdocs/travel_agency/seed_indian_data.sql',
    'c:/XAMPP/htdocs/travel_agency/new_packages.sql'
];
$output = '';
foreach ($files as $f) {
    if (file_exists($f)) {
        $output .= "-- File: " . basename($f) . "\n" . file_get_contents($f) . "\n\n";
    }
}
file_put_contents('c:/XAMPP/htdocs/travel_agency/railway_db.sql', $output);
echo "Consolidated " . count($files) . " files into railway_db.sql\n";
?>
