<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/dfd_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/dfd_export.html';

$svg = buildDFD();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for DFD Levels 0 & 1.\n";

function buildDFD() {
    $W = 1600; $H = 1450;

    // ── COLORS & STYLE ──
    $stroke = "#333333";
    $font = "Arial, sans-serif";
    $headerFont = "'Times New Roman', serif";

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"24\" font-family=\"$headerFont\" font-weight=\"bold\">3.2.3 Data Flow diagram</text>\n";

    // Marker for Arrows
    $s .= "<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='$stroke' />
            </marker>
          </defs>\n";

    // =========================================================================
    // LEVEL 0 DFD
    // =========================================================================
    $y0 = 200;
    $s .= "<text x=\"50\" y=\"110\" font-size=\"20\" font-family=\"$headerFont\" font-weight=\"bold\">Level -0 DFD:</text>\n";

    // Entities Level 0
    $cust0 = [150, $y0]; // Center of rect
    $admin0 = [1450, $y0];
    $sys0 = [800, $y0]; // Center of circle

    // Draw Entities
    drawRect($s, $cust0[0], $cust0[1], "Customer", $stroke);
    drawRect($s, $admin0[0], $admin0[1], "Admin", $stroke);
    drawCircle($s, $sys0[0], $sys0[1], "Travel Agency\nManagement\nSystem", $stroke, 85);

    // Arrows Level 0 (Straight parallel lines)
    drawArrow($s, 225, $y0-45, 715, $y0-45, "Search Packages", $stroke);
    drawArrow($s, 225, $y0-15, 715, $y0-15, "Book Ticket", $stroke);
    drawArrow($s, 225, $y0+15, 715, $y0+15, "Search Vehicle", $stroke);
    drawArrow($s, 715, $y0+45, 225, $y0+45, "Ticket Confirmation", $stroke);

    // Admin -> System
    drawArrow($s, 1375, $y0-35, 885, $y0-35, "Add Travel Package", $stroke);
    drawArrow($s, 1375, $y0+15, 885, $y0+15, "Rent Vehicle", $stroke);

    // System -> Admin
    drawArrow($s, 885, $y0-15, 1375, $y0-15, "Ticket Booking List", $stroke);
    drawArrow($s, 885, $y0+35, 1375, $y0+35, "Customer List", $stroke);

    $s .= "<text x=\"800\" y=\"360\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.3</text>\n";

    // =========================================================================
    // LEVEL 1 DFD
    // =========================================================================
    $s .= "<text x=\"50\" y=\"410\" font-size=\"20\" font-family=\"$headerFont\" font-weight=\"bold\">Level -1 DFD:</text>\n";

    // Processes Level 1
    $p1 = [800, 500];
    $p2 = [800, 720];
    $p3 = [800, 940];
    $p4 = [800, 1160];

    // Entities Level 1
    $cust1 = [150, 720];
    $admin1 = [1450, 720];

    // Draw Circles
    drawCircle($s, $p1[0], $p1[1], "Tour package\nRoute", $stroke, 65);
    drawCircle($s, $p2[0], $p2[1], "Ticket\nReservation", $stroke, 65);
    drawCircle($s, $p3[0], $p3[1], "Payment\nand Ticket\nConfirmation", $stroke, 65);
    drawCircle($s, $p4[0], $p4[1], "Rental\nServices", $stroke, 65);

    // Draw Rects
    drawRect($s, $cust1[0], $cust1[1], "Customer", $stroke);
    drawRect($s, $admin1[0], $admin1[1], "Admin", $stroke);

    // Connections Level 1 (All Straight/Polylines)
    // Vertical flows
    drawArrow($s, 800, 565, 800, 655, "", $stroke); // p1 -> p2
    drawArrow($s, 800, 785, 800, 875, "Ticket Detail", $stroke, false, true); // p2 -> p3
    drawArrow($s, 800, 1005, 800, 1095, "Rental Details", $stroke, false, true); // p3 -> p4

    // Cust connections (L-shaped straight lines)
    // Search Packages
    drawPolyArrow($s, [[225,700], [450,700], [450,500], [735,500]], "Search Packages", $stroke);
    // Book Ticket
    drawArrow($s, 225, 720, 735, 720, "Book Ticket", $stroke);
    // Rent Vehicle
    drawPolyArrow($s, [[225,740], [450,740], [450,1160], [735,1160]], "Rent Vehicle", $stroke);
    // Ticket Confirmation
    drawPolyArrow($s, [[735,940], [300,940], [300,755], [225,755]], "Ticket Confirmation", $stroke);

    // Admin connections
    // Add tour package route
    drawPolyArrow($s, [[1375,700], [1150,700], [1150,500], [865,500]], "Add tour package route", $stroke);
    // Ticket Booking List
    drawArrow($s, 865, 720, 1375, 720, "Ticket Booking List", $stroke);
    // Customer List
    drawPolyArrow($s, [[865,940], [1300,940], [1300,740], [1375,740]], "Customer List", $stroke);

    $s .= "<text x=\"800\" y=\"1350\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.3.1</text>\n";

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

function drawArrow(&$s, $x1, $y1, $x2, $y2, $text, $stroke, $reverse=false, $vertical=false) {
    $s .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\"/>\n";
    if ($text) {
        $mx = ($x1 + $x2) / 2;
        $my = ($y1 + $y2) / 2;
        if ($vertical) {
            $s .= "<text x=\"".($mx+10)."\" y=\"$my\" font-size=\"13\" font-family=\"Arial\" transform=\"rotate(360, ".($mx+10).", $my)\" text-anchor=\"middle\">$text</text>\n";
        } else {
            $s .= "<text x=\"$mx\" y=\"".($my-8)."\" font-size=\"13\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
        }
    }
}

function drawPolyArrow(&$s, $pts, $text, $stroke) {
    $points = "";
    foreach ($pts as $p) {
        $points .= "{$p[0]},{$p[1]} ";
    }
    $s .= "<polyline points=\"$points\" fill=\"none\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\"/>\n";
    if ($text) {
        // midpoint of the first segment for label
        $mx = ($pts[0][0] + $pts[1][0]) / 2;
        $my = ($pts[0][1] + $pts[1][1]) / 2;
        $s .= "<text x=\"$mx\" y=\"".($my-8)."\" font-size=\"13\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
    }
}
