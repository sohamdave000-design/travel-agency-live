<?php
echo "<h1>🖥️ Server Diagnostic Page</h1>";
echo "<p>Checking for required database variables...</p>";

$vars = ['MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD', 'MYSQLPORT'];
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Variable</th><th>Status</th></tr>";

foreach ($vars as $v) {
    $val = getenv($v) ?: ($_ENV[$v] ?? ($_SERVER[$v] ?? null));
    $status = $val ? "✅ FOUND" : "❌ MISSING";
    echo "<tr><td>$v</td><td>$status</td></tr>";
}
echo "</table>";

echo "<h2>Detailed PHP Info:</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "</pre>";
?>
