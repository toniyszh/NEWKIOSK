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
        : $basePath . "10.png";
}

$query = "SELECT TOP 1000 [ID], [Name], [OrderNo] 
          FROM [dbo].[Category] 
          WHERE [Inactive] = 0 
          ORDER BY [OrderNo]";

$stmt = sqlsrv_query($conn, $query);

$categories = '';
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        $iconPath = getCategoryIcon($row['Name']);

        $categories .= '<li class="category-item" data-category="' . $row['ID'] . '" onclick="filterItems(' . $row['ID'] . ')">';
        $categories .= '<img src="' . htmlspecialchars($iconPath) . '" class="sidebar-icon"> ' .
            htmlspecialchars($row['Name']) . '</li>';
    }
} else {
    $categories = '<li>Error loading categories</li>';
}

echo $categories;
