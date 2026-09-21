<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clutch Tech Dashboard Portal</title>
    <link rel="icon" type="image/png" href="view/img/logo_perusahaan.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f1f3f6 0%, #e3f2fd 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        @keyframes waveText {

            0%,
            40%,
            100% {
                transform: translateY(0px);
            }

            20% {
                transform: translateY(-15px);
            }
        }

        @keyframes logoShimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @keyframes logoZoomIn {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.15);
            }
        }

        .splash-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            z-index: 999999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.7s ease;
        }

        .splash-screen.hide {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }

        .splash-content {
            text-align: center;
        }

        .logo-shimmer-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
            overflow: hidden;
            border-radius: 8px;
            animation: popIn 0.6s ease-out forwards, logoZoomIn 4s ease-in-out forwards;
            transition: transform 0.5s ease-out;
        }

        .logo-shimmer-wrapper.final-shrink {
            transform: scale(1) !important;
        }

        .splash-logo-exedy {
            width: clamp(160px, 18vw, 250px);
            height: auto;
            display: block;
            filter: drop-shadow(0px 6px 8px rgba(0, 0, 0, 0.15));
        }

        .logo-shimmer-wrapper::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.8) 50%,
                    rgba(255, 255, 255, 0) 100%);
            background-size: 200% 100%;

            animation: logoShimmer 2.5s infinite;
            z-index: 1;
        }

        .splash-title {
            font-size: clamp(20px, 3vw, 40px);
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 25px;
            color: #0d47a1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        @keyframes textBlink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        .splash-text-loader {
            display: inline-block;
            font-family: monospace;
            font-size: 20px;
            font-weight: 800;
            color: #fd3300;
            letter-spacing: 2px;
            white-space: nowrap;
            overflow: hidden;
            border-right: 3px solid #fd3300;
            width: 19ch;
            margin: 0 auto;
            animation: typing 2.5s steps(19) infinite, blink 0.5s step-end infinite;
        }

        @keyframes typing {

            0%,
            15% {
                width: 0;
            }

            85%,
            100% {
                width: 19ch;
            }
        }

        @keyframes blink {
            50% {
                border-color: transparent;
            }
        }

        @keyframes popIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .portal-container {
            max-width: 900px;
            width: 100%;
            text-align: center;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .logo-container {
            animation: fadeInDown 0.8s ease-out forwards;
            animation-delay: 3s;
            opacity: 0;
            margin-bottom: 20px;
        }

        .logo-container img {
            width: clamp(200px, 25vw, 350px);
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .welcome-title {
            font-size: clamp(24px, 3vw, 36px);
            color: #0d47a1;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 10px;
            animation: fadeInDown 1s ease-out forwards;
            animation-delay: 3.2s;
            opacity: 0;
        }

        .welcome-subtitle {
            font-size: clamp(16px, 1.5vw, 20px);
            color: #546e7a;
            font-weight: 600;
            margin-bottom: 40px;
            animation: fadeInDown 1.2s ease-out forwards;
            animation-delay: 3.4s;
            opacity: 0;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 3.6s;
            opacity: 0;
        }

        .menu-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px 20px;
            text-decoration: none;
            color: #2f3b52;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .menu-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #1565c0, #1976d2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
            transform-origin: left;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(13, 71, 161, 0.15);
            border-color: #e3f2fd;
        }

        .menu-card:hover::before {
            transform: scaleX(1);
        }

        .menu-icon {
            font-size: 40px;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }

        .menu-icon img.menu-icon-img {
            width: 50px;
            height: auto;
        }

        .menu-icon img.menu-icon-img-wg {
            padding-top: 20px;
            width: 50px;
            height: auto;
        }


        .menu-card:hover .menu-icon {
            transform: scale(1.2);
        }

        .menu-title {
            font-size: 20px;
            font-weight: 800;
            color: #0d47a1;
        }

        .menu-desc {
            font-size: 14px;
            color: #8a94a6;
            font-weight: 600;
        }

        .footer {
            margin-top: 50px;
            font-size: 14px;
            color: #90a4ae;
            font-weight: 600;
            animation: fadeInUp 1s ease-out forwards;
            animation-delay: 3.8s;
            opacity: 0;
        }
    </style>
</head>

<body>

    <div class="splash-screen" id="splash-screen">
        <div class="splash-content">
            <div class="logo-shimmer-wrapper" id="logo-wrapper">
                <img src="view/img/logo_perusahaan.png" alt="Logo Perusahaan" class="splash-logo-exedy">
            </div>

            <h1 class="splash-title" id="splash-title"></h1>
            <script>
                const titleEl = document.getElementById('splash-title');
                const phrase = [{
                    t: "WELCOME TO ",
                    c: ""
                }, {
                    t: "CLUTCH TECH",
                    c: "#0076fd"
                }, {
                    t: " DASHBOARD",
                    c: ""
                }];
                let delay = 0,
                    splashHtml = '';
                phrase.forEach(p => {
                    for (let char of p.t) {
                        if (char === ' ') {
                            splashHtml += '&nbsp;';
                        } else {
                            let col = p.c ? `color:${p.c};` : '';
                            splashHtml += `<span style="display:inline-block; animation: waveText 1.2s infinite; animation-delay: ${delay}s; ${col}">${char}</span>`;
                            delay += 0.02;
                        }
                    }
                });
                titleEl.innerHTML = splashHtml;
            </script>
            <div class="splash-text-loader">PRECISION IS KEY</div>
        </div>
    </div>
    <div class="portal-container">
        <div class="logo-container">
            <img src="view/img/logo_perusahaan.png" alt="Logo Clutch Tech">
        </div>
        <h1 class="welcome-title">PRODUCTION DASHBOARD SYSTEM</h1>
        <p class="welcome-subtitle">Please select a section to monitor</p>

        <div class="menu-grid">
            <a href="view/hotpress.php" class="menu-card">
                <div class="menu-icon">
                    <img src="view/img/logo_hotpress.png" class="menu-icon-img" alt="Hotpress PNG">
                </div>
                <div class="menu-title">HOTPRESS</div>
                <div class="menu-desc">Hotpress Production</div>
            </a>

            <a href="view/drilling.php" class="menu-card">
                <div class="menu-icon">
                    <img src="view/img/logo_drilling.png" class="menu-icon-img" alt="Drilling PNG">
                </div>
                <div class="menu-title">DRILLING</div>
                <div class="menu-desc">Drilling Machine Status</div>
            </a>

            <a href="view/preforming.php" class="menu-card">
                <div class="menu-icon">
                    <img src="view/img/logo_preforming.png" class="menu-icon-img" alt="Preforming PNG">
                </div>
                <div class="menu-title">PREFORMING</div>
                <div class="menu-desc">Preforming Process Data</div>
            </a>
        </div>

        <div class="footer">
            &copy; 2026 Clutch Tech - All Rights Reserved
        </div>
    </div>
    <script>
        window.addEventListener('load', () => {
            const splash = document.getElementById('splash-screen');
            setTimeout(() => {
                splash.classList.add('hide');
            }, 3000);
        });
    </script>

</body>

</html>