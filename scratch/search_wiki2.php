<?php
$titles = ["The Leela Palace New Delhi", "Treebo Trend Manali", "Leela Palace Delhi", "The Leela Palace exterior"];

foreach($titles as $query) {
    $url = "https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($query) . "&srnamespace=6&format=json&srlimit=3";
    $json = file_get_contents($url, false, stream_context_create([
        "http" => ["header" => "User-Agent: MyTestBot/1.0"]
    ]));
    $res = json_decode($json, true);
    echo "Results for {$query}:\n";
    if(isset($res['query']['search'])) {
        foreach($res['query']['search'] as $item) {
            echo " - " . $item['title'] . "\n";
        }
    }
}
