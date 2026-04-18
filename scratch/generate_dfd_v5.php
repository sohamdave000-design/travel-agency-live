<?php
$svg_path  = 'C:/xampp/htdocs/travel_agency/dfd_export.svg';
$html_path = 'C:/xampp/htdocs/travel_agency/dfd_export.html';

$svg = buildDFD4();
file_put_contents($svg_path, $svg);
$html = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body>' . $svg . '</body></html>';
file_put_contents($html_path, $html);
echo "Done — SVG and HTML written for DFD Level 4.\n";

function buildDFD4() {
    $W = 1600; $H = 1500;
    $stroke = "#333333";
    $headerFont = "'Times New Roman', serif";

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";

    // Header
    $s .= "<text x=\"50\" y=\"50\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\">Level -4 DFD (Admin: Catalog Inventory Management):</text>\n";

    // Marker for Arrows
    $s .= "<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='$stroke' />
            </marker>
          </defs>\n";

    // ── ENTITIES / EXTERNAL SYSTEMS ──
    $admin = [200, 300];
    $filesystem = [1400, 550];

    // ── PROCESSES (Granular Level 4 sub-processes) ──
    $p521 = [800, 150]; // 5.2.1 Validate Multipart Form
    $p522 = [800, 400]; // 5.2.2 Sanitize & Process Images
    $p523 = [800, 650]; // 5.2.3 Bind Catalog Parameters
    $p524 = [800, 900]; // 5.2.4 Commit Database Write
    $p525 = [800, 1100]; // 5.2.5 Log Audit Trail

    // ── DATA STORES ──
    $db_catalog = [1400, 900];
    $db_audit = [200, 1100];

    // Draw Entities
    drawRect($s, $admin[0], $admin[1], "Administrator", $stroke);
    drawRect($s, $filesystem[0], $filesystem[1], "Local Filesystem\n(/assets/img/)", $stroke);

    // Draw Processes
    drawCircle($s, $p521[0], $p521[1], "5.2.1 Validate\nFields & Form", $stroke, 75);
    drawCircle($s, $p522[0], $p522[1], "5.2.2 Process &\nResize Images", $stroke, 75);
    drawCircle($s, $p523[0], $p523[1], "5.2.3 Prepare\nSQL Statement", $stroke, 75);
    drawCircle($s, $p524[0], $p524[1], "5.2.4 Execute\nDB Transaction", $stroke, 75);
    drawCircle($s, $p525[0], $p525[1], "5.2.5 Register\nAdmin Audit Log", $stroke, 75);

    // Draw Data Stores
    drawDataStore($s, $db_catalog[0], $db_catalog[1], "D2: Catalog DB", $stroke);
    drawDataStore($s, $db_audit[0], $db_audit[1], "D11: System Logs", $stroke);

    // ── CONNECTIONS ──

    // Admin -> 5.2.1
    drawPolyArrow($s, [[200, 265], [200, 150], [725, 150]], "Upload Request (POST)", $stroke);

    // 5.2.1 -> Admin (Error path)
    drawPolyArrow($s, [[725, 180], [400, 180], [400, 300], [285, 300]], "Validation Error", $stroke);

    // 5.2.1 -> 5.2.2
    drawArrow($s, 800, 225, 800, 325, "Binary Image Data", $stroke);

    // 5.2.2 -> Filesystem
    drawPolyArrow($s, [[875, 400], [1400, 400], [1400, 515]], "Save Image File", $stroke);
    drawPolyArrow($s, [[1400, 585], [1400, 650], [875, 650]], "File Path String", $stroke);

    // 5.2.2 -> 5.2.3
    drawArrow($s, 800, 475, 800, 575, "Sanitized Metadata", $stroke);

    // 5.2.3 -> 5.2.4
    drawArrow($s, 800, 725, 800, 825, "PDO Prepared Query", $stroke);

    // 5.2.4 -> Catalog DB
    drawArrow($s, 875, 900, 1315, 900, "SQL Commit", $stroke);

    // 5.2.4 -> 5.2.5
    drawArrow($s, 800, 975, 800, 1025, "Write Status", $stroke);

    // 5.2.5 -> Audit Logs
    drawArrow($s, 725, 1100, 285, 1100, "Log: 'Item Created'", $stroke);

    // 5.2.5 -> Admin 
    drawPolyArrow($s, [[200, 1065], [200, 335]], "Success Notification", $stroke);

    // Footer
    $s .= "<text x=\"800\" y=\"1450\" font-size=\"22\" font-family=\"$headerFont\" font-weight=\"bold\" text-anchor=\"middle\">Fig 3.2.3.4</text>\n";

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
