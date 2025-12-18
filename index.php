<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Yusei+Magic&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./js/script.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        yusei: ['"Yusei Magic"', 'sans-serif'],
                    },
                    colors: {
                        primary: 'var(--primary-color)',
                        secondary: 'var(--secondary-color)',
                    },
                },
            },
        };
    </script>
</head>

<style>
    :root {
        --primary-color: #ffebcd;
        --secondary-color: #721719;
    }
</style>
</head>


<body class="font-yusei">
    <!-- Start Screen with Video Background -->
    <div class="hidden md:block fixed inset-0" id="startScreen">
        <video class="w-screen h-screen object-cover" autoplay muted loop playsinline>
            <source src="images/background/1.mp4" type="video/mp4">
        </video>
    </div>

    <!-- Mobile Order Type Modal -->
    <div id="orderTypeModalMobile"
        class="hidden fixed top-0 left-0 w-full h-screen z-[9999] flex-col items-center justify-center"
        style="background: url('./images/background/mobile.png'); background-size: cover; background-position: center;">

        <img src="./images/logos/logo1.png" alt="Logo" class="w-40 h-40 max-w-xs mb-8">

        <h1 class="text-xl md:text-4xl text-black text-center px-4 mb-4">
            Where do you want to eat your meal?
        </h1>

        <div class="flex flex-row gap-4 px-4 w-full max-w-2xl justify-center">
            <button
                class="flex flex-col items-center justify-center gap-3 bg-[#7d1f1f] text-white font-semibold py-4 px-4 rounded-2xl border-2 border-[#f5e6d3] shadow-lg transition-all duration-200 transform hover:scale-105 w-40"
                onclick="handleOrderTypeSelection('dine-in')">
                <img src="./images/icons/dinein.png" class="w-16 h-16" alt="Dine In">
                <span class="text-lg">DINE IN</span>
            </button>

            <button
                class="flex flex-col items-center justify-center gap-3 bg-[#7d1f1f] text-white font-semibold py-8 px-8 rounded-2xl border-2 border-[#f5e6d3] shadow-lg transition-all duration-200 transform hover:scale-105 w-40"
                onclick="handleOrderTypeSelection('take-out')">
                <img src="./images/icons/takeout.png" class="w-16 h-16" alt="Take Out">
                <span class="text-lg">TAKE OUT</span>
            </button>
        </div>
    </div>

    <!-- Desktop Kiosk Order Type Modal -->
    <div id="orderTypeModals"
        class="hidden fixed top-0 left-0 w-full h-screen z-[9999] flex-col items-center justify-center"
        style="background-image: url('./images/background/exo.png'); background-size: cover; background-position: center;">

        <!-- Settings Button -->
        <button id="settingsBtn"
            class="absolute top-2.5 right-2.5 w-12 h-12 flex items-center justify-center rounded-full bg-black/50 border-2 border-gray-400 cursor-pointer hover:bg-black/70 transition-colors">
            <img src="./images/icons/settings.png" class="w-9 h-9" alt="Settings">
        </button>

        <!-- Title -->
        <h1 class="mt-[600px] mb-8 text-4xl text-center px-4 leading-tight font-bold text-white"
            style="color: var(--secondary-color); text-shadow: 0 0 8px #fff;">
            Where do you want to eat your meal?
        </h1>

        <!-- Button Container -->
        <div class="flex gap-4 justify-center w-full max-w-4xl px-4">
            <!-- QR Scan Button -->
            <button id="qrscanBtn"
                class="flex flex-col items-center justify-center w-2/5 h-[350px] rounded-3xl shadow-2xl cursor-pointer transition-all duration-200 hover:scale-105 hover:shadow-3xl border-2"
                style="background: var(--secondary-color); border-color: var(--primary-color);">
                <img src="./images/icons/scan.png" class="w-64 h-auto mb-2.5" alt="QR Scan">
            </button>

            <!-- Dine In / Take Out Buttons Container -->
            <div class="flex flex-col gap-4 w-[45%]">
                <!-- Dine In Button -->
                <button onclick="handleOrderTypeSelection('dine-in')"
                    class="flex flex-col items-center justify-center w-full h-[167.5px] rounded-3xl shadow-2xl cursor-pointer transition-all duration-200 hover:scale-105 hover:shadow-3xl border-2"
                    style="background: var(--secondary-color); border-color: var(--primary-color);">
                    <img src="./images/icons/DINE.png" class="w-64 h-auto" alt="Dine In">
                </button>

                <!-- Take Out Button -->
                <button onclick="handleOrderTypeSelection('take-out')"
                    class="flex flex-col items-center justify-center w-full h-[167.5px] rounded-3xl shadow-2xl cursor-pointer transition-all duration-200 hover:scale-105 hover:shadow-3xl border-2"
                    style="background: var(--secondary-color); border-color: var(--primary-color);">
                    <img src="./images/icons/out.png" class="w-64 h-auto" alt="Take Out">
                </button>
            </div>
        </div>
    </div>

    <script>
        function detectMobile() {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            return {
                isMobile: isMobile,
                isDesktop: !isMobile
            };
        }

        (function() {
            const deviceInfo = detectMobile();
            localStorage.setItem('isMobile', deviceInfo.isMobile);
            localStorage.setItem('isDesktop', deviceInfo.isDesktop);

            const mobileModal = document.getElementById("orderTypeModalMobile");
            const desktopModal = document.getElementById("orderTypeModals");

            if (deviceInfo.isMobile) {
                mobileModal.classList.remove("hidden");
                mobileModal.classList.add("flex");
                desktopModal.classList.add("hidden");
            } else {
                mobileModal.classList.add("hidden");
                desktopModal.classList.add("hidden");
            }
        })();

        const startScreen = document.getElementById("startScreen");

        if (startScreen) {
            startScreen.addEventListener("click", function() {
                const desktopbool = localStorage.getItem('isDesktop') === 'true';

                if (desktopbool) {
                    const desktopModal = document.getElementById("orderTypeModals");
                    desktopModal.classList.remove("hidden");
                    desktopModal.classList.add("flex");
                }
            });
        }

        async function handleOrderTypeSelection(type) {
            try {
                const response = await fetch('./api/generateRefNo.php');
                const result = await response.json();

                if (!result.success) throw new Error(result.message);

                localStorage.setItem('orderType', type);
                localStorage.setItem('referenceNo', result.data.referenceNo);

                window.location.href = "./pages/main.php";
            } catch (error) {
                alert("Error: " + error.message);
            }
        }

        document.getElementById("settingsBtn").addEventListener("click", () => {
            window.location.href = "./config/settings.php";
        });

        document.getElementById("qrscanBtn").addEventListener("click", () => {
            window.location.href = "./pages/kioskScanOrder.php";
        });
    </script>

</body>

</html>