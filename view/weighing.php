<?php
$page_title = "WEIGHING DASHBOARD";
$hide_shift = true;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="jpg" href="./img/logo_weighing.jpeg">
    <title>Weighing Dashboard</title>
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
            color: #ffff09;
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

        .meta-ticker-wrapper {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
            width: 100%;
            align-items: stretch;
            font-weight: bold;
        }

        .meta-box {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            flex: 0 0 auto;
        }

        .box-start {
            background: #e8f5e9;
            border: 2px solid #a5d6a7;
            color: #2e7d32;
        }

        .box-last {
            background: #ffebee;
            border: 2px solid #ef9a9a;
            color: #c62828;
        }

        .box-ticker {
            flex: 1;
            min-width: 250px;
            background: linear-gradient(90deg, #0d47a1, #78aeff);
            color: white;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 0 10px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .box-ticker:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        @media (max-width: 1024px) {
            .box-ticker {
                order: -1;
                flex: 1 1 100%;
                height: 45px;
            }

            .box-start,
            .box-last {
                flex: 1;
            }
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
            display: flex;
            align-items: center;
            height: 100%;
            white-space: nowrap;
            animation: ticker-anim 40s linear infinite;
        }

        .ticker-item {
            display: flex;
            align-items: center;
            height: 100%;
            padding: 0 35px;
            font-size: clamp(16px, 1.5vw, 18px);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .ticker-item span {
            color: #ffd54f;
            font-weight: 800;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .kpi-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        .kpi-head {
            color: white;
            font-weight: 900;
            padding: 10px;
            font-size: 16px;
        }

        .bg-purple {
            background: linear-gradient(90deg, #4a148c, #bf8bff);
        }

        .bg-green {
            background: linear-gradient(90deg, #2e7d32, #5cff64);
        }

        .bg-red {
            background: linear-gradient(90deg, #c62828, #ff2b2b);
        }

        .bg-blue {
            background: linear-gradient(90deg, #1565c0, #5da8ff);
        }

        .kpi-body {
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .kpi-badge {
            border: 1.5px solid;
            border-radius: 20px;
            padding: 3px 15px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .badge-purple {
            color: #4a148c;
            border-color: #4a148c;
        }

        .badge-green {
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .badge-red {
            color: #c62828;
            border-color: #c62828;
        }

        .badge-blue {
            color: #1565c0;
            border-color: #1565c0;
        }

        .kpi-val {
            font-size: clamp(35px, 3vw, 45px);
            font-weight: 900;
        }

        .kpi-unit {
            font-size: 16px;
            font-weight: bold;
            color: #7b8794;
        }

        @media (max-width: 1024px) {
            .kpi-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .kpi-row {
                grid-template-columns: 1fr;
            }
        }

        .bagian-tengah {
            display: grid;
            grid-template-columns: 4fr 1fr;
            gap: 20px;
            align-items: stretch;
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .bagian-tengah {
                grid-template-columns: 1fr;
            }

            .panel-info-cepat {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .panel-info-cepat {
                grid-template-columns: 1fr;
            }
        }

        .wadah-grafik {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            animation: fadeSlideUp 0.6s ease;
        }

        .judul-grafik {
            font-size: clamp(18px, 1.5vw, 26px);
            font-weight: 750;
            margin-bottom: 14px;
            color: #0d47a1;
            text-align: center;
            letter-spacing: 0.5px;
        }

        #grafikTrenTimbangan {
            width: 100%;
            flex: 1;
            min-height: 400px;
        }

        .panel-info-cepat {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .kartu-kecil {
            background: #ffffff;
            border-radius: 16px;
            padding: 15px;
            color: #ecf5ff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kartu-kecil:hover {
            transform: translateY(-2px);
        }

        .kartu-kecil.ungu {
            background: linear-gradient(90deg, #55329c, #8b5de9);
        }

        .kartu-kecil.hijau {
            background: linear-gradient(90deg, #2e7d32, #42cf49);
        }

        .kartu-kecil.biru {
            background: linear-gradient(90deg, #0d47a1, #1976d2);
        }

        .judul-kartu-kecil {
            font-size: clamp(16px, 1.5vw, 24px);
            font-weight: 600;
            color: #ecf5ff;
        }

        .nilai-kartu-kecil {
            font-size: clamp(28px, 2.5vw, 45px);
            font-weight: 750;
            margin-top: 4px;
        }

        .satuan-ct {
            font-size: clamp(14px, 1.2vw, 20px);
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kpi-subtitle-month {
            font-size: 16px;
            margin-top: 2px;
            opacity: 0.9;
            font-weight: bold;
        }

        .wadah-live {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            animation: fadeSlideUp 0.65s ease;
        }

        .live-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .live-indicator {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #ccc;
            margin-right: 10px;
        }

        .live-indicator.active {
            background-color: #f44336;
            box-shadow: 0 0 10px #f44336;
            animation: blink 1.5s infinite;
        }

        .live-title {
            font-size: clamp(18px, 1.5vw, 22px);
            font-weight: 800;
            color: #0d47a1;
        }

        .live-title.idle {
            color: #999;
        }

        .live-idle-text {
            color: #888;
            font-size: 16px;
            font-style: italic;
        }

        .live-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .live-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            background: #fdfdfd;
        }

        .live-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            flex-wrap: wrap;
        }

        .live-mc {
            font-weight: 800;
            font-size: clamp(16px, 1.5vw, 20px);
            color: #0d47a1;
        }

        .live-idwg {
            color: #555;
            font-size: clamp(14px, 1.2vw, 16px);
            font-weight: 600;
            margin-left: 8px;
        }

        .live-time {
            color: #d32f2f;
            font-weight: 700;
            font-size: clamp(14px, 1.2vw, 16px);
        }

        .live-details {
            display: flex;
            gap: 30px;
            font-size: clamp(14px, 1.2vw, 16px);
            font-weight: 600;
            color: #444;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .progress-wrap {
            background: #e0e0e0;
            border-radius: 8px;
            height: 26px;
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #4caf50, #81c784);
            height: 100%;
            width: 0%;
            transition: width 0.6s ease;
        }

        .progress-text {
            position: absolute;
            width: 100%;
            text-align: center;
            top: 3px;
            font-size: 15px;
            font-weight: 800;
            color: #222;
            z-index: 2;
        }

        .live-material {
            font-size: clamp(14px, 1.2vw, 16px);
            font-weight: 700;
            color: #55329c;
        }

        .wadah-tabel {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            animation: fadeSlideUp 0.7s ease;
            overflow-x: auto;
        }

        .tabel-weighing {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            min-width: 800px;
        }

        .tabel-weighing th {
            background: linear-gradient(135deg, #1565c0, #1976d2);
            color: #fff;
            padding: 14px;
            font-weight: 700;
            font-size: clamp(14px, 1.5vw, 18px);
            border: 1px solid #dce1e7;
        }

        .baris-induk {
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #dce1e7;
        }

        .baris-induk:hover {
            background-color: #f4f6fa;
        }

        .baris-induk td {
            padding: 14px;
            font-weight: 600;
            color: #333;
            font-size: clamp(16px, 1.5vw, 20px);
        }

        .btn-expand {
            background: none;
            border: none;
            cursor: pointer;
            color: #0d47a1;
            outline: none;
            font-size: 18px;
            font-weight: bold;
        }

        .btn-expand::after {
            content: '\25BC';
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .baris-induk.terbuka .btn-expand::after {
            transform: rotate(180deg);
        }

        .baris-detail {
            display: none;
            background-color: #f9fbfd;
        }

        .baris-detail.tampil {
            display: table-row;
        }

        .baris-detail td {
            padding: 0;
            border-bottom: 1px solid #dce1e7;
        }

        .tabel-anak {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .tabel-anak th {
            color: #f5f5f5;
            font-size: clamp(14px, 1.2vw, 18px);
            padding: 10px;
            border-top: none;
            background: #003369;
        }

        .tabel-anak td {
            padding: 12px;
            font-size: clamp(14px, 1.2vw, 17px);
            color: #555;
            border-bottom: 1px solid #ebeef3;
            font-weight: 500;
            vertical-align: middle;
            text-align: center;
        }

        .tabel-anak tbody tr:nth-child(odd) td {
            background-color: #ffffff;
        }

        .tabel-anak tbody tr:nth-child(even) td {
            background-color: #f1f5f9;
        }

        .tabel-anak tr:last-child td {
            border-bottom: none;
        }

        .badge-valid-biru {
            background-color: #e3f2fd;
            color: #1565c0;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .badge-prog-oren {
            background-color: #fff3e0;
            color: #e65100;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .badge-print-hijau {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .badge-print-merah {
            background-color: #ffebee;
            color: #c62828;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .badge-warning {
            background-color: #fff3e0;
            color: #e65100;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .badge-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .alert-box {
            background: #ffebee;
            border-left: 6px solid #d32f2f;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .alert-title {
            color: #c62828;
            font-weight: 800;
            font-size: clamp(16px, 1.5vw, 20px);
            margin-bottom: 4px;
        }

        .alert-details {
            color: #b71c1c;
            font-size: clamp(14px, 1.2vw, 16px);
            font-weight: 600;
        }

        .normal-box {
            background: linear-gradient(135deg, #2e7d32, #41e249);
            border: 1px solid #a5d6a7;
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .normal-text {
            color: #e8f5e9;
            font-weight: 800;
            font-size: clamp(16px, 1.5vw, 20px);
            margin-bottom: 12px;
        }

        .btn-history {
            background: linear-gradient(135deg, #455a64, #607d8b);
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-history:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-yearly {
            background: linear-gradient(135deg, #f57c00, #ff9800);
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-yearly:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .history-table-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            margin-top: 15px;
            animation: fadeSlideUp 0.5s ease;
        }

        .month-header {
            background: #003369;
            color: #fff;
            font-weight: bold;
            text-align: left;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
            margin-top: 15px;
        }

        .swal2-container {
            z-index: 9999999 !important;
        }
    </style>

    <script src="../amcharts4/core.js"></script>
    <script src="../amcharts4/charts.js"></script>
    <script src="../amcharts4/themes/animated.js"></script>
    <script src="../sweetalert2-11.26.24/package/dist/sweetalert2.all.min.js"></script>
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
                    t: "WEIGHING",
                    c: "#ffff09"
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

        <div class="meta-ticker-wrapper">
            <div class="meta-box box-start">START: <span id="val-mulai" style="margin-left: 5px;">--:--:--</span></div>
            <div class="box-ticker" onclick="openTickerModal()" title="View Summary" id="ticker-wrap" style="display: none;">
                <div class="ticker-content" id="ticker-content">Loading data...</div>
            </div>
            <div class="meta-box box-last">LAST: <span id="val-selesai" style="margin-left: 5px;">--:--:--</span></div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-head bg-purple">PLAN RPH</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-purple kpi-subtitle-top">TODAY</div>
                    <div><span class="kpi-val" style="color: #4a148c;" id="val-plan">0</span> <span class="kpi-unit">PKM</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-green">ACTUAL DONE</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-green kpi-subtitle-top">TODAY</div>
                    <div><span class="kpi-val" style="color: #2e7d32;" id="val-actual">0</span> <span class="kpi-unit">PKM</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-blue">ON PROGRESS</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-blue kpi-subtitle-top">TODAY</div>
                    <div><span class="kpi-val" style="color: #1565c0;" id="val-onprog">0</span> <span class="kpi-unit">PKM</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-red">OUTSTANDING</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-red kpi-subtitle-top">TODAY</div>
                    <div><span class="kpi-val" style="color: #c62828;" id="val-out">0</span> <span class="kpi-unit">PKM</span></div>
                </div>
            </div>
        </div>

        <div class="bagian-tengah">
            <div class="wadah-grafik">
                <div class="judul-grafik" id="judul-grafik-utama">TREN ACTUAL DONE VS PLAN RPH</div>
                <div id="grafikTrenTimbangan"></div>
            </div>
            <div class="panel-info-cepat">
                <div class="kartu-kecil ungu">
                    <div class="judul-kartu-kecil">ACHIEVEMENT RATE MONTHLY</div>
                    <div class="kpi-subtitle-month" style="color: #d1c4e9;">IN JUNE</div>
                    <div class="nilai-kartu-kecil"><span id="val-achieve">0</span>%</div>
                </div>
                <div class="kartu-kecil hijau">
                    <div class="judul-kartu-kecil">AVG MONTHLY CYCLE TIME</div>
                    <div class="kpi-subtitle-month" style="color: #c8e6c9;">IN JUNE</div>
                    <div class="nilai-kartu-kecil"><span id="val-ct-prog">0</span> <span class="satuan-ct">mnt/cycle</span></div>
                </div>
                <div class="kartu-kecil hijau">
                    <div class="judul-kartu-kecil">AVG ACTUAL DONE MONTHLY</div>
                    <div class="kpi-subtitle-month" style="color: #c8e6c9;">IN JUNE</div>
                    <div class="nilai-kartu-kecil"><span id="val-ct-act">0</span> <span class="satuan-ct">PKM</span></div>
                </div>
                <div class="kartu-kecil biru" onclick="toggleAvailableTable()" style="cursor: pointer;" title="Klik untuk melihat detail formula">
                    <div class="judul-kartu-kecil">FORMULA AVAILABLE</div>
                    <div class="kpi-subtitle-month formula-alltime" style="color: #bbdefb;">ALL TIME</div>
                    <div class="nilai-kartu-kecil"><span id="val-available">0</span> <span class="satuan-ct">PKM</span></div>
                </div>
            </div>
        </div>

        <div id="container-available-table" style="display: none; margin-bottom: 20px;">
            <div style="background: #ffffff; padding: 15px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: relative;">
                <div style="text-align: right; margin-bottom: 15px;">
                    <button onclick="toggleAvailableTable()" style="background: #e53935; color: white; border: none; padding: 6px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        TUTUP
                    </button>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: center; border: 1px solid #dee2e6;">
                        <thead style="background: linear-gradient(135deg, #1565c0, #5badff);">
                            <tr>
                                <th style="padding: 10px; border: 1px solid #dee2e6; color: #ffffff;">NO</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; color: #ffffff;">FORMULA</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; color: #ffffff;">QUANTITY</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-available">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="wadah-live" id="wadah-live">
            <div class="live-header">
                <div class="live-indicator" id="live-indicator"></div>
                <div class="live-title idle" id="live-title-text">LIVE WEIGHING</div>
            </div>
            <div class="live-content" id="live-content">
                <div class="live-idle-text">NO ACTIVE WEIGHING PROCESS</div>
            </div>
        </div>

        <div class="wadah-tabel">
            <table class="tabel-weighing">
                <thead>
                    <tr>
                        <th style="width: 50px;">NO</th>
                        <th>FORMULA</th>
                        <th>LAST FINISH</th>
                        <th>QUANTITY</th>
                        <th>OPERATOR</th>
                        <th style="width: 100px;">DETAIL</th>
                    </tr>
                </thead>
                <tbody id="body-tabel-weighing">
                </tbody>
            </table>
        </div>

    </div> <span id="live-time" style="display:none;"></span>

    <script>
        let grafik;
        let barisYangTerbuka = new Set();
        let oldSummaryContent = "";
        let summaryHarian = null;
        let summaryBulanan = null;

        function renderGabunganSummary() {
            if (!summaryHarian || !summaryBulanan) return; // Tunggu dua-duanya selesai

            let insightItems = [
                `MOST FORMULAS TODAY: <span>${summaryHarian.top_formula} (${summaryHarian.top_form_qty} PKM)</span>`,
                `FASTEST FORMULA TODAY: <span>${summaryHarian.tercepat_hari_ini}</span>`,
                `SLOWEST FORMULA TODAY: <span>${summaryHarian.terlama_hari_ini}</span>`,
                `RECORD HIGHEST FORMULA OUTPUT THIS MONTH: <span>${summaryBulanan.top_formula_bulan} (${summaryBulanan.top_form_bulan_qty} PKM)</span>`,
                `RECORD HIGHEST PKM OUTPUT THIS MONTH: <span>${summaryBulanan.rekor_qty} PKM (Tgl ${summaryBulanan.rekor_tgl})</span>`,
                `TOTAL PKM THIS MONTH: <span>${summaryBulanan.total_bulan_ini} PKM</span>`,
                `DAILY AVERAGE PKM THIS MONTH: <span>${summaryBulanan.rata_bulan_ini} PKM/Hari</span>`
            ];

            let htmlSummary = insightItems.map(item => `<div class="ticker-item">${item}</div>`).join('');
            window.globalInsightHtml = insightItems.map(item =>
                `<div style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #0d47a1;">
                    ${item.replace('<span>', '<span style="color: #e65100;">')}
                </div>`
            ).join('');

            const tickerWrap = document.getElementById('ticker-wrap');
            const tickerContent = document.getElementById('ticker-content');
            if (oldSummaryContent !== htmlSummary) {
                tickerContent.innerHTML = htmlSummary;
                oldSummaryContent = htmlSummary;
            }
            tickerWrap.style.display = 'block';
        }

        //refresh
        function setTanggalHariIni() {
            const tzoffset = (new Date()).getTimezoneOffset() * 60000;
            const localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0, 10);
            document.getElementById('filter-date').value = localISOTime;
            return localISOTime;
        }

        function openTickerModal() {
            if (!window.globalInsightHtml) {
                Swal.fire({
                    icon: 'info',
                    title: 'Loading...',
                    text: 'Data is being prepared by the system',
                    confirmButtonColor: '#0d47a1'
                });
                return;
            }
            Swal.fire({
                title: '<h2 style="color: #0d47a1; margin-bottom: 0; border-bottom: 2px solid #1976d2; padding-bottom: 10px;">DASHBOARD INSIGHTS</h2>',
                html: `<div style="text-align: left; max-height: 60vh; overflow-y: auto;">${window.globalInsightHtml}</div>`,
                width: '800px',
                showCloseButton: true,
                confirmButtonText: 'CLOSE',
                confirmButtonColor: '#c62828',
                backdrop: `rgba(0,0,0,0.7)`
            });
        }

        //ini untuk tampilan tabel, tabel induk dan anak 
        function renderTabel(dataTabel) {
            const tbody = document.getElementById('body-tabel-weighing');
            const currentExpanded = document.querySelectorAll('.baris-induk.terbuka');
            currentExpanded.forEach(row => {
                barisYangTerbuka.add(row.getAttribute('data-formula'));
            });
            tbody.innerHTML = '';
            if (!dataTabel || dataTabel.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="padding:20px; color:#888;">There is no completed weighing data for this date.</td></tr>`;
                return;
            }
            dataTabel.forEach(induk => {
                const namaFormula = induk.formula;
                const isExpanded = barisYangTerbuka.has(namaFormula) ? 'terbuka' : '';
                const childDisplay = barisYangTerbuka.has(namaFormula) ? 'tampil' : '';

                const trInduk = document.createElement('tr');
                trInduk.className = `baris-induk ${isExpanded}`;
                trInduk.setAttribute('data-formula', namaFormula);

                trInduk.innerHTML = `
                    <td>${induk.no}</td>
                    <td>${namaFormula}</td>
                    <td>${induk.last_finish}</td>
                    <td>${induk.quantity}</td>
                    <td>${induk.operator}</td>
                    <td><button class="btn-expand"></button></td>
                `;

                const trAnak = document.createElement('tr');
                trAnak.className = `baris-detail ${childDisplay}`;
                let htmlAnak = `
                    <td colspan="6">
                        <table class="tabel-anak">
                             <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>IDWG</th>
                                    <th>WG</th>
                                    <th>START</th>
                                    <th>FINISH</th>
                                    <th>DURATION</th>
                                    <th>OPERATOR</th>
                                    <th>INFORMATION</th> 
                                </tr>
                            </thead>
                            <tbody>
                `;
                let noAnak = 1;
                induk.detail.forEach(child => {
                    if (child.machines && child.machines.length > 0) {
                        let totalMesin = child.machines.length;
                        let m1 = child.machines[0];
                        let clsValid = (child.validation === "VALID") ? "badge-valid-biru" : "badge-prog-oren";
                        let classWarnaPrint = (child.print_status === "HAS BEEN PRINTED") ? "badge-print-hijau" : "badge-print-merah";
                        htmlAnak += `
                            <tr>
                                <td rowspan="${totalMesin}"><strong>${noAnak}</strong></td>
                                <td rowspan="${totalMesin}"><strong>${child.idwg}</strong></td>
                                <td><strong>${m1.wg}</strong></td>
                                <td>${m1.start}</td>
                                <td>${m1.finish}</td>
                                <td>${m1.duration}</td>
                                <td>${m1.operator}</td>
                                <td rowspan="${totalMesin}">
                                    <span class="${clsValid}">${child.validation}</span>
                                    <span class="${classWarnaPrint}">${child.print_status}</span>
                                    ${child.wip_status ?
                                        `<span class="${child.wip_status === 'WIP SCANNED'
                                            ? 'badge-success'
                                            : 'badge-warning'}">
                                            ${child.wip_status}
                                        </span>`
                                    : ''}
                                </td>
                            </tr>
                        `;
                        for (let i = 1; i < totalMesin; i++) {
                            let mx = child.machines[i];
                            htmlAnak += `
                                <tr>
                                    <td><strong>${mx.wg}</strong></td>
                                    <td>${mx.start}</td>
                                    <td>${mx.finish}</td>
                                    <td>${mx.duration}</td>
                                    <td>${mx.operator}</td>
                                </tr>
                            `;
                        }
                        noAnak++;
                    }
                });

                htmlAnak += `</tbody></table></td>`;
                trAnak.innerHTML = htmlAnak;

                trInduk.addEventListener('click', function() {
                    const isOpen = this.classList.contains('terbuka');
                    if (isOpen) {
                        this.classList.remove('terbuka');
                        trAnak.classList.remove('tampil');
                        barisYangTerbuka.delete(namaFormula);
                    } else {
                        this.classList.add('terbuka');
                        trAnak.classList.add('tampil');
                        barisYangTerbuka.add(namaFormula);
                    }
                });
                tbody.appendChild(trInduk);
                tbody.appendChild(trAnak);
            });
        }

        //untuk tampilan grafik
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);
            grafik = am4core.create("grafikTrenTimbangan", am4charts.XYChart);

            var sumbuKategori = grafik.xAxes.push(new am4charts.CategoryAxis());
            sumbuKategori.dataFields.category = "hari";
            sumbuKategori.renderer.grid.template.location = 0;
            sumbuKategori.renderer.minGridDistance = 10;
            sumbuKategori.renderer.labels.template.rotation = -45;
            sumbuKategori.renderer.labels.template.horizontalCenter = "right";
            sumbuKategori.renderer.labels.template.verticalCenter = "middle";

            var sumbuNilai = grafik.yAxes.push(new am4charts.ValueAxis());
            sumbuNilai.min = 0;

            var seri1 = grafik.series.push(new am4charts.ColumnSeries());
            seri1.dataFields.valueY = "proses";
            seri1.dataFields.categoryX = "hari";
            seri1.columns.template.fill = am4core.color("#00c853");
            seri1.stroke = am4core.color("#00c853");
            seri1.name = "Actual Done";
            seri1.columns.template.column.cornerRadiusTopLeft = 8;
            seri1.columns.template.column.cornerRadiusTopRight = 8;

            seri1.columns.template.propertyFields.fillOpacity = "opasitas";
            seri1.columns.template.propertyFields.strokeOpacity = "opasitas";
            seri1.columns.template.tooltipHTML = `
                <div style="text-align: center; border-bottom: 1px solid #ffffff55; padding-bottom: 5px; margin-bottom: 5px;">
                    <span style="font-size: 16px; font-weight: bold;">{hari}</span><br>
                    Total: <b>{valueY} PKM</b>
                </div>
                <div style="text-align: left; font-size: 13px; max-height: 150px; overflow-y: auto;">
                    {tooltip_info}
                </div>
            `;
            seri1.tooltip.pointerOrientation = "pointer";
            seri1.tooltip.getFillFromObject = false;
            seri1.tooltip.background.fill = am4core.color("#2f3b52");
            seri1.tooltip.label.fill = am4core.color("#ffffff");

            var penanda1 = seri1.bullets.push(new am4charts.LabelBullet());
            penanda1.label.text = "{valueY}";
            penanda1.label.verticalCenter = "bottom";
            penanda1.label.dy = -5;
            penanda1.label.fontWeight = "bold";
            penanda1.interactionsEnabled = false;

            var seri2 = grafik.series.push(new am4charts.LineSeries());
            seri2.dataFields.valueY = "target";
            seri2.dataFields.categoryX = "hari";
            seri2.strokeWidth = 1.5;
            seri2.stroke = am4core.color("#2979ff");
            seri2.name = "Plan RPH";

            seri2.propertyFields.strokeOpacity = "opasitas";
            var penanda2 = seri2.bullets.push(new am4charts.CircleBullet());
            penanda2.circle.fill = am4core.color("#2979ff");
            penanda2.circle.radius = 2;
            penanda2.circle.propertyFields.fillOpacity = "opasitas";
            penanda2.circle.propertyFields.strokeOpacity = "opasitas";
            var labelTarget = seri2.bullets.push(new am4charts.LabelBullet());
            labelTarget.label.text = "{valueY}";
            labelTarget.label.verticalCenter = "bottom";
            labelTarget.label.dy = -15
            labelTarget.label.fontWeight = "bold";
            labelTarget.label.fill = am4core.color("#2979ff");
            labelTarget.interactionsEnabled = false;

            labelTarget.label.adapter.add("text", function(text, target_bullet) {
                if (target_bullet.dataItem && target_bullet.dataItem.dataContext) {
                    var data = target_bullet.dataItem.dataContext;
                    if (data.target == data.proses) {
                        return "";
                    }
                }
                return text;
            });
            grafik.legend = new am4charts.Legend();
            grafik.legend.position = "top";

            setTanggalHariIni();
            ambilDataWeighing(false);
            ambilDataLive();
        });

        //get data dashboard keseluruhan tapi ga pakai live
        function ambilDataWeighing(dariKlikUser) {
            const tanggalPilih = document.getElementById('filter-date').value;

            fetch('addon/controller/weighing/api_weighing.php?date=' + tanggalPilih)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "error") {
                        return;
                    }

                    const tzoffset = (new Date()).getTimezoneOffset() * 60000;
                    const tglHariIni = (new Date(Date.now() - tzoffset)).toISOString().slice(0, 10);

                    //kalau data yang dipilih ga ada
                    if (dariKlikUser && tanggalPilih !== tglHariIni && data.punya_data === false) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Data!',
                            text: 'Weighing data for that date not found!',
                            confirmButtonColor: '#c62828',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            document.getElementById('filter-date').value = tglHariIni;
                            ambilDataWeighing(false);
                            ambilDataLive();
                        });
                        return;
                    }

                    //ambil data 
                    document.getElementById('val-plan').innerText = data.kpi.target_rph;
                    document.getElementById('val-actual').innerText = data.kpi.selesai_aktual;
                    document.getElementById('val-onprog').innerText = data.kpi.sedang_proses;
                    document.getElementById('val-out').innerText = data.kpi.sisa_target;
                    document.getElementById('val-mulai').innerText = data.kpi.waktu_mulai;
                    document.getElementById('val-selesai').innerText = data.kpi.waktu_selesai;
                    document.getElementById('val-achieve').innerText = data.kpi_monthly.achievement;
                    document.getElementById('val-ct-prog').innerText = data.kpi_monthly.avg_ct;
                    document.getElementById('val-ct-act').innerText = data.kpi_monthly.actual_done;

                    const dateObj = new Date(tanggalPilih);
                    const days = ["SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY"];
                    const months = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
                    let topSubtitle = (tanggalPilih === tglHariIni) ? "TODAY" : `${days[dateObj.getDay()]}, ${tanggalPilih.split('-').reverse().join('-')}`;
                    document.querySelectorAll('.kpi-subtitle-top').forEach(el => el.innerText = topSubtitle);

                    let monthName = months[dateObj.getMonth()];
                    document.querySelectorAll('.kpi-subtitle-month').forEach(el => {
                        if (el.classList.contains("formula-alltime")) {
                            el.innerText = "ALL TIME";
                        } else {
                            el.innerText = "IN " + monthName;
                        }
                    });
                    document.getElementById('judul-grafik-utama').innerText = "TREN ACTUAL DONE VS PLAN RPH IN " + monthName;

                    //isi running text
                    if (data.summary) {
                        let insightItems = [
                            `MOST FORMULAS TODAY: <span>${data.summary.top_formula} (${data.summary.top_form_qty} PKM)</span>`,
                            `FASTEST FORMULA TODAY: <span>${data.summary.tercepat_hari_ini}</span>`,
                            `SLOWEST FORMULA TODAY: <span>${data.summary.terlama_hari_ini}</span>`,
                            `RECORD HIGHEST FORMULA OUTPUT THIS MONTH: <span>${data.summary.top_formula_bulan} (${data.summary.top_form_bulan_qty} PKM)</span>`,
                            `RECORD HIGHEST PKM OUTPUT THIS MONTH: <span>${data.summary.rekor_qty} PKM (Tgl ${data.summary.rekor_tgl})</span>`,
                            `TOTAL PKM THIS MONTH: <span>${data.summary.total_bulan_ini} PKM</span>`,
                            `DAILY AVERAGE PKM THIS MONTH: <span>${data.summary.rata_bulan_ini} PKM/Hari</span>`
                        ];

                        let htmlSummary = insightItems.map(item => `<div class="ticker-item">${item}</div>`).join('');
                        window.globalInsightHtml = insightItems.map(item =>
                            `<div style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #0d47a1;">
                                ${item.replace('<span>', '<span style="color: #e65100;">')}
                            </div>`
                        ).join('');

                        const tickerWrap = document.getElementById('ticker-wrap');
                        const tickerContent = document.getElementById('ticker-content');
                        if (oldSummaryContent !== htmlSummary) {
                            tickerContent.innerHTML = htmlSummary;
                            oldSummaryContent = htmlSummary;
                        }
                        tickerWrap.style.display = 'block';
                    }

                    if (grafik && data.grafik) {
                        data.grafik.forEach(d => {
                            d.opasitas = (dariKlikUser && d.tanggal_asli === tanggalPilih) ? 1 : (dariKlikUser ? 0.3 : 1);
                        });
                        grafik.data = data.grafik;
                    }

                    if (data.tabel) {
                        renderTabel(data.tabel);
                    }
                })
                .catch(error => console.error('Failed to get main data:', error));


            fetch('addon/controller/weighing/api_weighing_available.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === "error") return;

                    document.getElementById('val-available').innerText = data.available_formula;

                    const tbodyAvailable = document.getElementById('tbody-available');
                    if (tbodyAvailable) {
                        tbodyAvailable.innerHTML = '';
                        if (data.available_list && data.available_list.length > 0) {
                            data.available_list.forEach(item => {
                                tbodyAvailable.innerHTML += `
                            <tr>
                                <td style="padding: 8px; border: 1px solid #dee2e6; font-weight: 600;">${item.no}</td>
                                <td style="padding: 8px; border: 1px solid #dee2e6; font-weight: 600;">${item.formula}</td>
                                <td style="padding: 8px; border: 1px solid #dee2e6; font-weight: 600;">${item.quantity}</td>
                            </tr>
                        `;
                            });
                        } else {
                            tbodyAvailable.innerHTML = `<tr><td colspan="3" style="padding: 10px; text-align: center;">Tidak ada formula dengan status ON PROGRESS saat ini.</td></tr>`;
                        }
                    }
                })
                .catch(error => console.error('Failed to get available data:', error));
        }

        //get data live
        function ambilDataLive() {
            const tanggalPilih = document.getElementById('filter-date').value;
            const tzoffset = (new Date()).getTimezoneOffset() * 60000;
            const tglHariIni = (new Date(Date.now() - tzoffset)).toISOString().slice(0, 10);

            //untuk mematikan live saat pilih tanggal
            if (tanggalPilih !== tglHariIni) {
                document.getElementById('live-indicator').classList.remove('active');
                document.getElementById('live-title-text').classList.add('idle');
                document.getElementById('live-content').innerHTML = '<div class="live-idle-text">Displays History. Live Weighing is off.</div>';
                return;
            }

            //get data dari api_live_weighing.php
            fetch('addon/controller/weighing/api_live_weighing.php?date=' + tanggalPilih)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "error") {
                        return;
                    }

                    if (data.live && data.live.length > 0) {
                        document.getElementById('live-indicator').classList.add('active');
                        document.getElementById('live-title-text').classList.remove('idle');

                        //html untuk tampilan live
                        let htmlLive = '<div class="live-grid">';
                        data.live.forEach(lv => {
                            htmlLive += `
                                <div class="live-card">
                                    <div class="live-card-header">
                                        <div>
                                            <span class="live-mc">${lv.mc}</span>
                                            <span class="live-idwg">(${lv.idwg})</span>
                                        </div>
                                        <div class="live-time">START: ${lv.start_time}</div>
                                    </div>
                                    <div class="live-details">
                                        <div>FORMULA : <span style="color: #1976d2;">${lv.formula}</span></div>
                                        <div>OPERATOR : <span style="color: #1976d2;">${lv.operator}</span></div>
                                    </div>
                                    <div class="progress-wrap">
                                        <div class="progress-bar" style="width: ${lv.percentage}%;"></div>
                                        <div class="progress-text">${lv.percentage}%</div>
                                    </div>
                                    <div class="live-material">Currently Weighing Material : ${lv.current_material}</div>
                                </div>
                            `;
                        });
                        htmlLive += '</div>';
                        document.getElementById('live-content').innerHTML = htmlLive;
                    } else {
                        document.getElementById('live-indicator').classList.remove('active');
                        document.getElementById('live-title-text').classList.add('idle');
                        document.getElementById('live-content').innerHTML = '<div class="live-idle-text">No Active Weighing Process</div>';
                    }
                })
                .catch(error => console.error('Failed to get live data:', error));
        }

        function toggleAvailableTable() {
            const tableContainer = document.getElementById('container-available-table');
            if (tableContainer.style.display === 'none' || tableContainer.style.display === '') {
                tableContainer.style.display = 'block';
            } else {
                tableContainer.style.display = 'none';
            }
        }

        //filter data by tanggal
        document.getElementById('filter-date').addEventListener('change', function() {
            ambilDataWeighing(true);
            ambilDataLive();
        });

        //loop 15 detik dashboard
        setInterval(() => {
            const tzoffset = (new Date()).getTimezoneOffset() * 60000;
            const tglHariIni = (new Date(Date.now() - tzoffset)).toISOString().slice(0, 10);
            const tanggalPilih = document.getElementById('filter-date').value;

            if (tanggalPilih === tglHariIni) {
                ambilDataWeighing(false);
            }
        }, 15000);

        setInterval(() => {
            ambilDataLive();
        }, 1500);

        //opening
        window.addEventListener('load', () => {
            const splash = document.getElementById('splash-screen');
            setTimeout(() => {
                splash.classList.add('hide');
            }, 5000);
        });
    </script>
</body>

</html>