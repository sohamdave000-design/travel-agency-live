<?php
function searchWikiCommons($query) {
    $url = "https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($query) . "&srnamespace=6&format=json&srlimit=1";
    $json = file_get_contents($url, false, stream_context_create([
        "http" => ["header" => "User-Agent: MyTestBot/1.0"]
    ]));
    $res = json_decode($json, true);
    if(isset($res['query']['search'][0]['title'])) {
        $title = $res['query']['search'][0]['title'];
        // Get image URL
        $url2 = "https://commons.wikimedia.org/w/api.php?action=query&titles=" . urlencode($title) . "&prop=imageinfo&iiprop=url&iiurlwidth=1200&format=json";
        $json2 = file_get_contents($url2, false, stream_context_create([
            "http" => ["header" => "User-Agent: MyTestBot/1.0"]
        ]));
        $res2 = json_decode($json2, true);
        if(isset($res2['query']['pages'])) {
            foreach($res2['query']['pages'] as $page) {
                if(isset($page['imageinfo'][0]['thumburl'])) {
                    return $page['imageinfo'][0]['thumburl'];
                }
            }
        }
    }
    return null;
}

$queries = [
    "Taj Mahal Palace exterior",
    "The Leela Palace New Delhi exterior",
    "Umaid Bhawan exterior",
    "Lemon Tree Hotel exterior",
    "Radisson Blu Resort Goa exterior",
    "Ginger Hotel exterior",
    "Manali hotel exterior"
];

foreach ($queries as $q) {
    echo $q . " => " . searchWikiCommons($q) . "\n";
}
