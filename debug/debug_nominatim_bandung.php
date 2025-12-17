<?php
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: NexusLogistics/1.0 (admin@nexus.test)\r\n",
        "ignore_errors" => true
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];

$context = stream_context_create($options);
$url = "https://nominatim.openstreetmap.org/search?q=Semarang&format=json&limit=1&addressdetails=1";
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "Error: file_get_contents failed.\n";
} else {
    echo "Response: " . $response . "\n";
}
