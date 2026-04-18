<?php
$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

// List of all mangled emoji strings to remove
$mangled = [
    'ðŸ“‹', // Clipboard 📋
    'ðŸ¤–', // Robot 🤖
    'ðŸ”¥', // Trending/Fire 🔥
    'ðŸ“ ', // Pin/Map 📍
    'ðŸ“',  // Pin/Map 📍
    'â ±ï¸', // Clock ⏱️
    'ðŸ¤ ', // Handshake 🤝
    'â ¤ï¸', // Heart ❤️
    'ðŸ’³', // Card 💳
    'ðŸ›¡ï¸', // Shield 🛡️
    'âœ…',   // Checkmark ✅
    'âœˆï¸', // Plane ✈️
    'ðŸŽ¨', // Palette 🎨
    'ðŸšŒ', // Bus 🚌
    'ðŸ ¨', // Hotel 🏨
    'ðŸŒ ', // Globe 🌐
    'ðŸŒ',  // Globe 🌐
    'ðŸ”’', // Lock 🔒
    'ðŸ’¬', // Chat 💬
    'ðŸŸ¢', // Green Circle 🟢
    'ðŸ”´', // Red Circle 🔴
    'ðŸ”µ', // Blue Circle 🔵
    'ðŸ ©', // Hotel/Building 🏨
];

foreach ($files as $file) {
    if ($file->isFile() && ($file->getExtension() === 'html' || $file->getExtension() === 'php') && $file->getFilename() !== 'emoji_cleaner.html') {
        $content = file_get_contents($file->getPathname());
        $newContent = $content;
        
        foreach ($mangled as $m) {
            $newContent = str_replace($m, '', $newContent);
        }
        
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Cleaned symbols in: " . $file->getPathname() . "<br>";
        }
    }
}
echo "Comprehensive cleanup complete!";
?>
