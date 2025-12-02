<?php
require_once dirname(__DIR__) . '/config/connection.php';

$query = "SELECT [ID], [Description], [Price], [PictureName], [CategoryID], [ItemLookupCode]
          FROM [dbo].[Item] 
          WHERE [Inactive] = 0 
          ORDER BY [OrderNo]";

$stmt = sqlsrv_query($conn, $query);

$menuItems = '';

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        $itemName = htmlspecialchars($row['Description']);
        $rawPrice = $row['Price'];
        $formattedPrice = number_format($rawPrice, 2);
        $categoryId = $row['CategoryID'];
        $itemCode = $row['ItemLookupCode'];

        $basePath = "../images/menus/";
        $defaultImage = $basePath . "10.png";

        if (!empty($row['PictureName'])) {

            $image = $basePath . $row['PictureName'];
        } else {

            $autoImageFilename = $row['Description'] . ".png";
            $autoImageRelative = $basePath . $autoImageFilename;
            $autoImageAbsolute = __DIR__ . '/../images/menus/' . $autoImageFilename;

            $image = file_exists($autoImageAbsolute) ? $autoImageRelative : $defaultImage;
        }

        $menuItems .= '<div class="menu-item" data-category="' . $categoryId . '" onclick="addToOrder(\'' . $itemName . '\', ' . $rawPrice . ', \'' . $itemCode . '\', event)"> 
            <img src="' . htmlspecialchars($image) . '" alt="' . $itemName . '" class="menu-image">
            <h3 style="margin-bottom: 0px; font-weight: bold;">' . $itemName . '</h3>
            <p style="margin-top: 0px">₱' . $formattedPrice . '</p>
        </div>';
    }
} else {
    $menuItems = '<div class="error-message">Error loading menu items</div>';
}

echo $menuItems;
