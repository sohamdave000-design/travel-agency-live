<?php
$url = "https://en.wikipedia.org/w/api.php?action=query&titles=The_Leela_Palaces,_Hotels_and_Resorts&prop=images&format=json";
$json = file_get_contents($url, false, stream_context_create([
    "http" => ["header" => "User-Agent: MyTestBot/1.0"]
]));
$res = json_decode($json, true);
if(isset($res['query']['pages'])) {
    foreach($res['query']['pages'] as $page) {
        if (isset($page['images'])) {
            foreach($page['images'] as $img) {
                echo $img['title'] . "\n";
            }
        }
    }
}
