<?php
// Allow only valid plugin slugs
$plugins = [
    'goodsoul-core' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/goodsoul-core.zip',
    'goodsoul-demo-installer' => 'https://my.smartdatasoft.com/wp-content/uploads/envato-products/goodsoul-demo-installer.zip',
];

// Validate request
if (!isset($_GET['plugin']) || !array_key_exists($_GET['plugin'], $plugins)) {
    http_response_code(403);
    exit('Invalid plugin request.');
}

$plugin_slug = $_GET['plugin'];
$plugin_url  = $plugins[$plugin_slug];
$plugin_data = false;

// Try file_get_contents if allow_url_fopen is enabled
if (ini_get('allow_url_fopen')) {
    $plugin_data = @file_get_contents($plugin_url);
}

// If file_get_contents fails, fallback to cURL
if (!$plugin_data) {
    $ch = curl_init($plugin_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $plugin_data = curl_exec($ch);
    $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if cURL succeeded
    if ($plugin_data === false || $http_code !== 200) {
        http_response_code(404);
        exit('Plugin not found or download failed.');
    }
}

// Clean all output buffers to prevent corruption
while (ob_get_level()) {
    ob_end_clean();
}

// Send proper headers for ZIP download
header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=\"{$plugin_slug}.zip\"");
header('Content-Length: ' . strlen($plugin_data));

// Output the file content
echo $plugin_data;
exit;