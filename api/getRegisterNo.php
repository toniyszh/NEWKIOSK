<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Load the config file
    $config = require_once __DIR__ . '/../config/config.php';

    if (!isset($config['register_no'])) {
        throw new Exception('Register number not found in configuration');
    }

    $response = [
        'success' => true,
        'data' => [
            'register_no' => (int) $config['register_no']
        ],
        'message' => 'Register number retrieved successfully'
    ];

    http_response_code(200);
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
    ]);
}

exit();
