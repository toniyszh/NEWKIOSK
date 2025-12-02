<?php
require_once dirname(__DIR__) . '/config/connection.php';

$categoryCode = $_GET['category_code'] ?? '10101';

// Fetch category ID
$categoryQuery = "SELECT [ID] FROM [dbo].[Category] WHERE [Code] = ?";
$stmt = sqlsrv_query($conn, $categoryQuery, [$categoryCode]);

$modifiers = [];

if ($stmt) {
    $category = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($category) {
        $categoryID = $category['ID'];

        $modifiersQuery = "
            SELECT 
                c.Code AS CategoryCode,
                c.Name AS CategoryName,
                m.ModifierID,
                m.FlavorName AS Modifier
            FROM Category c
            JOIN KIOSK_Category_Modifiers cm ON c.ID = cm.CategoryID
            JOIN KIOSK_MODIFIERS m ON cm.ModifierID = m.ModifierID
            WHERE c.ID = ?
        ";

        $stmt2 = sqlsrv_query($conn, $modifiersQuery, [$categoryID]);

        if ($stmt2) {

            $folderPath = __DIR__ . '/../images/modifiers/';

            $files = scandir($folderPath);

            while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {

                $modifierName = $row['Modifier'];
                $imagePath = "images/modifiers/default.png";


                foreach ($files as $file) {

                    $fileNoExt = pathinfo($file, PATHINFO_FILENAME);

                    if (strcasecmp($fileNoExt, $modifierName) === 0) {
                        $imagePath = "images/modifiers/" . $file;
                        break;
                    }
                }

                $row['image'] = $imagePath;
                $modifiers[] = $row;
            }
        }
    }
}

header("Content-Type: application/json");
echo json_encode($modifiers);
