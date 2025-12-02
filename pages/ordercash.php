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

    <h1>Order Success!</h1>
    <p>This number serves as your official order number. Please present it at the counter.</p>

    <?php if ($saveSuccess): ?>
        <div class="success-message">✓ Order saved successfully</div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="error-message">✗ <?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div id="orderNumber">Loading...</div>

    <script>
        function displayOrderNumber() {
            const referenceNo = localStorage.getItem('referenceNo');
            const kioskRegNo = localStorage.getItem('kioskRegNo') || '';
            const customerID = localStorage.getItem('customerID') || '';
            const cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');

            if (!referenceNo) {
                document.getElementById("orderNumber").innerText = 'Error: Reference number not found';
                document.getElementById("orderNumber").style.color = 'red';
                return;
            }

            // Prepare items array
            const items = cartItems.map(item => ({
                itemID: item.itemID || item.id,
                itemLookupCode: item.itemLookupCode || item.lookupCode,
                description: item.description || item.name,
                price: parseFloat(item.price) || 0,
                quantity: parseInt(item.quantity) || 0
            }));

            fetch('../api/saveQRCode.php', {
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
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const orderValue = data.qrCodeValue;
                        document.getElementById("orderNumber").innerText = orderValue;

                        const successMsg = document.querySelector('.success-message');
                        if (successMsg) {
                            successMsg.style.display = 'block';
                        }
                    } else {
                        document.getElementById("orderNumber").innerText = 'Error: ' + data.message;
                        document.getElementById("orderNumber").style.color = 'red';
                    }
                })
                .catch(error => {
                    document.getElementById("orderNumber").innerText = 'Error: ' + error.message;
                    document.getElementById("orderNumber").style.color = 'red';
                });
        }

        displayOrderNumber();
    </script>
</body>

</html>