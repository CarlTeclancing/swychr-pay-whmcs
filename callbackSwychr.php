<?php
require_once '../../../init.php';
require_once '../../../includes/functions.php';
require_once '../../../includes/gatewayfunctions.php';
require_once '../../../includes/invoicefunctions.php';

$gatewayModule = "swychr";
$gatewayParams = getGatewayVariables($gatewayModule);

if (!$gatewayParams["type"]) {
    die("Module Not Activated");
}

// Swychr should send JSON payload in webhook
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['transaction_id'])) {
    http_response_code(400);
    die("Invalid callback payload");
}

$transactionId = $input['transaction_id'];
$invoiceId = explode('-', $transactionId)[0];

// --- Step 1: Validate with Swychr API ---
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://pay.swychr.com/payment_link_status");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $gatewayParams['apiKey'],
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'transaction_id' => $transactionId
]));
$response = curl_exec($ch);
curl_close($ch);

$jsonData = json_decode($response, true);

if (!isset($jsonData['status'])) {
    logTransaction($gatewayParams["name"], $response, "Invalid Response");
    http_response_code(400);
    die("Invalid API response");
}

// --- Step 2: Check invoice & transaction ---
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);
checkCbTransID($transactionId);

// --- Step 3: Apply Payment if Successful ---
if ($jsonData['status'] == 1) {
    $amountPaid = $jsonData['data']['amount'] ?? 0;

    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $amountPaid,
        0,
        $gatewayModule
    );

    logTransaction($gatewayParams["name"], $jsonData, "Successful");
    http_response_code(200);
    echo "OK";
} else {
    logTransaction($gatewayParams["name"], $jsonData, "Failed");
    http_response_code(200);
    echo "FAILED";
}
