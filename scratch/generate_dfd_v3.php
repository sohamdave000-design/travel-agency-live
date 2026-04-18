<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/dfd_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/dfd_export.html';

$svg = buildDFD2_v2();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for DFD Level 2 (Reference Fig 3.2.3.2).\n";

function buildDFD2_v2() {
    $W = 1600; $H = 1100;
    $stroke = "#333333";
    $headerFont = "'Times New Roman', serif";

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\">Level -2 DFD:</text>\n";

    // Marker for Arrows
    $s .= "<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='$stroke' />
            </marker>
          </defs>\n";

    // ── ENTITIES ──
    $cust = [300, 480];
    $admin = [1350, 480];

    // ── PROCESSES ──
    $p1 = [800, 150]; // Tour Package Route
    $p2 = [800, 350]; // Tour Package Confirmation
    $p3 = [800, 550]; // Seat Confirmation
    $p4 = [300, 750]; // Rental Service
    $p5 = [800, 750]; // Payment and Ticket Confirmation

    // ── DATA STORES ──
    $db1 = [800, 50];   // Package DB
    $db2 = [1150, 550]; // bus_booking DB (beside P3)
    $db3 = [800, 920];  // bus_booking DB (below P5)
    $db4 = [100, 750];  // sr DB (left of P4)

    // Draw Entities
    drawRect($s, $cust[0], $cust[1], "Customer", $stroke);
    drawRect($s, $admin[0], $admin[1], "Admin", $stroke);

    // Draw Processes
    drawCircle($s, $p1[0], $p1[1], "Tour Package\nRoute", $stroke, 65);
    drawCircle($s, $p2[0], $p2[1], "Tour Package\nConfirmation", $stroke, 65);
    drawCircle($s, $p3[0], $p3[1], "Seat\nConfirmation", $stroke, 65);
    drawCircle($s, $p4[0], $p4[1], "Rental\nService", $stroke, 65);
    drawCircle($s, $p5[0], $p5[1], "Payment And\nTicket\nConfirmation", $stroke, 70);

    // Draw Data Stores
    drawDataStore($s, $db1[0], $db1[1], "Package DB", $stroke);
    drawDataStore($s, $db2[0], $db2[1], "bus_booking DB", $stroke);
    drawDataStore($s, $db3[0], $db3[1], "bus_booking DB", $stroke);
    drawDataStore($s, $db4[0], $db4[1], "sr DB", $stroke);

    // ── CONNECTIONS ──

    // Cust -> P1 (L-shape)
    drawPolyArrow($s, [[300, 445], [300, 150], [735, 150]], "Search Buses", $stroke);
    // Cust -> P2 (L-shape)
    drawPolyArrow($s, [[375, 480], [450, 480], [450, 350], [735, 350]], "Book Ticket", $stroke);
    // Cust -> P4 
    drawArrow($s, 300, 515, 300, 685, "Rent Vehicle", $stroke);

    // P1 <-> DB1
    drawRWArrow($s, 800, 85, 800, 65, $stroke);
    // P1 -> P2
    drawArrow($s, 800, 215, 800, 285, "Tour Route", $stroke);
    // P2 -> P3
    drawArrow($s, 800, 415, 800, 485, "Tour Route", $stroke);
    // P3 <-> DB2
    drawRWArrowHorizontal($s, 865, 550, 1060, 550, $stroke);
    // P3 -> P5
    drawArrow($s, 800, 615, 800, 680, "Ticket Detail", $stroke);

    // P5 -> Cust (L-shape)
    drawPolyArrow($s, [[730, 750], [450, 750], [450, 680], [350, 645]], "Ticket Confirmation", $stroke, true); // wait, Arrow to Cust
    
    // P5 <-> DB3
    drawRWArrow($s, 800, 820, 800, 890, $stroke);

    // P4 <-> DB4
    drawRWArrowHorizontal($s, 235, 750, 190, 750, $stroke, true);
    // P4 -> P5
    drawArrow($s, 365, 750, 730, 750, "Rental Detail", $stroke);

    // Admin -> P1 (Polyline)
    drawPolyArrow($s, [[1350, 445], [1350, 150], [865, 150]], "Add Tour Package Route", $stroke);
    // P5 -> Admin (L-shape)
    drawPolyArrow($s, [[870, 750], [1350, 750], [1350, 515]], "Customer List", $stroke);

    // Footer
    $s .= "<text x=\"800\" y=\"1050\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.3.2</text>\n";

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
    $w = 160; $h = 50;
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

function drawRWArrow(&$s, $x1, $y1, $x2, $y2, $stroke) {
    // Bidirectional arrow
    $s .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\" marker-start=\"url(#arrowhead)\"/>\n";
    $s .= "<text x=\"".($x1+15)."\" y=\"".(($y1+$y2)/2)."\" font-size=\"12\" font-family=\"Arial\">R/W</text>\n";
}

function drawRWArrowHorizontal(&$s, $x1, $y1, $x2, $y2, $stroke, $left=false) {
    $s .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" stroke=\"$stroke\" stroke-width=\"1.2\" marker-end=\"url(#arrowhead)\" marker-start=\"url(#arrowhead)\"/>\n";
    $s .= "<text x=\"".(($x1+$x2)/2)."\" y=\"".($y1-8)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\">R/W</text>\n";
}

function drawPolyArrow(&$s, $pts, $text, $stroke, $arrowAtEnd=true) {
    $points = "";
    foreach ($pts as $p) {
        $points .= "{$p[0]},{$p[1]} ";
    }
    $m = $arrowAtEnd ? " marker-end=\"url(#arrowhead)\"" : "";
    $s .= "<polyline points=\"$points\" fill=\"none\" stroke=\"$stroke\" stroke-width=\"1.2\"$m/>\n";
    if ($text) {
        $mx = ($pts[0][0] + $pts[1][0]) / 2;
        $my = ($pts[0][1] + $pts[1][1]) / 2;
        $s .= "<text x=\"$mx\" y=\"".($my-8)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
    }
}
