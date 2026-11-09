<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/../config/kioskLanguage.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="yourOrder">Your Order</title>

    <script src="../js/paymentmodal.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/item.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --green: #152d07;
            --cream: #ffefce;
            --red: #8c1b24;
            --red-soft: rgba(140, 27, 36, 0.08);
            --green-soft: rgba(21, 45, 7, 0.06);
            --border: rgba(21, 45, 7, 0.18);
            --border-mid: rgba(21, 45, 7, 0.28);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: 'Montserrat', serif;
            background: var(--cream);
            color: var(--green);
            overflow: hidden;
        }

        .page {
            display: grid;
            grid-template-rows: auto 1fr;
            height: 100vh;
            background-image: url('../images/background/selected.png');
            background-size: cover;
            background-position: center;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100px;
            padding: clamp(14px, 3vw, 24px) clamp(20px, 5vw, 48px);
            margin-top: 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 239, 206, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            position: relative;
            z-index: 10;
        }

        .header-back {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #152d07;
            border: 2px solid var(--border-mid);
            border-radius: 10px;
            padding: 10px 20px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #ffefce;
            cursor: pointer;
            transition: background 0.18s, border-color 0.18s, transform 0.15s;
            text-decoration: none;
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            pointer-events: none;
        }

        .header-title h1 {
            font-size: 40px;
            font-weight: 900;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--green);
            line-height: 1;
        }

        .header-title p {
            font-size: 15px;
            color: var(--green);
            opacity: 0.4;
            font-style: italic;
            margin-top: 3px;
        }

        .header-badge {
            min-width: 38px;
            height: 38px;
            border-radius: 50px;
            background: var(--green);
            color: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            padding: 0 10px;
        }

        .body {
            display: grid;
            grid-template-rows: 1fr auto;
            grid-template-columns: 1fr;
            overflow: hidden;
        }

        .items-panel {
            overflow-y: auto;
            padding: clamp(20px, 3vw, 36px) clamp(20px, 4vw, 48px);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .items-panel::-webkit-scrollbar {
            width: 6px;
        }

        .items-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .items-panel::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        .section-label {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--green);
            opacity: 0.38;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 88px 1fr auto;
            gap: 25px;
            align-items: center;
            background: rgba(255, 239, 206, 0.78);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: clamp(14px, 2vw, 20px);
            transition: box-shadow 0.2s, border-color 0.2s;
            animation: itemIn 0.3s ease both;
        }

        .cart-item:hover {
            box-shadow: 0 4px 20px rgba(21, 45, 7, 0.1);
            border-color: var(--border-mid);
        }

        @keyframes itemIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cart-item-img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            background: rgba(21, 45, 7, 0.05);
        }

        .cart-item-info {
            min-width: 0;
        }

        .cart-item-name {
            font-size: 23px;
            font-weight: 700;
            color: var(--green);
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .cart-item-price-each {
            font-size: 18px;
            color: var(--green);
            opacity: 0.4;
            font-style: italic;
            margin-bottom: 5px;
        }

        .cart-item-modifiers {
            font-size: 15px;
            color: var(--red);
            font-style: italic;
        }

        .cart-item-instruction {
            font-size: 15px;
            color: var(--green);
            opacity: 0.45;
            font-style: italic;
            margin-top: 3px;
        }

        .cart-item-controls {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .remove-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--green);
            opacity: 0.22;
            font-size: 18px;
            line-height: 1;
            padding: 3px 6px;
            border-radius: 6px;
            transition: opacity 0.15s, color 0.15s, background 0.15s;
        }

        .remove-btn:hover {
            opacity: 1;
            color: var(--red);
            background: var(--red-soft);
        }

        .qty-row {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #ffefce;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 4px 6px;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, transform 0.12s;
            font-family: inherit;
            line-height: 1;
        }

        .qty-btn.minus {
            background: rgba(140, 27, 36, 0.1);
            color: var(--red);
        }

        .qty-btn.minus:hover {
            background: rgba(140, 27, 36, 0.2);
            transform: scale(1.1);
        }

        .qty-btn.minus:active {
            transform: scale(0.92);
        }

        .qty-btn.plus {
            background: var(--green);
            color: var(--cream);
        }

        .qty-btn.plus:hover {
            background: #1e3f0a;
            transform: scale(1.1);
        }

        .qty-btn.plus:active {
            transform: scale(0.92);
        }

        .qty-display {
            min-width: 30px;
            text-align: center;
            font-size: 17px;
            font-weight: 800;
            color: var(--green);
        }

        .cart-item-total {
            font-size: 18px;
            font-weight: 800;
            color: var(--green);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 80px 20px;
            opacity: 0.32;
        }

        .empty-state svg {
            width: 60px;
            height: 60px;
        }

        .empty-state p {
            font-size: 17px;
            font-style: italic;
            text-align: center;
            line-height: 1.6;
        }

        /* ── Summary panel ── */
        .summary-panel {
            background: #1b3d0b;
            color: var(--cream);
            display: flex;
            flex-direction: column;
            gap: clamp(10px, 2vw, 16px);
            padding: clamp(16px, 2vw, 24px) clamp(22px, 4vw, 48px);
            border-top: 1px solid rgba(21, 45, 7, 0.3);
            border: 2px dashed rgba(255, 239, 206, 0.2);
            flex-shrink: 0;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--cream);
            opacity: 0.45;
        }

        .retro-top {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .retro-left {
            flex: 1;
            display: flex;
            gap: 60px;
        }

        .retro-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .re-lbl {
            font-size: 15px;
            color: rgba(255, 239, 206, 0.4);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .re-val {
            font-size: 40px;
            font-weight: 800;
            color: var(--cream);
        }

        .retro-vdiv {
            width: 1px;
            height: 100px;
            background: rgba(255, 239, 206, 0.15);
            align-self: stretch;
            margin: 0 clamp(16px, 3vw, 26px);
        }

        .retro-total {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .rt-lbl {
            font-size: 15px;
            color: rgba(255, 239, 206, 0.4);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .rt-val {
            font-size: 45px;
            font-weight: 900;
            color: var(--cream);
            letter-spacing: -0.02em;
        }

        .retro-hdiv {
            height: 1px;
            background: repeating-linear-gradient(to right,
                    rgba(255, 239, 206, 0.25) 0px,
                    rgba(255, 239, 206, 0.25) 6px,
                    transparent 6px,
                    transparent 12px);
        }

        .place-order-btn {
            background: #ffefce;
            border: 1.5px solid rgba(255, 239, 206, 0.4);
            color: #152d07;
            padding: 13px;
            font-size: 35px;
            font-weight: 900;
            cursor: pointer;
            border-radius: 8px;
            width: 100%;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            transition: background 0.2s, border-color 0.2s;
        }

        .place-order-btn:hover {
            background: #152d07;
            border-color: #ffefce;
            color: #ffefce;
        }

        .spin {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 2px solid rgba(255, 239, 206, 0.25);
            border-top-color: var(--cream);
            border-radius: 50%;
            animation: rotate 0.65s linear infinite;
            vertical-align: middle;
        }

        @keyframes rotate {
            to {
                transform: rotate(360deg);
            }
        }

        .toast {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%) translateY(16px);
            background: var(--green);
            color: var(--cream);
            padding: 12px 26px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.06em;
            opacity: 0;
            transition: opacity 0.22s, transform 0.22s;
            pointer-events: none;
            z-index: 999;
            white-space: nowrap;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ── Order Name Modal ── (must be OUTSIDE media query) */
        .order-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: var(--cream);
            z-index: 100;
            flex-direction: column;
        }

        .order-modal-overlay.active {
            display: flex;
        }

        .order-modal {
            display: grid;
            grid-template-rows: auto 1fr auto;
            width: 100%;
            height: 100%;
            background: var(--cream);
            /* removed blur/translucency */
        }

        .order-modal-header {
            background: var(--green);
            padding: 36px 48px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .order-modal-header p {
            font-size: 20px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(255, 239, 206, 0.4);
        }

        .order-modal-header h2 {
            font-size: 52px;
            font-weight: 900;
            color: var(--cream);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin: 0;
            line-height: 1;
        }

        /* Step indicator */
        .steps-bar {
            display: flex;
            align-items: center;
        }

        .step-dot {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
            font-family: 'Montserrat', serif;
        }

        .step-dot.done {
            background: rgba(255, 239, 206, 0.22);
            color: rgba(255, 239, 206, 0.55);
        }

        .step-dot.active {
            background: var(--cream);
            color: var(--green);
        }

        .step-dot.inactive {
            background: rgba(255, 239, 206, 0.08);
            color: rgba(255, 239, 206, 0.25);
        }

        .step-line {
            width: 40px;
            height: 1.5px;
            background: rgba(255, 239, 206, 0.15);
        }

        .step-line.done {
            background: rgba(255, 239, 206, 0.35);
        }

        .step-labels {
            display: flex;
            align-items: center;
        }


        .order-modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding: 56px 48px;
        }

        .order-modal-body label {
            display: block;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(21, 45, 7, 0.4);
            text-align: center;
        }

        .order-modal-input {
            width: 100%;
            max-width: 850px;
            padding: 30px 36px;
            font-size: 72px;
            font-weight: 900;
            font-family: 'Montserrat', serif;
            color: var(--green);
            background: rgba(255, 255, 255, 0.82);
            border: 2.5px solid var(--border);
            border-radius: 18px;
            outline: none;
            letter-spacing: 0.18em;
            text-align: center;
            text-transform: uppercase;
            transition: border-color 0.18s, background 0.18s;
        }

        .order-modal-input::placeholder {
            color: #152d0780;
            font-weight: 600;
            font-size: 40px;
            text-transform: none;
            letter-spacing: 0.10em;
        }

        .order-modal-input:focus {
            border-color: var(--green);
            background: #fff;
        }

        .order-modal-hint {
            font-size: 20px;
            color: rgba(21, 45, 7, 0.3);
            font-style: italic;
            text-align: center;
        }

        .order-modal-actions {
            padding: 28px 48px 44px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            border-top: 1px solid var(--border);
            background: rgba(255, 239, 206, 0.55);
        }

        .btn-confirm-pay {
            width: 100%;
            max-width: 620px;
            padding: 24px;
            background: var(--green);
            color: var(--cream);
            border: none;
            border-radius: 14px;
            font-size: 34px;
            font-weight: 900;
            font-family: 'Montserrat', serif;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            transition: background 0.18s, transform 0.12s;
        }

        .btn-confirm-pay:hover {
            background: #1e3f0a;
        }

        .btn-confirm-pay:active {
            transform: scale(0.98);
        }

        .btn-back-cart {
            width: 100%;
            max-width: 620px;
            padding: 18px;
            background: none;
            color: var(--green);
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Montserrat', serif;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }

        .btn-back-cart:hover {
            background: rgba(21, 45, 7, 0.05);
            border-color: var(--border-mid);
        }

        /* ── Mobile overrides ── */
        @media (max-width: 680px) {
            .summary-panel {
                flex-wrap: wrap;
                gap: 12px;
                padding: 16px 18px;
            }

            .summary-rows {
                gap: 16px;
            }

            .place-order-btn {
                width: 100%;
                margin-left: 0;
                text-align: center;
            }

            .items-panel {
                padding: 16px;
            }

            .cart-item {
                grid-template-columns: 70px 1fr auto;
                gap: 10px;
            }

            .cart-item-img {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1 data-i18n="yourOrder">Your Order</h1>
                <p id="headerSub">Loading…</p>
            </div>
            <a href="main.php" class="header-back">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0">
                    <path d="M10 13L5 8L10 3" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span data-i18n="backToMenu">Back to Menu</span>
            </a>
        </header>

        <!-- Body -->
        <div class="body">

            <!-- Items list -->
            <div class="items-panel" id="itemsPanel"></div>

            <!-- Summary bar at bottom -->
            <aside class="summary-panel">
                <div class="summary-title" data-i18n="orderSummary">Order Summary</div>
                <div class="retro-top">
                    <div class="retro-left">
                        <div class="retro-row">
                            <span class="re-lbl" data-i18n="subtotal">Subtotal</span>
                            <span class="re-val" id="sumSubtotal">—</span>
                        </div>
                        <div class="retro-row">
                            <span class="re-lbl"  data-i18n="serviceCharge">Service charge</span>
                            <span class="re-val"  id="sumServiceCharge">—</span>
                        </div>
                    </div>
                    <div class="retro-vdiv"></div>
                    <div class="retro-total">
                        <span class="rt-lbl" data-i18n="total">Total</span>
                        <span class="rt-val" id="sumTotal">—</span>
                    </div>
                </div>
                <div class="retro-hdiv"></div>
                <button class="place-order-btn" id="placeOrderBtn" onclick="placeOrder()" data-i18n="placeOrder">Place Order</button>
            </aside>

        </div>
    </div>

    <div class="toast" id="toast"></div>

    <!-- Order Name Modal — placed BEFORE the script so getElementById works immediately -->
    <div class="order-modal-overlay" id="orderModalOverlay">
        <div class="order-modal">

            <div class="order-modal-header">
                <div class="steps-bar">
                    <div class="step-dot done">✓</div>
                    <div class="step-line done"></div>
                    <div class="step-dot done">✓</div>
                    <div class="step-line done"></div>
                    <div class="step-dot active">3</div>
                    <div class="step-line"></div>
                    <div class="step-dot inactive">4</div>
                </div>
                <p data-i18n="orderStep">Step 3 of 4 — Almost there</p>
                <h2 data-i18n="confirmOrder">Confirm Order</h2>
            </div>

            <div class="order-modal-body">
                <label for="orderNameInput" data-i18n="enterOrderName">Enter your preferred name so we can call you when your order is
                    ready</label>
                <input class="order-modal-input" id="orderNameInput" type="text" maxlength="6" placeholder="e.g. JULIAN" data-i18n-placeholder="exampleName"
                    autocomplete="off" spellcheck="false">
                <p class="order-modal-hint" data-i18n="nameLimit">Max 6 characters</p>
            </div>

            <div class="order-modal-actions">
                <button class="btn-confirm-pay" id="btnConfirmPay" onclick="confirmAndPay()" data-i18n="confirmAndPay">Confirm &amp; Pay</button>
                <button class="btn-back-cart" onclick="closeOrderModal()" data-i18n="backToCart">← Back to Cart</button>
            </div>

        </div>
    </div>

    <!-- Script is LAST — all HTML elements exist by the time this runs -->
    <script>
        function esc(t) {
            if (!t) return "";
            return String(t).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function fmt(n) {
            return "₱" + Number(n ?? 0).toLocaleString("en-PH", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function toast(msg) {
            const el = document.getElementById("toast");
            el.textContent = msg;
            el.classList.add("show");
            setTimeout(() => el.classList.remove("show"), 2200);
        }

        function syncCartToServer(order) {
            const referenceNo = localStorage.getItem("referenceNo") || "";
            const orderType = localStorage.getItem('orderType') || 'dine-in';

            const cartPayload = order.flatMap(item => {
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

            return fetch("../api/addToCart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        ReferenceNo: referenceNo,
                        cart: cartPayload,
                        orderType: orderType
                    }),
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success && result.data) {
                        localStorage.setItem("totals", JSON.stringify(result.data));
                        return result;
                    } else {
                        console.error("Server Error:", result.message);
                        throw new Error(result.message);
                    }
                })
                .catch(err => {
                    console.error("Sync failed:", err);
                    toast(kioskT("syncFailed"));
                });
        }

        function renderTotals() {
            const t = JSON.parse(localStorage.getItem("totals") || "{}");
            document.getElementById("sumSubtotal").textContent = fmt(t.subtotal);
            document.getElementById("sumServiceCharge").textContent = fmt(t.service_charge);
            document.getElementById("sumTotal").textContent = fmt(t.total);
        }

        function setTotalsLoading(on) {
            const spinner = '<span class="spin"></span>';
            ["sumSubtotal", "sumServiceCharge", "sumTotal"].forEach(id => {
                if (on) document.getElementById(id).innerHTML = spinner;
            });
            document.getElementById("placeOrderBtn").disabled = on;
        }

        function renderCart() {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            const panel = document.getElementById("itemsPanel");
            const qty = order.reduce((s, i) => s + i.quantity, 0);
            panel.innerHTML = "";

            document.getElementById("headerSub").textContent =
                qty === 0 ? kioskT("noItems") : kioskT("itemCount", { count: qty });

            if (!order.length) {
                window.location.href = "main.php";
                return;
            }

            document.getElementById("placeOrderBtn").disabled = false;

            const lbl = document.createElement("div");
            lbl.className = "section-label";
            lbl.textContent = kioskT("items");
            panel.appendChild(lbl);

            order.forEach((item, idx) => {
                const modHtml = item.modifiers?.length ?
                    `<div class="cart-item-modifiers">+&nbsp;${item.modifiers.map(m =>
                        esc(m.name) + (Number(m.price) > 0 ? ` (+₱${Number(m.price).toFixed(2)})` : "")
                    ).join(", ")}</div>` : "";

                const instrHtml = item.specialInstruction ?
                    `<div class="cart-item-instruction">📝 ${esc(item.specialInstruction)}</div>` : "";

                const card = document.createElement("div");
                card.className = "cart-item";
                card.style.animationDelay = `${idx * 0.055}s`;
                card.innerHTML = `
                    <img class="cart-item-img"
                         src="${esc(item.imageSrc || "../images/default-item.png")}"
                         alt="${esc(item.name)}"
                         onerror="this.src='../images/default-item.png'">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${esc(item.name)}</div>
                        <div class="cart-item-price-each">${kioskT("eachPrice", { price: Number(item.price).toFixed(2) })}</div>
                        ${modHtml}${instrHtml}
                    </div>
                    <div class="cart-item-controls">
                        <button class="remove-btn" title="${kioskT("remove")}">✕</button>
                        <div class="qty-row">
                            <button class="qty-btn minus">−</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button class="qty-btn plus">+</button>
                        </div>
                        <div class="cart-item-total">${fmt(item.totalPrice)}</div>
                    </div>
                `;

                card.querySelector(".qty-btn.minus").addEventListener("click", () => updateQty(idx, -1));
                card.querySelector(".qty-btn.plus").addEventListener("click", () => updateQty(idx, 1));
                card.querySelector(".remove-btn").addEventListener("click", () => removeItem(idx));

                panel.appendChild(card);
            });

            renderTotals();
        }

        function updateQty(idx, change) {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            if (!order[idx]) return;

            if (change < 0 && order[idx].quantity <= 1) {
                removeItem(idx);
                return;
            }
            order[idx].quantity += change;

            const modSum = (order[idx].modifiers || []).reduce((s, m) => s + (Number(m.price) || 0), 0);
            order[idx].totalPrice = +((Number(order[idx].price) + modSum) * order[idx].quantity).toFixed(2);

            localStorage.setItem("currentOrder", JSON.stringify(order));
            renderCart();
            setTotalsLoading(true);

            syncCartToServer(order).then(() => {
                setTotalsLoading(false);
                renderTotals();
            });
        }

        function removeItem(idx) {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            if (idx < 0 || idx >= order.length) return;

            const name = order[idx].name;
            order.splice(idx, 1);
            localStorage.setItem("currentOrder", JSON.stringify(order));

            renderCart();
            setTotalsLoading(true);

            syncCartToServer(order).then(() => {
                setTotalsLoading(false);
                renderTotals();
                toast(kioskT("itemRemoved", { name }));
            });
        }

        function placeOrder() {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            if (!order.length) return;

            const overlay = document.getElementById("orderModalOverlay");
            const input = document.getElementById("orderNameInput");
            overlay.classList.add("active");
            setTimeout(() => input.focus(), 260);
        }

        function closeOrderModal() {
            document.getElementById("orderModalOverlay").classList.remove("active");
        }

        function confirmAndPay() {
            const input = document.getElementById("orderNameInput");
            const val = input.value.trim().toUpperCase();

            if (!val) {
                input.style.borderColor = "#8c1b24";
                input.focus();
                toast(kioskT("nameRequired"));
                setTimeout(() => input.style.borderColor = "", 1800);
                return;
            }

            localStorage.setItem("orderName", val);
            window.location.href = "kioskpayment.php";
        }

        // Close modal on overlay click
        document.getElementById("orderModalOverlay").addEventListener("click", function(e) {
            if (e.target === this) closeOrderModal();
        });

        // Enforce uppercase as user types
        document.getElementById("orderNameInput").addEventListener("input", function() {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });

        document.addEventListener("DOMContentLoaded", renderCart);
    </script>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="yourOrder">Your Order</title>

    <script src="../js/paymentmodal.js"></script>
    <script src="../js/item.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --green: #152d07;
            --cream: #ffefce;
            --red: #8c1b24;
            --red-soft: rgba(140, 27, 36, 0.08);
            --green-soft: rgba(21, 45, 7, 0.06);
            --border: rgba(21, 45, 7, 0.18);
            --border-mid: rgba(21, 45, 7, 0.28);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: 'Montserrat', serif;
            background: var(--cream);
            color: var(--green);
            overflow: hidden;
        }

        .page {
            display: grid;
            grid-template-rows: auto 1fr;
            height: 100vh;
            background-image: url('../images/background/selected.png');
            background-size: cover;
            background-position: center;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100px;
            padding: clamp(14px, 3vw, 24px) clamp(20px, 5vw, 48px);
            margin-top: 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 239, 206, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            position: relative;
            z-index: 10;
        }

        .header-back {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #152d07;
            border: 2px solid var(--border-mid);
            border-radius: 10px;
            padding: 10px 20px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #ffefce;
            cursor: pointer;
            transition: background 0.18s, border-color 0.18s, transform 0.15s;
            text-decoration: none;
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            pointer-events: none;
        }

        .header-title h1 {
            font-size: 40px;
            font-weight: 900;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--green);
            line-height: 1;
        }

        .header-title p {
            font-size: 15px;
            color: var(--green);
            opacity: 0.4;
            font-style: italic;
            margin-top: 3px;
        }

        .header-badge {
            min-width: 38px;
            height: 38px;
            border-radius: 50px;
            background: var(--green);
            color: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            padding: 0 10px;
        }

        .body {
            display: grid;
            grid-template-rows: 1fr auto;
            grid-template-columns: 1fr;
            overflow: hidden;
        }

        .items-panel {
            overflow-y: auto;
            padding: clamp(20px, 3vw, 36px) clamp(20px, 4vw, 48px);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .items-panel::-webkit-scrollbar {
            width: 6px;
        }

        .items-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .items-panel::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        .section-label {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--green);
            opacity: 0.38;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 88px 1fr auto;
            gap: 25px;
            align-items: center;
            background: rgba(255, 239, 206, 0.78);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: clamp(14px, 2vw, 20px);
            transition: box-shadow 0.2s, border-color 0.2s;
            animation: itemIn 0.3s ease both;
        }

        .cart-item:hover {
            box-shadow: 0 4px 20px rgba(21, 45, 7, 0.1);
            border-color: var(--border-mid);
        }

        @keyframes itemIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cart-item-img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            background: rgba(21, 45, 7, 0.05);
        }

        .cart-item-info {
            min-width: 0;
        }

        .cart-item-name {
            font-size: 23px;
            font-weight: 700;
            color: var(--green);
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .cart-item-price-each {
            font-size: 18px;
            color: var(--green);
            opacity: 0.4;
            font-style: italic;
            margin-bottom: 5px;
        }

        .cart-item-modifiers {
            font-size: 15px;
            color: var(--red);
            font-style: italic;
        }

        .cart-item-instruction {
            font-size: 15px;
            color: var(--green);
            opacity: 0.45;
            font-style: italic;
            margin-top: 3px;
        }

        .cart-item-controls {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .remove-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--green);
            opacity: 0.22;
            font-size: 18px;
            line-height: 1;
            padding: 3px 6px;
            border-radius: 6px;
            transition: opacity 0.15s, color 0.15s, background 0.15s;
        }

        .remove-btn:hover {
            opacity: 1;
            color: var(--red);
            background: var(--red-soft);
        }

        .qty-row {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #ffefce;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 4px 6px;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, transform 0.12s;
            font-family: inherit;
            line-height: 1;
        }

        .qty-btn.minus {
            background: rgba(140, 27, 36, 0.1);
            color: var(--red);
        }

        .qty-btn.minus:hover {
            background: rgba(140, 27, 36, 0.2);
            transform: scale(1.1);
        }

        .qty-btn.minus:active {
            transform: scale(0.92);
        }

        .qty-btn.plus {
            background: var(--green);
            color: var(--cream);
        }

        .qty-btn.plus:hover {
            background: #1e3f0a;
            transform: scale(1.1);
        }

        .qty-btn.plus:active {
            transform: scale(0.92);
        }

        .qty-display {
            min-width: 30px;
            text-align: center;
            font-size: 17px;
            font-weight: 800;
            color: var(--green);
        }

        .cart-item-total {
            font-size: 18px;
            font-weight: 800;
            color: var(--green);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 80px 20px;
            opacity: 0.32;
        }

        .empty-state svg {
            width: 60px;
            height: 60px;
        }

        .empty-state p {
            font-size: 17px;
            font-style: italic;
            text-align: center;
            line-height: 1.6;
        }

        /* ── Summary panel ── */
        .summary-panel {
            background: #1b3d0b;
            color: var(--cream);
            display: flex;
            flex-direction: column;
            gap: clamp(10px, 2vw, 16px);
            padding: clamp(16px, 2vw, 24px) clamp(22px, 4vw, 48px);
            border-top: 1px solid rgba(21, 45, 7, 0.3);
            border: 2px dashed rgba(255, 239, 206, 0.2);
            flex-shrink: 0;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--cream);
            opacity: 0.45;
        }

        .retro-top {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .retro-left {
            flex: 1;
            display: flex;
            gap: 60px;
        }

        .retro-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .re-lbl {
            font-size: 15px;
            color: rgba(255, 239, 206, 0.4);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .re-val {
            font-size: 40px;
            font-weight: 800;
            color: var(--cream);
        }

        .retro-vdiv {
            width: 1px;
            height: 100px;
            background: rgba(255, 239, 206, 0.15);
            align-self: stretch;
            margin: 0 clamp(16px, 3vw, 26px);
        }

        .retro-total {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .rt-lbl {
            font-size: 15px;
            color: rgba(255, 239, 206, 0.4);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .rt-val {
            font-size: 45px;
            font-weight: 900;
            color: var(--cream);
            letter-spacing: -0.02em;
        }

        .retro-hdiv {
            height: 1px;
            background: repeating-linear-gradient(to right,
                    rgba(255, 239, 206, 0.25) 0px,
                    rgba(255, 239, 206, 0.25) 6px,
                    transparent 6px,
                    transparent 12px);
        }

        .place-order-btn {
            background: #ffefce;
            border: 1.5px solid rgba(255, 239, 206, 0.4);
            color: #152d07;
            padding: 13px;
            font-size: 35px;
            font-weight: 900;
            cursor: pointer;
            border-radius: 8px;
            width: 100%;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            transition: background 0.2s, border-color 0.2s;
        }

        .place-order-btn:hover {
            background: #152d07;
            border-color: #ffefce;
            color: #ffefce;
        }

        .spin {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 2px solid rgba(255, 239, 206, 0.25);
            border-top-color: var(--cream);
            border-radius: 50%;
            animation: rotate 0.65s linear infinite;
            vertical-align: middle;
        }

        @keyframes rotate {
            to {
                transform: rotate(360deg);
            }
        }

        .toast {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%) translateY(16px);
            background: var(--green);
            color: var(--cream);
            padding: 12px 26px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.06em;
            opacity: 0;
            transition: opacity 0.22s, transform 0.22s;
            pointer-events: none;
            z-index: 999;
            white-space: nowrap;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ── Order Name Modal ── (must be OUTSIDE media query) */
        .order-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: var(--cream);
            z-index: 100;
            flex-direction: column;
        }

        .order-modal-overlay.active {
            display: flex;
        }

        .order-modal {
            display: grid;
            grid-template-rows: auto 1fr auto;
            width: 100%;
            height: 100%;
            background: var(--cream);
            /* removed blur/translucency */
        }

        .order-modal-header {
            background: var(--green);
            padding: 36px 48px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .order-modal-header p {
            font-size: 20px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(255, 239, 206, 0.4);
        }

        .order-modal-header h2 {
            font-size: 52px;
            font-weight: 900;
            color: var(--cream);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin: 0;
            line-height: 1;
        }

        /* Step indicator */
        .steps-bar {
            display: flex;
            align-items: center;
        }

        .step-dot {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
            font-family: 'Montserrat', serif;
        }

        .step-dot.done {
            background: rgba(255, 239, 206, 0.22);
            color: rgba(255, 239, 206, 0.55);
        }

        .step-dot.active {
            background: var(--cream);
            color: var(--green);
        }

        .step-dot.inactive {
            background: rgba(255, 239, 206, 0.08);
            color: rgba(255, 239, 206, 0.25);
        }

        .step-line {
            width: 40px;
            height: 1.5px;
            background: rgba(255, 239, 206, 0.15);
        }

        .step-line.done {
            background: rgba(255, 239, 206, 0.35);
        }

        .step-labels {
            display: flex;
            align-items: center;
        }


        .order-modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding: 56px 48px;
        }

        .order-modal-body label {
            display: block;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(21, 45, 7, 0.4);
            text-align: center;
        }

        .order-modal-input {
            width: 100%;
            max-width: 850px;
            padding: 30px 36px;
            font-size: 72px;
            font-weight: 900;
            font-family: 'Montserrat', serif;
            color: var(--green);
            background: rgba(255, 255, 255, 0.82);
            border: 2.5px solid var(--border);
            border-radius: 18px;
            outline: none;
            letter-spacing: 0.18em;
            text-align: center;
            text-transform: uppercase;
            transition: border-color 0.18s, background 0.18s;
        }

        .order-modal-input::placeholder {
            color: #152d0780;
            font-weight: 600;
            font-size: 40px;
            text-transform: none;
            letter-spacing: 0.10em;
        }

        .order-modal-input:focus {
            border-color: var(--green);
            background: #fff;
        }

        .order-modal-hint {
            font-size: 20px;
            color: rgba(21, 45, 7, 0.3);
            font-style: italic;
            text-align: center;
        }

        .order-modal-actions {
            padding: 28px 48px 44px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            border-top: 1px solid var(--border);
            background: rgba(255, 239, 206, 0.55);
        }

        .btn-confirm-pay {
            width: 100%;
            max-width: 620px;
            padding: 24px;
            background: var(--green);
            color: var(--cream);
            border: none;
            border-radius: 14px;
            font-size: 34px;
            font-weight: 900;
            font-family: 'Montserrat', serif;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            transition: background 0.18s, transform 0.12s;
        }

        .btn-confirm-pay:hover {
            background: #1e3f0a;
        }

        .btn-confirm-pay:active {
            transform: scale(0.98);
        }

        .btn-back-cart {
            width: 100%;
            max-width: 620px;
            padding: 18px;
            background: none;
            color: var(--green);
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Montserrat', serif;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }

        .btn-back-cart:hover {
            background: rgba(21, 45, 7, 0.05);
            border-color: var(--border-mid);
        }

        /* ── Mobile overrides ── */
        @media (max-width: 680px) {
            .summary-panel {
                flex-wrap: wrap;
                gap: 12px;
                padding: 16px 18px;
            }

            .summary-rows {
                gap: 16px;
            }

            .place-order-btn {
                width: 100%;
                margin-left: 0;
                text-align: center;
            }

            .items-panel {
                padding: 16px;
            }

            .cart-item {
                grid-template-columns: 70px 1fr auto;
                gap: 10px;
            }

            .cart-item-img {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <!-- Header -->
        <header class="header">
            <div class="header-title">
                <h1 data-i18n="yourOrder">Your Order</h1>
                <p id="headerSub">Loading…</p>
            </div>
            <a href="main.php" class="header-back">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0">
                    <path d="M10 13L5 8L10 3" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span data-i18n="backToMenu">Back to Menu</span>
            </a>
        </header>

        <!-- Body -->
        <div class="body">

            <!-- Items list -->
            <div class="items-panel" id="itemsPanel"></div>

            <!-- Summary bar at bottom -->
            <aside class="summary-panel">
                <div class="summary-title" data-i18n="orderSummary">Order Summary</div>
                <div class="retro-top">
                    <div class="retro-left">
                        <div class="retro-row">
                            <span class="re-lbl" data-i18n="subtotal">Subtotal</span>
                            <span class="re-val" id="sumSubtotal">—</span>
                        </div>
                        <div class="retro-row">
                            <span class="re-lbl" data-i18n="serviceCharge">Service charge</span>
                            <span class="re-val" id="sumServiceCharge">—</span>
                        </div>
                    </div>
                    <div class="retro-vdiv"></div>
                    <div class="retro-total">
                        <span class="rt-lbl" data-i18n="total">Total</span>
                        <span class="rt-val" id="sumTotal">—</span>
                    </div>
                </div>
                <div class="retro-hdiv"></div>
                <button class="place-order-btn" id="placeOrderBtn" onclick="placeOrder()" data-i18n="placeOrder">Place Order</button>
            </aside>

        </div>
    </div>

    <div class="toast" id="toast"></div>

    <!-- Order Name Modal — placed BEFORE the script so getElementById works immediately -->
    <div class="order-modal-overlay" id="orderModalOverlay">
        <div class="order-modal">

            <div class="order-modal-header">
                <div class="steps-bar">
                    <div class="step-dot done">✓</div>
                    <div class="step-line done"></div>
                    <div class="step-dot done">✓</div>
                    <div class="step-line done"></div>
                    <div class="step-dot active">3</div>
                    <div class="step-line"></div>
                    <div class="step-dot inactive">4</div>
                </div>
                <p data-i18n="orderStep">Step 3 of 4 — Almost there</p>
                <h2 data-i18n="confirmOrder">Confirm Order</h2>
            </div>

            <div class="order-modal-body">
                <label for="orderNameInput" data-i18n="enterOrderName">Enter your preferred name so we can call you when your order is
                    ready</label>
                <input class="order-modal-input" id="orderNameInput" type="text" maxlength="6" placeholder="e.g. JULIAN" data-i18n-placeholder="exampleName"
                    autocomplete="off" spellcheck="false">
                <p class="order-modal-hint" data-i18n="nameLimit">Max 6 characters</p>
            </div>

            <div class="order-modal-actions">
                <button class="btn-confirm-pay" id="btnConfirmPay" onclick="confirmAndPay()" data-i18n="confirmAndPay">Confirm &amp; Pay</button>
                <button class="btn-back-cart" onclick="closeOrderModal()" data-i18n="backToCart">← Back to Cart</button>
            </div>

        </div>
    </div>

    <!-- Script is LAST — all HTML elements exist by the time this runs -->
    <script>
        function esc(t) {
            if (!t) return "";
            return String(t).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function fmt(n) {
            return "₱" + Number(n ?? 0).toLocaleString("en-PH", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function toast(msg) {
            const el = document.getElementById("toast");
            el.textContent = msg;
            el.classList.add("show");
            setTimeout(() => el.classList.remove("show"), 2200);
        }

        function syncCartToServer(order) {
            const referenceNo = localStorage.getItem("referenceNo") || "";
            const orderType = localStorage.getItem('orderType') || 'dine-in';

            const cartPayload = order.flatMap(item => {
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

            return fetch("../api/addToCart.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        ReferenceNo: referenceNo,
                        cart: cartPayload,
                        orderType: orderType
                    }),
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success && result.data) {
                        localStorage.setItem("totals", JSON.stringify(result.data));
                        return result;
                    } else {
                        console.error("Server Error:", result.message);
                        throw new Error(result.message);
                    }
                })
                .catch(err => {
                    console.error("Sync failed:", err);
                    toast(kioskT("syncFailed"));
                });
        }

        function renderTotals() {
            const t = JSON.parse(localStorage.getItem("totals") || "{}");
            document.getElementById("sumSubtotal").textContent = fmt(t.subtotal);
            document.getElementById("sumServiceCharge").textContent = fmt(t.service_charge);
            document.getElementById("sumTotal").textContent = fmt(t.total);
        }

        function setTotalsLoading(on) {
            const spinner = '<span class="spin"></span>';
            ["sumSubtotal", "sumServiceCharge", "sumTotal"].forEach(id => {
                if (on) document.getElementById(id).innerHTML = spinner;
            });
            document.getElementById("placeOrderBtn").disabled = on;
        }

        function renderCart() {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            const panel = document.getElementById("itemsPanel");
            const qty = order.reduce((s, i) => s + i.quantity, 0);
            panel.innerHTML = "";

            document.getElementById("headerSub").textContent =
                qty === 0 ? kioskT("noItems") : kioskT("itemCount", { count: qty });

            if (!order.length) {
                window.location.href = "main.php";
                return;
            }

            document.getElementById("placeOrderBtn").disabled = false;

            const lbl = document.createElement("div");
            lbl.className = "section-label";
            lbl.textContent = kioskT("items");
            panel.appendChild(lbl);

            order.forEach((item, idx) => {
                const modHtml = item.modifiers?.length ?
                    `<div class="cart-item-modifiers">+&nbsp;${item.modifiers.map(m =>
                        esc(m.name) + (Number(m.price) > 0 ? ` (+₱${Number(m.price).toFixed(2)})` : "")
                    ).join(", ")}</div>` : "";

                const instrHtml = item.specialInstruction ?
                    `<div class="cart-item-instruction">📝 ${esc(item.specialInstruction)}</div>` : "";

                const card = document.createElement("div");
                card.className = "cart-item";
                card.style.animationDelay = `${idx * 0.055}s`;
                card.innerHTML = `
                    <img class="cart-item-img"
                         src="${esc(item.imageSrc || "../images/default-item.png")}"
                         alt="${esc(item.name)}"
                         onerror="this.src='../images/default-item.png'">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${esc(item.name)}</div>
                        <div class="cart-item-price-each">${kioskT("eachPrice", { price: Number(item.price).toFixed(2) })}</div>
                        ${modHtml}${instrHtml}
                    </div>
                    <div class="cart-item-controls">
                        <button class="remove-btn" title="${kioskT("remove")}">✕</button>
                        <div class="qty-row">
                            <button class="qty-btn minus">−</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button class="qty-btn plus">+</button>
                        </div>
                        <div class="cart-item-total">${fmt(item.totalPrice)}</div>
                    </div>
                `;

                card.querySelector(".qty-btn.minus").addEventListener("click", () => updateQty(idx, -1));
                card.querySelector(".qty-btn.plus").addEventListener("click", () => updateQty(idx, 1));
                card.querySelector(".remove-btn").addEventListener("click", () => removeItem(idx));

                panel.appendChild(card);
            });

            renderTotals();
        }

        function updateQty(idx, change) {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            if (!order[idx]) return;

            if (change < 0 && order[idx].quantity <= 1) {
                removeItem(idx);
                return;
            }
            order[idx].quantity += change;

            const modSum = (order[idx].modifiers || []).reduce((s, m) => s + (Number(m.price) || 0), 0);
            order[idx].totalPrice = +((Number(order[idx].price) + modSum) * order[idx].quantity).toFixed(2);

            localStorage.setItem("currentOrder", JSON.stringify(order));
            renderCart();
            setTotalsLoading(true);

            syncCartToServer(order).then(() => {
                setTotalsLoading(false);
                renderTotals();
            });
        }

        function removeItem(idx) {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            if (idx < 0 || idx >= order.length) return;

            const name = order[idx].name;
            order.splice(idx, 1);
            localStorage.setItem("currentOrder", JSON.stringify(order));

            renderCart();
            setTotalsLoading(true);

            syncCartToServer(order).then(() => {
                setTotalsLoading(false);
                renderTotals();
                toast(kioskT("itemRemoved", { name }));
            });
        }

        function placeOrder() {
            const order = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            if (!order.length) return;

            const overlay = document.getElementById("orderModalOverlay");
            const input = document.getElementById("orderNameInput");
            overlay.classList.add("active");
            setTimeout(() => input.focus(), 260);
        }

        function closeOrderModal() {
            document.getElementById("orderModalOverlay").classList.remove("active");
        }

        function confirmAndPay() {
            const input = document.getElementById("orderNameInput");
            const val = input.value.trim().toUpperCase();

            if (!val) {
                input.style.borderColor = "#8c1b24";
                input.focus();
                toast(kioskT("nameRequired"));
                setTimeout(() => input.style.borderColor = "", 1800);
                return;
            }

            localStorage.setItem("orderName", val);
            window.location.href = "kioskpayment.php";
        }

        // Close modal on overlay click
        document.getElementById("orderModalOverlay").addEventListener("click", function(e) {
            if (e.target === this) closeOrderModal();
        });

        // Enforce uppercase as user types
        document.getElementById("orderNameInput").addEventListener("input", function() {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });

        document.addEventListener("DOMContentLoaded", renderCart);
    </script>

</body>

</html>
