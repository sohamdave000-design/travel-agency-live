<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/dfd_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/dfd_export.html';

$svg = buildDFD3();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for DFD Level 3.\n";

function buildDFD3() {
    $W = 1600; $H = 1100;
    $stroke = "#333333";
    $headerFont = "'Times New Roman', serif";

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\">Level -3 DFD (Payment &amp; Confirmation):</text>\n";

    // Marker for Arrows
    $s .= "<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='$stroke' />
            </marker>
          </defs>\n";

    // ── EXTERNAL ENTITIES / SYSTEMS ──
    $cust = [200, 500];
    $gateway = [1400, 350];
    $smtp = [1400, 850];

    // ── PROCESSES (Sub-processes of P5) ──
    $p51 = [800, 150]; // 5.1 Validate Inventory/Price
    $p52 = [800, 400]; // 5.2 Execute Payment
    $p53 = [800, 650]; // 5.3 Update Booking Status
    $p54 = [800, 900]; // 5.4 Generate E-Ticket

    // ── DATA STORES ──
    $db_bookings = [200, 650];
    $db_payments = [1400, 650];

    // Draw Entities
    drawRect($s, $cust[0], $cust[1], "Customer", $stroke);
    drawRect($s, $gateway[0], $gateway[1], "Payment Gateway", $stroke);
    drawRect($s, $smtp[0], $smtp[1], "SMTP Mail Server", $stroke);

    // Draw Processes
    drawCircle($s, $p51[0], $p51[1], "5.1 Validate\nBooking & Price", $stroke, 70);
    drawCircle($s, $p52[0], $p52[1], "5.2 Execute\nPayment Process", $stroke, 70);
    drawCircle($s, $p53[0], $p53[1], "5.3 Confirm &\nUpdate Booking", $stroke, 70);
    drawCircle($s, $p54[0], $p54[1], "5.4 Generate\nE-Ticket/Invoice", $stroke, 70);

    // Draw Data Stores
    drawDataStore($s, $db_bookings[0], $db_bookings[1], "D2: Bookings DB", $stroke);
    drawDataStore($s, $db_payments[0], $db_payments[1], "D3: Payments DB", $stroke);

    // ── CONNECTIONS ──

    // Input from Level 2 (Implicit) 
    $s .= "<line x1=\"500\" y1=\"150\" x2=\"725\" y2=\"150\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\"/>\n";
    $s .= "<text x=\"550\" y=\"140\" font-size=\"12\" font-family=\"Arial\">From P3/P4: Selection Meta</text>\n";

    // 5.1 -> 5.2
    drawArrow($s, 800, 225, 800, 325, "Verified Amount", $stroke);

    // 5.2 <-> Payment Gateway
    drawArrow($s, 875, 385, 1315, 360, "Transaction Request", $stroke);
    drawArrow($s, 1315, 380, 875, 415, "Transaction ID/Success", $stroke);

    // 5.2 -> 5.3
    drawArrow($s, 800, 475, 800, 575, "Auth Token", $stroke);

    // 5.3 <-> Bookings DB
    drawArrow($s, 725, 650, 285, 650, "Update Status: 'Confirmed'", $stroke);

    // 5.3 -> Payments DB (Log)
    drawArrow($s, 875, 650, 1315, 650, "Store Payment Log", $stroke);

    // 5.3 -> 5.4
    drawArrow($s, 800, 725, 800, 825, "Success Trigger", $stroke);

    // 5.4 -> SMTP
    drawArrow($s, 875, 900, 1315, 860, "Send Mail Data", $stroke);

    // 5.4 -> Customer
    drawPolyArrow($s, [[725, 900], [200, 900], [200, 540]], "E-Ticket confirmation", $stroke);

    // Footer
    $s .= "<text x=\"800\" y=\"1050\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.3.3</text>\n";

    $s .= "</svg>\n";
    return $s;
}

function drawRect(&$s, $cx, $cy, $text, $stroke) {
    $w = 170; $h = 70;
    $x = $cx - $w/2; $y = $cy - $h/2;
    $s .= "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" fill=\"white\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $s .= "<text x=\"$cx\" y=\"".($cy+6)."\" font-size=\"14\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
}

function drawCircle(&$s, $cx, $cy, $text, $stroke, $r=70) {
    $s .= "<circle cx=\"$cx\" cy=\"$cy\" r=\"$r\" fill=\"white\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $lines = explode("\n", $text);
    $startY = $cy - (count($lines)-1)*10 + 5;
    foreach ($lines as $i => $line) {
        $s .= "<text x=\"$cx\" y=\"".($startY + $i*18)."\" font-size=\"14\" font-family=\"Arial\" text-anchor=\"middle\">$line</text>\n";
    }
}

function drawDataStore(&$s, $cx, $cy, $text, $stroke) {
    $w = 170; $h = 60;
    $x = $cx - $w/2; $y = $cy - $h/2;
    $s .= "<line x1=\"$x\" y1=\"$y\" x2=\"".($x+$w)."\" y2=\"$y\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $s .= "<line x1=\"$x\" y1=\"".($y+$h)."\" x2=\"".($x+$w)."\" y2=\"".($y+$h)."\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $s .= "<text x=\"$cx\" y=\"".($cy+5)."\" font-size=\"13\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
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
