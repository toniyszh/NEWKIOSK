<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = [
    'success' => false,
    'message' => 'An error occurred',
    'savedItems' => []
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
    $items = $input['items'] ?? [];

    if (!$referenceNo) {
        throw new Exception('Reference number is required', 400);
    }

    // Use referenceNo as transactionHoldId
    $transactionHoldId = $referenceNo;

    if (empty($items) || !is_array($items)) {
        throw new Exception('Items array is required and cannot be empty', 400);
    }

    // Verify TransactionHold exists
    $checkSql = "SELECT COUNT(*) as count FROM TransactionHold WHERE HoldNo = :holdNo";
    $checkParams = [':holdNo' => $transactionHoldId];
    $checkResult = fetch($checkSql, $checkParams, $pdo);

    if (!$checkResult || $checkResult->count == 0) {
        throw new Exception('Transaction Hold not found', 404);
    }

    $pdo->beginTransaction();

    try {
        $savedItems = [];
        $itemCount = 0;

        foreach ($items as $item) {
            // Validate required fields
            $itemCode = $item['itemCode'] ?? null;
            $description = $item['description'] ?? '';
            $qty = $item['qty'] ?? 0;
            $price = $item['price'] ?? 0.00;

            if (!$itemCode) {
                throw new Exception('Item code is required for all items', 400);
            }

            // Calculate extended price
            $extended = $qty * $price;
            $originalExtended = $extended;

            // Optional fields
            $salesRepNo = $item['salesRepNo'] ?? null;
            $discountReasonCode = $item['discountReasonCode'] ?? null;
            $originalPrice = $item['originalPrice'] ?? $price;
            $salesTax = $item['salesTax'] ?? 0.00;
            $uom = $item['uom'] ?? 'EA';
            $packingQty = $item['packingQty'] ?? 1;
            $itemType = $item['itemType'] ?? 'Regular';
            $lineAddOn = $item['lineAddOn'] ?? 0.00;
            $taxExemptAmt = $item['taxExemptAmt'] ?? 0.00;
            $lineDiscount = $item['lineDiscount'] ?? 0.00;
            $parentId = $item['parentId'] ?? null;
            $sync = $item['sync'] ?? 0;

            // Adjust extended price if there's a line discount
            if ($lineDiscount > 0) {
                $extended = $extended - $lineDiscount;
            }

            $sql = "INSERT INTO TransactionHoldEntries (
                TransactionHold_Id,
                ItemCode,
                Description,
                Qty,
                Price,
                Extended,
                SalesRep_no,
                Discount_ReasonCode,
                OriginalPrice,
                OriginalExtendedPrice,
                SalesTax,
                UOM,
                PackingQty,
                Itemtype,
                LineAddOn,
                TaxExemptAmt,
                LineDiscount,
                ParentID,
                Sync
            ) VALUES (
                :transactionHoldId,
                :itemCode,
                :description,
                :qty,
                :price,
                :extended,
                :salesRepNo,
                :discountReasonCode,
                :originalPrice,
                :originalExtendedPrice,
                :salesTax,
                :uom,
                :packingQty,
                :itemType,
                :lineAddOn,
                :taxExemptAmt,
                :lineDiscount,
                :parentId,
                :sync
            )";

            $params = [
                ':transactionHoldId' => $transactionHoldId,
                ':itemCode' => $itemCode,
                ':description' => $description,
                ':qty' => $qty,
                ':price' => $price,
                ':extended' => $extended,
                ':salesRepNo' => $salesRepNo,
                ':discountReasonCode' => $discountReasonCode,
                ':originalPrice' => $originalPrice,
                ':originalExtendedPrice' => $originalExtended,
                ':salesTax' => $salesTax,
                ':uom' => $uom,
                ':packingQty' => $packingQty,
                ':itemType' => $itemType,
                ':lineAddOn' => $lineAddOn,
                ':taxExemptAmt' => $taxExemptAmt,
                ':lineDiscount' => $lineDiscount,
                ':parentId' => $parentId,
                ':sync' => $sync
            ];

            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute($params)) {
                throw new Exception("Failed to save item: {$itemCode}", 500);
            }

            $itemCount++;
            $savedItems[] = [
                'itemCode' => $itemCode,
                'description' => $description,
                'qty' => $qty,
                'price' => $price,
                'extended' => $extended
            ];
        }

        $pdo->commit();

        http_response_code(200);
        $response = [
            'success' => true,
            'message' => "Successfully saved {$itemCount} item(s) to TransactionHoldEntries",
            'savedItems' => $savedItems,
            'data' => [
                'referenceNo' => $referenceNo,
                'transactionHoldId' => $transactionHoldId,
                'itemCount' => $itemCount
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
