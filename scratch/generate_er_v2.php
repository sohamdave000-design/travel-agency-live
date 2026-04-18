<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/er_diagram_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/er_diagram_export.html';

$svg = buildER();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for Fig 3.2.2 layout.\n";

function buildER() {
    $W = 1600; $H = 1000;

    // Entity centres
    $E = [
        'User'           => [800, 100],
        'Bus Booking'    => [800, 400],
        'Rental Service' => [350, 400],
        'Package'        => [1250, 400],
        'Rental Vehicle' => [350, 700],
        'Payments'       => [800, 750],
    ];

    // Diamond centres
    $D = [
        'has_top'    => [800, 250],
        'link'       => [350, 550],
        'has_mid'    => [800, 575],
        'has_left'   => [575, 700],
    ];

    // Card/Entity connections [e1, d1, label1, d2, e2, label2]
    // Actually just connections: from_cx, from_cy, to_cx, to_cy, label
    $Lines = [
        // User to has_top
        [800, 125, 800, 212, 'N'],
        // has_top to Bus Booking
        [800, 288, 800, 375, 'N'],
        // has_top to Rental Service (L-shaped, from left corner)
        [762, 250, 350, 250, ''],
        [350, 250, 350, 375, 'N'],
        // has_top to Package (L-shaped, from right corner)
        [838, 250, 1250, 250, ''],
        [1250, 250, 1250, 375, ''],
        // Rental Service to link
        [350, 425, 350, 512, '1'],
        // link to Rental Vehicle
        [350, 588, 350, 675, 'N'],
        // Bus Booking to has_mid
        [800, 425, 800, 537, 'N'],
        // has_mid to Payments
        [800, 613, 800, 725, 'N'],
        // Rental Vehicle to has_left
        [415, 700, 537, 700, '1'],
        // has_left to Payments
        [613, 700, 750, 735, '1'],
        // Package to Payments (L-shaped)
        [1250, 425, 1250, 750, '1'],
        [1250, 750, 865, 750, ''],
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
            $deco = (isset($a[3]) && $a[3]) ? ' text-decoration=\"underline\"' : '';
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
