<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/../config/kioskLanguage.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="paymentTitle">Mode of Payment</title>

    <script src="../js/script.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        .montserrat {
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
            font-weight: 700;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Montserrat", sans-serif;
        }

        body {
            background-image: url(../images/background/selected.png);
            color: #152d07;
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

        .payment-option.disabled {
            pointer-events: none;
            cursor: not-allowed;
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
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            font-size: 30px;
        }

        .total-row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            font-size: 50px;
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
            font-weight: 700;
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
            background-color: #ffebcd;
            border: 2px solid #152d07;
            padding: 2rem;
            margin: 0 1rem;
            border-radius: 10px;
            width: 700px;
            height: 470px;
        }

        .modal-inner {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .modal-icon img {
            max-width: 120px;
            height: auto;
            margin: 0 auto;
        }

        .modal h1 {
            font-size: 40px;
            font-weight: 700;

        }

        .modal p {
            font-size: 30px;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            padding-top: 1rem;
        }

        .modal-btn {
            border: 2px solid #152d07;
            padding: 0.75rem 2rem;
            cursor: pointer;
            height: 80px;
            width: 250px;
            background: #152d07;
            color: #ffebcd;
            font-size: 1.875rem;
            transition: all 0.3s;
            border-radius: 10px;
        }

        .modal-btn:hover {
            background-color: #152d07;
            color: #ffebcd;
        }

        .modal-btn.secondary {
            border-color: #721719;
            color: #721719;
            background-color: transparent;
        }

        .modal-btn.secondary:hover {
            background-color: #721719;
            color: #ffebcd;
        }

        .payment-confirm-icon {
            font-size: 80px;
            margin-top: 10px;
            display: block;
        }
    </style>
    <link rel="preload" as="image" href="../images/logos/logo1.webp">
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="../images/logos/logo1.webp" alt="Main Logo">
        </div>

        <div id="payment_container">
            <div id="payment_section">
                <div class="content-scroll">
                    <div class="content-inner">
                        <h2 class="section-title" style="font-size: 50px;" data-i18n="paymentHeading">MODE OF PAYMENT</h2>
                        <p class="divider"></p>
                        <div class="payment-options">
                            <div id="card" class="payment-option">
                                <p style="font-size: 25px" data-i18n="cardUpper">DEBIT / CREDIT CARD</p>
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
                                <p style="font-size: 25px" data-i18n="cashUpper">CASH</p>
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
                            <span data-i18n="subtotal">Subtotal</span>
                            <span id="subtotal"></span>
                        </div>
                        <div class="summary-row">
                            <span id="service-charge-label" data-i18n="paymentServiceCharge">Service Charge</span>
                            <span id="service-charge" ></span>
                        </div>
                    </div>
                    <div class="total-row">
                        <span data-i18n="totalUpper">TOTAL</span>
                        <span id="total"></span>
                    </div>
                </div>

                <div class="button-section">
                    <button id="btnBack" class="btn"
                        style="background: #721719; color: #ffebcd; border: 2px solid #ffebcd; border-radius: 10px;" data-i18n="backUpper">BACK</button>
                </div>
            </div>

            <!-- Processing Overlay -->
            <div id="processingOverlay" class="overlay">
                <div class="overlay-content">
                    <div>
                        <h4 data-i18n="useTerminal">Kindly process your payment</h4>
                        <h4 data-i18n="useTerminalLine2">using the card terminal.</h4>
                    </div>
                    <p class="divider"></p>
                    <div class="total-row">
                        <span data-i18n="totalUpper">TOTAL</span>
                        <span id="payment_total"></span>
                    </div>
                </div>
            </div>

            <!-- Success Overlay -->
            <div id="successOverlay" class="overlay">
                <div class="overlay-content">
                    <h4 data-i18n="collectReceipt">Please make sure to collect your official printed receipt. Thank you!</h4>
                    <h4 class="text-base" data-i18n="closeInThree">This Page will automatically close in 3 secs</h4>
                    <p class="divider"></p>
                </div>
                <div class="overlay-spacer"></div>
            </div>

            <!-- Error Overlay -->
            <div id="errorOverlay" class="overlay">
                <div class="overlay-content">
                    <h4 style="font-weight: 500;" data-i18n="terminalError">Card Terminal Error</h4>
                    <h4></h4>
                    <h4 class="text-base" data-i18n="closeInFive">This Page will automatically close in 5 secs</h4>
                </div>
                <div class="overlay-spacer"></div>
            </div>

            <!-- Back Confirmation Modal -->
            <div id="confirmModal" class="modal">
                <div class="modal-content">
                    <div class="modal-inner">
                        <div class="modal-icon"></div>
                        <h1 data-i18n="warning">Warning</h1>
                        <div>
                            <p><span data-i18n="confirmBack">Are you sure you want to go back to the cart?</span><br><span data-i18n="removeDiscount">Any applied discount will be removed.</span>
                            </p>
                        </div>
                        <div class="modal-buttons">
                            <button id="confirmYes" class="modal-btn" data-i18n="yes">Yes</button>
                            <button id="confirmNo" class="modal-btn secondary" data-i18n="no">No</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Over Modal -->
            <div id="startOverModal" class="modal">
                <div class="modal-content">
                    <div class="modal-inner">
                        <div class="modal-icon"></div>
                        <h1 data-i18n="warning">Warning</h1>
                        <div>
                            <p><span data-i18n="confirmStartOver">Are you sure you want to start over?</span><br><span data-i18n="removeCartItems">All items in your cart will be removed.</span></p>
                        </div>
                        <div class="modal-buttons">
                            <button id="btnStartOverYes" class="modal-btn" data-i18n="yes">Yes</button>
                            <button id="btnStartOverNo" class="modal-btn secondary" data-i18n="no">No</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Confirmation Modal -->
            <div id="paymentConfirmModal" class="modal">
                <div class="modal-content">
                    <div class="modal-inner">
                        <div class="modal-icon">
                            <span id="paymentConfirmIcon" class="payment-confirm-icon"></span>
                        </div>
                        <h1 data-i18n="confirmPayment">Confirm Payment</h1>
                        <div>
                            <p id="paymentConfirmText" data-i18n="confirmMethod">Proceed with this payment method?</p>
                        </div>
                        <div class="modal-buttons">
                            <button id="paymentConfirmProceed" class="modal-btn" data-i18n="proceed">Proceed</button>
                            <button id="paymentConfirmCancel" class="modal-btn secondary" data-i18n="cancel">Cancel</button>
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
        const paymentConfirmModal = document.getElementById("paymentConfirmModal");
        const paymentConfirmProceed = document.getElementById("paymentConfirmProceed");
        const paymentConfirmCancel = document.getElementById("paymentConfirmCancel");
        const paymentConfirmText = document.getElementById("paymentConfirmText");
        const paymentConfirmIcon = document.getElementById("paymentConfirmIcon");

        let pendingPaymentAction = null;

        // Extract reference number from URL if present (from scan page)
        const urlParams = new URLSearchParams(window.location.search);
        const refFromUrl = urlParams.get('referenceNo');
        if (refFromUrl && !getReferenceNo()) {
            setReferenceNo(refFromUrl);
        }

        if (!getRegisterNo()) {
            console.log("Loading register number from server...");
            loadRegisterNo().then(() => {});
        }

        const totals = getTotals();

        // ─── Back button ───────────────────────────────────────────────
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

        // ─── Start Over ────────────────────────────────────────────────
        btnStartOverYes.addEventListener("click", () => {
            startOverModal.classList.remove("show");
            clearAll();
            window.location.href = '../index.php';
        });

        btnStartOverNo.addEventListener("click", () => {
            startOverModal.classList.remove("show");
        });

        // ─── Modal backdrop / Escape dismiss ──────────────────────────
        confirmModal.addEventListener("click", (e) => {
            if (e.target === confirmModal) confirmModal.classList.remove("show");
        });

        startOverModal.addEventListener("click", (e) => {
            if (e.target === startOverModal) startOverModal.classList.remove("show");
        });

        paymentConfirmModal.addEventListener("click", (e) => {
            if (e.target === paymentConfirmModal) {
                paymentConfirmModal.classList.remove("show");
                pendingPaymentAction = null;
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                confirmModal.classList.remove("show");
                startOverModal.classList.remove("show");
                paymentConfirmModal.classList.remove("show");
                pendingPaymentAction = null;
            }
        });

        function showStartOverModal() {
            startOverModal.classList.add("show");
        }

        // ─── Payment Confirm Modal helpers ────────────────────────────
        function showPaymentConfirm(label, icon, action) {
            paymentConfirmIcon.innerHTML = icon;
            paymentConfirmText.textContent = kioskT("confirmPaymentWith", { method: label });
            pendingPaymentAction = action;
            paymentConfirmModal.classList.add("show");
        }

        paymentConfirmProceed.addEventListener("click", () => {
            paymentConfirmModal.classList.remove("show");
            if (pendingPaymentAction) {
                pendingPaymentAction();
                pendingPaymentAction = null;
            }
        });

        paymentConfirmCancel.addEventListener("click", () => {
            paymentConfirmModal.classList.remove("show");
            pendingPaymentAction = null;
        });

        // ─── Show totals ───────────────────────────────────────────────
        function showTotals() {
            const orderTotalsStr = localStorage.getItem('orderTotals');

            if (orderTotalsStr) {
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
            } else {
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
            }
        }

        let paymentChecker = null;
        let isChecking = false;

        showTotals();

        // ─── Payment option click handlers (now show confirm first) ───
        if (card) {
            card.addEventListener("click", (e) => {
                showPaymentConfirm(kioskT("cardMethod"),
                    "<img src='../images/icons/question.png' alt='confirm icon' />", () => sendPayment(e,
                        "CreditDebit"));
            });
        } else {
            console.error("Card element not found!");
        }

        if (qrph) {
            qrph.addEventListener("click", (e) => {
                showPaymentConfirm("QRPH", "<img src='../images/icons/question.png' alt='confirm icon' />", () =>
                    sendPayment(e, "GenericMerchantQR-qr:qrph"));
            });
        } else {
            console.error("QRPH element not found!");
        }

        if (cash) {
            cash.addEventListener("click", (e) => {
                showPaymentConfirm(kioskT("cashMethod"), "<img src='../images/icons/question.png' alt='confirm icon' />", () =>
                    processCashPayment(e));

            });
        } else {
            console.error("CASH element not found!");
        }

        // ─── Get payment totals ────────────────────────────────────────
        function getPaymentTotals() {
            const orderTotalsStr = localStorage.getItem('orderTotals');

            if (orderTotalsStr) {
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

        // ─── Cash payment ──────────────────────────────────────────────
        async function processCashPayment(event) {
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
            const customerID = localStorage.getItem('customerID') || '';

            const orderTotalsStr = localStorage.getItem('orderTotals');
            let cartItems = [];

            if (orderTotalsStr) {
                console.log("Scanned order detected, fetching order details...");
                try {
                    const response = await fetch('../api/fetchOrders.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            referenceNo: referenceNo
                        })
                    });

                    const data = await response.json();
                    console.log("Fetched order details:", data);

                    if (data.success && data.data && data.data.items) {
                        cartItems = data.data.items;
                        console.log("Successfully loaded scanned order items:", cartItems);
                    } else {
                        console.error("API Response:", data);
                        alert("Error: " + (data.message || "Could not fetch order items from server"));
                        return;
                    }
                } catch (error) {
                    console.error("Error fetching order items:", error);
                    alert("Error fetching order items: " + error.message);
                    return;
                }
            } else {
                cartItems = JSON.parse(localStorage.getItem('currentOrder') || '[]');
            }

            console.log('Raw cart items from localStorage:', cartItems);

            const items = cartItems.map(item => ({
                itemID: item.itemID || item.id || item.ItemID || '',
                itemLookupCode: item.itemLookupCode || item.lookupCode || item.ItemLookupCode || '',
                description: item.description || item.name || item.Description || '',
                price: parseFloat(item.price || item.UnitPriceSold || 0) || 0,
                quantity: parseInt(item.quantity || item.Quantity || 0) || 0
            }));

            const transactionItems = cartItems.flatMap(item => {
                const itemCode = item.itemLookupCode || item.lookupCode || item.ItemLookupCode || item
                    .itemCode || '';
                const qty = parseInt(item.quantity || item.Quantity || item.qty || 0) || 0;
                const basePrice = parseFloat(item.price || item.UnitPriceSold || 0) || 0;

                const parent = {
                    itemCode: itemCode,
                    description: item.description || item.name || item.Description || '',
                    qty: qty,
                    price: parseFloat(item.totalPrice || item.total || basePrice) /
                        qty, // ✅ 174 / 1 = 174 unit price
                    extended: parseFloat(item.totalPrice || item.total || (basePrice * qty)), // ✅ 174
                    salesRepNo: item.salesRepNo || '',
                    discountReasonCode: '',
                    originalPrice: basePrice, // ✅ 130 original
                    salesTax: 0.00,
                    uom: item.uom || 'ORDER',
                    packingQty: 1,
                    itemType: 0,
                    lineAddOn: 0.00,
                    taxExemptAmt: 0.00,
                    lineDiscount: parseFloat(item.lineDiscount || item.LineDiscount || 0) || 0,
                    parentId: null,
                    sync: 0,
                    modifiers: [],
                    specialInstruction: item.specialInstruction ?? null,
                };

                const modifiers = (item.modifiers || []).map(mod => ({
                    itemCode: mod.itemOption || '',
                    description: mod.name || '',
                    qty: qty,
                    price: 0,
                    salesRepNo: '',
                    discountReasonCode: 'Bundled',
                    originalPrice: 0,
                    salesTax: 0.00,
                    uom: 'EA',
                    packingQty: 1,
                    itemType: 1,
                    lineAddOn: 0.00,
                    taxExemptAmt: 0.00,
                    lineDiscount: 0.00,
                    parentId: null,
                    sync: 0,
                    modifiers: [],
                    specialInstruction: null
                }));

                return [parent, ...modifiers];
            }).filter(item => item.itemCode && item.qty > 0);

            console.log('Mapped transaction items before filter:', cartItems.map(item => ({
                itemCode: item.itemLookupCode || item.lookupCode || item.ItemLookupCode || item
                    .itemCode || 'MISSING',
                qty: parseInt(item.quantity || item.Quantity || item.qty || 0) || 0,
                originalItem: item
            })));

            console.log('Transaction Items to send:', transactionItems);

            if (transactionItems.length === 0) {
                console.error('No valid items found. Cart items count:', cartItems.length);
                alert('Error: No valid items found in cart');
                return;
            }
            console.log("Hold entries payload:", JSON.stringify(transactionItems, null, 2));
            try {
                const orderTypeMap = {
                    'For Here': 'Dine-In',
                    'To Go': 'Take Out'
                };

                const orderType = orderTypeMap[localStorage.getItem('orderType')] ?? localStorage.getItem('orderType');
                const orderName = localStorage.getItem('orderName') || '';
                const qrResponse = await fetch('../api/saveQRCode.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        referenceNo: referenceNo,
                        kioskRegNo: kioskRegNo,
                        customerID: customerID,
                        items: items,
                        orderType: orderType,
                        orderName: orderName
                    })
                });

                const qrData = await qrResponse.json();

                if (!qrData.success) {
                    throw new Error(qrData.message || 'Failed to save QR code');
                }
                console.log("Hold entries payload:", JSON.stringify(transactionItems, null, 2));

                const entriesResponse = await fetch('../api/saveholdentries.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        referenceNo: referenceNo,
                        items: transactionItems
                    })
                });

                const entriesData = await entriesResponse.json();
                console.log("Hold entries response:", JSON.stringify(entriesData, null, 2));

                if (!entriesData.success) {
                    throw new Error(entriesData.message || 'Failed to save transaction entries');
                }

                await printCashPaymentQRCode(referenceNo, kioskRegNo);

            } catch (error) {
                console.error("Error processing cash payment:", error);
                alert("Error processing cash payment: " + error.message);
            }
        }

        async function printCashPaymentQRCode(referenceNo, kioskRegNo) {
            console.log("Opening print window for reference:", referenceNo);

            try {
                const response = await fetch('../print_receipt.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
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

        async function syncKioskCartToTransactionItem() {
            const referenceNo = getReferenceNo();
            if (!referenceNo) {
                throw new Error('Reference number is required for cart sync');
            }

            const kioskRegNo = getRegisterNo() || 1;
            const orderType = localStorage.getItem('orderType') || 'dine-in';
            console.log('Order Type for sync:', orderType);
            console.log('Kiosk Register No:', kioskRegNo);

            const currentOrder = JSON.parse(localStorage.getItem('currentOrder') || '[]');
            console.log('Current Order for sync:', currentOrder);

            const cartPayload = currentOrder.flatMap(item => {
                const parent = {
                    item_code: item.itemCode,
                    description: item.name,
                    quantity: item.quantity,
                    price: item.price,
                    total: item.totalPrice,
                    taxable: true,
                    is_modifier: false,
                    special_instruction: item.specialInstruction || ''
                };

                const modifiers = (item.modifiers || []).map(mod => ({
                    item_code: mod.itemOption || '',
                    description: mod.name || '',
                    quantity: item.quantity,
                    price: 0,
                    total: 0,
                    taxable: false,
                    is_modifier: true,
                    special_instruction: ''
                }));

                return [parent, ...modifiers];
            });

            console.log('Cart Payload to send:', cartPayload, 'OrderType:', orderType, 'KioskRegNo:', kioskRegNo);

            if (cartPayload.length === 0) {
                console.warn('No items in cart payload');
                return;
            }

            const response = await fetch('../api/addToCart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ReferenceNo: referenceNo,
                    KioskRegNo: kioskRegNo,
                    cart: cartPayload,
                    orderType: orderType
                })
            });

            const result = await response.json();
            console.log('Sync response from addToCart.php:', result);

        }

        // ─── Card / QRPH payment ───────────────────────────────────────
        async function sendPayment(event, type) {
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

            try {
                await syncKioskCartToTransactionItem();
            } catch (err) {
                console.error('Sync to KIOSK_TransactionItem failed:', err);
                alert('Warning: Failed to update cart items for payment. Please retry.');
                payment_section.classList.remove('hidden');
                return;
            }

            payment_section.classList.add("hidden");

            const orderName = localStorage.getItem('orderName') || '';
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
                gift_certificate_amount: totals.gift_certificate_amount || 0,
                orderName: orderName
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

        // ─── Poll payment status ───────────────────────────────────────
        function checkPaymentStatus() {
            if (isChecking) return;
            isChecking = true;

            const payload = {
                Status: "checking",
                ReferenceNo: getReferenceNo(),
                KioskRegNo: getRegisterNo() || 1
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

                    isChecking = false;
                    paymentChecker = setTimeout(checkPaymentStatus, 300);
                })
                .catch(err => {
                    console.error("Status check error:", err);
                    isChecking = false;
                    paymentChecker = setTimeout(checkPaymentStatus, 300);
                });
        }

        const currentOrder = JSON.parse(localStorage.getItem('currentOrder') || '[]');
    </script>
</body>

</html>