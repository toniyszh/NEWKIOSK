<?php

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$versionFile = dirname(__DIR__) . '/cache/kiosk_content_version.txt';
$version = is_file($versionFile) ? trim((string) file_get_contents($versionFile)) : '0';

echo json_encode([
    'version' => $version !== '' ? $version : '0',
]);
