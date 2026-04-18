<?php
$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'html' && $file->getFilename() !== 'currency_fixer.html') {
        $content = file_get_contents($file->getPathname());
        
        // Replace mangled Rupee symbol (â‚¹) with true ₹
        // Note: â‚¹ is often the UTF-8 bytes for ₹ misread as ISO-8859-1
        $newContent = str_replace('â‚¹', '₹', $content);
        
        // Simple regex to replace $ with ₹ when it's before a number
        $newContent = preg_replace('/\$([0-9])/', '₹$1', $newContent);
        
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Fixed currency in: " . $file->getFilename() . "<br>";
        }
    }
}
echo "Currency normalization complete!";
?>
