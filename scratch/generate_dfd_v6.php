<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/dfd_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/dfd_export.html';

$svg = buildDFD4_v2();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for DFD Level 4 (Accurate to Code).\n";

function buildDFD4_v2() {
    $W = 1600; $H = 1000;
    $stroke = "#333333";
    $headerFont = "'Times New Roman', serif";

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\">Level -4 DFD (Admin: Catalog Management - Package Creation):</text>\n";

    // Marker for Arrows
    $s .= "<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='$stroke' />
            </marker>
          </defs>\n";

    // ── ENTITIES ──
    $admin = [200, 450];

    // ── PROCESSES (Actual logic in packages.html) ──
    $p1 = [800, 150]; // 5.2.1 Receive & Sanitize POST Data
    $p2 = [800, 450]; // 5.2.2 Map Parameters (Price/Image/Meta)
    $p3 = [800, 750]; // 5.2.3 Execute PDO Prepared Query

    // ── DATA STORES ──
    $db_catalog = [1400, 750];

    // Draw Entities
    drawRect($s, $admin[0], $admin[1], "Administrator", $stroke);

    // Draw Processes
    drawCircle($s, $p1[0], $p1[1], "5.2.1 Receive &\nSanitize POST Data", $stroke, 75);
    drawCircle($s, $p2[0], $p2[1], "5.2.2 Map Entity\nParameters", $stroke, 75);
    drawCircle($s, $p3[0], $p3[1], "5.2.3 Execute PDO\nPrepared Query", $stroke, 75);

    // Draw Data Stores
    drawDataStore($s, $db_catalog[0], $db_catalog[1], "D2: Catalog DB\n(packages table)", $stroke);

    // ── CONNECTIONS ──

    // Admin -> 5.2.1
    drawPolyArrow($s, [[200, 415], [200, 150], [725, 150]], "Form Submit (POST)", $stroke);

    // 5.2.1 -> 5.2.2
    drawArrow($s, 800, 225, 800, 375, "Sanitized Strings", $stroke);

    // 5.2.2 -> 5.2.3
    drawArrow($s, 800, 525, 800, 675, "Bound Parameters", $stroke);

    // 5.2.3 -> Catalog DB
    drawArrow($s, 875, 750, 1315, 750, "INSERT / UPDATE", $stroke);

    // 5.3 -> Admin
    drawPolyArrow($s, [[800, 825], [800, 900], [200, 900], [200, 485]], "Success/Error Message", $stroke);

    // Footer
    $s .= "<text x=\"800\" y=\"950\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.3.4</text>\n";

    $s .= "</svg>\n";
    return $s;
}

function drawRect(&$s, $cx, $cy, $text, $stroke) {
    $w = 170; $h = 70;
    $x = $cx - $w/2; $y = $cy - $h/2;
    $s .= "<rect x=\"$x\" y=\"$y\" width=\"$w\" height=\"$h\" fill=\"white\" stroke=\"$stroke\" stroke-width=\"1.5\"/>\n";
    $lines = explode("\n", $text);
    $startY = $cy - (count($lines)-1)*10 + 5;
    foreach ($lines as $i => $line) {
        $s .= "<text x=\"$cx\" y=\"".($startY + $i*18)."\" font-size=\"14\" font-family=\"Arial\" text-anchor=\"middle\">$line</text>\n";
    }
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
    $lines = explode("\n", $text);
    $startY = $cy - (count($lines)-1)*10 + 5;
    foreach ($lines as $i => $line) {
        $s .= "<text x=\"$cx\" y=\"".($startY + $i*15)."\" font-size=\"12\" font-family=\"Arial\" text-anchor=\"middle\">$line</text>\n";
    }
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
