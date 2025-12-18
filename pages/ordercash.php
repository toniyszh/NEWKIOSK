<?php
require_once __DIR__ . '/../api/connect.php';

$qrValue = '';
$saveSuccess = false;
$errorMessage = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline Order Number</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/script.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        padding: 20px;
    }

    #icon {
        width: 100px;
        height: 100px;
        margin: 20px auto;
        display: block;
    }

    #orderNumber {
        margin: 30px auto;
        padding: 20px;
        font-size: 48px;
        font-weight: bold;
        letter-spacing: 2px;
        color: #721719;
        border: 2px solid #721719;
        border-radius: 8px;
        background-color: #f9f9f9;
        max-width: 500px;
    }

    .success-message {
        color: green;
        margin: 10px 0;
    }

    .error-message {
        color: red;
        margin: 10px 0;
    }

    .loading-message {
        color: #666;
        margin: 10px 0;
        font-style: italic;
    }

    button {
        margin-top: 20px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
    }
    </style>
</head>

<body>
    <img id="icon" src="../images/icons/check.png" alt="Order Icon">

    <h2 class="text-xl font-bold mb-4">Order Success!</h2>
    <p>This number serves as your official order number. Please present it at the counter.</p>



    <div id="statusMessages"></div>

    <div id="orderNumber">Loading...</div>
    <button
        class="footer-btn return-btn w-full bg-[#721719] text-[#fff8f3] py-3 px-20 text-xl font-bold border-2 border-[#721719] cursor-pointer rounded-lg hover:bg-[#5d1515] transition-colors duration-200"
        onclick="finishOrder()">
        Create New Order
    </button>

    <script>
    function displayMessage(message, type = 'info') {
        const statusDiv = document.getElementById('statusMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = type === 'success' ? 'success-message' :
            type === 'error' ? 'error-message' : 'loading-message';
        messageDiv.textContent = message;
        statusDiv.appendChild(messageDiv);
    }

    async function displayOrderNumber() {
        const referenceNo = localStorage.getItem('referenceNo');
        const kioskRegNo = localStorage.getItem('kioskRegNo') || '';
        const customerID = localStorage.getItem('customerID') || '';
        const cartItems = JSON.parse(localStorage.getItem('currentOrder') || '[]');

        if (!referenceNo) {
            document.getElementById("orderNumber").innerText = 'Error: Reference number not found';
            document.getElementById("orderNumber").style.color = 'red';
            return;
        }

        // Prepare items array for saveQRCode.php
        const items = cartItems.map(item => ({
            itemID: item.itemID || item.id,
            itemLookupCode: item.itemLookupCode || item.lookupCode,
            description: item.description || item.name,


            price: parseFloat(item.price) || 0,
            quantity: parseInt(item.quantity) || 0
        }));


        console.log('Cart Items:', cartItems);

        const transactionItems = cartItems.map(item => {
            const mappedItem = {
                itemCode: item.itemLookupCode || item.lookupCode || item.itemCode || '',
                description: item.description || item.name || '',
                qty: parseInt(item.quantity) || parseInt(item.qty) || 0,
                price: parseFloat(item.price) || 0,
                salesRepNo: item.salesRepNo || '',
                discountReasonCode: item.discountReasonCode || '',
                originalPrice: parseFloat(item.originalPrice) || parseFloat(item.price) || 0,
                salesTax: parseFloat(item.salesTax) || 0.00,
                uom: item.uom || 'ORDER',
                packingQty: parseInt(item.packingQty) || 1,
                itemType: item.itemType || 0,
                lineAddOn: parseFloat(item.lineAddOn) || 0.00,
                taxExemptAmt: parseFloat(item.taxExemptAmt) || 0.00,
                lineDiscount: parseFloat(item.lineDiscount) || 0.00,
                parentId: item.parentId || null,
                sync: item.sync || 0
            };
            console.log('Mapped Item:', mappedItem);
            return mappedItem;
        }).filter(item => item.itemCode && item.qty > 0); // Filter out invalid items

        console.log('Transaction Items to send:', transactionItems);

        // Check if we have valid items
        if (transactionItems.length === 0) {
            throw new Error('No valid items found in cart');
        }

        try {


            // First API call: saveQRCode.php
            const qrResponse = await fetch('../api/saveQRCode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    referenceNo: referenceNo,
                    kioskRegNo: kioskRegNo,
                    customerID: customerID,
                    items: items
                })
            });

            const qrData = await qrResponse.json();

            if (!qrData.success) {
                throw new Error(qrData.message || 'Failed to save QR code');
            }


            document.getElementById("orderNumber").innerText = qrData.qrCodeValue;



            const entriesResponse = await fetch('../api/saveholdentries.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    referenceNo: referenceNo,
                    items: transactionItems
                })
            });

            const entriesData = await entriesResponse.json();

            if (!entriesData.success) {
                throw new Error(entriesData.message || 'Failed to save transaction entries');
            }



            // Optional: Clear localStorage after successful save
            // localStorage.removeItem('cartItems');
            // localStorage.removeItem('referenceNo');

        } catch (error) {
            console.error('Error:', error);
            displayMessage('✗ ' + error.message, 'error');
            document.getElementById("orderNumber").innerText = 'Error: ' + error.message;
            document.getElementById("orderNumber").style.color = 'red';
        }
    }

    displayOrderNumber();
    </script>
</body>

</html>