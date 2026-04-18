<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/er_diagram_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/er_diagram_export.html';

$svg = buildER();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written with refined attribute placements.\n";

function buildER() {
    $W = 1600; $H = 1200;

    // Entity centres (Shifted down by 100px)
    $E = [
        'User'           => [800, 200],
        'Bus Booking'    => [800, 500],
        'Rental Service' => [350, 500],
        'Package'        => [1250, 500],
        'Rental Vehicle' => [350, 900],
        'Payments'       => [800, 950],
        'AI Planner'     => [1250, 900],
    ];

    // Diamond centres (Final alignment for Rail routing)
    $D = [
        'has_top'    => [800, 350, 0],
        'link_L'     => [350, 700, 0],
        'link_R'     => [1250, 700, 0],
        'has_mid'    => [800, 725, 0],
        'has_lowL'   => [575, 950, 0], // Aligned to rail
        'has_lowR'   => [1025, 950, 0], // Aligned to rail
        'has_pkg'    => [1050, 850, 0], // Aligned to Package rail
    ];

    // Card/Entity connections (Wait for final check)
    $Lines = [
        // User to has_top
        [800, 225, 800, 312, '1'],
        [350, 350, 1250, 350, ''],
        [800, 388, 800, 475, 'N'],
        [350, 350, 350, 475, 'N'],
        [1250, 350, 1250, 475, 'N'],
        
        // Vertical Rails
        [350, 528, 350, 663, '1'],
        [350, 737, 350, 872, 'N'],
        [1250, 528, 1250, 663, '1'],
        [1250, 737, 1250, 872, 'N'],
        
        // Bus Booking Core (Vertical Spine - THE REFERENCE)
        [800, 528, 800, 687, 'N'],
        [800, 763, 800, 922, 'N'],
        
        // Rental Vehicle to Payments (Proper Orthogonal Rail)
        [350, 928, 350, 950, ''],
        [350, 950, 537, 950, '1'], // hits left tip (575-38)
        [613, 950, 725, 950, '1'], // hits right tip (575+38)
        
        // AI Planner to Payments (Proper Orthogonal Rail)
        [1250, 928, 1250, 950, ''],
        [1250, 950, 1063, 950, '1'], 
        [987, 950, 875, 950, '1'],
        
        // Package to Payments (REFINED DIRECT RELATIONSHIP)
        [1175, 528, 1052, 698, '1'], // Down to top-right tip of rotated diamond
        [998, 752, 850, 922, '1'],   // Down to Payments top edge exactly
    ];

    // Attributes (Further refined to avoid overlap)
    $A = [
        'User' => [
            ['user_name', -160, -90],
            ['email',      0, -110],
            ['user_id',   160, -90, true],
        ],
        'Bus Booking' => [
            ['booking_date', -180, -40],
            ['seats',        -180,  40],
            ['booking_id',    180, -40, true],
            ['route',         180,  20],
            ['booking_reference', 180, 80],
        ],
        'Rental Service' => [
            ['service_name', -180, -50],
            ['description',  -180,  40],
            ['rental_id',     130, -50, true],
            ['daily_rate',    130,  40],
        ],
        'Rental Vehicle' => [
            ['vehicle_id',   -180, -50, true],
            ['category',     -180,  40],
            ['model_name',    140, -50],
            ['daily_rate',     40,  120], // Moved to avoid connection overlap
        ],
        'Package' => [
            ['price',       -130, -60],
            ['hotel',       -130,  20],
            ['package_id',   180, -60, true],
            ['booking_date', 180,  20],
        ],
        'AI Planner' => [
            ['plan_id',      130, -60, true],
            ['destination', -130, -60],
            ['budget',       170,  20],
            ['duration',     170,  80],
            ['travel_style', -50, 110],
        ],
        'Payments' => [
            ['payment_method',     -200, 100],
            ['additional_services',-150, 150],
            ['payment_id',           0,   160, true],
            ['total_cost',          150,  150],
            ['payment_status',      200,  100],
        ],
    ];

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Text Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"24\" font-family=\"Times New Roman\" font-weight=\"bold\">3.2.2 E-R diagram</text>\n";

    // Draw Lines
    foreach ($Lines as $l) {
        $s .= "<line x1=\"{$l[0]}\" y1=\"{$l[1]}\" x2=\"{$l[2]}\" y2=\"{$l[3]}\" stroke=\"gray\" stroke-width=\"1.2\"/>\n";
        if (isset($l[4]) && $l[4] !== '') {
            $midX = ($l[0] + $l[2]) / 2;
            $midY = ($l[1] + $l[3]) / 2;
            $s .= "<text x=\"".($midX + 8)."\" y=\"".($midY - 8)."\" font-size=\"15\" font-family=\"Arial\" font-weight=\"bold\">{$l[4]}</text>\n";
        }
    }

    // Draw Diamonds
    foreach ($D as $k => $c) {
        $cx = $c[0]; $cy = $c[1];
        $ds = 38;
        $pts = "$cx,".($cy-$ds)." ".($cx+$ds).",$cy $cx,".($cy+$ds)." ".($cx-$ds).",$cy";
        $s .= "<polygon points=\"$pts\" fill=\"white\" stroke=\"black\" stroke-width=\"1\"/>\n";
        $label = explode('_', $k)[0];
        $s .= "<text x=\"$cx\" y=\"".($cy+5)."\" font-size=\"14\" font-family=\"Arial\" text-anchor=\"middle\">$label</text>\n";
    }

    // Draw Attributes
    foreach ($A as $en => $attrs) {
        $ex = $E[$en][0]; $ey = $E[$en][1];
        $ew = 150; $eh = 55;
        foreach ($attrs as $a) {
            $ax = $ex + $a[1]; $ay = $ey + $a[2];
            
            // Calculate edge start point
            $sx = $ex; $sy = $ey;
            if (abs($a[1]) > abs($a[2])) { // Primarily horizontal
                $sx = ($a[1] > 0) ? ($ex + $ew/2) : ($ex - $ew/2);
            } else { // Primarily vertical
                $sy = ($a[2] > 0) ? ($ey + $eh/2) : ($ey - $eh/2);
            }

            $s .= "<line x1=\"$sx\" y1=\"$sy\" x2=\"$ax\" y2=\"$ay\" stroke=\"#666\" stroke-width=\"1\"/>\n";
            $rw = max(strlen($a[0])*4.2 + 12, 45);
            $s .= "<ellipse cx=\"$ax\" cy=\"$ay\" rx=\"$rw\" ry=\"22\" fill=\"white\" stroke=\"#444\" stroke-width=\"1\"/>\n";
            
            $isPk = (isset($a[3]) && $a[3]);
            $s .= "<text x=\"$ax\" y=\"".($ay+5)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\" font-weight=\"".($isPk ? "bold" : "normal")."\">{$a[0]}</text>\n";
            if ($isPk) {
                $tw = strlen($a[0]) * 7;
                $s .= "<line x1=\"".($ax - $tw/2)."\" y1=\"".($ay+7)."\" x2=\"".($ax + $tw/2)."\" y2=\"".($ay+7)."\" stroke=\"black\" stroke-width=\"1.2\"/>\n";
            }
        }
    }

    // Draw Entities
    foreach ($E as $en => $c) {
        $ew = 150; $eh = 55;
        $rx = $c[0] - $ew/2; $ry = $c[1] - $eh/2;
        $s .= "<rect x=\"$rx\" y=\"$ry\" width=\"$ew\" height=\"$eh\" fill=\"white\" stroke=\"black\" stroke-width=\"1.5\"/>\n";
        $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+6)."\" font-size=\"17\" font-family=\"Arial\" text-anchor=\"middle\" font-weight=\"bold\">$en</text>\n";
    }

    // Footer Text
    $s .= "<text x=\"".($W/2)."\" y=\"1160\" font-size=\"22\" font-family=\"Times New Roman\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.2</text>\n";

    $s .= "</svg>\n";
    return $s;
}
?>
