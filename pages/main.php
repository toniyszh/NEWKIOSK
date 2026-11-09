<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/../config/kioskLanguage.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title data-i18n="menuTitle">Kiosk Menu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css" crossorigin>

    <link rel="stylesheet" href="../css/orderSummary.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">


    <script src="../js/modal.js" defer></script>
    <script src="../js/behaviors.js"></script>
    <script src="../js/paymentmodal.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/item.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/8.0.20/jsrsasign-all-min.js"></script>

    <style>
        .kiosk-menu {
            display: flex;
            flex-direction: column;
            font-family: "Caslon 540", "Caslon #540", Georgia, serif;
        }

        .kiosk-menu * {
            font-family: inherit;
        }

        .kiosk-menu-header {
            --header-control-height: clamp(56px, 6vw, 76px);
            position: relative;
            z-index: 101;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-sizing: border-box;
            height: 200px;
            flex: none;
            padding: 16px clamp(12px, 5vw, 32px);
            background: #141414;
            box-shadow: 0 5px 8px -2px rgba(0, 0, 0, 0.55),
                0 16px 30px -5px rgba(0, 0, 0, 0.65);
            color: #f4f4f4;
        }

        .kiosk-menu-logo {
            position: relative;
            width: clamp(76px, calc(40vw - 48px), 420px);
            aspect-ratio: 2.5;
            overflow: hidden;
        }

        .kiosk-menu-logo img {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: auto;
            /* The visible artwork sits above the center of the PNG. */
            transform: translateY(-39%);
        }

        .kiosk-menu-actions {
            display: flex;
            align-items: center;
            gap: clamp(8px, 2.5vw, 16px);
            margin-left: auto;
            white-space: nowrap;
        }

        .kiosk-language,
        .kiosk-menu-header .view-cart-btn {
            display: inline-flex;
            align-items: center;
            box-sizing: border-box;
            min-height: var(--header-control-height);
            border: 1px solid #aaa;
            border-radius: 999px;
        }

        .kiosk-language>*,
        .kiosk-menu-header .view-cart-btn {
            font-weight: 400;
            letter-spacing: 0.04em;
        }

        .kiosk-language>* {
            display: grid;
            place-items: center;
            min-height: calc(var(--header-control-height) - 2px);
            padding: 0 clamp(8px, 2.25vw, 16px);
            border: 0;
            border-radius: inherit;
            font-size: clamp(12px, 2.5vw, 16px);
        }

        .kiosk-language button {
            background: transparent;
            color: #bbb;
            cursor: pointer;
        }

        .kiosk-language button[aria-pressed="true"] {
            background: #f4f4f4;
            color: #141414;
        }

        .kiosk-menu-header .view-cart-btn {
            position: relative;
            gap: clamp(8px, 1.2vw, 14px);
            width: auto !important;
            padding: 0 clamp(11px, 2.5vw, 30px) !important;
            background: transparent;
            color: inherit;
            font-size: clamp(14px, 2.8vw, 24px) !important;
            cursor: pointer;
        }

        .kiosk-menu-header .view-cart-btn svg {
            width: clamp(18px, 2.5vw, 26px);
            height: auto;
        }

        .kiosk-cart-count {
            position: absolute;
            top: -11px;
            right: -7px;
            display: grid;
            place-items: center;
            box-sizing: border-box;
            min-width: 23px;
            height: 23px;
            padding: 0 5px;
            border-radius: 999px;
            background: #f4f4f4;
            color: #141414;
            font-size: 11px;
        }

        .kiosk-menu>.container {
            flex: 1;
            min-height: 0;
            height: auto;
        }

        .kiosk-menu .menu-content {
            align-self: stretch;
            box-sizing: border-box;
            height: auto;
        }

        .kiosk-menu .menu-header {
            font-weight: 400;
            letter-spacing: 0.09em;
        }

        .kiosk-menu .menu-grid {
            padding-bottom: 0;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .kiosk-menu .menu-content {
                flex: 1;
                min-height: 0;
            }
        }

        @media (min-width: 769px) {
            .kiosk-menu .sidebar-container {
                align-self: stretch;
            }

            .kiosk-menu .sidebar {
                box-sizing: border-box;
                max-height: 100%;
            }
        }
    </style>
