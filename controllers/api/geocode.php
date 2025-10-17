<?php

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (empty($query)) {
    echo json_encode(['error' => 'Query parameter is required']);
    exit;
}

// Clean and format the query
$query = trim($query);
if (!str_contains(strtolower($query), 'caloocan')) {
    $query .= ", Caloocan";
}
if (!str_contains(strtolower($query), 'philippines')) {
    $query .= ", Philippines";
}

// Setup the search URL
$url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
    'format' => 'json',
    'q' => $query,
    'countrycodes' => 'ph',
    'limit' => 10,
    'addressdetails' => 1,
    'accept-language' => 'en'
]);

// Make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'CEMOMS/1.0');
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
$formatted = array_values(array_filter(array_map(function($item) {
    if (!isset($item['address'])) {
        return null;
    }

    $address = $item['address'];
    
    // Only include results in Caloocan
    if (!isset($address['city']) || 
        !str_contains(strtolower($address['city']), 'caloocan')) {
        return null;
    }

    // Build address parts
    $parts = [];
    
    if (isset($address['house_number'])) {
        $parts[] = $address['house_number'];
    }
    
    if (isset($address['building'])) {
        $parts[] = $address['building'];
    }
    
    if (isset($address['road'])) {
        $parts[] = $address['road'];
    }
    
    if (isset($address['suburb'])) {
        $parts[] = $address['suburb'];
    }
    
    if (isset($address['barangay'])) {
        $parts[] = 'Barangay ' . $address['barangay'];
    }
    
    $parts[] = 'Caloocan City';
    
    return [
        'display_name' => implode(', ', array_filter($parts)),
        'lat' => $item['lat'],
        'lon' => $item['lon'],
        'type' => $item['type']
    ];
    
}, $results)));

// If no results found, return empty array
if (empty($formatted)) {
    echo json_encode([]);
    exit;
}

echo json_encode($formatted);