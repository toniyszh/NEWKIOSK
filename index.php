<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./js/script.js" defer></script>


    <style>
        .montserrat {
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        .enter-label {
            font-family: "Caslon 540", "Caslon #540", Georgia, serif;
        }
    </style>
</head>

<body class="font-montserrat">

    <!-- Mobile Order Type Modal -->
    <div id="orderTypeModalMobile"
        class="hidden fixed top-0 left-0 w-full h-screen z-[9999] flex-col items-center justify-center"
        style="background: url('./images/background/index.jpg'); background-size: cover; background-position: center;">



        <div style="width: 90%; max-width: 600px; padding: 0 1rem;">
            <button onclick="handleOrderTypeSelection('For Here')" style="display: flex; flex-direction: row; align-items: center; justify-content: center; width: 100%; height: auto; min-height: 100px; border-radius: 1.5rem; box-shadow: 0 10px 30px -12px #8c1b24; 
            border-width: 2px; border-style: solid; background: #2c401e; border-color: #ebf2e7; padding: 0.5rem;">
                <img src="./images/icons/forhere.png" alt="For Here"
                    style="width: clamp(50px, 12vw, 70px); height: clamp(50px, 12vw, 70px); margin: 10px; flex-shrink: 0;">
                <h3 class="enter-label"
                    style="font-weight: bold; color: #ebf2e7; font-size: clamp(1rem, 4vw, 1.25rem); text-align: center;">
                    ENTER</h3>
            </button>

        </div>
    </div>

    <!-- Desktop Kiosk Order Type Modal -->
    <div id="orderTypeModals"
        class="hidden fixed top-0 left-0 w-full h-screen z-[9999] flex-col items-center justify-center"
        style="background-image: url('./images/background/index.jpg'); background-size: cover; background-position: center;">

        <!-- <div id="logo"
     style="display: flex; align-items: center; justify-content: center; margin-top: 500px; margin-bottom: 0px;">
    <img src="./images/logos/logo1.png" alt="logo" style="width: auto; height: 250px;">
</div> -->
        <!-- Dine In / Take Out Buttons Container -->
        <div style="display: flex; justify-content: center; width: 60%;">
            <!-- Dine In Button -->
            <button onclick="handleOrderTypeSelection('For Here')"
                style="display: flex; flex-direction: row; align-items: center; justify-content: center; width: 100%; height: 200px; border-radius: 1.5rem; 
                border-width: 2px; border-style: solid;width: 70%;height: 100px; background: white; border-color: #ebf2e7;">

                <h3 class="enter-label" style=" color: black; font-size: xx-large;">ENTER
                </h3>
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
        window.addEventListener("DOMContentLoaded", function() {
            clearAll();
        });

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
                desktopModal.classList.remove("hidden");
                desktopModal.classList.add("flex");
            }
        })();

        async function handleOrderTypeSelection(type) {
            try {
                const response = await fetch('./api/generateRefNo.php');
                const result = await response.json();

                if (!result.success) throw new Error(result.message);

                try {
                    sessionStorage.removeItem('kioskLanguage');
                } catch {
                    // The order can start even when session storage is unavailable.
                }
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

        (function watchKioskContentChanges() {
            const versionUrl = './api/kioskContentVersion.php';
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

        // document.getElementById("qrscanBtn").addEventListener("click", () => {
        //     window.location.href = "./pages/kioskScanOrder.php";
        // });
    </script>

</body>

</html>
