<?php
$page_title = "PREFORMING DASHBOARD";
date_default_timezone_set('Asia/Jakarta');

$now = new DateTime();
$date_now = date('Y-m-d');
$day_num = date('N'); // 1 = Senin ... 7 = Minggu
$hour_float = $now->format('H') + ($now->format('i') / 60);

if ($day_num != 7 && $hour_float >= 0 && $hour_float < 7.5) {
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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/logo_perusahaan.png">
    <title>Preforming Dashboard</title>

    <script src="../assets/sweetalert2/sweetalert2.all.min.js"></script>

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

        .splash-logo-clutch-tech {
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
            color: #00c721;
            letter-spacing: 2px;
            white-space: nowrap;
            overflow: hidden;
            border-right: 3px solid #00c721;
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

        .machine-grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        @media (max-width: 900px) {
            .machine-grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .m-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            text-align: center;
        }

        .m-table th {
            background: #f1f5f9;
            color: #7b8794;
            padding: 8px;
            font-size: 13px;
            font-weight: 900;
            border-bottom: 2px solid #e0e0e0;
        }

        .m-table td {
            padding: 10px 8px;
            font-size: 15px;
            font-weight: bold;
            border-bottom: 1px dashed #e0e0e0;
            color: #333;
        }

        .m-table tr:last-child td {
            border-bottom: none;
        }

        .m-table .val-target {
            color: #1976d2;
            font-weight: 900;
        }

        .m-table .val-side {
            color: #0d47a1;
            font-weight: 900;
        }

        .m-table .val-actual {
            font-size: 18px;
            font-weight: 900;
        }

        .meta-ticker-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .meta-box {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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
        }

        .ticker-content {
            display: inline-block;
            white-space: nowrap;
            animation: ticker-anim 20s linear infinite;
        }

        @keyframes ticker-anim {
            0% {
                transform: translateX(100vw);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .ticker-item {
            padding: 0 35px;
            font-size: 16px;
            letter-spacing: 0.5px;
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
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .kpi-val {
            font-size: 35px;
            font-weight: 900;
        }

        .kpi-unit {
            font-size: 16px;
            font-weight: bold;
            color: #7b8794;
        }

        .section-box {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 16px;
            font-weight: 900;
            color: #0d47a1;
            border: 2px solid #0d47a1;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .machine-block {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
        }

        .m-head {
            padding: 10px 15px;
            color: white;
            font-weight: 900;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .head-run {
            background: #4caf50;
        }

        .head-off {
            background: #78909c;
        }

        .footer-row {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .cumulative-box {
            border: 2px solid #0d47a1;
            border-radius: 8px;
            display: flex;
            font-weight: 900;
            color: #0d47a1;
            overflow: hidden;
            background: white;
        }

        .cumulative-title {
            background: #f1f5f9;
            padding: 12px 15px;
            border-right: 2px solid #0d47a1;
            display: flex;
            align-items: center;
        }

        .cumulative-item {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cumulative-val {
            border: 1.5px solid #0d47a1;
            border-radius: 6px;
            padding: 2px 8px;
        }
    </style>
</head>

<body>
    <?php include 'addon/control_header.php'; ?>

    <div class="splash-screen" id="splash-screen">
        <div class="splash-content">
            <div class="logo-shimmer-wrapper" id="logo-wrapper">
                <img src="../assets/img/logo_preforming.png" alt="Logo" class="splash-logo-clutch-tech">
            </div>
            <h1 class="splash-title" id="splash-title"></h1>
            <script>
                const titleEl = document.getElementById('splash-title');
                const phrase = [{
                        t: "WELCOME TO ",
                        c: ""
                    },
                    {
                        t: "PREFORMING",
                        c: "#00c721"
                    },
                    {
                        t: " DASHBOARD",
                        c: ""
                    }
                ];
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

    <div class="main-container">
        <div class="meta-ticker-wrapper">
            <div class="meta-box box-start">START: <span id="val-start" style="margin-left: 5px;">00:00:00</span></div>
            <div class="box-ticker">
                <div class="ticker-content" id="ticker-content">Loading data...</div>
            </div>
            <div class="meta-box box-last">LAST: <span id="val-last-update" style="margin-left: 5px;">00:00:00</span></div>
        </div>

        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-head bg-purple">PLAN TOTAL</div>
                <div class="kpi-body">
                    <div><span class="kpi-val" style="color: #4a148c;" id="val-plan">0</span> <span class="kpi-unit">PCS</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-green">ACTUAL TOTAL</div>
                <div class="kpi-body">
                    <div><span class="kpi-val" style="color: #2e7d32;" id="val-actual">0</span> <span class="kpi-unit">PCS</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-red">OUTSTANDING</div>
                <div class="kpi-body">
                    <div><span class="kpi-val" style="color: #c62828;" id="val-gap">0</span> <span class="kpi-unit">PCS</span></div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-head bg-blue">ACHIEVEMENT</div>
                <div class="kpi-body">
                    <div><span class="kpi-val" style="color: #1565c0;" id="val-ach">0%</span></div>
                </div>
            </div>
        </div>

        <div class="section-box">
            <div class="section-title">PREFORMING MACHINES (SIDE A & B)</div>

            <div class="machine-grid-container" id="machine-container">
            </div>

            <div class="footer-row">
                <div class="cumulative-box">
                    <div class="cumulative-title">TOTAL CUMULATIVE</div>
                    <div class="cumulative-item">TARGET <span class="cumulative-val" id="ft-plan">0</span></div>
                    <div class="cumulative-item">ACTUAL <span class="cumulative-val" id="ft-actual">0</span></div>
                    <div class="cumulative-item">ACH% <span class="cumulative-val" id="ft-ach">0%</span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentShift = "<?= $shift_name ?>";
        let isHistoryMode = false;
        let sysDateAtLoad = '';
        let sysShiftAtLoad = '';

        // ------------------------------------------------------------
        // Jadwal shift Senin-Sabtu, Minggu libur, 
        // tanpa pengecualian jam khusus hari Sabtu
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

        function updateShiftDropdownOptions() {
            const dateEl = document.getElementById('filter-date');
            if (!dateEl) return;

            const filterDate = dateEl.value;
            const now = new Date();
            const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

            let kpiLabelText = "TODAY";
            if (filterDate !== todayStr) {
                let dArr = filterDate.split('-');
                kpiLabelText = `${dArr[2]} / ${dArr[1]} / ${dArr[0]}`;
            }
            document.querySelectorAll('.kpi-date-label').forEach(el => el.innerText = kpiLabelText);

            const shiftSelect = document.getElementById('filter-shift');
            if (!shiftSelect) return;

            const currentVal = shiftSelect.value || currentShift;
            shiftSelect.innerHTML = '';

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
                },
                {
                    val: 'Shift-2',
                    text: 'SHIFT 2 (15:30 - 23:30)'
                },
                {
                    val: 'Shift-3',
                    text: 'SHIFT 3 (23:30 - 07:30)'
                }
            ];

            for (let i = 0; i < maxShift; i++) {
                let opt = document.createElement('option');
                opt.value = shifts[i].val;
                opt.innerHTML = shifts[i].text;
                if (shifts[i].val === currentVal) opt.selected = true;
                shiftSelect.appendChild(opt);
            }
            if (!shiftSelect.querySelector(`option[value="${currentVal}"]`)) shiftSelect.value = shifts[maxShift - 1].val;
            currentShift = shiftSelect.value;
        }

        function loadAll() {
            const dtEl = document.getElementById('filter-date');
            const shEl = document.getElementById('filter-shift');
            const dt = dtEl ? dtEl.value : "<?= $date_now ?>";
            const sh = shEl ? shEl.value : "<?= $shift_name ?>";

            fetch(`addon/controller/preforming/api_preforming.php?date=${dt}&shift=${sh}`)
                .then(r => r.json())
                .then(res => {
                    document.getElementById('val-start').innerText = res.kpi.waktu_mulai || "00:00:00";
                    document.getElementById('val-last-update').innerText = res.kpi.waktu_selesai || "00:00:00";

                    document.getElementById('val-plan').innerText = res.kpi.plan;
                    document.getElementById('ft-plan').innerText = res.kpi.plan;
                    document.getElementById('val-actual').innerText = res.kpi.actual;
                    document.getElementById('ft-actual').innerText = res.kpi.actual;
                    document.getElementById('val-gap').innerText = res.kpi.gap;

                    document.getElementById('val-ach').innerText = res.kpi.ach + "%";
                    document.getElementById('ft-ach').innerText = res.kpi.ach + "%";

                    if (res.ticker_data && res.ticker_data.length > 0) {
                        document.getElementById('ticker-content').innerHTML = res.ticker_data.map(i => `<span class="ticker-item">${i}</span>`).join(' • ');
                    }

                    let htmlBoxes = '';
                    res.machines.forEach(m => {
                        let headClass = (m.status === 'OFF' && m.A.actual == 0 && m.B.actual == 0) ? 'head-off' : 'head-run';
                        let statusText = (m.status === 'OFF' && m.A.actual == 0 && m.B.actual == 0) ? 'OFF' : 'RUNNING';

                        let achA_Color = m.A.ach >= 100 ? '#2e7d32' : '#c62828';
                        let achB_Color = m.B.ach >= 100 ? '#2e7d32' : '#c62828';

                        let actA_Color = m.A.actual > 0 ? '#333' : '#b0bec5';
                        let actB_Color = m.B.actual > 0 ? '#333' : '#b0bec5';

                        htmlBoxes += `
                    <div class="machine-block">
                        <div class="m-head ${headClass}">
                            <span>${m.mc}</span>
                            <span style="font-size: 12px; border:1px solid #fff; padding:2px 8px; border-radius:15px;">${statusText}</span>
                        </div>
                        <table class="m-table">
                            <thead>
                                <tr>
                                    <th>SIDE</th>
                                    <th>TARGET</th>
                                    <th>ACTUAL</th>
                                    <th>ACV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="val-side">A</td>
                                    <td class="val-target">${m.A.target}</td>
                                    <td class="val-actual" style="color: ${actA_Color};">${m.A.actual}</td>
                                    <td style="color: ${achA_Color};">${m.A.ach}%</td>
                                </tr>
                                <tr>
                                    <td class="val-side">B</td>
                                    <td class="val-target">${m.B.target}</td>
                                    <td class="val-actual" style="color: ${actB_Color};">${m.B.actual}</td>
                                    <td style="color: ${achB_Color};">${m.B.ach}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>`;
                    });

                    document.getElementById('machine-container').innerHTML = htmlBoxes;
                }).catch(e => console.error(e));
        }

        window.onload = function() {
            let dateEl = document.getElementById('filter-date');
            if (dateEl) {
                const sys = getSystemShiftState();
                sysDateAtLoad = sys.sDate;
                sysShiftAtLoad = sys.sShift;
                dateEl.value = sysDateAtLoad;
                updateShiftDropdownOptions();
            }

            loadAll();
            setInterval(() => {
                loadAll();
            }, 10000);
            setTimeout(() => {
                let splash = document.getElementById('splash-screen');
                if (splash) {
                    splash.classList.add('hide');
                }
            }, 3000);
        };

        let filterDateEl = document.getElementById('filter-date');
        let filterShiftEl = document.getElementById('filter-shift');

        if (filterDateEl) {
            filterDateEl.addEventListener('change', function() {
                updateShiftDropdownOptions();
                loadAll();
            });
        }
        if (filterShiftEl) {
            filterShiftEl.addEventListener('change', () => {
                updateShiftDropdownOptions();
                loadAll();
            });
        }
    </script>
</body>

</html>