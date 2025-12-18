<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>Kiosk Menu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css" crossorigin>

    <link rel="stylesheet" href="../css/orderSummary.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Yusei+Magic&display=swap" rel="stylesheet">
    <script src="../js/modal.js" defer></script>
    <script src="../js/behaviors.js"></script>
    <script src="../js/paymentmodal.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/item.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qz-tray/qz-tray.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qz-tray/2.2.0/qz-tray.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/8.0.20/jsrsasign-all-min.js"></script>

</head>
<style>


</style>

<body>
    <div class="header">

    </div>
    <div class="container">


        <div class="sidebar-container">
            <button class="sidebar-arrow sidebar-arrow-left">
                < </button>

                    <div class="sidebar">
                        <ul>
                            <li class="category-item" data-category="all" onclick="filterItems('all')">
                                <img src="../images/logos/logo.png" style="height: 120px; width: 120px;"
                                    onerror="this.onerror=null;this.src='../images/logos/logo.png';"
                                    class="sidebar-icon" alt="Home Icon">
                                Home
                            </li>

                            <?php include '../api/categories.php'; ?>
                        </ul>
                    </div>

                    <button class="sidebar-arrow sidebar-arrow-right">></button>
        </div>


        <div class="menu-content">
            <div style="height: 80px; display: flex;margin-bottom: 20px;">
                <h1 class="menu-header"></h1>
            </div>


            <div class="menu-grid">

                <?php include '../api/fetchitems.php'; ?>

                <script>
                    function filterItems(categoryId) {
                        const menuItems = document.querySelectorAll('.menu-item');
                        const categoryItems = document.querySelectorAll('.category-item');
                        const nameHeader = document.querySelector('.menu-header');

                        if (categoryId === 'all') {
                            nameHeader.textContent = 'All Menu Items';
                        } else {
                            const selectedCategory = document.querySelector(
                                `.category-item[data-category="${categoryId}"]`);
                            if (selectedCategory) {
                                nameHeader.textContent = selectedCategory.textContent.trim();
                            } else {
                                nameHeader.textContent = 'Menu Items';
                            }
                        }


                        categoryItems.forEach(item => item.classList.remove('active'));

                        document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');

                        menuItems.forEach(item => {
                            if (categoryId === 'all' || item.dataset.category === categoryId.toString()) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }


                    filterItems('all');
                </script>

            </div>
        </div>

    </div>

    <div class="footer">
        <div class="footer-container" style="display: flex; justify-content: space-between; align-items: center;">
            <button class="footer-btn return-btn" onclick="finishOrder()"
                style="background: var(--secondary-color); color: #fff8f3; padding: 12px 22px; font-size: 20px; font-weight: bold; border: none; cursor: pointer; border-radius: 8px;">CANCEL
                ORDER</button>


            <button class="footer-btn view-cart-btn" onclick="showOrderModal()"
                style="background: var(--secondary-color); color: #fff8f3; padding: 10px 20px; font-size: 20px; border: none; cursor: pointer; border-radius: 8px;">CHECK
                OUT (<span>0</span>)</button>
            <span class="cart-price" style="color: black">₱0.00</span>
        </div>
    </div>
    </div>

    <!-- Order Summary Modal -->

    <div id="orderSummaryModal" class="modal"
        style="display: none;position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; align-items: center; justify-content: center; font-family: 'Yusei Magic', sans-serif;">
        <div class="modal-content"
            style="background: var(--primary-color); background-size: cover; padding-top:0%;  width: 100%; height: 100%; max-width: 1200px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); overflow: hidden; display: flex; flex-direction: column; max-height: 100vh;">

            <div
                style="  border-bottom-left-radius: 55px; width:90%;  border-bottom-right-radius: 55px; padding: 30px 40px 20px; background: var(--secondary-color);  border-bottom: 1px solid black; position: relative;">
                <img src="../images/icons/back.png" alt="" style="height: 28px;
                width: 30px; position: absolute; left: 20px;" onclick="closeOrderModal()" />
                <h2
                    style="margin: 0; font-size: 32px; font-weight: 700; color: var(--primary-color); text-align: center;">
                    YOUR ORDER SUMMARY</h2>
            </div>

            <div class="orderbox" style="padding: 30px 40px; overflow: auto; overflow:auto; ">
                <div id="orderItemsContainer" style="display: flex; flex-direction: column;">
                </div>
            </div>

            <div class="checkout-container"
                style="background: var(--secondary-color); padding: 30px 40px; border-top: 1px solid black; border-radius:20px; position: absolute; bottom: 0px; width: 91%;">
                <div class="price-row"
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div class="price-labels">
                        <p style="margin: 0 0 5px; font-size: 18px; color: var(--primary-color);">Subtotal</p>
                        <p style="margin: 0 0 5px; font-size: 18px; color: var(--primary-color);">Service Charge
                            (10%)</p>
                        <p style="margin: 0; font-size: 18px; color: var(--primary-color);">VAT (12%)</p>
                    </div>
                    <div class="price-valuess" style="text-align: right;">
                        <p id="subtotal" style="margin: 0 0 5px; font-size: 18px; color: var(--primary-color);">₱0.00
                        </p>
                        <p id="serviceCharge" style="margin: 0 0 5px; font-size: 18px; color: var(--primary-color); ">
                            ₱0.00</p>
                        <p id="vat" style="margin: 0; font-size: 18px; color: var(--primary-color);">₱0.00</p>
                    </div>
                </div>

                <div class="total-section"
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-top: 15px; border-top: 2px dotted var(--primary-color);">
                    <h3 style="margin: 0; font-size: 28px; font-weight: 700; color: var(--primary-color);">TOTAL</h3>
                    <h3 id="total" style="margin: 0; font-size: 28px; font-weight: 700; color: var(--primary-color);">
                        ₱0.00</h3>
                </div>

                <div class="button-container" style="display: flex; justify-content: flex-end; gap: 15px;">

                    <button class="btn btn-payment" onclick="proceedToPayment()" class="payment"
                        style="background: var(--primary-color); color: var(--secondary-color); padding: 15px 40px; font-size: 18px; font-weight: 800; border: none; cursor: pointer; border-radius: 8px; transition: all 0.2s ease;">
                        PROCEED TO PAYMENT</button>
                </div>
            </div>
        </div>
        <!-- Payment Options Modal -->
        <div id="paymentOptionsModal" class="modal"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0, 0, 0, 0.8); z-index: 9999; align-items: center; justify-content: center; font-family: 'Montserrat', sans-serif;">
            <div class="modal-content"
                style="background: white; width: 100%; max-width: 800px; border-radius: 24px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4); overflow: hidden; text-align: center; max-height: 90vh; overflow-y: auto;">

                <div style="padding: 40px 50px 30px; position: relative; border-bottom: 1px solid #f0f0f0;">
                    <h2 style="margin: 0; font-size: clamp(24px, 5vw, 36px); font-weight: 700; color: #333;">Select
                        Payment Method</h2>
                    <p style="margin: 15px 0 0; color: #666; font-size: clamp(16px, 3vw, 20px);">Choose how you'd like
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
                                    style="margin: 0; font-size: clamp(18px, 3.5vw, 24px); font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Credit / Debit Card</h3>
                                <p style="margin: 6px 0 0; font-size: clamp(14px, 2.5vw, 18px); color: #666;">Pay
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
                                <h3
                                    style="margin: 0; font-size: clamp(18px, 3.5vw, 24px); font-weight: 600; color: #333;">
                                    Pay at Counter</h3>
                                <p style="margin: 6px 0 0; font-size: clamp(14px, 2.5vw, 18px); color: #666;">Pay when
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
                        style="width: 100%; height: clamp(55px, 10vw, 70px); font-size: clamp(16px, 3vw, 20px); font-weight: 600; cursor: pointer; transition: all 0.2s ease; background: #721719; color: #ffebcd; border: 2px solid #ffebcd; border-radius: 10px;">
                        CANCEL
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
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

            const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
            const totals = calculateTotals(currentOrder);
            localStorage.setItem("totals", JSON.stringify(totals));
            updateTotal();


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

        function showOrderModal() {
            document.getElementById('orderSummaryModal').style.display = 'flex';
        }

        function closeOrderModal() {
            document.getElementById('orderSummaryModal').style.display = 'none';
        }




        function closePaymentModal() {
            document.getElementById('paymentOptionsModal').style.display = 'none';
        }
        window.addEventListener("DOMContentLoaded", () => {
            const totals = JSON.parse(localStorage.getItem("totals"));

        });
    </script>


</body>

</html>