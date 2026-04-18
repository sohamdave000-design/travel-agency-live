<?php
function getWikiImage($query) {
    // try to get from wikipedia
    $url = "https://en.wikipedia.org/w/api.php?action=query&titles=" . urlencode($query) . "&prop=pageimages&format=json&pithumbsize=1200";
    $json = file_get_contents($url, false, stream_context_create([
        "http" => ["header" => "User-Agent: MyTestBot/1.0"]
    ]));
    $res = json_decode($json, true);
    if(isset($res['query']['pages'])) {
        foreach($res['query']['pages'] as $page) {
            if(isset($page['thumbnail']['source'])) {
                return $page['thumbnail']['source'];
            }
        }
    }
    return null;
}

$hotels = [
    "Taj Mahal Palace Hotel", 
    "The Leela Palace New Delhi", 
    "Umaid Bhawan Palace", 
    "Lemon Tree Premier, Jaipur", 
    "Radisson Blu Resort Goa Cavelossim Beach", 
    "Ginger Hotel Bangalore", 
    "Treebo Trend Manali"
];

foreach ($hotels as $h) {
    echo $h . " => " . getWikiImage($h) . "\n";
}
