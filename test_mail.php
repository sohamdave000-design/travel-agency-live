<?php
require_once 'libs/MailService.php';

echo "<h1>SMTP Mail Debugger</h1>";
echo "<p>Testing connection to <strong>" . MAIL_HOST . ":" . MAIL_PORT . "</strong>...</p>";

$to = MAIL_USER;
$subject = "Debug Test - " . date("H:i:s");
$body = "This is a test email sent from the debug script.";

$result = MailService::send($to, $subject, $body, true);

echo "<pre style='background: #f1f5f9; padding: 1rem; border-radius: 8px; border: 1px solid #cbd5e1;'>";
if (is_array($result)) {
    foreach ($result as $line) {
        echo htmlspecialchars($line) . "\n";
    }
} else {
    echo "Unexpected result type.";
}
echo "</pre>";

$success = false;
if (is_array($result)) {
    foreach ($result as $line) {
        if (strpos($line, 'S: 250') !== false && strpos($line, 'QUIT') === false) {
             $success = true;
        }
    }
}

if ($success) {
    echo "<h3 style='color: #059669;'>✅ Success! The email was accepted by Gmail.</h3>";
    echo "<p>If you still don't see it, check your <strong>Spam</strong> folder.</p>";
} else {
    echo "<h3 style='color: #dc2626;'>❌ Failed. Please see the log above for details.</h3>";
    echo "<h4>Common Fixes:</h4>";
    echo "<ul>";
    echo "<li><strong>OpenSSL Error:</strong> In your XAMPP Control Panel, click 'Config' next to Apache, open <code>php.ini</code>, search for <code>extension=openssl</code>, and remove the <code>;</code> at the beginning of the line. Then Restart Apache.</li>";
    echo "<li><strong>Firewall:</strong> Your network or firewall might be blocking port 587.</li>";
    echo "</ul>";
}
?>
