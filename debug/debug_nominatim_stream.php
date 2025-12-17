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
$url = "https://nominatim.openstreetmap.org/search?q=Jakarta&format=json&limit=10&addressdetails=1";
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "Error: file_get_contents failed.\n";
    print_r(error_get_last());
} else {
    echo "Response: " . substr($response, 0, 500) . "\n";
}
