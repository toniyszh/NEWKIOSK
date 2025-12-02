<?php
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

// Accept both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ref = $_GET['ref'] ?? null;
    $kiosk = $_GET['kiosk'] ?? "1";
} else {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $ref = $input['referenceNo'] ?? null;
    $kiosk = $input['kioskRegNo'] ?? "1";
}

if (!$ref) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo "Missing reference number";
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing reference number']);
    }
    exit;
}

$printerName = "POS-80";

try {
    $connector = new WindowsPrintConnector($printerName);
    $printer = new Printer($connector);

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
    $printer->text("CASH PAYMENT\n");
    $printer->selectPrintMode();
    $printer->text("------------------------------\n");

    $printer->text("Order Number:\n");
    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
    $printer->text($ref . "\n");
    $printer->selectPrintMode();
    $printer->text("------------------------------\n");

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->qrCode($ref, Printer::QR_ECLEVEL_H, 8);
    $printer->feed(2);

    $printer->text("Please present this receipt\n");
    $printer->text("at the counter to complete\n");
    $printer->text("your cash payment.\n");
    $printer->feed();

    $printer->cut();
    $printer->close();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo "Print job completed successfully";
    } else {
        echo json_encode(['success' => true, 'message' => 'Print job completed']);
    }
} catch (Exception $e) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo "Print error: " . $e->getMessage();
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
