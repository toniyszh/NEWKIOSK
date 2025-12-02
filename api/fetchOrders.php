<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'An error occurred',
    'data' => []
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode($response);
    exit();
}

try {
    require_once __DIR__ . '/connect.php';

    $json = file_get_contents('php://input');

    if (empty($json)) throw new Exception('No data received');

    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) throw new Exception('Invalid JSON: ' . json_last_error_msg());
    if (empty($data['referenceNo'])) throw new Exception('ReferenceNo is required', 406);
    if (!$kioskRegNo) throw new Exception('KioskRegNo is required', 500);

    $referenceNo = $data['referenceNo'];

    // Fetch order items
    $sql = "SELECT 
                [ItemLookupCode],
                [Description],
                [Quantity],
                [UnitPriceSold],
                [ExtendedAmt],
                [OriginalPrice],
                [OriginalExtendedAmt],
                [Taxable],
                [LineDiscount]
            FROM [KIOSK_TransactionItem]
            WHERE [ReferenceNo] = :referenceNo AND [KioskRegNo] = :kioskRegNo
            ORDER BY [DateTime] ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':referenceNo' => $referenceNo,
        ':kioskRegNo' => $kioskRegNo
    ]);

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        throw new Exception('No items found for this order', 404);
    }

    // Calculate totals
    $subtotal = 0;
    $totalTaxable = 0;
    $totalQuantity = 0;

    foreach ($items as $item) {
        $subtotal += $item['ExtendedAmt'];
        if ($item['Taxable']) {
            $totalTaxable += $item['ExtendedAmt'];
        }
        $totalQuantity += $item['Quantity'];
    }

    $serviceCharge = $subtotal * 0.10;
    $vat = $totalTaxable * 0.12;
    $grandTotal = $subtotal + $serviceCharge + $vat;

    $response['success'] = true;
    $response['data'] = [
        'referenceNo' => $referenceNo,
        'items' => $items,
        'summary' => [
            'subtotal' => round($subtotal, 2),
            'serviceCharge' => round($serviceCharge, 2),
            'vat' => round($vat, 2),
            'grandTotal' => round($grandTotal, 2),
            'totalItems' => $totalQuantity
        ]
    ];
    $response['message'] = 'Order retrieved successfully';
    http_response_code(200);
} catch (\Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
    $response['data'] = [];
}

echo json_encode($response);
exit();
