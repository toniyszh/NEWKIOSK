<?php
require_once dirname(__DIR__) . '/config/connection.php';


$query = "SELECT i.[ID], i.[Description], i.[ExtendedDescription], i.[Price], i.[PictureName], i.[CategoryID], i.[ItemLookupCode],
                 i.[SubCategoryID],
                 u.[id] as unavailable_id, u.[datetime] as unavailable_datetime, u.[userid] as unavailable_userid
          FROM [dbo].[Item] i
          LEFT JOIN [dbo].[UnAvailableItems] u ON u.[itemid] = i.[ID]
          WHERE i.[Inactive] = 0 
          ORDER BY i.[OrderNo]";

$stmt = sqlsrv_query($conn, $query);

$menuItems = '';

if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        $itemName = htmlspecialchars($row['Description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $description = trim($row['ExtendedDescription'] ?? '');
        $descriptionHtml = $description === '' ? '' : '<p class="menu-item-description">' .
            htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
        $rawPrice = $row['Price'];
        $formattedPrice = number_format($rawPrice, 2);
        $categoryId = $row['CategoryID'];
        $itemCode = $row['ItemLookupCode'];
        $basePath = "../images/menus/";
        $defaultImage = $basePath . "10.png";

        if (!empty($row['PictureName'])) {
            $image = $basePath . $row['PictureName'];
        } else {

            $basePath = "../images/menus/";
            $defaultImage = $basePath . "10.png";


            $exactFilename = $row['Description'] . '.png';
            $exactAbsolute = __DIR__ . '/../images/menus/' . $exactFilename;
            $exactRelative = $basePath . $exactFilename;

            if (file_exists($exactAbsolute)) {
                $image = $exactRelative;
            } else {

                $slug = strtolower($row['Description']);
                $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
                $slug = trim($slug, '-');

                $slugFilename = $slug . '.png';
                $slugAbsolute = __DIR__ . '/../images/menus/' . $slugFilename;
                $slugRelative = $basePath . $slugFilename;

                if (file_exists($slugAbsolute)) {
                    $image = $slugRelative;
                } else {

                    $image = $defaultImage;
                }
            }
        }

        $isUnavailable = !is_null($row['unavailable_id']);

        $onclick = '';
        $addButton = '';
        if (!$isUnavailable) {
            $handler = 'addToOrder(' . json_encode($row['Description'], JSON_INVALID_UTF8_SUBSTITUTE) . ', ' .
                $rawPrice . ', ' . json_encode((string) $itemCode, JSON_INVALID_UTF8_SUBSTITUTE) . ', event)';
            $onclick = ' onclick="' . htmlspecialchars($handler, ENT_QUOTES, 'UTF-8') . '"';
            $addButton = '<button class="menu-item-add" type="button" aria-label="Add ' . $itemName . '">+</button>';
        }

        $menuItems .= '<div class="menu-item' . ($isUnavailable ? ' unavailable' : '') . '" data-category="' . $categoryId . '" data-item-id="' . $row['ID'] . '" data-subcategory-id="' . $row['SubCategoryID'] . '" data-item-lookup-code="' . $itemCode . '"' . $onclick . '>
            <img src="' . htmlspecialchars($image) . '" alt="' . $itemName . '" class="menu-image">
            <div class="menu-item-details">
                <h3>' . $itemName . '</h3>' . $descriptionHtml . '
                <div class="menu-item-actions">
                    <p class="menu-item-price">₱' . $formattedPrice . '</p>' . $addButton . '
                </div>
            </div>' .
            ($isUnavailable ? '<div class="unavailable-indicator">Unavailable</div>' : '') .
            '</div>';
    }
} else {
    $menuItems = '<div class="error-message">Error loading menu items</div>';
}
echo $menuItems;
