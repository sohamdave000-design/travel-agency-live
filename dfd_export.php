<!DOCTYPE html><html><head><style>*{margin:0;padding:0;}body{background:white;}</style></head><body><?xml version="1.0" encoding="UTF-8"?>
<svg width="1600" height="1000" xmlns="http://www.w3.org/2000/svg">
<rect width="1600" height="1000" fill="white"/>
<text x="50" y="50" font-size="22" font-family="'Times New Roman', serif" font-weight="bold">Level -4 DFD (Admin: Catalog Management - Package Creation):</text>
<defs>
            <marker id='arrowhead' markerWidth='10' markerHeight='7' refX='10' refY='3.5' orient='auto'>
                <polygon points='0 0, 10 3.5, 0 7' fill='#333333' />
            </marker>
          </defs>
<rect x="115" y="415" width="170" height="70" fill="white" stroke="#333333" stroke-width="1.5"/>
<text x="200" y="455" font-size="14" font-family="Arial" text-anchor="middle">Administrator</text>
<circle cx="800" cy="150" r="75" fill="white" stroke="#333333" stroke-width="1.5"/>
<text x="800" y="145" font-size="14" font-family="Arial" text-anchor="middle">5.2.1 Receive &</text>
<text x="800" y="163" font-size="14" font-family="Arial" text-anchor="middle">Sanitize POST Data</text>
<circle cx="800" cy="450" r="75" fill="white" stroke="#333333" stroke-width="1.5"/>
<text x="800" y="445" font-size="14" font-family="Arial" text-anchor="middle">5.2.2 Map Entity</text>
<text x="800" y="463" font-size="14" font-family="Arial" text-anchor="middle">Parameters</text>
<circle cx="800" cy="750" r="75" fill="white" stroke="#333333" stroke-width="1.5"/>
<text x="800" y="745" font-size="14" font-family="Arial" text-anchor="middle">5.2.3 Execute PDO</text>
<text x="800" y="763" font-size="14" font-family="Arial" text-anchor="middle">Prepared Query</text>
<line x1="1315" y1="720" x2="1485" y2="720" stroke="#333333" stroke-width="1.5"/>
<line x1="1315" y1="780" x2="1485" y2="780" stroke="#333333" stroke-width="1.5"/>
<text x="1400" y="745" font-size="12" font-family="Arial" text-anchor="middle">D2: Catalog DB</text>
<text x="1400" y="760" font-size="12" font-family="Arial" text-anchor="middle">(packages table)</text>
<polyline points="200,415 200,150 725,150 " fill="none" stroke="#333333" stroke-width="1.2" marker-end="url(#arrowhead)"/>
<text x="200" y="274.5" font-size="12" font-family="Arial" text-anchor="middle">Form Submit (POST)</text>
<line x1="800" y1="225" x2="800" y2="375" stroke="#333333" stroke-width="1.2" marker-end="url(#arrowhead)"/>
<text x="800" y="292" font-size="12" font-family="Arial" text-anchor="middle">Sanitized Strings</text>
<line x1="800" y1="525" x2="800" y2="675" stroke="#333333" stroke-width="1.2" marker-end="url(#arrowhead)"/>
<text x="800" y="592" font-size="12" font-family="Arial" text-anchor="middle">Bound Parameters</text>
<line x1="875" y1="750" x2="1315" y2="750" stroke="#333333" stroke-width="1.2" marker-end="url(#arrowhead)"/>
<text x="1095" y="742" font-size="12" font-family="Arial" text-anchor="middle">INSERT / UPDATE</text>
<polyline points="800,825 800,900 200,900 200,485 " fill="none" stroke="#333333" stroke-width="1.2" marker-end="url(#arrowhead)"/>
<text x="800" y="854.5" font-size="12" font-family="Arial" text-anchor="middle">Success/Error Message</text>
<text x="800" y="950" font-size="22" font-family="'Times New Roman', serif" font-weight="bold" text-anchor="middle">Fig 3.2.3.4</text>
</svg>
</body></html>