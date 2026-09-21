<?php
$page_title = "WIP TRACKING";
$is_wip_tracking = true;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WIP Tracking Dashboard</title>
    <link rel="icon" type="jpg" href="./img/logo_wip.jpeg">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f1f3f6;
            padding-bottom: 50px;
        }

        .main-container {
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
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
            background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.8) 50%, rgba(255, 255, 255, 0) 100%);
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

        .splash-text-loader {
            display: inline-block;
            font-family: monospace;
            font-size: 20px;
            font-weight: 800;
            color: #da8300;
            letter-spacing: 2px;
            white-space: nowrap;
            overflow: hidden;
            border-right: 3px solid #b4b48e;
            width: 17ch;
            margin: 0 auto;
            animation: typing 2.5s steps(16) infinite, blink 0.5s step-end infinite;
        }

        @keyframes typing {

            0%,
            15% {
                width: 0;
            }

            85%,
            100% {
                width: 17ch;
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

        .sticky-area {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            padding-bottom: 10px;
            background-color: #f1f3f6;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ticker-wrap {
            width: 100%;
            overflow: hidden;
            background: #ffd54f;
            color: #fff;
            padding: 10px 0;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            display: block;
        }

        @keyframes ticker-anim {
            0% {
                transform: translateX(100vw);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .ticker-content {
            display: inline-block;
            white-space: nowrap;
            animation: ticker-anim 25s linear infinite;
        }

        .ticker-item {
            display: inline-block;
            padding: 0 40px;
            font-size: clamp(18px, 1.5vw, 25px);
            font-weight: 700;
            letter-spacing: 1px;
            color: #1565c0;
        }

        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            margin-top: 15px;
            padding: 0;
            overflow: auto;
            height: calc(100vh - 220px);
            min-height: 400px;
        }

        .achievement-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            min-width: 1400px;
        }

        .achievement-table th,
        .achievement-table td {
            border-bottom: 1px solid #dce1e7;
            border-right: 1px solid #dce1e7;
            padding: 10px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .achievement-table th:first-child,
        .achievement-table td:first-child {
            border-left: 1px solid #dce1e7;
        }

        .achievement-table thead th {
            position: -webkit-sticky;
            position: sticky;
            z-index: 20;
            color: #fff;
            font-weight: 750;
            font-size: clamp(12px, 1vw, 15px);
            border-bottom: 1px solid #0d47a1;
            border-right: 1px solid #0d47a1;
        }

        .achievement-table thead tr:nth-child(1) th {
            top: 0;
            height: 45px;
            background-color: #1565c0;
        }

        .achievement-table th.header-wip {
            background-color: #004ba0;
            letter-spacing: 3px;
            font-size: 16px;
        }

        .achievement-table thead tr:nth-child(2) th {
            top: 45px;
            height: 40px;
            background-color: #2e7d32;
            font-size: 13px;
        }

        .achievement-table td {
            font-weight: 700;
            color: #333;
            font-size: clamp(13px, 1.2vw, 17px);
        }

        .td-part {
            text-align: left !important;
            padding-left: 15px !important;
            color: #0d47a1 !important;
        }

        .td-pcs {
            color: #8a94a6;
            font-weight: 800;
            font-size: 110%;
        }

        .achievement-table tbody tr:nth-child(odd) td {
            background-color: #f9fbfd;
        }

        .achievement-table tbody tr:nth-child(even) td {
            background-color: #ffffff;
        }

        .achievement-table tbody tr:hover td {
            background-color: #e3f2fd;
        }

        .achievement-table tbody td:nth-child(1) {
            position: -webkit-sticky;
            position: sticky;
            left: 0;
            z-index: 5;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .achievement-table thead tr:nth-child(1) th:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 30;
            background-color: #1565c0 !important;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .achievement-table tbody tr:nth-child(odd) td:nth-child(1) {
            background-color: #f9fbfd !important;
        }

        .achievement-table tbody tr:nth-child(even) td:nth-child(1) {
            background-color: #ffffff !important;
        }

        .achievement-table tbody tr:hover td:nth-child(1) {
            background-color: #e3f2fd !important;
        }
    </style>
</head>

<body>

    <div class="splash-screen" id="splash-screen">
        <div class="splash-content">
            <div class="logo-shimmer-wrapper" id="logo-wrapper">
                <img src="./img/logo_utama.png" alt="Exedy Logo" class="splash-logo-exedy">
            </div>
            <h1 class="splash-title" id="splash-title"></h1>
            <script>
                const titleEl = document.getElementById('splash-title');
                const phrase = [{
                    t: "WELCOME TO ",
                    c: ""
                }, {
                    t: "WIP TRACKING",
                    c: "#da8300"
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
            <div class="splash-text-loader">FOCUS ON BASIC</div>
        </div>
    </div>

    <?php include 'addon/control_headerr.php'; ?>

    <div class="main-container">
        <div class="sticky-area">
            <div class="ticker-wrap" id="ticker-wrap">
                <div class="ticker-content">
                    <div class="ticker-item">🛠️ ON PROGRESS | COMING SOON 🛠️</div>
                    <div class="ticker-item">🛠️ ON PROGRESS | COMING SOON 🛠️</div>
                    <div class="ticker-item">🛠️ ON PROGRESS | COMING SOON 🛠️</div>
                    <div class="ticker-item">🛠️ ON PROGRESS | COMING SOON 🛠️</div>
                    <div class="ticker-item">🛠️ ON PROGRESS | COMING SOON 🛠️</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <table class="achievement-table">
                <thead>
                    <tr>
                        <th rowspan="2">P/No</th>
                        <th rowspan="2">ID CUST</th>
                        <th rowspan="2">TYPE</th>
                        <th rowspan="2">PO</th>
                        <th rowspan="2">STOCK</th>
                        <th rowspan="2">PHP</th>
                        <th rowspan="2">BLC</th>
                        <th colspan="6" class="header-wip">WIP</th>
                    </tr>
                    <tr>
                        <th>FINAL</th>
                        <th>PRINTING</th>
                        <th>PREFORMING</th>
                        <th>SANDING</th>
                        <th>DRILLING</th>
                        <th>HOTPRESS</th>
                    </tr>
                </thead>
                <tbody id="wip-body">
                </tbody>
            </table>
        </div>
    </div>


    <script>
        function ambilDataWIP() {
            const tgl = document.getElementById('filter-date').value;
            const searchVal = document.getElementById('search-part').value;

            fetch('addon/controller/wip_tracking/api_wip.php?date=' + tgl + '&search=' + encodeURIComponent(searchVal))
                .then(res => res.json())
                .then(data => {
                    if (data.status === "error") return;
                    let htmlTabel = "";
                    if (data.table_data.length === 0) {
                        htmlTabel = `<tr><td colspan="13" style="padding:30px; color:#888; font-size:16px;">There is no WIP data for this search.</td></tr>`;
                    } else {
                        data.table_data.forEach(row => {
                            htmlTabel += `
                                <tr>
                                    <td class="td-part">${row.partnumber}</td>
                                    <td>${row.id_cust}</td>
                                    <td>${row.type}</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="td-pcs">-</td>
                                    <td class="td-pcs">-</td>
                                    <td class="td-pcs">-</td>
                                    <td class="td-pcs">-</td>
                                    <td class="td-pcs">-</td>
                                    <td class="td-pcs">-</td>
                                </tr>
                            `;
                        });
                    }
                    document.getElementById('wip-body').innerHTML = htmlTabel;
                })
                .catch(err => console.error(err));
        }

        // INISIALISASI
        ambilDataWIP();
        document.getElementById('filter-date').addEventListener('change', ambilDataWIP);
        document.getElementById('search-part').addEventListener('input', ambilDataWIP);
        //ambil tiap 1 dtk
        setInterval(ambilDataWIP, 1000);
        window.addEventListener('load', () => {
            const splash = document.getElementById('splash-screen');
            setTimeout(() => {
                splash.classList.add('hide');
            }, 5000);
        });
    </script>

</body>

</html>