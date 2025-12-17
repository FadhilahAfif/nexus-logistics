<?php
$url = "https://nominatim.openstreetmap.org/search?q=Jakarta&format=json&limit=10&addressdetails=1";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'NexusLogistics/1.0 (admin@nexus.test)');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $info['http_code'] . "\n";
if ($error) {
    echo "Curl Error: " . $error . "\n";
}
echo "Response: " . substr($output, 0, 500) . "\n";
