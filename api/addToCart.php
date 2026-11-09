<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
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

    $kioskRegNo = $kioskRegNo ?: ($data['KioskRegNo'] ?? null);

    if (!$kioskRegNo) throw new Exception('KioskRegNo is required', 500);
    if (empty($data['ReferenceNo'])) throw new Exception('ReferenceNo is required', 406);
    if (empty($data['cart'])) throw new Exception('Cart is empty', 406);

    $referenceNo = $data['ReferenceNo'];
    $orderType = trim(strtolower($data['orderType'] ?? 'dine-in'));

    error_log("DEBUG addToCart.php - ReferenceNo: $referenceNo, OrderType: $orderType");

    $pdo->beginTransaction();


    $sql = "DELETE si FROM KIOSK_TransactionItem_SpecialInstruction si
            INNER JOIN KIOSK_TransactionItem ti ON ti.ID = si.KIOSK_TransactionItem_ID
            WHERE ti.ReferenceNo = :referenceNo AND ti.KioskRegNo = :kioskRegNo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':referenceNo' => $referenceNo, ':kioskRegNo' => $kioskRegNo]);

    $sql = "DELETE FROM KIOSK_TransactionItem WHERE ReferenceNo = :referenceNo AND KioskRegNo = :kioskRegNo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':referenceNo' => $referenceNo, ':kioskRegNo' => $kioskRegNo]);

    // Prepare insert statements
    $sqlItem = "INSERT INTO [KIOSK_TransactionItem] 
        ([KioskRegNo], [ReferenceNo], [ItemLookupCode], [Description], [Quantity], [UnitPriceSold], [ExtendedAmt], [OriginalPrice], [OriginalExtendedAmt], [Taxable], [DateTime], [DiscountCode], [LineDiscount]) 
        VALUES 
        (:kioskRegNo, :referenceNo, :itemLookupCode, :description, :quantity, :unitPriceSold, :extendedAmt, :originalPrice, :originalExtendedAmt, :taxable, GETDATE(), '', 0)";
    $stmtItem = $pdo->prepare($sqlItem);

    $sqlInstruction = "INSERT INTO [KIOSK_TransactionItem_SpecialInstruction] 
        ([KioskRegNo], [KIOSK_TransactionItem_ID], [Instruction]) 
        VALUES 
        (:kioskRegNo, :transactionItemId, :instruction)";
    $stmtInstruction = $pdo->prepare($sqlInstruction);
    $descriptionLimit = columnCharLimit($pdo, 'KIOSK_TransactionItem', 'Description', 50);
    $itemLookupCodeLimit = columnCharLimit($pdo, 'KIOSK_TransactionItem', 'ItemLookupCode', 25);
    $discountCodeLimit = columnCharLimit($pdo, 'KIOSK_TransactionItem', 'DiscountCode', 20);
    $instructionLimit = columnCharLimit($pdo, 'KIOSK_TransactionItem_SpecialInstruction', 'Instruction', 255);

    foreach ($data['cart'] as $item) {
        $isParent    = !($item['is_modifier'] ?? false);
        $discountCode = $isParent ? '' : 'Bundled';

        $description = $item['description'] ?? '';
        $isTakeout = isTakeoutOrderType($orderType);

        error_log("DEBUG item - Code: {$item['item_code']}, isParent: " . ($isParent ? 'true' : 'false') . ", isTakeout: " . ($isTakeout ? 'true' : 'false') . ", description: $description");

        if ($isParent && $isTakeout && $description !== '') {
            $suffix = ' (Take Out)';
            if (stripos($description, $suffix) === false) {
                $description = rtrim($description);
                $description .= ' -' . $suffix;
                error_log("DEBUG - Updated description to: $description");
            }
        }
        if (!$isParent && $description !== '') {
            $modifierSuffix = '--->';
            if (stripos($description, $modifierSuffix) === false) {
                $description = $modifierSuffix . rtrim($description);
            }
        }
        $description = limitText($description, $descriptionLimit);

        $stmtItem = $pdo->prepare("INSERT INTO [KIOSK_TransactionItem] 
        ([KioskRegNo], [ReferenceNo], [ItemLookupCode], [Description], [Quantity], [UnitPriceSold], [ExtendedAmt], [OriginalPrice], [OriginalExtendedAmt], [Taxable], [DateTime], [DiscountCode], [LineDiscount]) 
        VALUES 
        (:kioskRegNo, :referenceNo, :itemLookupCode, :description, :quantity, :unitPriceSold, :extendedAmt, :originalPrice, :originalExtendedAmt, :taxable, GETDATE(), :discountCode, 0)");

        $stmtItem->execute([
            'kioskRegNo'          => $kioskRegNo,
            'referenceNo'         => $referenceNo,
            'itemLookupCode'      => limitText($item['item_code'] ?? '', $itemLookupCodeLimit),
            'description'         => $description,
            'quantity'            => $item['quantity'],
            'unitPriceSold'       => $item['price'],
            'extendedAmt'         => $item['total'],
            'originalPrice'       => $item['total'],
            'originalExtendedAmt' => $item['total'],
            'taxable'             => $item['taxable'],
            'discountCode'        => limitText($discountCode, $discountCodeLimit)
        ]);


        if ($isParent) {
            $parentId = $pdo->lastInsertId();

            if (!empty($item['special_instruction'])) {
                $stmtInstruction->execute([
                    'kioskRegNo'        => $kioskRegNo,
                    'transactionItemId' => $parentId,
                    'instruction'       => limitText($item['special_instruction'], $instructionLimit)
                ]);
            }
        }
    }

    $totals = recomputeRegister($kioskRegNo, $referenceNo, $pdo, $orderType);

    $pdo->commit();

    $response['success'] = true;
    $response['data']    = $totals;
    $response['message'] = 'Item added to cart successfully';
    http_response_code(200);
} catch (\Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
    $response['data']    = [];
}

echo json_encode($response);
exit();
