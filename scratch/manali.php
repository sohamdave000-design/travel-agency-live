<?php
$titles = ["Manali hotel", "Manali resort exterior", "hotel in Manali"];
foreach($titles as $query) {
    $url = "https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($query) . "&srnamespace=6&format=json&srlimit=3";
    $json = file_get_contents($url, false, stream_context_create(["http" => ["header" => "User-Agent: MyTestBot/1.0"]]));
    $res = json_decode($json, true);
    if(isset($res['query']['search'])) {
        foreach($res['query']['search'] as $item) {
            $url2 = "https://commons.wikimedia.org/w/api.php?action=query&titles=" . urlencode($item['title']) . "&prop=imageinfo&iiprop=url&iiurlwidth=1200&format=json";
            $json2 = file_get_contents($url2, false, stream_context_create(["http" => ["header" => "User-Agent: MyTestBot/1.0"]]));
            $res2 = json_decode($json2, true);
            if(isset($res2['query']['pages'])) {
                foreach($res2['query']['pages'] as $page) {
                    if(isset($page['imageinfo'][0]['thumburl'])) {
                        echo "{$query} -> " . $page['imageinfo'][0]['thumburl'] . "\n";
                    }
                }
            }
        }
    }
}
