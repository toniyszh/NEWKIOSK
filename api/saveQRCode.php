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
    $totalDiscount = $input['totalDiscount'] ?? 0;
    $poNumber = $input['poNumber'] ?? null;
    $freightCharge = $input['freightCharge'] ?? 0;

    if (!$referenceNo) {
        throw new Exception('Reference number is required', 400);
    }

    $qrCodeValue = $referenceNo;


    $checkSql = "SELECT COUNT(*) as count FROM KIOSK_TransactionHold WHERE ReferenceNo = :referenceNo";
    $checkParams = [':referenceNo' => $referenceNo];
    $checkResult = fetch($checkSql, $checkParams, $pdo);

    if ($checkResult && $checkResult->count > 0) {
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

    $pdo->beginTransaction();

    try {

        $sql1 = "INSERT INTO KIOSK_TransactionHold (ReferenceNo, KioskRegNo, CustomerID, DateCreated, QrCodeValue) 
                VALUES (:referenceNo, :kioskRegNo, :customerID, GETDATE(), :qrCodeValue)";

        $params1 = [
            ':referenceNo' => $referenceNo,
            ':kioskRegNo' => $kioskRegNo,
            ':customerID' => $customerID ?: null,
            ':qrCodeValue' => $qrCodeValue
        ];

        $stmt1 = $pdo->prepare($sql1);
        if (!$stmt1->execute($params1)) {
            throw new Exception('Failed to save to KIOSK_TransactionHold', 500);
        }


        $holdNoSql = "SELECT MAX(ISNULL(HoldNo, 0)) + 1 as nextHoldNo FROM TRANSACTIONHOLD";
        $holdNoStmt = $pdo->query($holdNoSql);
        $holdNoResult = $holdNoStmt->fetch(PDO::FETCH_OBJ);
        $holdNo = $holdNoResult->nextHoldNo ?? 1;

        $sql2 = "INSERT INTO TransactionHold (
            ID,
           HoldNo,
            DateTime, 
            RegisterNo, 
            SaleType, 
            TotalDiscount, 
            Cust_AcctNo, 
            PONumber, 
            FreightCharge,
            CashierID,
            Batchno,
            TableNo,
            Sync
        ) VALUES (
        :ID,
        :holdNo,
            GETDATE(), 
            :registerNo, 
            :saleType, 
            :totalDiscount, 
            :custAcctNo, 
            :poNumber, 
            :freightCharge,
            :cashierID,
            :batchno,
            :tableNo,
            :sync
        )";

        $params2 = [
            ':ID' => $referenceNo,
            ':holdNo' => $referenceNo,
            ':registerNo' => 1,
            ':saleType' => 'Retail',
            ':totalDiscount' => 0.00,
            ':custAcctNo' => '',
            ':poNumber' => '',
            ':freightCharge' => $referenceNo,
            ':cashierID' => '1155',
            ':batchno' => 4,
            ':tableNo' => $referenceNo,
            ':sync' => 0
        ];


        $stmt2 = $pdo->prepare($sql2);
        if (!$stmt2->execute($params2)) {
            throw new Exception('Failed to save to TransactionHold', 500);
        }


        $pdo->commit();

        http_response_code(200);
        $response = [
            'success' => true,
            'message' => 'QR code saved successfully to both tables',
            'qrCodeValue' => $referenceNo,
            'data' => [
                'referenceNo' => $referenceNo,
                'kioskRegNo' => $kioskRegNo,
                'customerID' => $customerID,
                'holdNo' => $holdNo
            ]
        ];
    } catch (Exception $e) {

        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
    $response['success'] = false;
}

echo json_encode($response);
exit();
