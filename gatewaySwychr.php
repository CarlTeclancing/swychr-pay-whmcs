<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function swychr_MetaData()
{
    return [
        'DisplayName' => 'Swychr Payment Gateway',
        'APIVersion' => '1.1',
    ];
}

function swychr_config()
{
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'Swychr Payment Gateway',
        ],
        'apiKey' => [
            'FriendlyName' => 'API Key',
            'Type' => 'text',
            'Size' => '50',
            'Description' => 'Enter your Swychr API Key',
        ],
    ];
}

function swychr_link($params)
{
    $gatewayurl = "https://pay.swychr.com/create_payment_links";

    $invoiceId   = $params['invoiceid'];
    $amount      = $params['amount'];
    $clientName  = $params['clientdetails']['firstname'] . ' ' . $params['clientdetails']['lastname'];
    $clientEmail = $params['clientdetails']['email'];
    $clientPhone = $params['clientdetails']['phonenumber'];
    $countryCode = $params['clientdetails']['country'];

    // transaction_id must be unique
    $transactionId = $invoiceId . '-' . time();

    $postfields = [
        'country_code'       => $countryCode,
        'name'               => $clientName,
        'email'              => $clientEmail,
        'mobile'             => $clientPhone,
        'amount'             => $amount,
        'transaction_id'     => $transactionId,
        'description'        => "Invoice #" . $invoiceId,
        'pass_digital_charge'=> true,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $gatewayurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $params['apiKey'],
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
    $response = curl_exec($ch);
    curl_close($ch);

    $jsonData = json_decode($response, true);

    // Adjust according to real response key for payment URL
    if (isset($jsonData['data']['payment_url'])) {
        $paymentLink = $jsonData['data']['payment_url'];
        return '<a href="' . $paymentLink . '" class="btn btn-success">Pay Now with Swychr</a>';
    } else {
        return "Error creating payment link. Please contact support.";
    }
}
