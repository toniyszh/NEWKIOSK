<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner - Kiosk</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap');

        .oswald {
            font-family: "Oswald", sans-serif;
            font-optical-sizing: auto;
            font-weight: 200;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            font-family: "Oswald", sans-serif;
        }

        body {
            background: #1a1a1a;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .container {
            background-image: url('../images/background/tsuruorig.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 0;
            box-shadow: 0 0 0 2px #000000;
            width: 95%;
            height: 95vh;
            max-width: 1400px;
            padding: 60px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            width: 10px;
            height: 10px;
            margin: 0 auto 200px;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5em;
            color: white;

        }

        h1 {
            color: #000000;
            margin-bottom: 30px;
            font-size: 4.5em;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .instructions {
            color: #333333;
            margin-bottom: 50px;
            font-size: 2em;
            line-height: 1.8;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 400;
        }

        #reader {
            border: 4px solid #000000;
            border-radius: 0;
            margin: 40px auto;
            overflow: hidden;
            max-width: 700px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #reader video {
            width: 100% !important;
            height: auto !important;
            min-height: 500px;
        }

        .result-container {
            margin-top: 40px;
            padding: 50px;
            border-radius: 0;
            display: none;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            border: 3px solid #000000;
        }

        .result-container.show {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .result-container.success {
            background: #f8f9fa;
            color: #000000;
        }

        .result-container.error {
            background: #f8f9fa;
            color: #000000;
        }

        .result-title {
            font-size: 2.5em;
            font-weight: 600;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .result-data {
            background: #ffffff;
            padding: 30px;
            border-radius: 0;
            word-break: break-all;
            margin-top: 25px;
            font-size: 1.8em;
            line-height: 1.6;
            border: 2px solid #cccccc;
            font-family: 'Courier New', monospace;
        }

        .scan-again-btn {
            background: #000000;
            color: white;
            border-radius: 10px;
            padding: 25px 60px;
            font-size: 1.8em;
            cursor: pointer;
            margin-top: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.2s ease;
        }

        .scan-again-btn:hover {
            background: #333333;
        }

        .scan-again-btn:active {
            background: #000000;
        }

        .loading {
            color: #000000;
            margin-top: 30px;
            font-size: 2em;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-icon {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .divider {
            width: 100px;
            height: 3px;
            background: #000000;
            margin: 30px auto;
        }

        .order-summary {
            display: none;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            max-width: 900px;
            margin: 30px auto;
        }

        .order-summary.show {
            display: block;
        }

        .order-items {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 30px;
            max-height: 400px;
            overflow-y: auto;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            font-size: 1.3em;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-qty {
            font-size: 0.9em;
            color: #666;
        }

        .item-price {
            text-align: right;
            font-weight: 600;
            min-width: 100px;
        }

        .order-totals {
            border: 2px solid #721719;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .total-row.grand-total {
            border-top: 2px dotted #721719;
            padding-top: 15px;
            font-weight: 700;
            font-size: 1.5em;
        }

        .total-label {
            color: #000;
        }

        .total-value {
            color: #000;
            font-weight: 600;
        }

        .payment-button {
            background: #000000;
            color: white;
            border: none;
            padding: 25px 60px;
            font-size: 1.8em;
            border-radius: 0;
            cursor: pointer;
            margin-top: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.2s ease;
            width: 100%;
        }

        .payment-button:hover {
            background: #333333;
        }

        .payment-button:active {
            background: #000000;
        }

        .error-message {
            background: #f8d7da;
            color: #721c21;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 1.2em;
            border: 1px solid #f5c2c7;
        }
    </style>
</head>

<body>
    <div class="container">


        <div class="logo">
            <img src="../images/icons/qr.png" alt="" style="height: 300px; width: 300px;">
        </div>

        <h1 style="color: #721719;">Scan your QR Here</h1>

        <div class="divider"></div>

        <p class="instructions" style="color: #721719;">
            Kindly scan your QR code to view your orders and proceed with your preferred mode of payment.
        </p>

        <div class="scanner-frame">
            <input type="text" id="qrInput" inputmode="none" autocomplete="off" placeholder="Scanner ready..."
                style="font-size: 1.5em; padding: 20px; width: 100%; max-width: 500px; border: 3px solid #000; border-radius: 0; text-align: center; opacity: 0; position: absolute; left: -9999px;">
        </div>

        <div class="loading" id="loadingText" style="display: none;">
            Scanning in Progress...
        </div>

        <div class="result-container" id="resultContainer">
            <div class="status-icon" id="statusIcon"></div>
            <div class="result-title" id="resultTitle"></div>
            <div class="result-data" id="resultData"></div>
            <button class="scan-again-btn" onclick="scanAgain()">Scan Again</button>
        </div>

        <!-- Order Summary Section -->
        <div class="order-summary" id="orderSummary" style="position: relative; z-index: 10000; pointer-events: auto; background: transparent">
            <h2
                style="text-align: center; margin-bottom: 30px; font-size: 2em; color: #000; text-transform: uppercase;">
                ORDER SUMMARY</h2>

            <div class="order-reference" style="text-align: center; margin-bottom: 30px;">
                <p style="font-size: 1.4em; margin: 0;"><strong>Reference No:</strong> <span id="referenceDisplay"
                        style="color: #721c21; font-weight: 700;"></span></p>
            </div>

            <div class="order-items" id="orderItemsDisplay" style="background: transparent; border: 1px solid #721719;"></div>

            <div class="order-totals" style="border: 2px solid #721719; border-radius: 10px; background: transparent; color: #000">
                <div class="total-row">
                    <span class="total-label">Subtotal</span>
                    <span class="total-value">₱<span id="subtotalDisplay">0.00</span></span>
                </div>
                <div class="total-row">
                    <span class="total-label">Service Charge (10%)</span>
                    <span class="total-value">₱<span id="serviceChargeDisplay">0.00</span></span>
                </div>
                <div class="total-row">
                    <span class="total-label">VAT (12%)</span>
                    <span class="total-value">₱<span id="vatDisplay">0.00</span></span>
                </div>
                <div class="total-row grand-total">
                    <span class="total-label">TOTAL AMOUNT</span>
                    <span class="total-value">₱<span id="grandTotalDisplay">0.00</span></span>
                </div>
            </div>
            <button class="payment-button" onclick="proceedToPayment()" style="background: #721719; color: #ffff; border: 2px solid #ffebcd; border-radius: 10px;">PROCEED TO PAYMENT</button>
            <button class="scan-again-btn" style="width: 100%; margin-top: 15px;" onclick="resetAndScan()">SCAN ANOTHER
                ORDER</button>

            <div class="error-message" id="errorMessage" style="display: none;"></div>
        </div>
    </div>

    <div id="fullBack" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
    z-index: 9999;
    pointer-events: auto;"></div>

    <script>
    document.getElementById("fullBack").addEventListener("touchstart", (e) => {

        const summary = document.getElementById("orderSummary");

        if (summary && summary.contains(e.target)) {
            return;
        }
        history.back();
    });

    const summary = document.getElementById("orderSummary");
    if (summary) {
        summary.style.pointerEvents = "auto";
    }

        let currentReferenceNo = null;

        const qrInput = document.getElementById("qrInput");
        const loadingText = document.getElementById("loadingText");
        const resultContainer = document.getElementById("resultContainer");
        const orderSummary = document.getElementById("orderSummary");
        const statusIcon = document.getElementById("statusIcon");
        const resultTitle = document.getElementById("resultTitle");
        const resultData = document.getElementById("resultData");
        const errorMessage = document.getElementById("errorMessage");


        document.addEventListener('keypress', function(event) {

            if (event.key === 'Enter') {
                const scannedValue = qrInput.value.trim();
                if (scannedValue) {
                    currentReferenceNo = scannedValue;
                    qrInput.value = '';
                    fetchOrderDetails(currentReferenceNo);
                }
                event.preventDefault();
            } else {

                qrInput.value += event.key;
                event.preventDefault();
            }
        });

        function fetchOrderDetails(referenceNo) {
            loadingText.style.display = "block";
            resultContainer.style.display = "none";
            orderSummary.classList.remove("show");

            fetch('../api/fetchOrders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        referenceNo: referenceNo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loadingText.style.display = "none";

                    if (data.success) {
                        displayOrderSummary(data.data);
                    } else {
                        showError(data.message || 'Failed to retrieve order details');
                        resetAndScan();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingText.style.display = "none";
                    showError('Failed to retrieve order. Please try again.');
                    resetAndScan();
                });
        }

        function displayOrderSummary(orderData) {

            document.getElementById('referenceDisplay').textContent = orderData.referenceNo;

            const itemsContainer = document.getElementById('orderItemsDisplay');
            itemsContainer.innerHTML = '';

            orderData.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'order-item';
                itemElement.innerHTML = `
                    <div class="item-details">
                        <div class="item-name">${item.Description}</div>
                        <div class="item-qty">Qty: ${item.Quantity}</div>
                    </div>
                    <div class="item-price">₱${parseFloat(item.ExtendedAmt).toFixed(2)}</div>
                `;
                itemsContainer.appendChild(itemElement);
            });

            const summary = orderData.summary;
            document.getElementById('subtotalDisplay').textContent = summary.subtotal.toFixed(2);
            document.getElementById('serviceChargeDisplay').textContent = summary.serviceCharge.toFixed(2);
            document.getElementById('vatDisplay').textContent = summary.vat.toFixed(2);
            document.getElementById('grandTotalDisplay').textContent = summary.grandTotal.toFixed(2);


            localStorage.setItem('scannedOrderData', JSON.stringify({
                
                referenceNo: orderData.referenceNo,
                items: orderData.items,
                summary: orderData.summary
            }));

    


            document.querySelector('.scanner-frame').style.display = 'none';
            document.querySelector('h1').style.display = 'none';
            document.querySelector('.instructions').style.display = 'none';
            orderSummary.classList.add('show');
            errorMessage.style.display = 'none';
        }

        function showError(message) {
            errorMessage.textContent = '❌ ' + message;
            errorMessage.style.display = 'block';
            orderSummary.classList.add('show');
        }

        function proceedToPayment() {
            if (currentReferenceNo) {

                const subtotal = parseFloat(document.getElementById('subtotalDisplay').textContent);
                const serviceCharge = parseFloat(document.getElementById('serviceChargeDisplay').textContent);
                const vat = parseFloat(document.getElementById('vatDisplay').textContent);
                const grandTotal = parseFloat(document.getElementById('grandTotalDisplay').textContent);

                localStorage.setItem('orderTotals', JSON.stringify({
                    subtotal: subtotal,
                    serviceCharge: serviceCharge,
                    vat: vat,
                    grandTotal: grandTotal
                }));

                window.location.href = '../pages/kioskpayment.php?referenceNo=' + encodeURIComponent(currentReferenceNo);
            }
        }

        function resetAndScan() {
            currentReferenceNo = null;
            orderSummary.classList.remove('show');
            document.querySelector('.scanner-frame').style.display = 'block';
            document.querySelector('h1').style.display = 'block';
            document.querySelector('.instructions').style.display = 'block';
            document.getElementById('orderItemsDisplay').innerHTML = '';
            errorMessage.style.display = 'none';
            localStorage.removeItem('scannedOrderData');
            qrInput.value = '';
            qrInput.focus();
        }

        function scanAgain() {
            resetAndScan();
        }

        window.onload = () => {
            qrInput.focus();
        };
    </script>
</body>

</html>