<?php
include(__DIR__ . "/../database.php");
date_default_timezone_set('Asia/Jakarta');

$now = new DateTime();
$time = $now->format('H:i:s');
$day_num = $now->format('N'); // 1 = Senin ... 7 = Minggu
$hour_float = $now->format('H') + ($now->format('i') / 60);
$date_now = date('Y-m-d');

if ($day_num != 7 && $hour_float >= 0 && $hour_float < 7.5) {
    // Sebelum jam 07:30 dianggap masih kelanjutan Shift-3 hari sebelumnya
    $date_now = date('Y-m-d', strtotime('-1 day'));
    $shift_name = 'Shift-3';
} else {
    if ($hour_float >= 7.5 && $hour_float < 15.5) {
        $shift_name = 'Shift-1';
    } elseif ($hour_float >= 15.5 && $hour_float < 23.5) {
        $shift_name = 'Shift-2';
    } else {
        $shift_name = 'Shift-3';
    }
}
$time_now = date('H:i:s');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./img/logo_perusahaan.png">
    <title>Drilling Dashboard</title>

    <script src="../amcharts4/core.js"></script>
    <script src="../amcharts4/charts.js"></script>
    <script src="../amcharts4/themes/animated.js"></script>
    <script src="../sweetalert2/sweetalert2.all.min.js"></script>

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
            color: #b4b48e;
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
            margin-bottom: 10px;
            margin-top: 10px;
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
            animation: ticker-anim 140s linear infinite;
        }

        .ticker-item {
            padding: 0 35px;
            font-size: 16px;
            letter-spacing: 0.5px;
        }

        .ticker-val {
            color: #ffd54f;
            font-weight: 800;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
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
            .box-ticker {
                order: -1;
                flex: 1 1 100%;
                height: 45px;
            }

            .box-start,
            .box-last {
                flex: 1;
            }

            .kpi-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .kpi-row {
                grid-template-columns: 1fr;
            }
        }

        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            padding: 16px;
            margin-bottom: 16px;
            animation: fadeSlideUp 0.7s ease;
        }

        .table-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .table-title {
            font-size: clamp(20px, 2vw, 26px);
            font-weight: 900;
            color: #0d47a1;
            text-transform: uppercase;
        }

        .table-wrapper {
            width: 100%;
            max-height: 65vh;
            overflow-y: auto;
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            background-color: #fff;
        }

        .achievement-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1500px;
            text-align: center;
        }

        .achievement-table th {
            position: sticky;
            padding: 12px 5px;
            border: 1px solid #0d47a1;
        }

        .achievement-table thead tr:nth-child(1) th {
            top: 0;
            z-index: 20;
            background: #1565c0;
            color: #fff;
            font-weight: 900;
            font-size: 20px;
        }

        .achievement-table thead tr:nth-child(2) th {
            top: 58px;
            z-index: 19;
            background: #1976d2;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }

        .achievement-table td {
            border: 1px solid #e3e6ea;
            padding: 10px 8px;
            font-size: 15px;
            vertical-align: middle;
        }

        .row-hidden {
            display: none;
        }

        .achievement-table.show-all .row-hidden {
            display: table-row;
        }

        .row-active td {
            font-weight: 800;
            background: #ffffff;
            color: #000;
        }

        .row-history td {
            background: #f9fbfd;
            color: #444;
        }

        .row-cum td {
            background: #ffe082 !important;
            color: #e65100;
            border-top: 3px solid #ffb300;
            font-weight: 900;
        }

        .btn-see-all-wrapper {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .btn-see-all {
            background: #e0e0e0;
            color: #000;
            border: none;
            padding: 10px 50px;
            font-weight: 900;
            font-size: 18px;
            border-radius: 30px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-see-all:hover {
            background: #ccc;
            transform: scale(1.05);
        }

        .btn-toggle-graphic {
            background: linear-gradient(135deg, #0d47a1, #1976d2);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 15px;
        }

        .btn-toggle-graphic:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .chart-card {
            position: relative;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            padding: 16px;
            margin-bottom: 16px;
            width: 100%;
            height: 500px;
            animation: fadeSlideUp 0.7s ease;
            display: flex;
            flex-direction: column;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .chart-title {
            font-size: clamp(18px, 1.8vw, 24px);
            font-weight: 750;
            color: #0d47a1;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        #chartdiv,
        #chartdiv-historical {
            width: 100%;
            height: 100%;
            flex: 1;
        }

        .legend-badge {
            font-size: 13px;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 6px;
            color: white;
            display: inline-block;
            margin-left: 5px;
        }

        .badge-up {
            background-color: #80EF80;
        }

        .badge-down {
            background-color: #FF746C;
        }

        .gantt-scroll-container {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 15px;
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fafafa;
        }

        .gantt-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 15px 15px 5px 15px;
            min-width: 1000px;
            position: relative;
        }

        .machine-row {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .machine-info-header {
            display: flex;
            gap: 25px;
            font-weight: bold;
            font-size: 14px;
            color: #2f3b52;
        }

        .machine-info-header .mc-name {
            font-size: 18px;
            font-weight: 800;
            color: #0d47a1;
            min-width: 60px;
        }

        .progress-bar-bg {
            position: relative;
            width: 100%;
            height: 25px;
            border: 1px solid #000;
            background-color: #bdbdbd;
            border-radius: 8px;
            overflow: hidden;
        }

        .progress-block {
            position: absolute;
            height: 100%;
            top: 0;
            cursor: pointer;
        }

        .progress-block:hover {
            opacity: 0.8;
        }

        .zoom-controls {
            display: flex;
            gap: 10px;
        }

        .zoom-btn {
            background: #1976d2;
            color: #fff;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        #gantt-popover {
            position: fixed;
            z-index: 9999;
            background: #fff;
            border: 2px solid #1976d2;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            transform: translate(-50%, -100%);
            margin-top: -10px;
            display: none;
            color: #333;
            min-width: 200px;
        }

        #gantt-popover .close-btn {
            position: absolute;
            top: 5px;
            right: 8px;
            cursor: pointer;
            color: #fff;
            font-size: 16px;
        }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shift-dropdown {
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 2px solid #1976d2;
            font-weight: 800;
            color: #0d47a1;
            background-color: #fff;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .chart-card {
                height: 400px;
            }

            .chart-header {
                flex-direction: column;
                gap: 8px;
            }

            .table-title {
                font-size: 16px;
            }
        }

        .swal2-container {
            z-index: 9999999 !important;
        }

        .header-controls-flex {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-graph-sort {
            padding: 6px 15px;
            border: none;
            border-radius: 6px;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
            color: #fff;
        }

        .btn-sort-now {
            background: #42a5f5;
        }

        .btn-sort-now.active {
            background: #0d47a1;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-sort-monthly {
            background: #81c784;
        }

        .btn-sort-monthly.active {
            background: #1b5e20;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-sort-yearly {
            background: #ba68c8;
        }

        .btn-sort-yearly.active {
            background: #4a148c;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-sort-disabled {
            background: #e0e0e0 !important;
            color: #9e9e9e !important;
            cursor: not-allowed;
        }

        .chart-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .chart-loading-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0d47a1;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="splash-screen" id="splash-screen">
        <div class="splash-content">
            <div class="logo-shimmer-wrapper" id="logo-wrapper">
                <img src="./img/logo_drilling.png" alt="Company Logo" class="splash-logo-exedy">
            </div>
            <h1 class="splash-title" id="splash-title"></h1>
            <script>
                const titleEl = document.getElementById('splash-title');
                const phrase = [{
                    t: "WELCOME TO ",
                    c: ""
                }, {
                    t: "DRILLING",
                    c: "#b4b48e"
                }, {
                    t: " DASHBOARD",
                    c: ""
                }];
                let delay = 0,
                    splashHtml = '';
                phrase.forEach(p => {
                    for (let char of p.t) {
                        if (char === ' ') splashHtml += '&nbsp;';
                        else {
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

    <?php
    $page_title = "DASHBOARD DRILLING";
    include 'addon/control_header.php';
    ?>
    <div class="main-container">
        <div class="meta-ticker-wrapper">
            <div class="meta-box box-start">START: <span id="val-start" style="margin-left: 5px;">--:--:--</span></div>
            <div class="box-ticker" onclick="openTickerModal()" title="Click to View Insight Summary">
                <div class="ticker-content" id="ticker-content">Loading...</div>
            </div>
            <div class="meta-box box-last">LAST: <span id="val-last-update" style="margin-left: 5px;">--:--:--</span></div>
        </div>
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-head bg-purple">TARGET</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-purple kpi-date-label">TODAY</div>
                    <div><span class="kpi-val" style="color: #4a148c;" id="kpi-val-target">0</span> <span class="kpi-unit">PCS</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-green">ACTUAL</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-green kpi-date-label">TODAY</div>
                    <div><span class="kpi-val" style="color: #2e7d32;" id="kpi-val-actual">0</span> <span class="kpi-unit">PCS</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-red">OUTSTANDING</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-red kpi-date-label">TODAY</div>
                    <div><span class="kpi-val" style="color: #c62828;" id="kpi-val-gap">0</span> <span class="kpi-unit">PCS</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-blue">ACHIEVEMENT</div>
                <div class="kpi-body">
                    <div class="kpi-badge badge-blue kpi-date-label">TODAY</div>
                    <div><span class="kpi-val" style="color: #1565c0;" id="kpi-val-ach">0%</span></div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header-flex">
                <div class="table-title" id="judul-tabel">ACHIEVEMENT DETAIL SHIFT 1</div>
            </div>
            <div class="table-wrapper">
                <table class="achievement-table" id="achievementTable">
                    <thead></thead>
                    <tbody>
                        <tr>
                            <td colspan="20" style="padding:40px;">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn-see-all-wrapper" id="btn-see-all-wrapper">
                <button class="btn-see-all" id="btn-see-all" onclick="toggleSeeAll()">SEE ALL</button>
            </div>
        </div>

        <div id="graphic-container" style="display: block;">

            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">PRODUCTION TREND GRAPH IN THIS SHIFT</div>
                    <select id="filter-machine" class="shift-dropdown" style="padding: 5px 10px; font-size: 14px;">
                        <option value="ALL">ALL MACHINE</option>
                        <option value="DR-01">MACHINE 1 (DR-01)</option>
                        <option value="DR-02">MACHINE 2 (DR-02)</option>
                        <option value="DR-03">MACHINE 3 (DR-03)</option>
                        <option value="DR-04">MACHINE 4 (DR-04)</option>
                    </select>
                </div>
                <div id="chartdiv"></div>
            </div>

            <div class="chart-card">
                <div class="chart-loading-overlay" id="loading-historical">
                    <div class="spinner"></div>
                    <div style="font-weight: 800; color: #0d47a1;">FETCHING DATA...</div>
                </div>
                <div class="chart-header">
                    <div class="chart-title" id="title-historical">PRODUCTION TREND GRAPH IN ...</div>

                    <div class="header-controls-flex">
                        <button class="btn-graph-sort btn-sort-yearly" id="btn-sort-yearly" onclick="openYearlySelect()">YEARLY</button>
                        <button class="btn-graph-sort btn-sort-monthly" id="btn-sort-monthly" onclick="openMonthlySelect()">MONTHLY</button>
                        <button class="btn-graph-sort btn-sort-now active" id="btn-sort-now" onclick="resetToNow()">NOW</button>

                        <select id="filter-machine-historical" class="shift-dropdown" style="padding: 5px 10px; font-size: 14px; margin-left: 5px;">
                            <option value="ALL">ALL MACHINE</option>
                            <option value="DR-01">MACHINE 1 (DR-01)</option>
                            <option value="DR-02">MACHINE 2 (DR-02)</option>
                            <option value="DR-03">MACHINE 3 (DR-03)</option>
                            <option value="DR-04">MACHINE 4 (DR-04)</option>
                        </select>
                    </div>
                </div>
                <div id="chartdiv-historical"></div>
            </div>

            <div class="chart-card" style="height: auto; min-height: 500px;">
                <div class="chart-header">
                    <div class="chart-title">MACHINE STATUS
                        <div><span class="legend-badge badge-up">UPTIME</span><span class="legend-badge badge-down">DOWNTIME</span></div>
                    </div>
                    <div class="zoom-controls">
                        <button class="zoom-btn" onclick="zoomProgressBar(100)">+ ZOOM IN</button>
                        <button class="zoom-btn" onclick="zoomProgressBar(-100)">- ZOOM OUT</button>
                    </div>
                </div>
                <div class="gantt-scroll-container" id="gantt-scroll-container">
                    <div class="gantt-wrapper" id="gantt-wrapper" style="width: 100%;"></div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <button class="btn-toggle-graphic" id="btnToggleGraphic" onclick="toggleGraphic()">HIDE GRAPHIC ▲</button>
        </div>

        <div id="gantt-popover"></div>
    </div>

    <script>
        const DRILLING_MACHINES = ['DR-01', 'DR-02', 'DR-03', 'DR-04'];

        let currentShift = "<?= $shift_name ?>";
        let isExpanded = false;
        let activeJamIndex = -1;
        let chart;
        let chartHistorical;
        let refreshInterval;
        let isHistoryMode = false;
        let rawDataCache = [];
        let historicalDataCache = [];
        let isGraphicVisible = true;
        let currentZoomPercent = 100;
        let globalInsights = [];
        let sysDateAtLoad = '';
        let sysShiftAtLoad = '';
        let globalDowntimeRaw = [];
        let globalMonthData = null;
        let currentTotalPcs = 0;

        let currentHistoricalMode = 'now';
        let currentHistoricalMonth = new Date().getMonth() + 1;
        let currentHistoricalYear = new Date().getFullYear();

        am4core.useTheme(am4themes_animated);

        function openTickerModal() {
            if (globalInsights.length === 0) {
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
                html: `<div style="text-align: left; max-height: 60vh; overflow-y: auto;">${globalInsights.join('')}</div>`,
                width: '800px',
                showCloseButton: true,
                confirmButtonText: 'CLOSE',
                confirmButtonColor: '#c62828',
                backdrop: `rgba(0,0,0,0.7)`
            });
        }

        function setJudulTabelDinamis(shiftName) {
            const filterDateStr = document.getElementById('filter-date').value;
            const dateObj = new Date(filterDateStr);
            const days = ["SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY"];
            document.getElementById('judul-tabel').innerText = `ACHIEVEMENT DETAIL ${shiftName.replace('-', ' ').toUpperCase()} ON ${days[dateObj.getDay()]}, ${filterDateStr.split('-').reverse().join('-')}`;
        }

        function toggleGraphic() {
            const container = document.getElementById('graphic-container');
            const btn = document.getElementById('btnToggleGraphic');
            isGraphicVisible = !isGraphicVisible;
            if (isGraphicVisible) {
                container.style.display = 'block';
                btn.innerText = 'HIDE GRAPHIC ▲';
            } else {
                container.style.display = 'none';
                btn.innerText = 'SEE GRAPHIC ▼';
            }
        }

        function updateRunningTextAndInsights(data, totalPcs) {
            let mcTotals = {};
            let mcTargets = {};
            DRILLING_MACHINES.forEach(mc => {
                mcTotals[mc] = 0;
                mcTargets[mc] = 0;
            });
            data.forEach(r => {
                mcTotals[r.mc] += Number(r.actual);
                mcTargets[r.mc] += Number(r.target);
            });
            let highAct = -1,
                lowAct = Infinity,
                highMc = '-',
                lowMc = '-';
            let validCount = 0,
                arSum = 0;
            for (let mc in mcTotals) {
                let act = mcTotals[mc];
                let tgt = mcTargets[mc];
                if (act > highAct) {
                    highAct = act;
                    highMc = mc;
                }
                if (act < lowAct) {
                    lowAct = act;
                    lowMc = mc;
                }
                if (tgt > 0) {
                    validCount++;
                    let ar = Math.floor((act / tgt) * 100);
                    arSum += (ar > 100 ? 100 : ar);
                }
            }
            if (highAct === -1) highAct = 0;
            if (lowAct === Infinity) lowAct = 0;
            let avgAr = validCount > 0 ? Math.floor(arSum / validCount) : 0;

            function getBreakdownText(mc) {
                if (!globalDowntimeRaw || globalDowntimeRaw.length === 0) return "";
                let bdSec = 0;
                globalDowntimeRaw.forEach(b => {
                    if (b.machine === mc && b.status === 'DOWNTIME') bdSec += b.duration;
                });
                if (bdSec === 0) return "";
                let h = Math.floor(bdSec / 3600);
                let m = Math.floor((bdSec % 3600) / 60);
                let s = bdSec % 60;
                let timeStr = [];
                if (h > 0) timeStr.push(`${h} H`);
                if (m > 0) timeStr.push(`${m} M`);
                if (s > 0) timeStr.push(`${s} S`);
                return ` | Downtime Duration: ${timeStr.join(' ')}`;
            }
            let insightItems = [
                `THIS SHIFT HIGHEST OUTPUT: <span>${highMc} (${highAct} PCS${getBreakdownText(highMc)})</span>`,
                `THIS SHIFT LOWEST OUTPUT: <span>${lowMc} (${lowAct} PCS${getBreakdownText(lowMc)})</span>`,
                `TOTAL ACTUAL PRODUCTION: <span>${totalPcs} PCS</span>`,
                `AVERAGE ACHIEVEMENT RATE: <span>${avgAr}%</span>`
            ];
            if (globalMonthData) {
                insightItems.push(
                    `HIGHEST MACHINE (THIS MONTH): <span>${globalMonthData.top_mc.mc} (${globalMonthData.top_mc.val} PCS) - DATE : ${globalMonthData.top_mc.date} ${globalMonthData.top_mc.shift}</span>`,
                    `LOWEST MACHINE (THIS MONTH): <span>${globalMonthData.low_mc.mc} (${globalMonthData.low_mc.val} PCS) - DATE : ${globalMonthData.low_mc.date} ${globalMonthData.low_mc.shift}</span>`,
                    `HIGHEST CUMULATIVE ALL SHIFT (MONTH): <span>DATE : ${globalMonthData.high_cum.date} (${globalMonthData.high_cum.total} PCS) | S1: ${globalMonthData.high_cum.s1} PCS, S2: ${globalMonthData.high_cum.s2} PCS, S3: ${globalMonthData.high_cum.s3} PCS</span>`,
                    `LOWEST CUMULATIVE ALL SHIFT (MONTH): <span>DATE : ${globalMonthData.low_cum.date} (${globalMonthData.low_cum.total} PCS) | S1: ${globalMonthData.low_cum.s1} PCS, S2: ${globalMonthData.low_cum.s2} PCS, S3: ${globalMonthData.low_cum.s3} PCS</span>`,
                    `MOST PRODUCTIVE SHIFT: <span>${globalMonthData.top_shift.name} (Avg: ${globalMonthData.top_shift.avg} PCS/Day)</span>`,
                    `LOWEST PRODUCTIVE SHIFT: <span>${globalMonthData.low_shift.name} (Avg: ${globalMonthData.low_shift.avg} PCS/Day)</span>`,
                    `TOTAL ACTUAL (THIS MONTH): <span>${globalMonthData.total_month} PCS</span>`
                );
            }
            let tickerHtml = insightItems.map(item => `<span class="ticker-item">${item.replace('<span>', '<span class="ticker-val">')}</span>`).join(' &nbsp; &nbsp; &nbsp; &nbsp; ');
            document.getElementById('ticker-content').innerHTML = tickerHtml + tickerHtml;
            globalInsights = insightItems.map(item => `<div style="padding: 12px 0; border-bottom: 1px solid #eee; font-size: 15px; font-weight: bold; color: #0d47a1;">${item.replace('<span>', '<span style="color: #e65100; font-weight: 900;">')}</div>`);
        }

        function setFilterNow() {
            let sys = getSystemShiftState();
            document.getElementById('filter-date').value = sys.sDate;
            currentShift = sys.sShift;
            updateShiftDropdownOptions();
        }

        // ------------------------------------------------------------
        // Jadwal shift setiap hari kerja (Senin-Sabtu),
        // ------------------------------------------------------------
        function getSystemShiftState() {
            const now = new Date();
            const dayNum = now.getDay() === 0 ? 7 : now.getDay();
            const hourFloat = now.getHours() + (now.getMinutes() / 60);
            let sShift = 'Shift-3';
            let sDate = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
            if (dayNum !== 7 && hourFloat >= 0 && hourFloat < 7.5) {
                let prev = new Date(now);
                prev.setDate(prev.getDate() - 1);
                sDate = new Date(prev.getTime() - (prev.getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
                sShift = 'Shift-3';
            } else {
                if (hourFloat >= 7.5 && hourFloat < 15.5) sShift = 'Shift-1';
                else if (hourFloat >= 15.5 && hourFloat < 23.5) sShift = 'Shift-2';
                else sShift = 'Shift-3';
            }
            return {
                sDate,
                sShift
            };
        }

        function checkAutoShiftChange() {
            if (isHistoryMode) return;
            let currentSys = getSystemShiftState();
            if (sysDateAtLoad !== currentSys.sDate || sysShiftAtLoad !== currentSys.sShift) {
                window.location.reload();
            }
        }

        function updateShiftDropdownOptions() {
            const filterDate = document.getElementById('filter-date').value;
            const now = new Date();
            const todayStr = getSystemShiftState().sDate;
            const shiftSelect = document.getElementById('filter-shift');
            const currentVal = shiftSelect.value || currentShift;
            shiftSelect.innerHTML = '';

            let kpiLabelText = "TODAY";
            if (filterDate !== todayStr) {
                let dArr = filterDate.split('-');
                kpiLabelText = `${dArr[2]} / ${dArr[1]} / ${dArr[0]}`;
            }
            document.querySelectorAll('.kpi-date-label').forEach(el => el.innerText = kpiLabelText);

            let maxShift = 3;
            isHistoryMode = (filterDate < todayStr);
            if (filterDate === todayStr) {
                const timeFloat = now.getHours() + now.getMinutes() / 60;
                if (timeFloat >= 7.5 && timeFloat < 15.5) maxShift = 1;
                else if (timeFloat >= 15.5 && timeFloat < 23.5) maxShift = 2;
                else maxShift = 3;
                if (parseInt(currentVal.replace('Shift-', '')) < maxShift) isHistoryMode = true;
            }

            let shifts = [{
                val: 'Shift-1',
                text: 'SHIFT 1 (07:30 - 15:30)'
            }, {
                val: 'Shift-2',
                text: 'SHIFT 2 (15:30 - 23:30)'
            }, {
                val: 'Shift-3',
                text: 'SHIFT 3 (23:30 - 07:30)'
            }];
            for (let i = 0; i < maxShift; i++) {
                let opt = document.createElement('option');
                opt.value = shifts[i].val;
                opt.innerHTML = shifts[i].text;
                if (shifts[i].val === currentVal) opt.selected = true;
                shiftSelect.appendChild(opt);
            }
            if (!shiftSelect.querySelector(`option[value="${currentVal}"]`)) shiftSelect.value = shifts[maxShift - 1].val;
            currentShift = shiftSelect.value;
            setJudulTabelDinamis(currentShift);

            if (filterDate !== todayStr) {
                let dateObj = new Date(filterDate);
                loadHistoricalData('history', dateObj.getMonth() + 1, dateObj.getFullYear(), filterDate);
            } else {
                if (currentHistoricalMode === 'history') {
                    resetToNow();
                }
            }
        }

        function toggleSeeAll() {
            isExpanded = !isExpanded;
            const tb = document.getElementById('achievementTable');
            if (isExpanded) {
                tb.classList.add('show-all');
                document.getElementById('btn-see-all').innerText = 'HIDE ALL';
            } else {
                tb.classList.remove('show-all');
                document.getElementById('btn-see-all').innerText = 'SEE ALL';
            }
        }

        function renderAchievementTable(tableData) {
            const thead = document.querySelector('#achievementTable thead');
            const tbody = document.querySelector('#achievementTable tbody');
            thead.innerHTML = '';
            tbody.innerHTML = '';
            const machines = DRILLING_MACHINES;
            let mcGroup = {};
            machines.forEach(mc => mcGroup[mc] = []);
            tableData.forEach(r => mcGroup[r.mc].push(r));
            let trHead1 = document.createElement('tr');
            let trHead2 = document.createElement('tr');
            machines.forEach((mc, idx) => {
                let data = mcGroup[mc];
                let status = 'BREAKDOWN';
                let statusColor = '#ffea00';
                if (data.length > 0) {
                    let lastTimeMs = new Date(`${document.getElementById('filter-date').value}T${data[data.length-1].last}`).getTime();
                    if ((Date.now() - lastTimeMs) > 300000) {
                        status = 'BREAKDOWN';
                        statusColor = '#ffeb3b';
                    } else {
                        status = 'RUNNING';
                        statusColor = '#00e676';
                    }
                } else {
                    status = 'BREAKDOWN';
                    statusColor = '#ff5252';
                }
                let th1 = document.createElement('th');
                th1.colSpan = 4;
                th1.innerHTML = `MACHINE ${idx+1} <br> <span style="color:${statusColor}; font-size:14px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">${status}</span>`;
                trHead1.appendChild(th1);
                trHead2.innerHTML += `<th>PARTNO | OPR</th><th>TARGET</th><th>ACTUAL</th><th>ACV %</th>`;
            });
            thead.appendChild(trHead1);
            thead.appendChild(trHead2);
            let maxHistory = 0;
            machines.forEach(mc => {
                if (mcGroup[mc].length > 1) {
                    maxHistory = Math.max(maxHistory, mcGroup[mc].length - 1);
                }
            });
            for (let i = 0; i < maxHistory; i++) {
                let tr = document.createElement('tr');
                tr.className = `row-history ${isExpanded || isHistoryMode ? '' : 'row-hidden'}`;
                machines.forEach(mc => {
                    let data = mcGroup[mc];
                    if (i < data.length - 1) {
                        let r = data[i];
                        let acv = Math.round((r.actual / r.target) * 100);
                        if (acv > 100) acv = 100;
                        tr.innerHTML += `<td style="text-align:left; font-size:13px; font-weight:bold; line-height:1.4;">${r.pn}<br><span style="color:#1565c0">${r.opr}</span><br><span style="font-size:10px; color:#555; background:#e0e0e0; padding:2px 4px; border-radius:3px;">${r.start} - ${r.last}</span></td><td>${r.target}</td><td>${r.actual}</td><td>${acv}%</td>`;
                    } else {
                        tr.innerHTML += `<td colspan="4" style="background:#f9fbfd; color:#ccc">-</td>`;
                    }
                });
                tbody.appendChild(tr);
            }
            let trActive = document.createElement('tr');
            trActive.className = 'row-active';
            machines.forEach(mc => {
                let data = mcGroup[mc];
                if (data.length === 0) {
                    trActive.innerHTML += `<td colspan="4" style="color:#888; font-style:italic;">MACHINE NO ACTIVE</td>`;
                } else {
                    let r = data[data.length - 1];
                    let acv = Math.round((r.actual / r.target) * 100);
                    if (acv > 100) acv = 100;
                    trActive.innerHTML += `<td style="text-align:left; font-size:14px; font-weight:bold; line-height:1.4;">${r.pn}<br><span style="color:#1565c0">${r.opr}</span><br><span style="font-size:11px; color:#fff; background:#1976d2; padding:2px 4px; border-radius:3px;">${r.start} - ${r.last}</span></td><td>${r.target}</td><td>${r.actual}</td><td>${acv}%</td>`;
                }
            });
            tbody.appendChild(trActive);
            let trCum = document.createElement('tr');
            trCum.className = 'row-cum';
            machines.forEach(mc => {
                let data = mcGroup[mc];
                let totActual = 0;
                let totTarget = 0;
                data.forEach(d => {
                    totActual += d.actual;
                    totTarget += d.target;
                });
                let totAcv = totTarget > 0 ? Math.round((totActual / totTarget) * 100) : 0;
                if (totAcv > 100) totAcv = 100;
                trCum.innerHTML += `<td style="text-align:left;">CUMULATIVE</td><td>${totTarget}</td><td>${totActual}</td><td>${totAcv}%</td>`;
            });
            tbody.appendChild(trCum);
            const btnSeeAllWrap = document.getElementById('btn-see-all-wrapper');
            const tbObj = document.getElementById('achievementTable');
            if (isHistoryMode || maxHistory === 0) {
                btnSeeAllWrap.style.display = 'none';
                tbObj.classList.add('show-all');
            } else {
                btnSeeAllWrap.style.display = 'block';
                document.getElementById('btn-see-all').innerText = isExpanded ? 'HIDE ALL' : 'SEE ALL';
                if (isExpanded) tbObj.classList.add('show-all');
                else tbObj.classList.remove('show-all');
            }
        }

        async function loadAchievementData(dariKlikUser = false) {
            try {
                const selDate = document.getElementById('filter-date').value;
                currentShift = document.getElementById('filter-shift').value;
                setJudulTabelDinamis(currentShift);
                const url = `addon/controller/drilling/get_achievement.php?date=${selDate}&shift=${currentShift}`;
                const res = await fetch(url);
                const result = await res.json();
                const tglHariIni = getSystemShiftState().sDate;

                if (dariKlikUser && selDate !== tglHariIni && result.total_pcs === 0) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Data Not Found',
                        text: 'Drilling data for that date and shift not found!',
                        confirmButtonColor: '#0d47a1'
                    });
                    setFilterNow();
                    return loadAchievementData(false);
                }
                document.getElementById('val-start').innerText = result.start_time_val;
                document.getElementById('val-last-update').innerText = result.last_update;
                let target = result.kpi_target || 0;
                let actual = result.kpi_actual || 0;
                let gap = target - actual;
                if (gap < 0) gap = 0;
                let ach = target > 0 ? Math.round((actual / target) * 100) : 0;
                document.getElementById('kpi-val-target').innerText = target;
                document.getElementById('kpi-val-actual').innerText = actual;
                document.getElementById('kpi-val-gap').innerText = gap;
                document.getElementById('kpi-val-ach').innerText = ach + "%";
                rawDataCache = result.data;
                currentTotalPcs = result.total_pcs;
                globalMonthData = result.monthly_data;
                renderAchievementTable(result.table_data);
                renderChart(rawDataCache, document.getElementById('filter-machine').value);
                updateRunningTextAndInsights(rawDataCache, currentTotalPcs);
            } catch (e) {
                console.error(e);
            }
        }

        function renderChart(data, filterMc) {
            if (chart) chart.dispose();
            chart = am4core.create("chartdiv", am4charts.XYChart);
            chart.colors.step = 2;
            let grouped = {};
            data.forEach(r => {
                if (!grouped[r.jam]) grouped[r.jam] = {};
                grouped[r.jam][r.mc] = {
                    target: r.target,
                    actual: r.actual
                };
            });
            let shiftKey = (currentShift == 'Shift-1') ? 1 : (currentShift == 'Shift-2') ? 2 : 3;
            let jams = [];
            if (shiftKey == 1) jams = ["07:30", "08:30", "09:30", "10:30", "11:30", "12:30", "13:30", "14:30", "15:30"];
            else if (shiftKey == 2) jams = ["15:30", "16:30", "17:30", "18:30", "19:30", "20:30", "21:30", "22:30", "23:30"];
            else jams = ["23:30", "00:30", "01:30", "02:30", "03:30", "04:30", "05:30", "06:30", "07:30"];
            let chartData = [];
            jams.forEach((jam, idx) => {
                if (idx === jams.length - 1) return;
                let rowObj = {
                    "time": `${jam}-${jams[idx+1]}`
                };
                if (filterMc === "ALL") {
                    DRILLING_MACHINES.forEach(mc => {
                        rowObj[mc] = grouped[jam]?.[mc]?.actual || 0;
                    });
                    rowObj["target"] = 125;
                } else {
                    rowObj[filterMc] = grouped[jam]?.[filterMc]?.actual || 0;
                    rowObj["target"] = grouped[jam]?.[filterMc]?.target || 125;
                }
                chartData.push(rowObj);
            });
            chart.data = chartData;
            let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "time";
            categoryAxis.renderer.labels.template.rotation = -45;
            let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.title.text = "Production (PCS)";
            valueAxis.min = 0;

            function createSeries(field, name, isTarget) {
                let series = chart.series.push(new am4charts.LineSeries());
                series.dataFields.valueY = field;
                series.dataFields.categoryX = "time";
                series.name = name;
                series.tooltipText = "{name}: [bold]{valueY}[/] pcs";
                series.strokeWidth = 3;
                let bullet = series.bullets.push(new am4charts.CircleBullet());
                bullet.circle.strokeWidth = 2;
                bullet.circle.fill = am4core.color("#fff");
                if (isTarget) {
                    series.strokeDasharray = "5,5";
                    series.stroke = am4core.color("#d32f2f");
                    bullet.circle.stroke = am4core.color("#d32f2f");
                }
            }
            if (filterMc === "ALL") {
                DRILLING_MACHINES.forEach(mc => createSeries(mc, mc, false));
                createSeries("target", "Target/Machine", true);
            } else {
                createSeries(filterMc, filterMc, false);
                createSeries("target", "Target", true);
            }
            chart.cursor = new am4charts.XYCursor();
            chart.legend = new am4charts.Legend();
        }

        function updateSortButtonsState(mode) {
            const btnNow = document.getElementById('btn-sort-now');
            const btnMonthly = document.getElementById('btn-sort-monthly');
            const btnYearly = document.getElementById('btn-sort-yearly');

            btnNow.className = 'btn-graph-sort btn-sort-now';
            btnMonthly.className = 'btn-graph-sort btn-sort-monthly';
            btnYearly.className = 'btn-graph-sort btn-sort-yearly';

            if (mode === 'history') {
                btnNow.classList.add('btn-sort-disabled');
                btnMonthly.classList.add('btn-sort-disabled');
                btnYearly.classList.add('btn-sort-disabled');
            } else {
                if (mode === 'now') btnNow.classList.add('active');
                if (mode === 'monthly') btnMonthly.classList.add('active');
                if (mode === 'yearly') btnYearly.classList.add('active');
            }
        }

        function resetToNow() {
            if (isHistoryMode) return;
            currentHistoricalMode = 'now';
            loadHistoricalData('now');
        }

        async function openYearlySelect() {
            if (isHistoryMode) return;

            const {
                value: year
            } = await Swal.fire({
                title: '<h3 style="color:#0d47a1; font-weight:900; margin:0;">SELECT YEAR</h3><p style="font-size:14px; color:#555; font-weight:normal; margin-top:5px;">Type a valid year (e.g., 2024, 2026)</p>',
                html: `<div style="background:#f4f7fb; padding: 20px; border-radius: 12px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                         <input type="text" id="swal-input-year-only" class="swal2-input" placeholder="YYYY" value="${currentHistoricalYear}" style="text-align:center; font-weight:900; color:#4a148c; font-size:24px; letter-spacing: 2px; width: 60%; margin: 0 auto; border: 2px solid #ba68c8; border-radius: 10px;">
                       </div>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '<b>SEARCH</b>',
                confirmButtonColor: '#4a148c',
                cancelButtonColor: '#9e9e9e',
                preConfirm: () => {
                    const y = document.getElementById('swal-input-year-only').value.trim();
                    if (!y) return Swal.showValidationMessage('Year cannot be empty!');

                    if (!/^\d{4}$/.test(y) || parseInt(y) < 2000 || parseInt(y) > 2100) {
                        return Swal.showValidationMessage('Invalid year! Please enter a valid 4-digit year (e.g., 2024).');
                    }
                    return y;
                }
            });

            if (year) {
                loadHistoricalData('yearly', null, year);
            }
        }

        async function openMonthlySelect() {
            if (isHistoryMode) return;

            let monthOpts = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                .map((m, i) => `<option value="${i+1}" ${i+1 == currentHistoricalMonth ? 'selected' : ''}>${m}</option>`).join('');

            const {
                value: formValues
            } = await Swal.fire({
                title: '<h3 style="color:#0d47a1; font-weight:900; margin:0;">SELECT MONTH & YEAR</h3><p style="font-size:14px; color:#555; font-weight:normal; margin-top:5px;">Choose target month and type valid year</p>',
                html: `
                    <div style="background:#f4f7fb; padding: 20px; border-radius: 12px; display:flex; flex-direction:column; gap:15px; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                        <select id="swal-input-month" class="swal2-select" style="display:flex; width: 90%; margin: 0 auto; font-weight:bold; color:#1b5e20; border: 2px solid #81c784; border-radius: 10px; font-size:18px;">
                            <option value="" disabled>-- Select Month --</option>
                            ${monthOpts}
                        </select>
                        <input type="text" id="swal-input-year" class="swal2-input" placeholder="YYYY" value="${currentHistoricalYear}" style="display:flex; width: 90%; margin: 0 auto; text-align:center; font-weight:900; color:#1b5e20; border: 2px solid #81c784; border-radius: 10px; font-size:24px; letter-spacing: 2px;">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '<b>SEARCH</b>',
                confirmButtonColor: '#1b5e20',
                cancelButtonColor: '#9e9e9e',
                preConfirm: () => {
                    const m = document.getElementById('swal-input-month').value;
                    const y = document.getElementById('swal-input-year').value.trim();
                    if (!m || !y) return Swal.showValidationMessage('Please fill both Month and Year!');

                    if (!/^\d{4}$/.test(y) || parseInt(y) < 2000 || parseInt(y) > 2100) {
                        return Swal.showValidationMessage('Invalid year! Please enter a valid 4-digit year (e.g., 2024).');
                    }
                    return {
                        month: m,
                        year: y
                    };
                }
            });

            if (formValues) {
                loadHistoricalData('monthly', formValues.month, formValues.year);
            }
        }

        async function loadHistoricalData(type, month = null, year = null, histDate = null) {
            document.getElementById('loading-historical').classList.add('active');
            try {
                let url = `addon/controller/drilling/get_historical_trend.php?type=${type}`;
                if (month) url += `&month=${month}`;
                if (year) url += `&year=${year}`;
                if (histDate) url += `&date=${histDate}`;

                const res = await fetch(url);
                const result = await res.json();

                if (result.total_data === 0 && type !== 'now' && type !== 'history') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Not Found',
                        text: 'No production data found for selected period.',
                        confirmButtonColor: '#0d47a1'
                    });
                    document.getElementById('loading-historical').classList.remove('active');
                    return;
                } else if (result.total_data === 0 && type === 'history') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Not Found',
                        text: 'No production data found for that date.',
                        confirmButtonColor: '#0d47a1'
                    }).then(() => {
                        setFilterNow();
                    });
                    document.getElementById('loading-historical').classList.remove('active');
                    return;
                }

                currentHistoricalMode = type;
                if (month) currentHistoricalMonth = month;
                if (year) currentHistoricalYear = year;
                updateSortButtonsState(type);

                const titleEl = document.getElementById('title-historical');
                if (type === 'yearly') {
                    titleEl.innerText = `PRODUCTION TREND GRAPH IN ${result.year}`;
                } else {
                    titleEl.innerText = `PRODUCTION TREND GRAPH IN ${result.month_name.toUpperCase()}, ${result.year}`;
                }

                historicalDataCache = result.data;
                renderHistoricalChart(historicalDataCache, document.getElementById('filter-machine-historical').value, type);

            } catch (error) {
                console.error("Error loading historical data:", error);
            }
            document.getElementById('loading-historical').classList.remove('active');
        }

        function renderHistoricalChart(data, filterMc, type) {
            if (chartHistorical) chartHistorical.dispose();
            chartHistorical = am4core.create("chartdiv-historical", am4charts.XYChart);
            chartHistorical.colors.step = 2;

            let chartData = [];
            data.forEach(r => {
                let rowObj = {
                    "time": r.time
                };
                if (filterMc === "ALL") {
                    DRILLING_MACHINES.forEach(mc => {
                        rowObj[mc] = r[mc] || 0;
                    });
                    rowObj["target"] = r.target || 0;
                } else {
                    rowObj[filterMc] = r[filterMc] || 0;
                    rowObj["target"] = r.target || 0;
                }
                chartData.push(rowObj);
            });
            chartHistorical.data = chartData;

            let categoryAxis = chartHistorical.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "time";
            categoryAxis.renderer.labels.template.rotation = 0;
            if (type !== 'yearly') categoryAxis.renderer.minGridDistance = 20;

            let valueAxis = chartHistorical.yAxes.push(new am4charts.ValueAxis());
            valueAxis.title.text = "Production (PCS)";
            valueAxis.min = 0;

            function createSeriesHist(field, name, isTarget) {
                let series = chartHistorical.series.push(new am4charts.LineSeries());
                series.dataFields.valueY = field;
                series.dataFields.categoryX = "time";
                series.name = name;
                series.tooltipText = "{name}: [bold]{valueY}[/] pcs";
                series.strokeWidth = 3;
                let bullet = series.bullets.push(new am4charts.CircleBullet());
                bullet.circle.strokeWidth = 2;
                bullet.circle.fill = am4core.color("#fff");
                if (isTarget) {
                    series.strokeDasharray = "5,5";
                    series.stroke = am4core.color("#d32f2f");
                    bullet.circle.stroke = am4core.color("#d32f2f");
                }
            }

            if (filterMc === "ALL") {
                DRILLING_MACHINES.forEach(mc => createSeriesHist(mc, mc, false));
                if (type !== 'yearly') createSeriesHist("target", "Target/Machine", true);
            } else {
                createSeriesHist(filterMc, filterMc, false);
                if (type !== 'yearly') createSeriesHist("target", "Target", true);
            }

            chartHistorical.cursor = new am4charts.XYCursor();
            chartHistorical.legend = new am4charts.Legend();
        }

        document.getElementById('filter-machine-historical').addEventListener('change', () => {
            renderHistoricalChart(historicalDataCache, document.getElementById('filter-machine-historical').value, currentHistoricalMode);
        });

        async function loadDowntimeData() {
            const d = document.getElementById('filter-date').value;
            const s = document.getElementById('filter-shift').value;
            const res = await fetch(`addon/controller/drilling/get_downtime.php?date=${d}&shift=${s}`);
            const result = await res.json();
            globalDowntimeRaw = result.data;
            renderCustomGantt(result);
            if (rawDataCache.length > 0) {
                updateRunningTextAndInsights(rawDataCache, currentTotalPcs);
            }
        }

        function renderCustomGantt(resultData) {
            const wrapper = document.getElementById('gantt-wrapper');
            const container = document.getElementById('gantt-scroll-container');
            const currentScroll = container.scrollLeft;
            let html = '';
            const shiftStartTs = new Date(resultData.start_shift).getTime();
            const totalShiftMs = new Date(resultData.end_shift).getTime() - shiftStartTs;
            DRILLING_MACHINES.forEach(mc => {
                const summary = resultData.summary[mc];
                html += `<div class="machine-row"><div class="machine-info-header"><span class="mc-name">${mc}</span><span>START UPTIME : <span style="font-weight:normal">${summary.start_uptime}</span></span><span>LAST UPTIME : <span style="font-weight:normal">${summary.last_uptime}</span></span><span>AVG ALL TIME / PCS : <span style="font-weight:normal">${summary.avg_time} SCD/PCS</span></span></div><div class="progress-bar-bg">`;
                resultData.data.filter(b => b.machine === mc).forEach(b => {
                    const left = ((new Date(b.start).getTime() - shiftStartTs) / totalShiftMs) * 100;
                    const width = (b.duration * 1000 / totalShiftMs) * 100;
                    const avg = b.pcs > 0 ? (b.duration / b.pcs).toFixed(1) : 0;
                    html += `<div class="progress-block" style="left: ${left}%; width: ${width}%; background-color: ${b.color};" onclick="tampilkanDetailBlok(event, '${mc}', '${b.status}', '${b.start}', '${b.end}', ${b.pcs}, ${avg})"></div>`;
                });
                html += `</div></div>`;
            });
            // Setiap shift berdurasi 8 jam seragam pada skema baru
            const maxHours = 8;
            html += `<div class="timeline-axis" style="position: relative; width: 100%; height: 35px; margin-top: 10px; border-top: 2px solid #bdbdbd;">`;
            for (let i = 0; i <= maxHours; i++) {
                let left = (i / maxHours) * 100;
                let d = new Date(shiftStartTs + (i * 3600000));
                let tStr = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
                html += `<div style="position: absolute; left: ${left}%; transform: translateX(-50%); text-align: center;"><div style="width: 2px; height: 10px; background-color: #bdbdbd; margin: 0 auto;"></div><div style="font-size: 13px; font-weight: 800; color: #555; margin-top: 4px;">${tStr}</div></div>`;
            }
            html += `</div>`;
            wrapper.innerHTML = html;
            container.scrollLeft = currentScroll;
        }

        function tampilkanDetailBlok(e, mc, status, start, end, pcs, avg) {
            const pop = document.getElementById('gantt-popover');
            pop.style.backgroundColor = status === 'UPTIME' ? '#80EF80' : '#FF746C';
            pop.style.color = '#fff';
            let html = `<div class="close-btn" onclick="document.getElementById('gantt-popover').style.display='none'">✖</div><div style="text-align:center; font-size:18px; margin-bottom:10px; border-bottom: 1px solid rgba(255,255,255,0.4);">${mc}</div>`;
            if (status === 'UPTIME') html += `<div>STATUS: <b>UPTIME</b></div><div>TIME: ${start.split(' ')[1]} - ${end.split(' ')[1]}</div><div style="margin-top: 5px;">TOTAL: <b>${pcs} PCS</b></div><div>AVG: <b>${avg} SCD/PCS</b></div>`;
            else html += `<div>STATUS: <b>DOWNTIME</b></div><div>TIME: ${start.split(' ')[1]} - ${end.split(' ')[1]}</div>`;
            pop.innerHTML = html;
            pop.style.left = e.clientX + 'px';
            pop.style.top = Math.max(0, e.clientY - 15) + 'px';
            pop.style.display = 'block';
        }
        document.addEventListener('click', e => {
            if (!e.target.classList.contains('progress-block') && !e.target.closest('#gantt-popover')) document.getElementById('gantt-popover').style.display = 'none';
        });

        function zoomProgressBar(amt) {
            currentZoomPercent += amt;
            if (currentZoomPercent < 100) currentZoomPercent = 100;
            document.getElementById('gantt-wrapper').style.width = currentZoomPercent + '%';
        }

        window.onload = function() {
            let sysStart = getSystemShiftState();
            sysDateAtLoad = sysStart.sDate;
            sysShiftAtLoad = sysStart.sShift;
            document.getElementById('filter-date').value = sysStart.sDate;
            currentShift = sysStart.sShift;

            updateShiftDropdownOptions();

            loadAchievementData(false);
            loadDowntimeData();

            if (!isHistoryMode) loadHistoricalData('now');

            setInterval(() => {
                if (!isHistoryMode) {
                    checkAutoShiftChange();
                    loadAchievementData(false);
                    loadDowntimeData();
                }
            }, 5000);
        };

        document.getElementById('filter-date').addEventListener('change', () => {
            updateShiftDropdownOptions();
            loadAchievementData(true);
            loadDowntimeData();
        });

        document.getElementById('filter-shift').addEventListener('change', () => {
            currentShift = document.getElementById('filter-shift').value;
            setJudulTabelDinamis(currentShift);
            loadAchievementData(true);
            loadDowntimeData();
        });

        document.getElementById('filter-machine').addEventListener('change', () => renderChart(rawDataCache, document.getElementById('filter-machine').value));

        window.addEventListener('load', () => setTimeout(() => document.getElementById('splash-screen').classList.add('hide'), 4000));
    </script>
</body>

</html>