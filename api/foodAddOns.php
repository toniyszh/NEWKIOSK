<?php
require_once dirname(__DIR__) . '/config/connection.php';

$parentItemCode = isset($_GET['parentItemCode']) ? $_GET['parentItemCode'] : '';
$categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : '';

if (empty($parentItemCode) && empty($categoryId)) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'parentItemCode or categoryId is required']);
    exit;
}

$folderPath = __DIR__ . '/../images/menus/';
$files = is_dir($folderPath) ? scandir($folderPath) : [];

function resolveImage($row, $files)
{
    $itemName = $row['Description'] ?? '';
    $imagePath = "images/menus/10.png";

    if (!empty($row['PictureName'])) {
        $candidate = "images/menus/" . $row['PictureName'];
        if (file_exists(__DIR__ . '/../' . $candidate)) {
            return $candidate;
        }
    } else {
        foreach ($files as $file) {
            $fileNoExt = pathinfo($file, PATHINFO_FILENAME);
            if (strcasecmp($fileNoExt, $itemName) === 0) {
                return "images/menus/" . $file;
            }
        }
    }

    return $imagePath;
}

// If categoryId is provided, fetch items by category (for customization)
if (!empty($categoryId)) {
    $result = [
        'Name'        => 'Customization Options',
        'Description' => 'Customization Options',
        'Price'       => 0,
        'addOns'      => [],
    ];

    $categoryQuery = "
        SELECT i.[ID], i.[ItemLookupCode], i.[Description], i.[Price], i.[PictureName]
        FROM [dbo].[Item] i
        WHERE i.[CategoryID] = 18 AND i.[Inactive] = 0
        AND i.[ItemLookupCode] NOT IN ('100014', '100015')
        ORDER BY i.[OrderNo]
    ";
    $categoryStmt = sqlsrv_query($conn, $categoryQuery);

    if ($categoryStmt) {
        while ($row = sqlsrv_fetch_array($categoryStmt, SQLSRV_FETCH_ASSOC)) {
            $childImage = resolveImage($row, $files);

            $result['addOns'][] = [
                'ItemCode'    => $row['ItemLookupCode'] ?? '',
                'ItemOption'  => $row['ItemLookupCode'] ?? '',
                'Qty'         => 1,
                'Name'        => $row['Description'] ?? '',
                'Description' => $row['Description'] ?? '',
                'Price'       => $row['Price'] ?? 0,
                'image'       => $childImage,
            ];
        }
    }

    header("Content-Type: application/json");
    echo json_encode($result);
    exit;
}

// Otherwise, fetch bundled items by parent item code (for add-ons)
if (empty($parentItemCode)) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'parentItemCode is required']);
    exit;
}

$parentQuery = "SELECT * FROM [dbo].[Item] WHERE ItemLookupCode = ?";
$parentStmt  = sqlsrv_query($conn, $parentQuery, [$parentItemCode]);

if (!$parentStmt || !($parentRow = sqlsrv_fetch_array($parentStmt, SQLSRV_FETCH_ASSOC))) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Parent item not found']);
    exit;
}

$parentImage = resolveImage($parentRow, $files);

$result = [
    'Name'        => $parentRow['Description'] ?? '',
    'Modifier'    => $parentRow['Description'] ?? '',
    'Description' => $parentRow['Description'] ?? '',
    'Price'       => $parentRow['Price'] ?? 0,
    'image'       => $parentImage,
    'addOns'      => [],
];


$bundleQuery = "
    SELECT b.ItemCode, b.ItemOption, b.Qty, i.*
    FROM [dbo].[BundledItemsOptions] b
    INNER JOIN [dbo].[Item] i ON i.ItemLookupCode = b.ItemOption
    WHERE b.Parent_Itemcode = ?
";
$bundleStmt = sqlsrv_query($conn, $bundleQuery, [$parentItemCode]);

if ($bundleStmt) {
    while ($row = sqlsrv_fetch_array($bundleStmt, SQLSRV_FETCH_ASSOC)) {
        $childImage = resolveImage($row, $files);

        $result['addOns'][] = [
            'ItemCode'    => $row['ItemCode']   ?? '',
            'ItemOption'  => $row['ItemOption'] ?? '',
            'Qty'         => $row['Qty']        ?? 1,
            'Name'        => $row['Description'] ?? '',
            'Description' => $row['Description'] ?? '',
            'Price'       => $row['Price']       ?? 0,
            'image'       => $childImage,
        ];
    }
}

header("Content-Type: application/json");
echo json_encode($result);
