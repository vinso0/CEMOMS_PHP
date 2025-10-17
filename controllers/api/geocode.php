<?php
// controllers/api/geocode.php

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (empty($query)) {
    echo json_encode(['error' => 'Query parameter is required']);
    exit;
}

// Clean and format the query
$query = trim($query);

// Always add Caloocan to the search to narrow results
if (!stripos($query, 'caloocan')) {
    $query .= ", Caloocan City, Metro Manila, Philippines";
}

// Setup the search URL with better parameters
$url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
    'format' => 'json',
    'q' => $query,
    'countrycodes' => 'ph',
    'limit' => 10,
    'addressdetails' => 1,
    'accept-language' => 'en',
    'bounded' => 0, // Don't restrict to viewport
    'dedupe' => 1 // Remove duplicate results
]);

// Make the request with proper headers
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'CEMOMS/1.0 (https://cemoms.local)');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'Failed to fetch results: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Process results
$results = json_decode($response, true) ?: [];

// Filter and format results
$formatted = [];
$seen = []; // Track unique addresses

foreach ($results as $item) {
    if (!isset($item['address']) || !isset($item['lat']) || !isset($item['lon'])) {
        continue;
    }

    $address = $item['address'];
    
    // Build a comprehensive address
    $parts = [];
    
    // Add house number and road
    if (isset($address['house_number'])) {
        $parts[] = $address['house_number'];
    }
    
    if (isset($address['road'])) {
        $parts[] = $address['road'];
    } elseif (isset($address['street'])) {
        $parts[] = $address['street'];
    }
    
    // Add suburb or neighbourhood
    if (isset($address['suburb'])) {
        $parts[] = $address['suburb'];
    } elseif (isset($address['neighbourhood'])) {
        $parts[] = $address['neighbourhood'];
    }
    
    // Add barangay
    if (isset($address['village'])) {
        $parts[] = 'Brgy. ' . $address['village'];
    } elseif (isset($address['quarter'])) {
        $parts[] = 'Brgy. ' . $address['quarter'];
    }
    
    // Always add city
    if (isset($address['city'])) {
        $parts[] = $address['city'];
    } elseif (isset($address['municipality'])) {
        $parts[] = $address['municipality'];
    }
    
    // Add province/state
    if (isset($address['state'])) {
        $parts[] = $address['state'];
    }
    
    $displayName = implode(', ', array_filter($parts));
    
    // Skip if empty or duplicate
    if (empty($displayName) || isset($seen[$displayName])) {
        continue;
    }
    
    $seen[$displayName] = true;
    
    $formatted[] = [
        'display_name' => $displayName,
        'lat' => $item['lat'],
        'lon' => $item['lon'],
        'type' => $item['type'] ?? 'location',
        'importance' => $item['importance'] ?? 0
    ];
}

// Sort by importance (more important locations first)
usort($formatted, function($a, $b) {
    return $b['importance'] <=> $a['importance'];
});

// Limit to top 5 results
$formatted = array_slice($formatted, 0, 5);

echo json_encode($formatted);