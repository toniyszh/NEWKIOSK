<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode of Payment</title>

    <script src="../js/script.js"></script>
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
            box-sizing: border-box;
            font-family: "Oswald", sans-serif;
        }

        body {
            background: url('../images/background/tsurumain.png');
            color: #721719;
            font-size: 1.875rem;
        }

        .container {
            max-width: 56rem;
            margin: 0 auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            height: 20%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header img {
            height: 11rem;
            width: auto;
        }

        #payment_container {
            position: relative;
            flex: 1;
        }

        #payment_section {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        #payment_section.hidden {
            display: none;
        }

        .content-scroll {
            flex: 1;
        }



        .section-title {
            font-size: 2.25rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 7rem;
        }

        .divider {
            border-top: 2px solid #721719;
            width: 100%;
            margin: 5rem 0;
        }

        .payment-options {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            margin: 7rem 0;
        }

        .payment-option {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .payment-option p {
            font-size: 1.125rem;
            font-weight: 500;
        }

        .payment-box {
            border: 2px solid #721719;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 16rem;
            height: 16rem;
        }

        .payment-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .summary-section {
            height: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-evenly;
        }

        .summary-items {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 2.5rem;
        }

        .button-section {
            height: 15%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem;
        }

        .btn {
            cursor: pointer;
            width: 500px;
            height: 100px;
            font-size: 50px;

        }

        .btn:hover {
            background-color: #fff;
            color: #721719;
        }

        .overlay {
            display: none;
            flex-direction: column;
            height: 100%;
        }

        .overlay.flex {
            display: flex;
        }

        .overlay.hidden {
            display: none;
        }

        .overlay-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3rem;
        }

        .overlay-spacer {
            height: 40%;
        }

        .overlay h4 {
            font-size: 2.25rem;
            font-weight: 500;
            text-align: center;
        }

        .overlay .text-base {
            font-size: 1rem;
        }

        .overlay .divider {
            margin: 0;
        }

        .overlay .total-row {
            font-size: 2.25rem;
            font-weight: 500;
        }

        .modal {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.75);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: #000;
            border: 2px solid #fff;
            padding: 2rem;
            margin: 0 1rem;
            border-radius: 0.5rem;
            width: 900px;
            height: 600px;
        }

        .modal-inner {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .modal-icon {
            margin-top: 20px;
        }

        .modal-icon img {
            max-width: 120px;
            height: auto;
            margin: 0 auto;
        }

        .modal h1 {
            font-size: 2.8rem;
            font-weight: 500;
        }

        .modal p {
            font-size: 2.5rem;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            padding-top: 1rem;
        }

        .modal-btn {
            border: 2px solid #fff;
            padding: 0.75rem 2rem;
            cursor: pointer;
            height: 100px;
            width: 200px;
            background: transparent;
            color: #fff;
            font-size: 1.875rem;
            transition: all 0.3s;
        }

        .modal-btn:hover {
            background-color: #fff;
            color: #000;
        }

        .modal-btn.secondary {
            border-color: #9ca3af;
            color: #9ca3af;
        }

        .modal-btn.secondary:hover {
            background-color: #9ca3af;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../images/logos/logo1.png" alt="Main Logo">
        </div>

        <div id="payment_container">
            <div id="payment_section">
                <div class="content-scroll">
                    <div class="content-inner">
                        <h2 class="section-title" style="font-size: 50px;">MODE OF PAYMENT</h2>
                        <p class="divider"></p>
                        <div class="payment-options">
                            <div id="card" class="payment-option">
                                <p style="font-size: 25px">DEBIT / CREDIT CARD</p>
                                <div class="payment-box">
                                    <img src="../images/payment/card.png" alt="cc" style="height: 200px; width: 200px">
                                </div>
                            </div>

                            <div id="qrph" class="payment-option">
                                <p style="font-size: 25px">QRPH</p>
                                <div class="payment-box">
                                    <img src="../images/payment/qr.png" alt="qrph" style="height: 200px; width: 200px">
                                </div>
                            </div>
                            <div id="cash" class="payment-option">
                                <p style="font-size: 25px">CASH</p>
                                <div class="payment-box">
                                    <img src="../images/payment/cash.png" alt="cash"
                                        style="height: 200px; width: 200px">
                                </div>
                            </div>
                        </div>
                        <p class="divider"></p>
                    </div>
                </div>
                <div class="summary-section">
                    <div class="summary-items">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal"></span>
                        </div>
                        <div class="summary-row">
                            <span>Discount</span>
                            <span id="discount_amount"></span>
                        </div>
                        <div class="summary-row">
                            <span id="service-charge-label">Service Charge</span>
                            <span id="service-charge"></span>
                        </div>
                    </div>
                    <div class="total-row">
                        <span>TOTAL</span>
                        <span id="total"></span>
                    </div>
                </div>

                <div class="button-section">
                    <button id="btnBack" class="btn"
                        style="background: #721719; color: #ffebcd; border: 2px solid #ffebcd; border-radius: 10px;">BACK</button>
                </div>
            </div>

            <div id="processingOverlay" class="overlay">
                <div class="overlay-content">
                    <div>
                        <h4>Kindly process your payment</h4>
                        <h4>using the card terminal.</h4>
                    </div>
                    <p class="divider"></p>
                    <div class="total-row">
                        <span>TOTAL</span>
                        <span id="payment_total"></span>
                    </div>
                </div>
                <div class="overlay-spacer"></div>
            </div>

            <div id="successOverlay" class="overlay">
                <div class="overlay-content">
                    <h4>Please make sure to collect your official printed receipt</h4>
                    <h4>Thank you!</h4>
                    <h4 class="text-base">This Page will automatically close in 3 secs</h4>
                    <p class="divider"></p>
                </div>
                <div class="overlay-spacer"></div>
            </div>

            <div id="errorOverlay" class="overlay">
                <div class="overlay-content">
                    <h4 style="font-weight: 500;">Card Terminal Error</h4>
                    <h4></h4>
                    <h4 class="text-base">This Page will automatically close in 5 secs</h4>
                </div>
                <div class="overlay-spacer"></div>
            </div>

            <div id="confirmModal" class="modal">
                <div class="modal-content">
                    <div class="modal-inner">
                        <div class="modal-icon">
                            <img src="images/icons/caution-sign.png" alt="Warning">
                        </div>
                        <h1>Warning</h1>
                        <div>
                            <p>Are you sure you want to go back to the cart?<br>Any applied discount will be removed.
                            </p>
                        </div>
                        <div class="modal-buttons">
                            <button id="confirmYes" class="modal-btn">Yes</button>
                            <button id="confirmNo" class="modal-btn secondary">No</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="startOverModal" class="modal">
                <div class="modal-content">
                    <div class="modal-inner">
                        <div class="modal-icon">
                            <img src="images/icons/caution-sign.png" alt="Warning">
                        </div>
                        <h1>Warning</h1>
                        <div>
                            <p>Are you sure you want to start over?<br>All items in your cart will be removed.</p>
                        </div>
                        <div class="modal-buttons">
                            <button id="btnStartOverYes" class="modal-btn">Yes</button>
                            <button id="btnStartOverNo" class="modal-btn secondary">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        const card = document.getElementById("card");
        const qrph = document.getElementById("qrph");
        const cash = document.getElementById("cash");
        const payment_section = document.getElementById("payment_section");
        const processingOverlay = document.getElementById("processingOverlay");
        const successOverlay = document.getElementById("successOverlay");
        const errorOverlay = document.getElementById("errorOverlay");
        const confirmModal = document.getElementById("confirmModal");
        const confirmYes = document.getElementById("confirmYes");
        const confirmNo = document.getElementById("confirmNo");
        const startOverModal = document.getElementById("startOverModal");
        const btnStartOverYes = document.getElementById("btnStartOverYes");
        const btnStartOverNo = document.getElementById("btnStartOverNo");

        console.log("Payment page script loaded");
        console.log("Card element:", card);
        console.log("QRPH element:", qrph);

        // Extract reference number from URL if present (from scan page)
        const urlParams = new URLSearchParams(window.location.search);
        const refFromUrl = urlParams.get('referenceNo');
        if (refFromUrl && !getReferenceNo()) {
            console.log("Reference number found in URL, storing in localStorage:", refFromUrl);
            setReferenceNo(refFromUrl);
        }

        if (!getRegisterNo()) {
            console.log("Loading register number from server...");
            loadRegisterNo().then(() => {
                console.log("Register number stored in localStorage:", getRegisterNo());
            });
        } else {
            console.log("Register number already in localStorage:", getRegisterNo());
        }

        const totals = getTotals();
        const btnBack = document.getElementById("btnBack");
        btnBack.addEventListener("click", (e) => {
            e.preventDefault();
            const totals = getTotals();
            const hasDiscount = totals.discount + totals.less_vat;

            if (hasDiscount > 0) {
                confirmModal.classList.add("show");
            } else {
                window.location.href = 'main.php';
            }
        });

        confirmYes.addEventListener("click", () => {
            confirmModal.classList.remove("show");
            const payload = {
                Action: 'NoDiscount',
                ReferenceNo: getReferenceNo()
            };
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            };

            fetch('../api/request_discount.php', options)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;
                    setTotals(data.data);
                    window.location.href = 'main.php';
                })
                .catch(err => {
                    console.error('Error removing discount:', err);
                    window.location.href = 'main.php';
                });
        });

        confirmNo.addEventListener("click", () => {
            confirmModal.classList.remove("show");
        });

        btnStartOverYes.addEventListener("click", () => {
            startOverModal.classList.remove("show");
            clearAll();
            window.location.href = '../index.php';
        });

        btnStartOverNo.addEventListener("click", () => {
            startOverModal.classList.remove("show");
        });

        confirmModal.addEventListener("click", (e) => {
            if (e.target === confirmModal) {
                confirmModal.classList.remove("show");
            }
        });

        startOverModal.addEventListener("click", (e) => {
            if (e.target === startOverModal) {
                startOverModal.classList.remove("show");
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                confirmModal.classList.remove("show");
                startOverModal.classList.remove("show");
            }
        });

        function showStartOverModal() {
            startOverModal.classList.add("show");
        }


        function showTotals() {
            // Check if orderTotals from scan page are available
            const orderTotalsStr = localStorage.getItem('orderTotals');

            if (orderTotalsStr) {
                // Use totals from scanned order
                const orderTotals = JSON.parse(orderTotalsStr);
                const subtotalEl = document.getElementById("subtotal");
                const discountEl = document.getElementById("discount_amount");
                const serviceChargeEl = document.getElementById("service-charge");
                const totalEl = document.getElementById("total");
                const paymentTotalEl = document.getElementById("payment_total");

                if (subtotalEl) subtotalEl.textContent = "₱" + (orderTotals.subtotal || 0).toFixed(2);
                if (discountEl) discountEl.textContent = "₱" + (0).toFixed(2);
                if (serviceChargeEl) serviceChargeEl.textContent = "₱" + (orderTotals.serviceCharge || 0).toFixed(2);
                if (totalEl) totalEl.textContent = "₱" + (orderTotals.grandTotal || 0).toFixed(2);
                if (paymentTotalEl) paymentTotalEl.textContent = "₱" + (orderTotals.grandTotal || 0).toFixed(2);

                // Log for debugging
                console.log("Displaying order totals from scan:", orderTotals);
            } else {
                // Use totals from regular cart
                const totals = getTotals();
                const subtotalEl = document.getElementById("subtotal");
                const discountEl = document.getElementById("discount_amount");
                const serviceChargeEl = document.getElementById("service-charge");
                const totalEl = document.getElementById("total");
                const paymentTotalEl = document.getElementById("payment_total");

                if (subtotalEl) subtotalEl.textContent = "₱" + (totals.subtotal || 0).toFixed(2);
                if (discountEl) discountEl.textContent = "₱" + (totals.discount || 0).toFixed(2);
                if (serviceChargeEl) serviceChargeEl.textContent = "₱" + (totals.service_charge || 0).toFixed(2);
                if (totalEl) totalEl.textContent = "₱" + (totals.total || 0).toFixed(2);
                if (paymentTotalEl) paymentTotalEl.textContent = "₱" + (totals.total || 0).toFixed(2);

                console.log("Displaying totals from cart:", totals);
            }
        }

        let paymentChecker = null;
        let isChecking = false;

        showTotals();


        if (card) {
            console.log("Adding click listener to card element");
            card.addEventListener("click", (e) => {
                console.log("Card clicked!");
                sendPayment(e, "CreditDebit");
            });
        } else {
            console.error("Card element not found!");
        }

        if (qrph) {
            console.log("Adding click listener to qrph element");
            qrph.addEventListener("click", (e) => {
                console.log("QRPH clicked!");
                sendPayment(e, "GenericMerchantQR-qr:qrph");
            });
        } else {
            console.error("QRPH element not found!");
        }

        if (cash) {
            console.log("Adding click listener to cash element");
            cash.addEventListener("click", (e) => {
                console.log("CASH clicked!");
                processCashPayment(e);
            });
        } else {
            console.error("CASH element not found!");
        }

        function getPaymentTotals() {
            // Check if orderTotals from scan page are available
            const orderTotalsStr = localStorage.getItem('orderTotals');

            if (orderTotalsStr) {
                // Use totals from scanned order
                const orderTotals = JSON.parse(orderTotalsStr);
                return {
                    subtotal: orderTotals.subtotal || 0,
                    service_charge: orderTotals.serviceCharge || 0,
                    total: orderTotals.grandTotal || 0,
                    vat: orderTotals.vat || 0,
                    discount: 0,
                    less_vat: 0,
                    scanned_gift_cert_codes: [],
                    gift_certificate_amount: 0
                };
            } else {
                // Use totals from regular cart
                const totals = getTotals();
                return {
                    subtotal: totals.subtotal || 0,
                    service_charge: totals.service_charge || 0,
                    total: totals.total || 0,
                    vat: totals.vat || 0,
                    discount: totals.discount || 0,
                    less_vat: totals.less_vat || 0,
                    scanned_gift_cert_codes: totals.scanned_gift_cert_codes || [],
                    gift_certificate_amount: totals.gift_certificate_amount || 0
                };
            }
        }

        function processCashPayment(event) {
            if (event) event.preventDefault();
            console.log("Cash payment initiated");

            const referenceNo = getReferenceNo();
            if (!referenceNo) {
                alert("Error: Reference number is required. Please scan an order first.");
                return;
            }

            const totals = getPaymentTotals();
            if (!totals || (totals.total === 0 && totals.subtotal === 0)) {
                alert("Error: Could not load cart totals. Please go back and try again.");
                return;
            }

            const kioskRegNo = getRegisterNo() || 1;

            const payload = {
                referenceNo: referenceNo,
                kioskRegNo: kioskRegNo,
                items: [],
                customerID: null
            };

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            };

            // Save QR code to database
            fetch('../api/saveQRCode.php', options)
                .then(res => res.json())
                .then(data => {
                    console.log("QR code save response:", data);
                    if (!data.success) {
                        alert("Error saving reference number: " + data.message);
                        return;
                    }

                    // Print the reference number
                    printCashPaymentQRCode(referenceNo, kioskRegNo);
                })
                .catch(err => {
                    console.error("Error saving QR code:", err);
                    alert("Error processing cash payment: " + err.message);
                });
        }

        async function printCashPaymentQRCode(referenceNo, kioskRegNo) {
            console.log("Opening print window for reference:", referenceNo);

            try {
                const response = await fetch('../print_receipt.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        referenceNo: referenceNo,
                        kioskRegNo: kioskRegNo
                    })
                });

                const result = await response.json();

                if (result.success) {
                    console.log("Print successful:", result.message);
                } else {
                    console.error("Print failed:", result.message);
                    alert("Print failed: " + result.message);
                }
            } catch (error) {
                console.error("Print error:", error);
                alert("Error sending print job");
            }


            payment_section.classList.add("hidden");
            successOverlay.classList.add("flex");
            successOverlay.classList.remove("hidden");

            setTimeout(() => {
                clearAll();
                window.location.href = "../index.php";
            }, 3000);
        }

        function sendPayment(event, type) {
            if (event) event.preventDefault();
            console.log("Payment initiated for type:", type);

            const referenceNo = getReferenceNo();
            if (!referenceNo) {
                alert("Error: Reference number is required. Please scan an order first.");
                return;
            }

            const totals = getPaymentTotals();
            console.log("Totals retrieved:", totals);


            if (!totals || (totals.total === 0 && totals.subtotal === 0)) {
                alert("Error: Could not load cart totals. Please go back and try again.");
                return;
            }

            payment_section.classList.add("hidden");

            const body = {
                Status: "pay",
                ReferenceNo: referenceNo,
                KioskRegNo: getRegisterNo() || 1,
                PaymentType: type,
                subtotal: totals.subtotal || 0,
                service_charge: totals.service_charge || 0,
                total: totals.total || 0,
                discount: totals.discount || 0,
                vat: totals.vat || 0,
                less_vat: totals.less_vat || 0,
                scanned_gift_cert_codes: totals.scanned_gift_cert_codes || [],
                gift_certificate_amount: totals.gift_certificate_amount || 0
            };

            console.log("Payment body:", body);

            const options = {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(body),
            };

            fetch("../api/payment_gateway.php", options)
                .then((result) => {
                    console.log("Fetch response status:", result.status);
                    return result.json();
                })
                .then((data) => {
                    console.log("Payment gateway response:", data);
                    if (!data.success) {
                        console.error("Failed:", data.message);
                        payment_section.classList.remove("hidden");
                        alert("Error: " + data.message);
                        return;
                    }
                    processingOverlay.classList.add("flex");
                    processingOverlay.classList.remove("hidden");

                    paymentChecker = setTimeout(checkPaymentStatus, 1000);
                })
                .catch((err) => {
                    console.error("Payment error:", err);
                    payment_section.classList.remove("hidden");
                    alert("Error processing payment: " + err.message);
                });
        }

        function checkPaymentStatus() {
            if (isChecking) return;
            isChecking = true;

            const payload = {
                Status: "checking",
                ReferenceNo: getReferenceNo(),
                KioskRegNo: getRegisterNo() || 1,
            };
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            };

            fetch('../api/payment_gateway.php', options)
                .then(res => res.json())
                .then(res => {
                    console.log("Payment status check:", res);
                    const status = res.status ? res.status.toLowerCase() : "error";

                    if (!res.success) {
                        console.error("Status check failed:", res.message);
                        isChecking = false;
                        return;
                    }

                    if (status === "success") {
                        clearTimeout(paymentChecker);
                        console.log("✅ Payment success!");
                        processingOverlay.classList.add("hidden");
                        processingOverlay.classList.remove("flex");
                        successOverlay.classList.add("flex");
                        successOverlay.classList.remove("hidden");

                        setTimeout(() => {
                            clearAll();
                            window.location.href = "../index.php";
                        }, 3000);
                        isChecking = false;
                        return;
                    }

                    if (status === "error") {
                        clearTimeout(paymentChecker);
                        console.log("❌ Payment failed.");
                        const errorMsg = res.message || "Payment failed.";
                        errorOverlay.querySelector("h4:nth-of-type(2)").innerText = errorMsg;
                        processingOverlay.classList.add("hidden");
                        processingOverlay.classList.remove("flex");
                        errorOverlay.classList.add("flex");
                        errorOverlay.classList.remove("hidden");

                        setTimeout(() => {
                            errorOverlay.classList.add("hidden");
                            errorOverlay.classList.remove("flex");
                            payment_section.classList.remove("hidden");
                        }, 5000);
                        isChecking = false;
                        return;
                    }
                    //  CHAANGE THIS LAGI FOR DEBUGGING PURPOSES

                    isChecking = false;
                    paymentChecker = setTimeout(checkPaymentStatus, 300);
                })
                .catch(err => {
                    console.error("Status check error:", err);
                    isChecking = false;

                    paymentChecker = setTimeout(checkPaymentStatus, 300);
                });
        }
    </script>
</body>

</html>