<?php

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/connect.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    $output = [];
    $output['php_version'] = phpversion();
    $output['config_loaded'] = isset($config);

    if (isset($config)) {
        $output['printer_names'] = $config['printer_names'] ?? 'not set';
    }

    // Test escpos library
    try {
        $reflectionClass = new ReflectionClass('\Mike42\Escpos\Printer');
        $output['escpos_available'] = true;
        $output['escpos_path'] = $reflectionClass->getFileName();
    } catch (Throwable $e) {
        $output['escpos_available'] = false;
        $output['escpos_error'] = $e->getMessage();
    }

    // Test network connector
    try {
        $reflectionClass = new ReflectionClass('\Mike42\Escpos\Network');
        $output['network_connector_available'] = true;
    } catch (Throwable $e) {
        $output['network_connector_available'] = false;
    }

    // Test Windows connector
    try {
        $reflectionClass = new ReflectionClass('\Mike42\Escpos\PrintConnectors\WindowsPrintConnector');
        $output['windows_connector_available'] = true;
    } catch (Throwable $e) {
        $output['windows_connector_available'] = false;
    }

    // Test File connector
    try {
        $reflectionClass = new ReflectionClass('\Mike42\Escpos\PrintConnectors\FilePrintConnector');
        $output['file_connector_available'] = true;
    } catch (Throwable $e) {
        $output['file_connector_available'] = false;
    }

    // Test Gfx Image
    try {
        $reflectionClass = new ReflectionClass('\Gfx\Image');
        $output['gfx_available'] = true;
    } catch (Throwable $e) {
        $output['gfx_available'] = false;
    }

    $output['temp_dir'] = sys_get_temp_dir();
    $output['temp_dir_writable'] = is_writable(sys_get_temp_dir());

    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}
