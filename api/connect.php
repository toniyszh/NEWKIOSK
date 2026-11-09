<?php

$config = require __DIR__ . '/../config/config.php';

$serverName   = $config['db_server'];
$databaseName = $config['db_name'];
$username     = $config['db_user'];
$password     = $config['db_pass'];
$kioskRegNo   = $config['register_no'] ?? null;

try {
    $pdo = new PDO("sqlsrv:Server=$serverName;Database=$databaseName;Encrypt=no;TrustServerCertificate=yes", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'data' => []
    ]);
    exit();
}

function fetchAll($sql, $params = [], $pdo)
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function fetch($sql, $params = [], $pdo)
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function columnCharLimit($pdo, $tableName, $columnName, $default = null)
{
    static $limits = [];
    $key = $tableName . '.' . $columnName;

    if (array_key_exists($key, $limits)) {
        return $limits[$key];
    }

    $sql = "SELECT CHARACTER_MAXIMUM_LENGTH 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = :tableName AND COLUMN_NAME = :columnName";
    $column = fetch($sql, [
        ':tableName' => $tableName,
        ':columnName' => $columnName
    ], $pdo);

    $limit = $column ? (int) $column->CHARACTER_MAXIMUM_LENGTH : (int) $default;
    $limits[$key] = $limit > 0 ? $limit : $default;

    return $limits[$key];
}

function limitText($value, $limit)
{
    $text = trim((string) ($value ?? ''));

    $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    if (!$limit || $length <= $limit) {
        return $text;
    }

    return function_exists('mb_substr') ? mb_substr($text, 0, $limit, 'UTF-8') : substr($text, 0, $limit);
}

function isTakeoutOrderType($orderType)
{
    $normalized = trim(strtolower((string) $orderType));
    return in_array($normalized, ['take-out', 'takeout', 'to go', 'to-go', 'take out'], true);
}

function recomputeRegister($kioskRegNo, $referenceNo, $pdo, $orderType = 'dine-in')
{
    $totals = [];

    $sql = "EXEC Proc_RecomputeRegister_Kiosk :kioskRegNo, :referenceNo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':kioskRegNo' => $kioskRegNo,
        ':referenceNo' => $referenceNo
    ]);

    $sql = "SELECT 
                ISNULL(SUM(ExtendedAmt), 0) total, 
                ISNULL(SUM(LineDiscount), 0) discount, 
                ISNULL(SUM(ISNULL(LessVat, 0)), 0) less_vat, 
                ISNULL(SUM(OriginalPrice * Quantity), 0) gross 
            FROM [KIOSK_TransactionItem] 
            WHERE KioskRegNo = :kioskRegNo AND ReferenceNo = :referenceNo";
    $params = [
        ':kioskRegNo' => $kioskRegNo,
        ':referenceNo' => $referenceNo
    ];
    $register = fetch($sql, $params, $pdo);

    $totals['total'] = (float) (round($register->total, 2) ?? 0);
    $totals['subtotal'] = (float) (round($register->total, 2) ?? 0);
    $totals['discount'] = (float) (round($register->discount, 2) ?? 0);
    $totals['less_vat'] = (float) (round($register->less_vat, 2) ?? 0);

    if (isTakeoutOrderType($orderType)) {
        $totals['service_charge'] = 0.00;
    } else {
        $sql = "EXEC Proc_ComputeServiceCharge_Kiosk :kioskRegNo, :referenceNo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':kioskRegNo' => $kioskRegNo,
            ':referenceNo' => $referenceNo
        ]);

        $sql = "SELECT * FROM [RegisterServiceCharge_Kiosk] WHERE RegisterNo = :kioskRegNo";
        $params = [':kioskRegNo' => $kioskRegNo];
        $serviceCharge = fetch($sql, $params, $pdo);

        $totals['service_charge'] = (float) round($serviceCharge->Amount ?? 0, 2);
    }

    $totals['total'] = (float) (round($totals['total'] + $totals['service_charge'], 2) ?? 0);

    return $totals;
}
