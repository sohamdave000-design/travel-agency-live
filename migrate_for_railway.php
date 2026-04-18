<?php
/**
 * Railway Migration Script
 * 1. Renames all .html files (recursively) to .php
 * 2. Updates 'require_once', 'include', and 'require' paths from .html to .php
 */

$root = __DIR__;
$files_to_rename = [];

// Step 1: Find all .html files
$it = new RecursiveDirectoryIterator($root);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ($file->getExtension() === 'html') {
        // Exclude specific files if needed
        $files_to_rename[] = $file->getPathname();
    }
}

echo "Found " . count($files_to_rename) . " files to rename.\n";

// Step 2: Update content in all files (searching for .html strings in PHP tags)
// We'll search in .php files (including the ones we are about to rename)
$all_files_to_update = [];
$it = new RecursiveDirectoryIterator($root);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if (in_array($file->getExtension(), ['php', 'html'])) {
         $all_files_to_update[] = $file->getPathname();
    }
}

foreach ($all_files_to_update as $filePath) {
    $content = file_get_contents($filePath);
    $original = $content;
    
    // Update internal PHP includes/requires
    // Matches: require_once 'config/database.php'; include "header.php"; etc.
    $content = preg_replace("/(require|include)(_once)?\s+(['\"])(.*?)\.html(['\"])/", "$1$2 $3$4.php$3", $content);
    
    // Special case for database.php as it's the core config
    $content = str_replace('database.php', 'database.php', $content);

    if ($content !== $original) {
        file_put_contents($filePath, $content);
        echo "Updated references in: " . basename($filePath) . "\n";
    }
}

// Step 3: Execute the renames
foreach ($files_to_rename as $oldPath) {
    $newPath = preg_replace('/\.html$/', '.php', $oldPath);
    if (rename($oldPath, $newPath)) {
        echo "Renamed: " . basename($oldPath) . " -> " . basename($newPath) . "\n";
    } else {
        echo "FAILED to rename: " . basename($oldPath) . "\n";
    }
}

echo "Migration Complete.\n";
?>