</head>

<body class="kiosk-menu">
    <header class="kiosk-menu-header">
        <div class="kiosk-menu-logo">
            <img src="../images/logos/logo.png" alt="Worth Your While">
        </div>
        <div class="kiosk-menu-actions">
            <div class="kiosk-language" role="group" aria-label="Menu language" data-i18n-aria-label="menuLanguage">
                <button type="button" lang="en" data-kiosk-language="en" aria-pressed="true" aria-label="English" disabled>EN</button>
                <button type="button" lang="ja" data-kiosk-language="ja" aria-pressed="false" disabled>日本語</button>
            </div>
            <button type="button" class="view-cart-btn" onclick="Tocart()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 3h3l2.5 11h11L21 6H6"></path>
                    <circle cx="9" cy="19" r="1.5"></circle>
                    <circle cx="17" cy="19" r="1.5"></circle>
                </svg>
                <bdi data-i18n="myOrder">My Order</bdi>
                <span class="kiosk-cart-count">0</span>
            </button>
        </div>
    </header>
    <div class="container">


        <div class="sidebar-container">
            <button class="sidebar-arrow sidebar-arrow-left">
                < </button>

                    <div class="sidebar">
                        <ul>
                            <?php include '../api/categories.php'; ?>
                        </ul>
                    </div>

                    <button class="sidebar-arrow sidebar-arrow-right">></button>
        </div>


        <div class="menu-content">
            <!-- <div style="height: 60px; color:#714a45; display: flex;margin-bottom: 10px;">
                <h1 class="menu-header"></h1>
            </div> -->
            <section aria-label="Menu items" data-i18n-aria-label="menuItems">
                <div style="height: 130px; color:white;font-size:40px; display: flex;margin-bottom: 10px;">
                    <p class="menu-header" data-i18n="menuItemsHeading">Menu Items</p>
                </div>


                <div class="menu-grid">



                    <?php include '../api/fetchitems.php'; ?>

                    <script>
                        function filterItems(categoryId) {
                            const menuItems = document.querySelectorAll('.menu-item');
                            const categoryItems = document.querySelectorAll('.category-item');
                            const selectedCategoryId = String(categoryId);
                            const selectedCategory = document.querySelector(
                                `.category-item[data-category="${selectedCategoryId}"]`);

                            if (!selectedCategory) return;

                            const heading = document.querySelector('.menu-header');
                            heading.removeAttribute('data-i18n');
                            heading.textContent = selectedCategory.textContent.trim();

                            menuItems.forEach(item => {
                                item.style.display = item.dataset.category === selectedCategoryId ? '' : 'none';
                            });
                            categoryItems.forEach(item => item.classList.toggle('active', item === selectedCategory));
                        }

                        const firstCategory = document.querySelector('.sidebar .category-item');
                        if (firstCategory) filterItems(firstCategory.dataset.category);
                    </script>

                </div>
            </section>

        </div>

    </div>


    <!-- Payment Options Modal -->
    <div id="paymentOptionsModal" class="modal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0, 0, 0, 0.8); z-index: 9999; align-items: center; justify-content: center;">
        <div class="modal-content"
            style="background: white; width: 100%; max-width: 800px; border-radius: 24px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4); overflow: hidden; text-align: center; max-height: 90vh; overflow-y: auto;">

            <div style="padding: 40px 50px 30px; position: relative; border-bottom: 1px solid #f0f0f0;">
                <h2 style="margin: 0; font-size: clamp(24px, 5vw, 36px); font-weight: 700; color: #333;" data-i18n="selectPayment">Select
                    Payment Method</h2>
                <p style="margin: 15px 0 0; color: #666; font-size: clamp(16px, 3vw, 20px);" data-i18n="choosePayment">Choose how you'd like
                    to pay for your order</p>
            </div>



            <div style="padding: clamp(20px, 5vw, 50px);">
                <div style="display: flex; flex-direction: column; gap: clamp(16px, 3vw, 24px);">


                    <button onclick="processPayment('debit-credit')"
                        style="display: flex; align-items: center; width: 100%; min-height: 80px; height: clamp(80px, 12vw, 100px); background: white; border: 1px solid #e0e0e0; border-radius: 16px; padding: 0 clamp(15px, 3vw, 30px); cursor: pointer; transition: all 0.2s ease; text-align: left; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <div
                            style="width: clamp(45px, 8vw, 60px); height: clamp(45px, 8vw, 60px); border-radius: 12px; background: #721719; display: flex; align-items: center; justify-content: center; margin-right: clamp(12px, 3vw, 24px); flex-shrink: 0;">
                            <span style="color: white; font-size: clamp(20px, 4vw, 28px);">💳</span>
                        </div>
                        <div style="flex-grow: 1; min-width: 0;">
                            <h3
                                style="margin: 0; font-size: clamp(18px, 3.5vw, 24px); font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" data-i18n="card">
                                Credit / Debit Card</h3>
                            <p style="margin: 6px 0 0; font-size: clamp(14px, 2.5vw, 18px); color: #666;" data-i18n="payWithCard">Pay
                                securely with your card</p>
                        </div>
                        <span
                            style="color: #721719; font-size: clamp(20px, 4vw, 28px); margin-left: clamp(8px, 2vw, 20px); flex-shrink: 0;">›</span>
                    </button>


                    <button onclick="processPayment('counter')"
                        style="display: flex; align-items: center; width: 100%; min-height: 80px; height: clamp(80px, 12vw, 100px); background: white; border: 1px solid #e0e0e0; border-radius: 16px; padding: 0 clamp(15px, 3vw, 30px); cursor: pointer; transition: all 0.2s ease; text-align: left; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <div
                            style="width: clamp(45px, 8vw, 60px); height: clamp(45px, 8vw, 60px); border-radius: 12px; background: #721719; display: flex; align-items: center; justify-content: center; margin-right: clamp(12px, 3vw, 24px); flex-shrink: 0;">
                            <span style="color: white; font-size: clamp(20px, 4vw, 28px);">💵</span>
                        </div>
                        <div style="flex-grow: 1; min-width: 0;">
                            <h3 style="margin: 0; font-size: clamp(18px, 3.5vw, 24px); font-weight: 600; color: #333;" data-i18n="payAtCounter">
                                Pay at Counter</h3>
                            <p style="margin: 6px 0 0; font-size: clamp(14px, 2.5vw, 18px); color: #666;" data-i18n="payAtPickup">Pay when
                                you pick up your order</p>
                        </div>
                        <span
                            style="color: #72171980; font-size: clamp(20px, 4vw, 28px); margin-left: clamp(8px, 2vw, 20px); flex-shrink: 0;">›</span>
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="padding: clamp(20px, 4vw, 30px) clamp(20px, 5vw, 50px) clamp(30px, 5vw, 40px); background: #f9f9f9; border-top: 1px solid #f0f0f0;">
                <button onclick="closePaymentModal()"
                    style="width: 100%; height: clamp(55px, 10vw, 70px); font-size: clamp(16px, 3vw, 20px); font-weight: 600; cursor: pointer; transition: all 0.2s ease; background: #721719; color: #ffebcd; border: 2px solid #ffebcd; border-radius: 10px;" data-i18n="cancelUpper">
                    CANCEL
                </button>
            </div>
        </div>
    </div>
    </div>
    <script>
        const _originalSetItem = localStorage.setItem.bind(localStorage);
        localStorage.setItem = (key, value) => {
            _originalSetItem(key, value);
            if (key === 'totals') updateTotals();
        };

        window.addEventListener('storage', (e) => {
            if (e.key === 'totals') updateTotals();
        });

        function updateTotals() {
            const {
                service_charge = 0, total = 0
            } = JSON.parse(localStorage.getItem('totals') || '{}');

            const serviceChargeEl = document.getElementById('service-charge');
            const totalEl = document.getElementById('total');

            if (serviceChargeEl) serviceChargeEl.textContent = `₱${parseFloat(service_charge).toFixed(2)}`;
            if (totalEl) totalEl.textContent = `₱${parseFloat(total).toFixed(2)}`;
        }

        updateTotals();

        function proceedToPayment() {

            const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            const totals = calculateTotals(currentOrder);
            localStorage.setItem("totals", JSON.stringify(totals));
            updateTotal();

            showPaymentModal();
        }

        function showPaymentModal() {
            const modal = document.getElementById('paymentOptionsModal');
            if (modal) {
                modal.style.display = 'flex';

                document.body.style.overflow = 'hidden';
            }
        }


        function closePaymentModal() {
            const modal = document.getElementById('paymentOptionsModal');
            if (modal) {
                modal.style.display = 'none';

                document.body.style.overflow = 'auto';
            }
        }




        function proceedToPayment() {

            const mobilebool = localStorage.getItem('isMobile');

            if (mobilebool === 'true') {
                showPaymentModal()

            } else {

                window.location.href = '../pages/kioskpayment.php';

            }
        }

        function showPaymentModal() {
            const modal = document.getElementById('paymentOptionsModal');
            if (modal) {
                modal.style.display = 'flex';

                document.body.style.overflow = 'hidden';
            }


        }


        function closePaymentModal() {
            const modal = document.getElementById('paymentOptionsModal');
            if (modal) {
                modal.style.display = 'none';

                document.body.style.overflow = 'auto';
            }
        }

        function processPayment(paymentMethod) {

            localStorage.setItem('selectedPaymentMethod', paymentMethod);


            const mobilebool = localStorage.getItem('isMobile');
            if (paymentMethod === 'counter') {


                window.location.href = '../pages/ordercash.php';
            } else {

                window.location.href = '../pages/orderqr.php';
            }

            closePaymentModal();

        }




        function selectOrderType(type) {

            const mapping = {
                'DINE_IN': 'dine-in',
                'TAKE_OUT': 'take-out',
                'TAKEOUT': 'take-out',
                'DINEIN': 'dine-in'
            };


            let finalType = type;
            if (typeof configuredOrderType === 'string' && configuredOrderType.trim() !== '') {
                const key = configuredOrderType.toUpperCase().replace(/\s|-/g, '_');
                if (mapping[key]) {
                    finalType = mapping[key];
                }
            }


            try {
                localStorage.setItem('orderType', finalType);
            } catch (e) {
                console.warn('Could not persist orderType to localStorage', e);
            }

            document.getElementById('orderTypeModal').style.display = 'none';
        }

        function Tocart() {
            const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");

            if (currentOrder.length === 0) {
                showOrderModal();
                return;
            }

            window.location.href = '../pages/cart.php';
        }

        function closeOrderModal() {
            document.getElementById('orderSummaryModal').style.display = 'none';
        }




        function closePaymentModal() {
            document.getElementById('paymentOptionsModal').style.display = 'none';
        }

        (function watchKioskContentChanges() {
            const versionUrl = '../api/kioskContentVersion.php';
            let currentVersion = null;

            async function checkVersion() {
                try {
                    const response = await fetch(versionUrl + '?t=' + Date.now(), {
                        cache: 'no-store'
                    });
                    if (!response.ok) return;

                    const data = await response.json();
                    const nextVersion = String(data.version || '0');

                    if (currentVersion === null) {
                        currentVersion = nextVersion;
                        return;
                    }

                    if (nextVersion !== currentVersion) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.warn('Kiosk content refresh check failed', error);
                }
            }

            checkVersion();
            setInterval(checkVersion, 3000);
        })();

        window.addEventListener("DOMContentLoaded", () => {
            const totals = JSON.parse(localStorage.getItem("totals"));

        });
    </script>


</body>

</html>
