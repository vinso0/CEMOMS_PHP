<?php
// controllers/api/geocode_proxy.php

// CORS and JSON headers for the browser response
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Validate inputs
$lat = isset($_GET['lat']) ? trim($_GET['lat']) : null;
$lng = isset($_GET['lng']) ? trim($_GET['lng']) : null;

if ($lat === null || $lng === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing lat or lng parameter']);
    exit;
}

// Build Nominatim URL (HTTPS)
$nominatimUrl = sprintf(
    'https://nominatim.openstreetmap.org/reverse?format=json&lat=%s&lon=%s&zoom=18&addressdetails=1',
    urlencode($lat),
    urlencode($lng)
);

// Use curl for better control (timeouts + headers)
$ch = curl_init($nominatimUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => false,
    CURLOPT_TIMEOUT => 5,                // seconds
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_HTTPHEADER => [
        'User-Agent: CEMOMS-PHP/1.0',    // REQUIRED by Nominatim usage policy
        'Accept: application/json'
    ],
]);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

// Handle curl/network errors
if ($result === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach geocoding service', 'detail' => $curlErr]);
    exit;
}

// Pass through successful JSON or wrap error
if ($httpCode >= 200 && $httpCode < 300) {
    // Optionally sanitize/validate JSON here
    echo $result;
} else {
    http_response_code($httpCode ?: 502);
    echo json_encode(['error' => 'Geocoding service error', 'status' => $httpCode, 'raw' => $result]);
}
