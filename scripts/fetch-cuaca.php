<?php
$url = "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-JawaBarat.json";
$json = file_get_contents($url);
$data = json_decode($json, true);

$features = [];

foreach ($data['weather']['forecast']['area'] as $area) {
    $nama = $area['name'][0]['#text'];
    $lat = floatval($area['latitude']);
    $lon = floatval($area['longitude']);

    $curah = $area['parameter'][8]['timerange'][0]['value'][0]['#text'] ?? null;
    $angin = $area['parameter'][10]['timerange'][0]['value'][0]['#text'] ?? null;

    $banjir = "rendah";
    if ($curah >= 50) $banjir = "tinggi";
    elseif ($curah >= 20) $banjir = "sedang";

    $features[] = [
        "type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [$lon, $lat]
        ],
        "properties" => [
            "kota" => $nama,
            "curah_hujan" => floatval($curah),
            "kecepatan_angin" => floatval($angin),
            "rawan_banjir" => $banjir
        ]
    ];
}

$geojson = [
    "type" => "FeatureCollection",
    "features" => $features
];

file_put_contents(__DIR__ . '/../data/prakiraan.geojson', json_encode($geojson, JSON_PRETTY_PRINT));
echo "GeoJSON updated.";
?>
