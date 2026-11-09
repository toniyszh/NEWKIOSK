<?php
require_once dirname(__DIR__) . '../config/connection.php';


function getCategoryIcon($categoryName)
{

    $cleanName = strtolower(trim($categoryName));
    $cleanName = str_replace(' ', '_', $cleanName);
    $filename = $cleanName . '.png';

    $basePath = "../images/categories/";
    $absolutePath = __DIR__ . '/../images/categories/' . $filename;


    return file_exists($absolutePath)
        ? $basePath . $filename
        : $basePath . "mains.png";
}

/*CONFIGURABLE CATEGORY SEQUENCE*/

$query = "SELECT
    c.ID,
    c.Name,
    c.OrderNo,
    c.DepartmentID
FROM dbo.Category c
WHERE c.Code NOT IN ('10014', '10016', '10018', '10019', '10020','1118')
  AND c.Inactive = 0
ORDER BY
    CASE c.ID
	WHEN 22 THEN 1   -- NEW DRINKS
        WHEN 17 THEN 1   -- NEW
        WHEN 10 THEN 2   -- BARISTAS RECO
        WHEN 2  THEN 3   -- ALL DAY BREAKFAST
        WHEN 1  THEN 4   -- MAINS
        WHEN 3  THEN 5   -- PASTA
        WHEN 4  THEN 6   -- SANDWICH
        WHEN 5  THEN 7   -- SIDES
        WHEN 6  THEN 8   -- PASTRIES
        WHEN 7  THEN 9   -- PANCAKES AND FRENCH TOAST
        WHEN 8  THEN 10  -- HOT DRINKS
        WHEN 9  THEN 11  -- OVER ICE
        WHEN 11 THEN 12  -- ICE BLENDED
        WHEN 12 THEN 13  -- TEA
        WHEN 13 THEN 14  -- JUICE
        WHEN 15 THEN 15  -- MILKSHAKE
        ELSE 999
    END;";

$stmt = sqlsrv_query($conn, $query);

$categories = '';
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        $iconPath = getCategoryIcon($row['Name']);
        $departmentId = $row['DepartmentID'];
        $subCategoryQuery = "SELECT TOP 1000 [ID], [Name], [OrderNo] FROM [dbo].[SubCategory] WHERE CategoryID = ? AND inactive=0 ORDER BY [OrderNo]";
        $subCategoryStmt = sqlsrv_query($conn, $subCategoryQuery, array($row['ID']));


        $categories .= '<li class="category-item" data-category="' . $row['ID'] . '" data-department="' . $departmentId . '" onclick="filterItems(' . $row['ID'] . ')">';
        $categories .= '<img src="' . htmlspecialchars($iconPath) . '" class="sidebar-icon"> ' .
            htmlspecialchars($row['Name']) . '</li>';
    }
} else {
    $categories = '<li>Error loading categories</li>';
}

echo $categories;
