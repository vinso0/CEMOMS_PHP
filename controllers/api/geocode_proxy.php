<?php
// controllers/api/geocode_proxy.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$lat = $_GET['lat'] ?? null;
$lng = $_GET['lng'] ?? null;

if (!$lat || !$lng) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing lat or lng parameter']);
    exit;
}

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";

$context = stream_context_create([
    'http' => [
        'header' => [
            'User-Agent: CEMOMS-PHP/1.0',
            'Accept: application/json'
        ],
        'timeout' => 5
    ]
]);

$result = file_get_contents($url, false, $context);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch geocoding data']);
} else {
    echo $result;
}
?>
