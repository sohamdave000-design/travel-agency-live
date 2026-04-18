<?php
require_once dirname(__DIR__) . '/config/database.php';

try {
    // UTF-8 bytes for ★ (E2 98 85) interpreted as Windows-1252/ISO-8859-1 is Ôÿà
    // We search for the corrupted sequence and replace it with "-Star"
    
    // First, let's get all hotel names to see exactly what we're dealing with
    $hotels = $pdo->query("SELECT id, name FROM hotels")->fetchAll();
    $updatedCount = 0;

    foreach ($hotels as $hotel) {
        $oldName = $hotel['name'];
        
        // Replace common mojibake patterns for the star symbol
        $newName = str_replace(['Ôÿà', 'â˜…', '★'], ' Star', $oldName);
        
        // Clean up double spaces or "- Star" 
        $newName = str_replace(' - Star', ' - Star', $newName);
        $newName = preg_replace('/\s+/', ' ', $newName);
        $newName = trim($newName);

        if ($newName !== $oldName) {
            $stmt = $pdo->prepare("UPDATE hotels SET name = ? WHERE id = ?");
            $stmt->execute([$newName, $hotel['id']]);
            echo "Updated: '$oldName' -> '$newName'\n";
            $updatedCount++;
        }
    }

    echo "Finished. Total hotels updated: $updatedCount\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
