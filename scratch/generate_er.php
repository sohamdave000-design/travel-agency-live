<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/er_diagram_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/er_diagram_export.html';

$svg = buildER();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written.\n";function buildER() {
    $W = 1600; $H = 1150;

    // ── Entity centres  [cx, cy, label] ──────────────────────────────────────
    $E = [
        'user'     => [700,  175, 'User'],
        'wishlist' => [ 95,  200, 'Wishlist'],
        'reviews'  => [1280, 155, 'Reviews'],
        'packages' => [1165, 330, 'Packages'],
        'rentals'  => [148,  468, 'Rentals'],
        'bookings' => [660,  465, 'Bookings'],
        'hotels'   => [1215, 468, 'Hotels'],
        'buses'    => [178,  790, 'Buses'],
        'payments' => [600,  790, 'Payments'],
        'ai_plans' => [1140, 790, 'AI Plans'],
    ];

    // ── Relationship diamonds  [cx, cy, label] ───────────────────────────────
    $D = [
        'maintains'   => [397,  200, 'maintains'],
        'writes'      => [990,  163, 'writes'],
        'places'      => [660,  318, 'places'],
        'listed_in'   => [468,  318, 'listed in'],
        'books_pack'  => [912,  392, 'books'],
        'books_rent'  => [404,  468, 'books'],
        'books_hotel' => [938,  468, 'books'],
        'books_bus'   => [418,  630, 'books'],
        'has'         => [600,  630, 'has'],
        'creates'     => [900,  630, 'creates'],
    ];

    // ── Connections  [entity_key, diamond_key, cardinality_at_entity] ─────────
    $C = [
        ['user',     'places',      '1'],
        ['bookings', 'places',      'N'],
        ['user',     'maintains',   '1'],
        ['wishlist', 'maintains',   'N'],
        ['user',     'writes',      '1'],
        ['reviews',  'writes',      'N'],
        ['wishlist', 'listed_in',   'N'],
        ['packages', 'listed_in',   '1'],
        ['bookings', 'books_pack',  'N'],
        ['packages', 'books_pack',  '1'],
        ['bookings', 'books_rent',  'N'],
        ['rentals',  'books_rent',  '1'],
        ['bookings', 'books_hotel', 'N'],
        ['hotels',   'books_hotel', '1'],
        ['bookings', 'books_bus',   'N'],
        ['buses',    'books_bus',   '1'],
        ['bookings', 'has',         '1'],
        ['payments', 'has',         '1'],
        ['bookings', 'creates',     '1'],
        ['ai_plans', 'creates',     'N'],
    ];

    // ── Attributes  [entity => [[label, is_pk, dx, dy],…]] ──────────────────
    $A = [
        'user' => [
            ['user_id',   true,  -60, -78],
            ['user_name', false,  20, -88],
            ['email',     false, 115, -72],
            ['phone',     false, 200, -35],
            ['password',  false, 188,  28],
        ],
        'wishlist' => [
            ['wishlist_id', true,  -55, -68],
            ['created_at',  false, -75,  22],
        ],
        'reviews' => [
            ['review_id',  true,   60, -62],
            ['rating',     false, 135, -25],
            ['comment',    false, 145,  22],
            ['created_at', false,  88,  65],
        ],
        'packages' => [
            ['package_id',  true,   62, -65],
            ['name',        false, 148, -28],
            ['destination', false, 158,  22],
            ['price',       false,  98,  65],
            ['duration',    false,   5,  78],
        ],
        'rentals' => [
            ['rental_id',    true,  -68, -65],
            ['name',         false,-115, -12],
            ['type',         false,-108,  42],
            ['city',         false, -50,  75],
            ['price_per_day',false,  68,  75],
        ],
        'bookings' => [
            ['booking_id',   true,  -105, -65],
            ['booking_date', false,   -5, -78],
            ['status',       false,  105, -62],
            ['booking_type', false, -105,  65],
            ['total_price',  false,  155,  18],
        ],
        'hotels' => [
            ['hotel_id',       true,   72, -65],
            ['name',           false, 155, -25],
            ['price_per_night',false, 165,  25],
            ['rating',         false,  85,  65],
        ],
        'buses' => [
            ['bus_id',        true,  -72, -62],
            ['from_location', false,-128,   8],
            ['to_location',   false,-118,  60],
            ['price',         false,  18,  78],
            ['departure_date',false, 145,  45],
        ],
        'payments' => [
            ['payment_id',     true,  -78, -68],
            ['payment_method', false,-128, -28],
            ['transaction_id', false, -35,  72],
            ['amount',         false,  95,  65],
            ['status',         false, 145,  -8],
        ],
        'ai_plans' => [
            ['plan_id',     true,    68, -65],
            ['destination', false,  158, -25],
            ['duration',    false,  165,  25],
            ['budget',      false,   88,  68],
            ['travel_style',false,  -10,  80],
            ['plan_result', false, -125,  58],
            ['created_at',  false, -128,  -2],
        ],
    ];

    // ════════════════════════════════════════════════════════════════
    //  BUILD SVG
    // ════════════════════════════════════════════════════════════════
    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // ── 1. relationship lines + cardinality labels ───────────────────
    foreach ($C as [$ek, $dk, $card]) {
        [$ex, $ey] = $E[$ek];
        [$dx, $dy] = $D[$dk];
        $s .= "<line x1=\"$ex\" y1=\"$ey\" x2=\"$dx\" y2=\"$dy\" stroke=\"black\" stroke-width=\"1.6\"/>\n";

        // card label: placed 20 % of the way from entity towards diamond
        $lx = round($ex + ($dx-$ex)*0.20);
        $ly = round($ey + ($dy-$ey)*0.20);
        // small offset so label doesn't sit on the line
        $ox = ($dx > $ex) ?  10 : -10;
        $oy = ($dy > $ey) ?  14 : -6;
        if (abs($dx-$ex) < 5)  { $ox =  12; }   // near-vertical
        if (abs($dy-$ey) < 5)  { $oy = -10; }   // near-horizontal
        $s .= "<text x=\"".($lx+$ox)."\" y=\"".($ly+$oy)."\" font-size=\"14\" font-family=\"Arial\" font-weight=\"bold\">$card</text>\n";
    }

    // ── 2. attribute lines + ovals ───────────────────────────────────
    foreach ($A as $ek => $attrs) {
        [$ex, $ey] = $E[$ek];
        foreach ($attrs as [$lbl, $pk, $adx, $ady]) {
            $ax = $ex + $adx;  $ay = $ey + $ady;
            $s .= "<line x1=\"$ex\" y1=\"$ey\" x2=\"$ax\" y2=\"$ay\" stroke=\"black\" stroke-width=\"1.2\"/>\n";
            $rw = max(strlen($lbl)*5 + 8, 45);
            $sw = $pk ? "2" : "1.5";
            $s .= "<ellipse cx=\"$ax\" cy=\"$ay\" rx=\"$rw\" ry=\"17\" fill=\"white\" stroke=\"black\" stroke-width=\"$sw\"/>\n";
            $deco = $pk ? ' text-decoration="underline" font-weight="bold"' : '';
            $s .= "<text x=\"$ax\" y=\"".($ay+5)."\" font-size=\"11\" font-family=\"Arial\" text-anchor=\"middle\"$deco>$lbl</text>\n";
        }
    }

    // ── 3. relationship diamonds ─────────────────────────────────────
    $ds = 38;
    foreach ($D as [$cx, $cy, $lbl]) {
        $pts = "$cx,".($cy-$ds)." ".($cx+$ds).",$cy $cx,".($cy+$ds)." ".($cx-$ds).",$cy";
        $s .= "<polygon points=\"$pts\" fill=\"white\" stroke=\"black\" stroke-width=\"1.6\"/>\n";
        $s .= "<text x=\"$cx\" y=\"".($cy+5)."\" font-size=\"11\" font-family=\"Arial\" text-anchor=\"middle\" font-weight=\"bold\">$lbl</text>\n";
    }

    // ── 4. entity rectangles (on top so they cover line ends) ────────
    $ew = 130; $eh = 50;
    foreach ($E as [$cx, $cy, $lbl]) {
        $rx = $cx - $ew/2;  $ry = $cy - $eh/2;
        $s .= "<rect x=\"$rx\" y=\"$ry\" width=\"$ew\" height=\"$eh\" fill=\"white\" stroke=\"black\" stroke-width=\"2\"/>\n";
        $s .= "<text x=\"$cx\" y=\"".($cy+6)."\" font-size=\"15\" font-family=\"Arial\" font-weight=\"bold\" text-anchor=\"middle\">$lbl</text>\n";
    }

    // ── 5. title ─────────────────────────────────────────────────────
    $s .= "<text x=\"".($W/2)."\" y=\"30\" font-size=\"19\" font-family=\"Arial\" font-weight=\"bold\" text-anchor=\"middle\">E-R Diagram — Travel Agency Website</text>\n";

    $s .= "</svg>\n";
    return $s;
}
