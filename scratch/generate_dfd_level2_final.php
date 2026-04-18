<?php
    // DFD Level 2 Final - Vertical Flow (Matching User Reference)
    $W = 1600; $H = 1400;

    // Entities
    $E = [
        'Customer' => [300, 500],
        'Admin'    => [1350, 500],
    ];

    // Processes (Circular)
    $P = [
        '2.1 Tour Package Route'           => [825, 180],
        '2.2 Tour Package Confirmation'    => [825, 450],
        '2.3 Seat Confirmation'            => [825, 760], // More space
        '2.4 Payment And Ticket Confirmation' => [825, 1070], // More space
        '2.5 Rental Service'               => [325, 1070],
    ];

    // Data Stores
    $D = [
        'Package DB'      => [825, 50],
        'sr DB'           => [100, 1070],
        'bus_booking DB'  => [1200, 760], // Moved right
        'bus_booking DB 2' => [825, 1280], // Bottom one
    ];

    // Flows [From, To, Label, Optional Offset]
    // Flows [From, To, Label, Optional Offset, Points Override]
    $F = [
        ['Customer', '2.1 Tour Package Route', 'Search Buses', [-110, -180]],
        ['Customer', '2.2 Tour Package Confirmation', 'Book Ticket', [-40, -30]],
        ['Customer', '2.5 Rental Service', 'Rent Vehicle', [-40, 100]],
        
        ['Admin', '2.1 Tour Package Route', 'Add Tour Package Route', [110, -180]],
        
        ['2.1 Tour Package Route', 'Package DB', 'R/W', [30, -50]],
        ['2.1 Tour Package Route', '2.2 Tour Package Confirmation', 'Tour Route', [40, 0]],
        ['2.2 Tour Package Confirmation', '2.3 Seat Confirmation', 'Tour Route', [40, 0]],
        ['2.3 Seat Confirmation', '2.4 Payment And Ticket Confirmation', 'Ticket Detail', [40, 0]],
        
        ['2.3 Seat Confirmation', 'bus_booking DB', 'R/W', [0, -40]],
        ['2.4 Payment And Ticket Confirmation', 'bus_booking DB 2', 'R/W', [40, 0]],
        ['2.5 Rental Service', 'sr DB', 'R/W', [0, -40]],
        
        // Polylines to avoid overlaps
        ['2.5 Rental Service', '2.4 Payment And Ticket Confirmation', 'Rental Confirmation', [0, -30], [[325,1070], [575,1070], [825,1070]]],
        ['2.4 Payment And Ticket Confirmation', 'Admin', 'Customer List', [120, -10], [[825,1070], [1350,1070], [1350,540]]],
    ];

    $s = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $s .= "<svg width=\"$W\" height=\"$H\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    $s .= "<rect width=\"$W\" height=\"$H\" fill=\"white\"/>\n";
    $s .= "<text x=\"50\" y=\"80\" font-size=\"35\" font-family=\"Arial\" font-weight=\"bold\">Level -2 DFD:</text>\n";

    // Draw Flows
    foreach ($F as $f) {
        if (isset($f[4])) {
            $pts = "";
            foreach($f[4] as $p) $pts .= "{$p[0]},{$p[1]} ";
            $s .= "<polyline points=\"$pts\" fill=\"none\" stroke=\"black\" stroke-width=\"2\" marker-end=\"url(#arrow)\"/>\n";
            $midX = $f[4][1][0] + ($f[3][0] ?? 0);
            $midY = $f[4][1][1] + ($f[3][1] ?? 0);
        } else {
            $p1 = isset($E[$f[0]]) ? $E[$f[0]] : $P[$f[0]];
            $p2 = isset($E[$f[1]]) ? $E[$f[1]] : (isset($P[$f[1]]) ? $P[$f[1]] : $D[$f[1]]);
            $s .= "<line x1=\"{$p1[0]}\" y1=\"{$p1[1]}\" x2=\"{$p2[0]}\" y2=\"{$p2[1]}\" stroke=\"black\" stroke-width=\"2\" marker-end=\"url(#arrow)\"/>\n";
            $midX = ($p1[0] + $p2[0]) / 2 + ($f[3][0] ?? 10);
            $midY = ($p1[1] + $p2[1]) / 2 + ($f[3][1] ?? -10);
        }
        $s .= "<text x=\"$midX\" y=\"$midY\" font-size=\"16\" font-family=\"Arial\" fill=\"#333\" font-weight=\"bold\">{$f[2]}</text>\n";
    }

    // Draw Processes (Circles)
    foreach ($P as $name => $c) {
        $s .= "<circle cx=\"{$c[0]}\" cy=\"{$c[1]}\" r=\"95\" fill=\"white\" stroke=\"black\" stroke-width=\"3\"/>\n";
        $parts = explode(' ', $name, 2);
        $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]-15)."\" font-size=\"18\" font-family=\"Arial\" font-weight=\"bold\" text-anchor=\"middle\">{$parts[0]}</text>\n";
        $text = $parts[1];
        if (strlen($text) > 20) {
            $words = explode(' ', $text);
            $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+15)."\" font-size=\"15\" font-family=\"Arial\" text-anchor=\"middle\">" . implode(' ', array_slice($words, 0, 2)) . "</text>\n";
            $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+35)."\" font-size=\"15\" font-family=\"Arial\" text-anchor=\"middle\">" . implode(' ', array_slice($words, 2)) . "</text>\n";
        } else {
            $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+20)."\" font-size=\"16\" font-family=\"Arial\" text-anchor=\"middle\">$text</text>\n";
        }
    }

    // Draw Entities (Rectangles)
    foreach ($E as $name => $c) {
        $s .= "<rect x=\"".($c[0]-90)."\" y=\"".($c[1]-40)."\" width=\"180\" height=\"80\" fill=\"white\" stroke=\"black\" stroke-width=\"3\"/>\n";
        $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+10)."\" font-size=\"22\" font-family=\"Arial\" font-weight=\"bold\" text-anchor=\"middle\">$name</text>\n";
    }

    // Draw Data Stores (Parallel Lines)
    foreach ($D as $name => $c) {
        $cleanName = str_replace(' 2', '', $name);
        $s .= "<line x1=\"".($c[0]-110)."\" y1=\"".($c[1]-30)."\" x2=\"".($c[0]+110)."\" y2=\"".($c[1]-30)."\" stroke=\"black\" stroke-width=\"3\"/>\n";
        $s .= "<line x1=\"".($c[0]-110)."\" y1=\"".($c[1]+30)."\" x2=\"".($c[0]+110)."\" y2=\"".($c[1]+30)."\" stroke=\"black\" stroke-width=\"3\"/>\n";
        $s .= "<text x=\"{$c[0]}\" y=\"".($c[1]+7)."\" font-size=\"18\" font-family=\"Arial\" font-weight=\"bold\" text-anchor=\"middle\">$cleanName</text>\n";
    }

    // Arrow marker
    $s .= "<defs><marker id=\"arrow\" viewBox=\"0 0 10 10\" refX=\"10\" refY=\"5\" markerWidth=\"8\" markerHeight=\"8\" orient=\"auto\"><path d=\"M 0 0 L 10 5 L 0 10 z\" fill=\"black\"/></marker></defs>\n";
    $s .= "</svg>";

    file_put_contents('scratch/dfd_level2_vertical.svg', $s);
    file_put_contents('er_diagram_export.html', "<html><body style='margin:0;padding:50px;background:white;'><img src='scratch/dfd_level2_vertical.svg' style='width:1450px; border:1px solid #eee;'></body></html>");
    echo "Done — Vertical Flow DFD Level 2 generated.\n";
