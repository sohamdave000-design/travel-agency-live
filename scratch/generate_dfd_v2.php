<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/dfd_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/dfd_export.html';

$svg = buildDFD2();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for DFD Level 2.\n";

function buildDFD2() {
    $W = 1600; $H = 1000;
    $stroke = "#333333";
    $headerFont = "'Times New Roman', serif";

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"24\" font-family=\"$headerFont\" font-weight=\"bold\">3.2.4 Level -2 DFD (Booking Engine)</text>\n";

    // Marker for Arrows
    $s .= "<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='$stroke' />
            </marker>
          </defs>\n";

    // ── ENTITIES & DATASTORES ──
    $cust = [150, 450];
    $inventory = [1400, 250];
    $bookings_db = [1400, 750]; // Aligned with 3.3
    $payment_sys = [800, 950]; // External System

    // ── PROCESSES ──
    $p31 = [800, 250];
    $p32 = [800, 500];
    $p33 = [800, 750];

    // Draw Circles (Processes)
    drawCircle($s, $p31[0], $p31[1], "3.1 Verify\nAvailability", $stroke, 70);
    drawCircle($s, $p32[0], $p32[1], "3.2 Calculate Price\n& Add-ons", $stroke, 70);
    drawCircle($s, $p33[0], $p33[1], "3.3 Create Pending\nReservation", $stroke, 70);

    // Draw Rects (Entities/External Systems)
    drawRect($s, $cust[0], $cust[1], "Customer", $stroke);
    drawRect($s, $payment_sys[0], $payment_sys[1], "Payment System", $stroke);

    // Draw Data Stores (Open-ended rects)
    drawDataStore($s, $inventory[0], $inventory[1], "D1: Catalog Inventory", $stroke);
    drawDataStore($s, $bookings_db[0], $bookings_db[1], "D2: Bookings DB", $stroke);

    // ── CONNECTIONS ──
    
    // Cust -> 3.1
    drawPolyArrow($s, [[225, 430], [500, 430], [500, 250], [725, 250]], "Booking Request", $stroke);
    
    // 3.1 <-> Inventory
    drawArrow($s, 875, 235, 1310, 235, "Check Availability", $stroke);
    drawArrow($s, 1310, 265, 875, 265, "Status Return", $stroke);
    
    // 3.1 -> 3.2
    drawArrow($s, 800, 325, 800, 425, "Available Payload", $stroke);
    
    // Cust -> 3.2
    drawArrow($s, 225, 500, 725, 500, "Selection details", $stroke);
    
    // 3.2 -> 3.3
    drawArrow($s, 800, 575, 800, 675, "Total Price", $stroke);
    
    // 3.3 -> Bookings DB
    drawArrow($s, 875, 750, 1310, 750, "Create Record", $stroke);
    
    // 3.3 -> 4.0 Payment (Hand-over)
    drawArrow($s, 800, 825, 800, 912, "Hand-over to Payment", $stroke);

    // Footer
    $s .= "<text x=\"800\" y=\"980\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.4</text>\n";

    $s .= "</svg>\n";
    return $s;
}

function drawRect(&$s, $cx, $cy, $text, $stroke) {
    $w = 150; $h = 70;
    $x = $cx - $w/2; $y = $cy - $h/2;
    $s .= "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" fill=\"white\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $s .= "<text x=\"$cx\" y=\"".($cy+6)."\" font-size=\"16\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
}

function drawCircle(&$s, $cx, $cy, $text, $stroke, $r=70) {
    $s .= "<circle cx=\"$cx\" cy=\"$cy\" r=\"$r\" fill=\"white\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $lines = explode("\n", $text);
    $startY = $cy - (count($lines)-1)*10 + 5;
    foreach ($lines as $i => $line) {
        $s .= "<text x=\"$cx\" y=\"".($startY + $i*20)."\" font-size=\"14\" font-family=\"Arial\" text-anchor=\"middle\">$line</text>\n";
    }
}

function drawDataStore(&$s, $cx, $cy, $text, $stroke) {
    $w = 180; $h = 60;
    $x = $cx - $w/2; $y = $cy - $h/2;
    // Data store is two horizontal lines
    $s .= "<line x1=\"$x\" y1=\"$y\" x2=\"".($x+$w)."\" y2=\"$y\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $s .= "<line x1=\"$x\" y1=\"".($y+$h)."\" x2=\"".($x+$w)."\" y2=\"".($y+$h)."\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    // Plus a vertical line on the left side
    $s .= "<line x1=\"$x\" y1=\"$y\" x2=\"$x\" y2=\"".($y+$h)."\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $s .= "<text x=\"".($cx+5)."\" y=\"".($cy+5)."\" font-size=\"13\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
}

function drawArrow(&$s, $x1, $y1, $x2, $y2, $text, $stroke) {
    $s .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\"/>\n";
    if ($text) {
        $mx = ($x1 + $x2) / 2;
        $my = ($y1 + $y2) / 2;
        $s .= "<text x=\"$mx\" y=\"".($my-8)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
    }
}

function drawPolyArrow(&$s, $pts, $text, $stroke) {
    $points = "";
    foreach ($pts as $p) {
        $points .= "{$p[0]},{$p[1]} ";
    }
    $s .= "<polyline points=\"$points\" fill=\"none\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\"/>\n";
    if ($text) {
        $mx = ($pts[0][0] + $pts[1][0]) / 2;
        $my = ($pts[0][1] + $pts[1][1]) / 2;
        $s .= "<text x=\"$mx\" y=\"".($my-8)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
    }
}
