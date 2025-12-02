<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/responsive.css" crossorigin>
    <script src="./js/script.js" defer></script>

    <style>
        #orderTypeModals {
            display: none;
        }

        #orderTypeModalMobile {
            display: flex;
        }

        @media (max-width: 768px) {
            .video-background {
                display: none;
            }
        }
    </style>
</head>

<body>


    <div class="container" id="startScreen">
        <video class="video-background" autoplay muted loop playsinline>
            <source src="images/background/1.mp4" type="video/mp4">
        </video>
    </div>

    <!--  mobile start -->
    <div id="orderTypeModalMobile" class="modal" style="align-items:center; justify-content:center; position:fixed; top:0; left:0;
        width:100%; height:100vh; background:url('./images/background/mobile.png');
        background-size:cover; background-position:center; z-index:9999; flex-direction:column;">



        <img src="./images/logos/logo1.png" alt="Logo" class="logo">

        <h1 class="modal-title" style="color: black;">Where do you want to eat your meal?</h1>

        <div class="buttons-container">


            <button class="order-type-btn" onclick="handleOrderTypeSelection('dine-in')" style="color: #ffffff">
                <img src="./images/icons/dinein.png"> DINE IN
            </button>

            <button class="order-type-btn" onclick="handleOrderTypeSelection('take-out')" style="color: #ffffff">
                <img src="./images/icons/takeout.png"> TAKE OUT
            </button>

        </div>
    </div>



    <!-- kiosk start -->
    <div id="orderTypeModals" class="modal" style="align-items:center; justify-content:center; position:fixed; top:0; left:0;
    width:100%; height:100vh; background-image:url('./images/background/exo.png');
    background-size:cover; background-position:center; z-index:9999; flex-direction:column;">

        <button id="settingsBtn" style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.5);
        color:#fff; border:2px solid #a7a0a0; border-radius:50%; width:50px; height:50px;
        font-size:32px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
            <img src="./images/icons/settings.png" style="width:35px; height:35px;">
        </button>

        <!-- <img src="./images/logos/logo1.png" alt="Logo" class="logo" style="height: 300px; width: 300px; margin: 0 auto 200px;"> -->


        <h1
            style="margin-top: 600px; margin-bottom: 30px; font-size: 40px; text-align: center; color:var(--secondary-color); padding: 0 15px; line-height: 1.3; flex-shrink: 0; text-shadow: 0 0 8px #ffffffff; font-weight: bold;">
            Where do you want to eat your meal?</h1>

        <div style="display: flex; gap: 15px; justify-content: center; width: 100%; max-width: 800px;">


            <button
                style="display: flex; flex-direction: column; align-items: center; justify-content: center; 
            width: 40%; height: 350px; font-size: 18px; font-weight: 600; border-radius: 20px; 
            background: var(--secondary-color); color: #fff; border: 2px solid var(--primary-color); 
            cursor: pointer; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease;"
                id="qrscanBtn">
                <img src="./images/icons/scan.png" style="width: 250px; height: auto; margin-bottom: 10px;">
            </button>

            <div style="display: flex; flex-direction: column; gap: 15px; width: 45%;">

                <button
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; 
                width: 100%; height: 167.5px; font-size: 18px; font-weight: 600; border-radius: 20px; 
                background: var(--secondary-color); color: white; border: 2px solid var(--primary-color); 
                cursor: pointer; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease;"
                    onclick="handleOrderTypeSelection('dine-in')">
                    <img src="./images/icons/DINE.png" style="width: 250px; height: auto;">
                </button>

                <button
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; 
                width: 100%; height: 167.5px; font-size: 18px; font-weight: 600; border-radius: 20px; 
                background: var(--secondary-color); color: white; border: 2px solid var(--primary-color); 
                cursor: pointer; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease;"
                    onclick="handleOrderTypeSelection('take-out')">
                    <img src="./images/icons/out.png" style="width: 250px; height: auto;">
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


            if (deviceInfo.isMobile) {
                document.getElementById("orderTypeModalMobile").style.display = "flex";
                document.getElementById("orderTypeModals").style.display = "none";
            } else {
                document.getElementById("orderTypeModalMobile").style.display = "none";
                document.getElementById("orderTypeModals").style.display = "none";
            }
        })();

        const startScreen = document.getElementById("startScreen");

        if (startScreen) {
            startScreen.addEventListener("click", function() {
                const desktopbool = localStorage.getItem('isDesktop') === 'true';

                if (desktopbool) {
                    document.getElementById("orderTypeModals").style.display = "flex";
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