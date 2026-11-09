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

    $referenceNo                = $input['referenceNo'] ?? null;
    $items                      = $input['items'] ?? [];
    $topLevelSpecialInstruction = $input['specialInstruction'] ?? null;

    if (!$referenceNo) {
        throw new Exception('Reference number is required', 400);
    }

    if (empty($items) || !is_array($items)) {
        throw new Exception('Items array is required and cannot be empty', 400);
    }


    $checkSql    = "SELECT th.ID, COUNT(*) as count 
                FROM TransactionHold th
                INNER JOIN KIOSK_TransactionHold kth ON kth.ReferenceNo = th.HoldNo
                WHERE th.HoldNo = :holdNo 
                GROUP BY th.ID";
    $checkParams = [':holdNo' => $referenceNo];
    $checkResult = fetch($checkSql, $checkParams, $pdo);

    if (!$checkResult || $checkResult->count == 0) {
        throw new Exception('Transaction Hold not found', 404);
    }

    $transactionHoldId = $checkResult->ID;

    $pdo->beginTransaction();

    try {
        $savedItems = [];
        $itemCount  = 0;
        $lastParentId = null;

        foreach ($items as $item) {

            $itemCode    = $item['itemCode'] ?? null;
            $description = substr($item['description'] ?? '', 0, 100);
            $qty         = $item['qty'] ?? 0;
            $price       = (float)($item['price'] ?? 0.00);

            if (!$itemCode) {
                throw new Exception('Item code is required for all items', 400);
            }

            $modifiers          = $item['modifiers'] ?? [];
            $totalModifierPrice = 0;
            foreach ($modifiers as $modifier) {
                $totalModifierPrice += (float)($modifier['price'] ?? 0);
            }


            $originalPrice = $price;

            $salesRepNo         = $item['salesRepNo'] ?? null;
            $discountReasonCode = $item['discountReasonCode'] ?? null;
            if ($discountReasonCode === 'Bundled' && $description !== '') {
                $description = '--->' . $description;
            }
            $salesTax           = $item['salesTax'] ?? 0.00;
            $uom                = $item['uom'] ?? 'EA';
            $packingQty         = $item['packingQty'] ?? 1;
            $itemType           = (int)($item['itemType'] ?? 0);
            $lineAddOn          = 0.00;
            $taxExemptAmt       = $item['taxExemptAmt'] ?? 0.00;
            $lineDiscount       = $item['lineDiscount'] ?? 0.00;

            $sync               = $item['sync'] ?? 0;

            if ($discountReasonCode === 'Bundled') {
                $parentId = $lastParentId;
            } else {
                $parentId = $item['parentId'] ?? null;
            }


            $specialInstruction = $item['specialInstruction'] ?? $topLevelSpecialInstruction ?? null;


            $extended = $qty * $price;


            if ($lineDiscount > 0) {
                $extended = $extended - $lineDiscount;
            }

            $originalExtended = $extended;


            $sql = "INSERT INTO TransactionHoldEntries (
                TransactionHold_Id, ItemCode, Description, Qty, Price, Extended,
                SalesRep_no, Discount_ReasonCode, OriginalPrice, OriginalExtendedPrice,
                SalesTax, UOM, PackingQty, Itemtype, LineAddOn, TaxExemptAmt,
                LineDiscount, ParentID, Sync
            ) VALUES (
                :transactionHoldId, :itemCode, :description, :qty, :price, :extended,
                :salesRepNo, :discountReasonCode, :originalPrice, :originalExtendedPrice,
                :salesTax, :uom, :packingQty, :itemType, :lineAddOn, :taxExemptAmt,
                :lineDiscount, :parentId, :sync
            )";

            $params = [
                ':transactionHoldId'     => $transactionHoldId,
                ':itemCode'              => $itemCode,
                ':description'           => $description,
                ':qty'                   => $qty,
                ':price'                 => $price,
                ':extended'              => $extended,
                ':salesRepNo'            => $salesRepNo,
                ':discountReasonCode'    => $discountReasonCode,
                ':originalPrice'         => $originalPrice,
                ':originalExtendedPrice' => $originalExtended,
                ':salesTax'              => $salesTax,
                ':uom'                   => $uom,
                ':packingQty'            => $packingQty,
                ':itemType'              => $itemType,
                ':lineAddOn'             => $lineAddOn,
                ':taxExemptAmt'          => $taxExemptAmt,
                ':lineDiscount'          => $lineDiscount,
                ':parentId'              => $parentId,
                ':sync'                  => $sync
            ];

            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute($params)) {
                throw new Exception("Failed to save item: {$itemCode}", 500);
            }

            $mainItemId = $pdo->lastInsertId();

            if ($discountReasonCode === 'Bundled') {
                $childId = $mainItemId;

                // Update parent ParentID = child ID
                $updateParentStmt = $pdo->prepare("UPDATE TransactionHoldEntries SET ParentID = :childId WHERE ID = :parentId");
                $updateParentStmt->execute([
                    ':childId'  => $childId,
                    ':parentId' => $lastParentId
                ]);


                $updateChildStmt = $pdo->prepare("UPDATE TransactionHoldEntries SET ParentID = :childId WHERE ID = :rowId");
                $updateChildStmt->execute([
                    ':childId' => $childId,
                    ':rowId'   => $childId
                ]);
            }

            if ($discountReasonCode !== 'Bundled') {
                $lastParentId = $mainItemId;


                $selfUpdateStmt = $pdo->prepare("UPDATE TransactionHoldEntries SET ParentID = :id WHERE ID = :id2");
                $selfUpdateStmt->execute([
                    ':id'  => $mainItemId,
                    ':id2' => $mainItemId
                ]);
            }

            if ($discountReasonCode !== 'Bundled') {
                $lastParentId = $mainItemId;
            }

            // Insert special instruction into TransactionHoldModifier
            if ($specialInstruction !== null && trim((string)$specialInstruction) !== '') {
                $siSql = "INSERT INTO TransactionHoldModifier (HoldNo, ItemLookupCode, Modifier, THE_ID)
                          VALUES (:holdNo, :itemLookupCode, :modifier, :theId)";

                $siParams = [
                    ':holdNo'         => $transactionHoldId,  // use the same numeric ID, no extra query needed
                    ':itemLookupCode' => $itemCode,
                    ':modifier'       => substr(trim((string)$specialInstruction), 0, 255),
                    ':theId'          => $mainItemId
                ];

                $siStmt = $pdo->prepare($siSql);
                if (!$siStmt->execute($siParams)) {
                    throw new Exception("Failed to save special instruction for item: {$itemCode}", 500);
                }
            }

            $itemCount++;
            $itemDetails = [
                'itemCode'           => $itemCode,
                'description'        => $description,
                'qty'                => $qty,
                'price'              => $price,
                'extended'           => $extended,
                'specialInstruction' => $specialInstruction,
                'modifiers'          => []
            ];

            // Insert modifiers as child rows
            if (!empty($modifiers)) {
                foreach ($modifiers as $modifier) {
                    $modifierName     = $modifier['name'] ?? 'Add-on';
                    $modifierPrice    = (float)($modifier['price'] ?? 0);
                    $modifierExtended = $modifierPrice * $qty;
                    $modifierDescription = '--->' . substr($modifierName, 0, 98);


                    // Look up modifier item code from Item table
                    try {
                        $modifierCodeSql  = "SELECT TOP 1 ItemLookupCode FROM [dbo].[Item] WHERE Description = :description";
                        $modifierCodeStmt = $pdo->prepare($modifierCodeSql);
                        $modifierCodeStmt->execute([':description' => $modifierName]);
                        $modifierCodeResult = $modifierCodeStmt->fetch(PDO::FETCH_OBJ);
                        $modifierItemCode   = $modifierCodeResult ? $modifierCodeResult->ItemLookupCode : '';
                    } catch (Exception $lookupEx) {
                        $modifierItemCode = '';
                    }

                    // Fallback to parent item code if not found
                    if (empty($modifierItemCode)) {
                        $modifierItemCode = $itemCode;
                    }

                    $modifierSql = "INSERT INTO TransactionHoldEntries (
                        TransactionHold_Id, ItemCode, Description, Qty, Price, Extended,
                        SalesRep_no, Discount_ReasonCode, OriginalPrice, OriginalExtendedPrice,
                        SalesTax, UOM, PackingQty, Itemtype, LineAddOn, TaxExemptAmt,
                        LineDiscount, ParentID, Sync
                    ) VALUES (
                        :transactionHoldId, :itemCode, :description, :qty, :price, :extended,
                        :salesRepNo, :discountReasonCode, :originalPrice, :originalExtendedPrice,
                        :salesTax, :uom, :packingQty, :itemType, :lineAddOn, :taxExemptAmt,
                        :lineDiscount, :parentId, :sync
                    )";

                    $modifierParams = [
                        ':transactionHoldId'     => $transactionHoldId,
                        ':itemCode'              => $modifierItemCode,
                        ':description'           => $modifierDescription,
                        ':qty'                   => $qty,
                        ':price'                 => $modifierPrice,
                        ':extended'              => $modifierExtended,
                        ':salesRepNo'            => $salesRepNo,
                        ':discountReasonCode'    => $discountReasonCode,
                        ':originalPrice'         => $modifierPrice,
                        ':originalExtendedPrice' => $modifierExtended,
                        ':salesTax'              => 0.00,
                        ':uom'                   => 'EA',
                        ':packingQty'            => 1,
                        ':itemType'              => 1,
                        ':lineAddOn'             => 0.00,
                        ':taxExemptAmt'          => 0.00,
                        ':lineDiscount'          => 0.00,
                        ':parentId'              => $mainItemId,
                        ':sync'                  => $sync
                    ];

                    $modifierStmt = $pdo->prepare($modifierSql);
                    if (!$modifierStmt->execute($modifierParams)) {
                        throw new Exception("Failed to save modifier: {$modifierName}", 500);
                    }

                    $itemDetails['modifiers'][] = [
                        'name'     => $modifierName,
                        'price'    => $modifierPrice,
                        'quantity' => $qty,
                        'extended' => $modifierExtended
                    ];
                }
            }

            $savedItems[] = $itemDetails;
        }

        $pdo->commit();

        http_response_code(200);
        $response = [
            'success'    => true,
            'message'    => "Successfully saved {$itemCount} item(s) to TransactionHoldEntries",
            'savedItems' => $savedItems,
            'data'       => [
                'referenceNo'       => $referenceNo,
                'transactionHoldId' => $transactionHoldId,
                'itemCount'         => $itemCount
            ],
            'debug_items_order' => array_map(fn($i) => [
                'itemCode'           => $i['itemCode'],
                'description'        => $i['description'],
                'discountReasonCode' => $i['discountReasonCode'] ?? null,
            ], $items)
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    $errorCode = $e->getCode();
    http_response_code(is_numeric($errorCode) && $errorCode > 0 ? (int)$errorCode : 500);
    $response['message'] = $e->getMessage();
    $response['success'] = false;
}

echo json_encode($response);
exit();
