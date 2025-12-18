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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/script.js"></script>
</head>

<body class="font-sans text-center p-5">
    <h1 class="text-3xl font-bold mb-4">Order QR</h1>
    <p class="text-gray-600 mb-5">Scan this QR at the kiosk to load your order.</p>

    <?php if ($saveSuccess): ?>
        <div class="text-green-600 my-2.5">✓ QR Code saved to database successfully</div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="text-red-600 my-2.5">✗ <?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div id="qrcode" class="my-5 mx-auto w-full max-w-[250px]"></div>
    <button
        class="footer-btn return-btn bg-[#721719] text-[#fff8f3] py-3 px-12 text-xl font-bold border-2 border-[#721719] cursor-pointer rounded-lg hover:bg-[#5d1515] transition-colors duration-200"
        onclick="finishOrder()">
        Create New Order
    </button>

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
                        // document.querySelector('.success-message').style.display = 'block';
                    } else {
                        // document.getElementById("qrValue").innerText = 'Error: ' + data.message;
                    }
                })
            // .catch(error => {
            //     document.getElementById("qrValue").innerText = 'Error: ' + error.message;
            // });
        }

        generateQRCode();
    </script>
</body>

</html>