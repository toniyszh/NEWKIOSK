<?php

$slot = $_GET['s'] ?? '';
$allowedSlots = ['slot1', 'slot2', 'slot3'];

if (!in_array($slot, $allowedSlots, true)) {
    http_response_code(404);
    exit;
}

$assetDir = realpath(__DIR__ . '/../images/adsBanner');
if ($assetDir === false) {
    http_response_code(404);
    exit;
}

$files = glob($assetDir . DIRECTORY_SEPARATOR . $slot . '_*.*');
if (!$files) {
    http_response_code(404);
    exit;
}

usort($files, function ($a, $b) {
    $timeDiff = filemtime($b) - filemtime($a);
    return $timeDiff !== 0 ? $timeDiff : strcmp($b, $a);
});

$file = realpath($files[0]);
if ($file === false || strpos($file, $assetDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($file)) {
    http_response_code(404);
    exit;
}

$mime = function_exists('mime_content_type') ? mime_content_type($file) : false;
if (!$mime || strpos($mime, 'image/') !== 0) {
    $mime = 'application/octet-stream';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache, must-revalidate');
readfile($file);
