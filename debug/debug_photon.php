<?php
$url = "https://photon.komoot.io/api/?q=Bandung&limit=5";
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: NexusLogistics/1.0\r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
echo $response;
