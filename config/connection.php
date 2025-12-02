<?php

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }


$config = require __DIR__ . '/config.php';


$_SESSION['register_no'] = $config['register_no'];

$_SESSION['queue_prefix'] = $config['queue_prefix'];

$serverName   = $config['db_server'];
$databaseName = $config['db_name'];
$username     = $config['db_user'];
$password     = $config['db_pass'];

$connectionOptions = [
    "Database" => $databaseName,
    "Uid"      => $username,
    "PWD"      => $password
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    echo "Connection failed.<br>";
    echo "Register No: " . $config['register_no'] . "<br>";
    die(print_r(sqlsrv_errors(), true));
}

$pdo = new PDO("sqlsrv:Server=$serverName;Database=$databaseName", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
