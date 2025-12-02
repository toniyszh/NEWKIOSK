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
    <title>Offline Order QR</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        padding: 20px;
    }

    #qrcode {
        margin: 20px auto;
        width: 100%;
        max-width: 250px;

    }

    #qrValue {
        margin-top: 15px;
        font-weight: bold;
        word-wrap: break-word;
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
    <h1>Order QR</h1>
    <p>Scan this QR at the kiosk to load your order.</p>

    <?php if ($saveSuccess): ?>
    <div class="success-message">✓ QR Code saved to database successfully</div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
    <div class="error-message">✗ <?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div id="qrcode"></div>
    <!-- <div id="qrValue"></div> -->
      




    <script src="../js/qrcode.min.js"></script>
    <script>
    let qrcode;

    function generateQRCode() {
        document.getElementById("qrcode").innerHTML = "";

        const referenceNo = localStorage.getItem('referenceNo');
        const kioskRegNo = localStorage.getItem('kioskRegNo') || '';
        const customerID = localStorage.getItem('customerID') || '';
        const cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');

        if (!referenceNo) {
            document.getElementById("qrValue").innerText = 'Error: Reference number not found in localStorage';
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
                    const qrValue = data.qrCodeValue;

                    qrcode = new QRCode(document.getElementById("qrcode"), {
                        text: qrValue,
                        width: 250,
                        height: 250,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.L
                    });

                    // document.getElementById("qrValue").innerText = `Order Code: ${qrValue}`;
                    document.querySelector('.success-message').style.display = 'block';
                } else {
                    // document.getElementById("qrValue").innerText = 'Error: ' + data.message;
                }
            })
            .catch(error => {
                document.getElementById("qrValue").innerText = 'Error: ' + error.message;
            });
    }

    generateQRCode();
    </script>
</body>

</html>