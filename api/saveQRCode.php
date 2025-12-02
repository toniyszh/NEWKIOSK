<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'An error occurred',
    'qrCodeValue' => null
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode($response);
    exit();
}

try {
    require_once __DIR__ . '/connect.php';


    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON input', 400);
    }

    $referenceNo = $input['referenceNo'] ?? null;
    $customerID = $input['customerID'] ?? null;
    $items = $input['items'] ?? [];
    $kioskRegNo = $input['kioskRegNo'] ?? $config['kioskRegNo'] ?? 'KIOSK001';

    if (!$referenceNo) {
        throw new Exception('Reference number is required', 400);
    }


    $qrCodeValue = $referenceNo;

    // Check if record already exists
    $checkSql = "SELECT COUNT(*) as count FROM KIOSK_TransactionHold WHERE ReferenceNo = :referenceNo";
    $checkParams = [':referenceNo' => $referenceNo];
    $checkResult = fetch($checkSql, $checkParams, $pdo);

    if ($checkResult && $checkResult->count > 0) {
        // Record already exists, just return success
        http_response_code(200);
        $response = [
            'success' => true,
            'message' => 'QR code already saved',
            'qrCodeValue' => $referenceNo,
            'data' => [
                'referenceNo' => $referenceNo,
                'kioskRegNo' => $kioskRegNo,
                'customerID' => $customerID
            ]
        ];
        echo json_encode($response);
        exit();
    }

    $sql = "INSERT INTO KIOSK_TransactionHold (ReferenceNo, KioskRegNo, CustomerID, DateCreated, QrCodeValue) 
            VALUES (:referenceNo, :kioskRegNo, :customerID, GETDATE(), :qrCodeValue)";

    $params = [
        ':referenceNo' => $referenceNo,
        ':kioskRegNo' => $kioskRegNo,
        ':customerID' => $customerID ?: null,
        ':qrCodeValue' => $qrCodeValue
    ];

    $stmt = $pdo->prepare($sql);

    if (!$stmt->execute($params)) {
        throw new Exception('Failed to save QR code to database', 500);
    }

    http_response_code(200);
    $response = [
        'success' => true,
        'message' => 'QR code saved successfully',
        'qrCodeValue' => $referenceNo,
        'data' => [
            'referenceNo' => $referenceNo,
            'kioskRegNo' => $kioskRegNo,
            'customerID' => $customerID
        ]
    ];
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
    $response['success'] = false;
}

echo json_encode($response);
exit();
