Swychr Payment Gateway for WHMCS

This module integrates the Swychr Payment API into WHMCS, allowing customers to pay invoices using Swychr and automatically updating invoice status via Swychr’s callback/webhook system.

🚀 Features

Generate payment links via Swychr API (/create_payment_links).

Redirect customers to Swychr hosted checkout.

Auto-verify transactions via Swychr callback (/payment_link_status).

Automatically mark invoices as paid in WHMCS.

Transaction logging in WHMCS for audit purposes.

📂 File Structure
/modules
  /gateways
    swychr.php                # Main gateway module
    /callback
      swychr.php              # Callback/verification handler

⚙️ Installation

Upload files to your WHMCS installation:

swychr.php → modules/gateways/

swychr.php (callback) → modules/gateways/callback/

In WHMCS Admin Panel:

Go to Setup → Payments → Payment Gateways.

Activate Swychr Payment Gateway.

Enter your API Key from the Swychr dashboard.

In your Swychr Merchant Dashboard, set the callback (webhook) URL to:

https://yourdomain.com/modules/gateways/callback/swychr.php

🔑 Configuration Options
Field	Description
API Key	Your API key from the Swychr dashboard.
💳 Payment Flow

Customer selects Swychr Payment during checkout in WHMCS.

WHMCS calls create_payment_links API → gets a unique payment_url.

Customer is redirected to Swychr checkout page.

After payment, Swychr sends a callback to WHMCS → /modules/gateways/callback/swychr.php.

Callback verifies the transaction via payment_link_status.

If successful, WHMCS marks the invoice as paid.

🔍 Testing

Use your Swychr sandbox/test mode API key if available.

Place a test order in WHMCS and check:

“Pay Now with Swychr” button generates a valid payment link.

Callback properly updates invoice status after test payment.

⚠️ Notes

Ensure your WHMCS server is publicly accessible so Swychr can reach the callback URL.

Double-check the key name in API response for the payment link (in this code, assumed as data.payment_url). Update if Swychr uses a different key.

Make sure transaction_id is unique (we use invoiceid-timestamp).

📜 License

This module is provided as-is, under an open license. You may modify it for your WHMCS installation.