<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/er_diagram_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/er_diagram_export.html';

$svg = buildER();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written with AI Planner added.\n";

function buildER() {
    $W = 1800; $H = 1100;

    // Entity centres
    $E = [
        'User'           => [800, 100],
        'Bus Booking'    => [800, 400],
        'Rental Service' => [250, 400],
        'Package'        => [1150, 400],
        'AI Planner'     => [1550, 400],
        'Rental Vehicle' => [250, 700],
        'Payments'       => [800, 750],
    ];

    // Diamond centres
    $D = [
        'has_top'    => [800, 250],
        'link'       => [250, 550],
        'has_mid'    => [800, 575],
        'has_left'   => [525, 700],
    ];

    // Card/Entity connections
    $Lines = [
        // User to has_top
        [800, 125, 800, 212, 'N'],
        // split line
        [250, 250, 1550, 250, ''],
        // has_top to Bus Booking
        [800, 288, 800, 375, 'N'],
        // to Rental Service
        [250, 250, 250, 375, 'N'],
        // to Package
        [1150, 250, 1150, 375, 'N'],
        // to AI Planner
        [1550, 250, 1550, 375, 'N'],
        
        // Rental Service to link
        [250, 425, 250, 512, '1'],
        // link to Rental Vehicle
        [250, 588, 250, 675, 'N'],
        // Bus Booking to has_mid
        [800, 425, 800, 537, 'N'],
        // has_mid to Payments
        [800, 613, 800, 725, 'N'],
        // Rental Vehicle to has_left
        [315, 700, 487, 700, '1'],
        // has_left to Payments
        [563, 700, 750, 735, '1'],
        // Package to Payments (L-shaped)
        [1150, 425, 1150, 750, '1'],
        [1150, 750, 865, 750, ''],
    ];

    // Attributes
    $A = [
        'User' => [
            ['user_name', -100, -70],
            ['email', -130, 20],
            ['user_id', 110, -30, true],
        ],
        'Bus Booking' => [
            ['booking_date', -110, -60],
            ['seats', -110, 40],
            ['booking_id', 110, -60, true],
            ['route', 120, 0],
            ['booking_reference', 110, 70],
        ],
        'Rental Service' => [
            ['service_name', -120, -50],
            ['description', -120, 50],
            ['rental_id', 110, -50, true],
            ['daily_rate', 110, 50],
        ],
        'Rental Vehicle' => [
            ['vehicle_id', -110, -40, true],
            ['category', -110, 50],
            ['model_name', 110, -30],
            ['daily_rate', 110, 50],
        ],
        'Package' => [
            ['price', -100, -50],
            ['hotel', -110, 30],
            ['package_id', 110, -50, true],
            ['booking_date', 115, 30],
        ],
        'AI Planner' => [
            ['plan_id', -100, -50, true],
            ['destination', -110, 30],
            ['budget', 110, -50],
            ['duration', 120, 0],
            ['travel_style', 110, 70],
        ],
        'Payments' => [
            ['payment_method', -110, -70],
            ['additional_services', -110, 50],
            ['payment_id', 0, 90, true],
            ['total_cost', 110, -60],
            ['payment_status', 120, 40],
        ],
    ];

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Text Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"24\" font-family=\"Times New Roman\" font-weight=\"bold\">3.2.2 E-R diagram</text>\n";

    // Draw Lines
    foreach ($Lines as $l) {
        $s .= "<line x1=\"{$l[0]}\" y1=\"{$l[1]}\" x2=\"{$l[2]}\" y2=\"{$l[3]}\" stroke=\"gray\" stroke-width=\"1\"/>\n";
        if (isset($l[4]) && $l[4] !== '') {
            $midX = ($l[0] + $l[2]) / 2;
            $midY = ($l[1] + $l[3]) / 2;
            $s .= "<text x=\"".($midX + 5)."\" y=\"".($midY - 5)."\" font-size=\"14\" font-family=\"Arial\">{$l[4]}</text>\n";
        }
    }

    // Draw Diamonds
    foreach ($D as $k => $c) {
        $cx = $c[0]; $cy = $c[1];
        $ds = 38;
        $pts = "$cx,".($cy-$ds)." ".($cx+$ds).",$cy $cx,".($cy+$ds)." ".($cx-$ds).",$cy";
        $s .= "<polygon points=\"$pts\" fill=\"white\" stroke=\"gray\" stroke-width=\"1\"/>\n";
        $label = explode('_', $k)[0];
        $s .= "<text x=\"$cx\" y=\"".($cy+5)."\" font-size=\"14\" font-family=\"Arial\" text-anchor=\"middle\">$label</text>\n";
    }

    // Draw Attributes
    foreach ($A as $en => $attrs) {
        $ex = $E[$en][0]; $ey = $E[$en][1];
        foreach ($attrs as $a) {
            $ax = $ex + $a[1]; $ay = $ey + $a[2];
            $s .= "<line x1=\"$ex\" y1=\"$ey\" x2=\"$ax\" y2=\"$ay\" stroke=\"gray\" stroke-width=\"0.8\"/>\n";
            $rw = max(strlen($a[0])*4.5 + 10, 40);
            $s .= "<ellipse cx=\"$ax\" cy=\"$ay\" rx=\"$rw\" ry=\"22\" fill=\"white\" stroke=\"gray\" stroke-width=\"1\"/>\n";
            $deco = (isset($a[3]) && $a[3]) ? ' style=\"text-decoration: underline;\"' : '';
            $s .= "<text x=\"$ax\" y=\"".($ay+5)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\"$deco>{$a[0]}</text>\n";
        }
    }

    // Draw Entities
    foreach ($E as $en => $c) {
        $ew = 130; $eh = 50;
        $rx = $c[0] - $ew/2; $ry = $c[1] - $eh/2;
        $s .= "<rect x=\"$rx\" y=\"$ry\" width=\"$ew\" height=\"$eh\" fill=\"white\" stroke=\"gray\" stroke-width=\"1.5\"/>\n";
        $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+6)."\" font-size=\"16\" font-family=\"Arial\" text-anchor=\"middle\">$en</text>\n";
    }

    // Footer Text
    $s .= "<text x=\"".($W/2)."\" y=\"950\" font-size=\"22\" font-family=\"Times New Roman\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.2</text>\n";

    $s .= "</svg>\n";
    return $s;
}
