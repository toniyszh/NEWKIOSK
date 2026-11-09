<?php
require_once dirname(__DIR__) . '../config/connection.php';


function getCategoryIcon($categoryName)
{

    $cleanName = strtolower(trim($categoryName));
    $cleanName = str_replace(' ', '_', $cleanName);
    $cleanName = preg_replace('/[\/\\\\:*?"<>|]/', '_', $cleanName);
    $filename = $cleanName . '.png';

    $basePath = "../images/categories/";
    $absolutePath = __DIR__ . '/../images/categories/' . $filename;


    return file_exists($absolutePath)
        ? $basePath . $filename
        : $basePath . "mains.png";
}


$sequenceTableExists = false;
$sequenceTableStmt = sqlsrv_query($conn, "SELECT OBJECT_ID(N'dbo.KIOSK_CategorySequence', N'U') AS TableID");
if ($sequenceTableStmt && ($sequenceTableRow = sqlsrv_fetch_array($sequenceTableStmt, SQLSRV_FETCH_ASSOC))) {
    $sequenceTableExists = !empty($sequenceTableRow['TableID']);
}

$params = [];

if ($sequenceTableExists) {
    $query = "SELECT
        c.ID,
        c.Name,
        c.OrderNo,
        c.DepartmentID
    FROM dbo.Category c
    OUTER APPLY (
        SELECT TOP 1
            kcs.CategoryID,
            kcs.SequenceNo,
            kcs.IsActive
        FROM dbo.KIOSK_CategorySequence kcs
        WHERE kcs.CategoryID = c.ID
        ORDER BY kcs.SequenceNo
    ) seq
    WHERE c.Code NOT IN ('10014', '10016', '10018', '10019', '10020','1118')
      AND c.Inactive = 0
      AND (seq.CategoryID IS NULL OR seq.IsActive = 1)
    ORDER BY
        COALESCE(seq.SequenceNo, c.OrderNo, 999),
        c.Name;";
} else {
    $query = "SELECT
        c.ID,
        c.Name,
        c.OrderNo,
        c.DepartmentID
    FROM dbo.Category c
    WHERE c.Code NOT IN ('10014', '10016', '10018', '10019', '10020','1118')
      AND c.Inactive = 0
    ORDER BY
        COALESCE(c.OrderNo, 999),
        c.Name;";
}

$stmt = sqlsrv_query($conn, $query, $params);

$categories = '';
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        $iconPath = getCategoryIcon($row['Name']);
        $departmentId = $row['DepartmentID'];

        $categories .= '<li class="category-item" data-category="' . $row['ID'] . '" data-department="' . $departmentId . '" onclick="filterItems(' . $row['ID'] . ')">';
        $categories .= '<img src="' . htmlspecialchars($iconPath) . '" class="sidebar-icon"> ' .
            htmlspecialchars($row['Name']) . '</li>';
    }
} else {
    $categories = '<li>Error loading categories</li>';
}

echo $categories;
