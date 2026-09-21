<?php
$page_title = "HOTPRESS DASHBOARD";
date_default_timezone_set('Asia/Jakarta');

$now = new DateTime();
$date_now = date('Y-m-d');
$day_num = date('N');
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
     <link rel="icon" type="image/png" href="/assets/img/logo_perusahaan.png">
     <title>Hotpress Dashboard</title>

     <script src="/assets/amcharts4/core.js"></script>
     <script src="/assets/amcharts4/charts.js"></script>
     <script src="/assets/amcharts4/themes/animated.js"></script>
     <script src="/assets/sweetalert2/sweetalert2.all.min.js"></script>

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
               color: #fd3300;
               letter-spacing: 2px;
               white-space: nowrap;
               overflow: hidden;
               border-right: 3px solid #fd3300;
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
               margin-bottom: 15px;
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
               display: inline-block;
               white-space: nowrap;
               animation: ticker-anim 40s linear infinite;
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

          .table-responsive-wrapper {
               width: 100%;
               overflow-x: auto;
               -webkit-overflow-scrolling: touch;
               border-radius: 0 0 8px 8px;
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
               .kpi-row {
                    grid-template-columns: repeat(2, 1fr);
               }
          }

          @media (max-width: 768px) {
               .kpi-row {
                    grid-template-columns: 1fr;
               }

               .meta-boxes-row {
                    flex-direction: column;
               }

               .status-grid {
                    grid-template-columns: 1fr;
               }

               .cumulative-box {
                    width: 100%;
                    flex-wrap: wrap;
               }
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

          .status-grid {
               display: grid;
               grid-template-columns: repeat(3, 1fr);
               gap: 15px;
          }

          .stat-item {
               border-radius: 12px;
               color: white;
               padding: 15px 20px;
               font-weight: 900;
               font-size: 24px;
               display: flex;
               justify-content: space-between;
               align-items: center;
               cursor: pointer;
               transition: 0.2s;
          }

          .stat-item:hover {
               transform: translateY(-3px);
          }

          .stat-run {
               background: linear-gradient(90deg, #2e7d32, #4caf50);
          }

          .stat-bd {
               background: linear-gradient(90deg, #e65100, #ff9800);
          }

          .stat-off {
               background: linear-gradient(90deg, #455a64, #78909c);
          }

          #status-detail-container {
               display: none;
               margin-top: 15px;
               padding: 15px;
               border-radius: 8px;
               font-weight: bold;
               color: #fff;
               text-align: center;
               font-size: 18px;
               animation: popIn 0.3s;
          }

          .machine-block {
               margin-bottom: 15px;
               border-radius: 8px;
               overflow: hidden;
               box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
          }

          .m-head {
               padding: 8px 15px;
               color: white;
               font-weight: 900;
               font-size: 16px;
               display: flex;
               align-items: center;
          }

          .m-head span {
               border: 1.5px solid white;
               border-radius: 20px;
               padding: 2px 10px;
               font-size: 11px;
               margin-left: 10px;
          }

          .head-run {
               background: #4caf50;
          }

          .head-bd {
               background: #ff9800;
          }

          .head-off {
               background: #78909c;
          }

          .table-custom {
               width: 100%;
               border-collapse: collapse;
               background: white;
               font-size: 15px;
               text-align: center;
          }

          .table-custom th,
          .table-custom td {
               padding: 12px 10px;
               border-bottom: 1px solid #e0e0e0;
               border-right: 1px solid #e0e0e0;
          }

          .table-custom th {
               background: #f8f9fa;
               color: #0d47a1;
               font-weight: 800;
               font-size: 14px;
          }

          .table-custom tr:nth-child(even) td {
               background: #fafbfc;
          }

          .btn-action {
               border: none;
               padding: 5px 10px;
               border-radius: 4px;
               color: white;
               font-weight: bold;
               cursor: pointer;
               font-size: 11px;
               transition: 0.2s;
               margin: 2px;
          }

          .btn-act-bd {
               background: #ffa928;
          }

          .btn-act-bd:hover {
               background: #e65100;
          }

          .btn-act-graph {
               background: #1976d2;
          }

          .btn-act-graph:hover {
               background: #0d47a1;
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

          .btn-show-off {
               flex: 1;
               background: #e3f2fd;
               border: 2px dashed #1976d2;
               color: #0d47a1;
               font-weight: 900;
               border-radius: 8px;
               cursor: pointer;
               transition: 0.3s;
               padding: 12px;
               font-size: 16px;
          }

          .btn-show-off:hover {
               background: #bbdefb;
          }

          @media (max-width: 768px) {
               .meta-ticker-row {
                    flex-direction: column;
               }

               .status-grid {
                    grid-template-columns: 1fr;
               }

               .table-custom {
                    font-size: 12px;
               }

               .cumulative-box {
                    width: 100%;
                    flex-wrap: wrap;
               }
          }

          .swal2-container {
               z-index: 9999999 !important;
          }
     </style>
</head>

<body>
     <div class="splash-screen" id="splash-screen">
          <div class="splash-content">
               <div class="logo-shimmer-wrapper" id="logo-wrapper">
                    <img src="/assets/img/logo_hotpress.png" alt="Logo" class="splash-logo-clutch-tech">
               </div>
               <h1 class="splash-title" id="splash-title"></h1>
               <script>
                    const titleEl = document.getElementById('splash-title');
                    const phrase = [{
                         t: "WELCOME TO ",
                         c: ""
                    }, {
                         t: "HOTPRESS",
                         c: "#fd3300"
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

     <?php include 'addon/control_header.php'; ?>

     <div class="main-container">
          <div class="meta-ticker-wrapper">
               <div class="meta-box box-start">START: <span id="val-start" style="margin-left: 5px;">00:00:00</span></div>
               <div class="box-ticker" onclick="openTickerModal()" title="View Summary">
                    <div class="ticker-content" id="ticker-content">Loading data...</div>
               </div>
               <div class="meta-box box-last">LAST: <span id="val-last-update" style="margin-left: 5px;">00:00:00</span></div>
          </div>

          <div class="kpi-row">
               <div class="kpi-card">
                    <div class="kpi-head bg-purple">PLAN RPH</div>
                    <div class="kpi-body">
                         <div class="kpi-badge badge-purple kpi-date-label">TODAY</div>
                         <div><span class="kpi-val" style="color: #4a148c;" id="val-plan">0</span> <span class="kpi-unit">CYCLE</span></div>
                    </div>
               </div>
               <div class="kpi-card">
                    <div class="kpi-head bg-green">ACTUAL</div>
                    <div class="kpi-body">
                         <div class="kpi-badge badge-green kpi-date-label">TODAY</div>
                         <div><span class="kpi-val" style="color: #2e7d32;" id="val-actual">0</span> <span class="kpi-unit">CYCLE</span></div>
                    </div>
               </div>
               <div class="kpi-card">
                    <div class="kpi-head bg-red">OUTSTANDING</div>
                    <div class="kpi-body">
                         <div class="kpi-badge badge-red kpi-date-label">TODAY</div>
                         <div><span class="kpi-val" style="color: #c62828;" id="val-gap">0</span> <span class="kpi-unit">CYCLE</span></div>
                    </div>
               </div>
               <div class="kpi-card">
                    <div class="kpi-head bg-blue">ACHIEVEMENT</div>
                    <div class="kpi-body">
                         <div class="kpi-badge badge-blue kpi-date-label">TODAY</div>
                         <div><span class="kpi-val" style="color: #1565c0;" id="val-ach">0%</span></div>
                    </div>
               </div>
          </div>

          <div class="section-box">
               <div class="section-title">MACHINE STATUS</div>
               <div class="status-grid">
                    <div class="stat-item stat-run" onclick="showStatusDetail('RUNNING')">
                         <span> RUNNING</span><span id="cnt-run" style="font-size: 40px;">0</span>
                    </div>
                    <div class="stat-item stat-bd" onclick="showStatusDetail('BREAKDOWN')">
                         <span> BREAKDOWN</span><span id="cnt-bd" style="font-size: 40px;">0</span>
                    </div>
                    <div class="stat-item stat-off" onclick="showStatusDetail('OFF')">
                         <span> OFF</span><span id="cnt-off" style="font-size: 40px;">0</span>
                    </div>
               </div>
               <div id="status-detail-container"></div>
          </div>

          <div class="section-box">
               <div class="section-title">MACHINE DETAIL</div>
               <div id="machine-container"></div>

               <div class="footer-row">
                    <div class="cumulative-box">
                         <div class="cumulative-title">TOTAL cumulative-</div>
                         <div class="cumulative-item">TARGET <span class="cumulative-val" id="ft-plan">0</span></div>
                         <div class="cumulative-item">ACTUAL <span class="cumulative-val" id="ft-actual">0</span></div>
                         <div class="cumulative-item">ACH% <span class="cumulative-val" id="ft-ach">0%</span></div>
                    </div>
                    <button id="btn-toggle-off" class="btn-show-off" style="display: none;" onclick="toggleOfflineRows()">SHOW 0 OFFLINE MACHINES</button>
               </div>
          </div>
     </div>

     <script>
          am4core.useTheme(am4themes_animated);
          let currentShift = "<?= $shift_name ?>";
          let isOfflineShown = false;
          let isHistoryMode = false;
          let openBdRows = {};
          let openGraphRows = {};
          let charts = {};
          let globalGraphData = {};
          let sysDateAtLoad = '';
          let sysShiftAtLoad = '';
          let globalMachinesData = [];
          let statusTimeout = null;
          let currentActiveStatus = '';

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

          function showStatusDetail(statusType, isAutoRefresh = false) {
               const container = document.getElementById('status-detail-container');
               if (!isAutoRefresh && currentActiveStatus === statusType && container.style.display === 'block') {
                    container.style.display = 'none';
                    clearTimeout(statusTimeout);
                    currentActiveStatus = '';
                    return;
               }
               currentActiveStatus = statusType;
               let bgColor = '',
                    borderColor = '';
               if (statusType === 'RUNNING') {
                    bgColor = '#2e7d32';
                    borderColor = '3px dashed #4caf50';
               } else if (statusType === 'BREAKDOWN') {
                    bgColor = '#e65100';
                    borderColor = '3px dashed #ff9800';
               } else {
                    bgColor = '#455a64';
                    borderColor = '3px dashed #78909c';
               }

               let countFromBox = parseInt(document.getElementById(statusType === 'RUNNING' ? 'cnt-run' : (statusType === 'BREAKDOWN' ? 'cnt-bd' : 'cnt-off')).innerText) ||
                    0;
               let text = '';

               if (countFromBox === 0) {
                    text = `There are no machines in ${statusType} condition at this time.`;
               } else {
                    let filtered = [];
                    if (statusType === 'RUNNING') filtered = isHistoryMode ? globalMachinesData.filter(m => m.act > 0) : globalMachinesData.filter(m => m.status !== 'OFF' && m.status !== 'RUNNING_BD');
                    else if (statusType === 'BREAKDOWN') filtered = isHistoryMode ? globalMachinesData.filter(m => m.status === 'RUNNING_BD' || (m.bd_history && m.bd_history.length > 0)) : globalMachinesData.filter(m => m.status === 'RUNNING_BD');
                    else if (statusType === 'OFF') filtered = isHistoryMode ? globalMachinesData.filter(m => m.act == 0 && (!m.bd_history || m.bd_history.length === 0)) : globalMachinesData.filter(m => m.status === 'OFF');
                    let mcList = filtered.map(m => m.mc).join(', ') || '(Waiting for data...)';
                    text = `${isHistoryMode ?
                    'HISTORY LIST' : 'MACHINE LIST'} <span style="color:#fff">${statusType}</span>: <br><span style="font-size: 22px; letter-spacing:1px;">${mcList}</span>`;
               }

               container.style.backgroundColor = bgColor;
               container.style.border = borderColor;
               container.innerHTML = text;
               container.style.display = 'block';

               clearTimeout(statusTimeout);
               statusTimeout = setTimeout(() => {
                    container.style.display = 'none';
                    currentActiveStatus = '';
               }, 15000);
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

          function updateShiftDropdownOptions() {
               const filterDate = document.getElementById('filter-date').value;
               const now = new Date();
               const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

               let kpiLabelText = "TODAY";
               if (filterDate !== todayStr) {
                    let dArr = filterDate.split('-');
                    kpiLabelText = `${dArr[2]} / ${dArr[1]} / ${dArr[0]}`;
               }
               document.querySelectorAll('.kpi-date-label').forEach(el => el.innerText = kpiLabelText);
               const shiftSelect = document.getElementById('filter-shift');
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
          }

          function toggleBdRow(mc) {
               openBdRows[mc] = !openBdRows[mc];
               let row = document.getElementById('bd-row-' + mc);
               if (row) row.style.display = openBdRows[mc] ? 'table-row' : 'none';
          }

          function toggleGraphRow(mc) {
               openGraphRows[mc] = !openGraphRows[mc];
               let row = document.getElementById('graph-row-' + mc);
               if (row) {
                    row.style.display = openGraphRows[mc] ?
                         'table-row' : 'none';
                    if (openGraphRows[mc] && globalGraphData[mc]) setTimeout(() => renderChart(mc, globalGraphData[mc]), 100);
               }
          }

          function toggleOfflineRows() {
               isOfflineShown = !isOfflineShown;
               let rows = document.querySelectorAll('.offline-block');
               rows.forEach(r => {
                    r.style.display = isOfflineShown ? 'block' : 'none';
               });
               let btn = document.getElementById('btn-toggle-off');
               btn.innerHTML = isOfflineShown ? `HIDE ${btn.dataset.cnt} OFFLINE MACHINES` : `SHOW ${btn.dataset.cnt} OFFLINE MACHINES`;
          }

          function renderChart(mc, data) {
               if (charts[mc]) {
                    charts[mc].dispose();
                    delete charts[mc];
               }
               let chart = am4core.create("chartdiv-" + mc, am4charts.XYChart);
               chart.data = data;

               let catAxis = chart.xAxes.push(new am4charts.CategoryAxis());
               catAxis.dataFields.category = "pcs";
               catAxis.renderer.minGridDistance = 15;

               let valAxis = chart.yAxes.push(new am4charts.ValueAxis());
               valAxis.min = 0;
               valAxis.extraMax = 0.1;

               let s2 = chart.series.push(new am4charts.ColumnSeries());
               s2.dataFields.valueY = "cycle_time";
               s2.dataFields.categoryX = "pcs";
               s2.name = "Cycle Time";
               s2.fill = am4core.color("#ff9800");
               s2.stroke = am4core.color("#ff9800");
               s2.clustered = false;
               s2.columns.template.width = am4core.percent(80);
               let labelCYC = s2.bullets.push(new am4charts.LabelBullet());
               labelCYC.label.text = "{valueY}";
               labelCYC.label.verticalCenter = "top";
               labelCYC.label.dy = 10;
               labelCYC.label.fill = am4core.color("#000");
               labelCYC.label.fontWeight = "bold";
               let s1 = chart.series.push(new am4charts.ColumnSeries());
               s1.dataFields.valueY = "machine_time";
               s1.dataFields.categoryX = "pcs";
               s1.name = "Machine Time";
               s1.fill = am4core.color("#1976d2");
               s1.stroke = am4core.color("#1976d2");
               s1.clustered = false;
               s1.columns.template.width = am4core.percent(45);

               let labelMC = s1.bullets.push(new am4charts.LabelBullet());
               labelMC.label.text = "{valueY}";
               labelMC.label.verticalCenter = "top";
               labelMC.label.dy = 10;
               labelMC.label.fill = am4core.color("#fff");
               labelMC.label.fontWeight = "bold";

               chart.legend = new am4charts.Legend();
               chart.legend.position = "bottom";
               chart.legend.paddingBottom = 10;
               s2.columns.template.column.cornerRadiusTopLeft = 5;
               s2.columns.template.column.cornerRadiusTopRight = 5;
               s1.columns.template.column.cornerRadiusTopLeft = 5;
               s1.columns.template.column.cornerRadiusTopRight = 5;
               chart.cursor = new am4charts.XYCursor();
               charts[mc] = chart;
          }

          function checkAutoShiftChange() {
               if (isHistoryMode) return;
               let currentSys = getSystemShiftState();
               if (sysDateAtLoad !== currentSys.sDate || sysShiftAtLoad !== currentSys.sShift) {
                    window.location.reload();
               }
          }

          function loadAll(dariKlik = false) {

               const dt = document.getElementById('filter-date').value;
               const sh = document.getElementById('filter-shift').value;

               fetch(`addon/controller/hotpress/api_hotpress.php?date=${dt}&shift=${sh}`).then(r => r.json()).then(res => {
                    const tzoffset = (new Date()).getTimezoneOffset() * 60000;
                    const tglHariIni = (new Date(Date.now() - tzoffset)).toISOString().slice(0, 10);
                    let tidakAdaData = false;

                    if (res.punya_data === false) {
                         tidakAdaData = true;
                    } else if (res.kpi && res.kpi.plan == 0 && res.kpi.actual == 0 && res.kpi.gap == 0) {
                         tidakAdaData = true;
                    }

                    if (dariKlik && dt !== tglHariIni && tidakAdaData) {
                         Swal.fire({
                              icon: 'warning',
                              title: 'Data Not Found',
                              text: 'Hotpress data for that date and shift not found!',
                              confirmButtonColor: '#0d47a1'
                         });
                         document.getElementById('filter-date').value = tglHariIni;
                         updateShiftDropdownOptions();
                         return loadAll(false);
                    }

                    document.getElementById('val-start').innerText = res.kpi.waktu_mulai || "00:00:00";
                    document.getElementById('val-last-update').innerText = res.kpi.waktu_selesai || "00:00:00";

                    document.getElementById('val-plan').innerText = res.kpi.plan;
                    document.getElementById('ft-plan').innerText = res.kpi.plan;
                    document.getElementById('val-actual').innerText = res.kpi.actual;
                    document.getElementById('ft-actual').innerText = res.kpi.actual;
                    document.getElementById('val-gap').innerText = res.kpi.gap;
                    document.getElementById('val-ach').innerText = res.kpi.ach + "%";
                    document.getElementById('ft-ach').innerText = res.kpi.ach + "%";

                    document.getElementById('cnt-run').innerText = res.status.run;
                    document.getElementById('cnt-bd').innerText = res.status.bd;
                    document.getElementById('cnt-off').innerText = res.status.off;

                    let tk = res.ticker_data;
                    let insights = [
                         `THIS SHIFT HIGHEST OUTPUT: <span>${tk.high_out_shift.mc || '-'} (${tk.high_out_shift.val || 0} PCS)</span>`,
                         `THIS SHIFT LOWEST OUTPUT: <span>${tk.low_out_shift.mc || '-'} (${tk.low_out_shift.val || 0} PCS)</span>`,
                         `FASTEST AVG CYCLE TIME (THIS SHIFT): <span>${tk.fast_cyc_shift.mc || '-'} (${tk.fast_cyc_shift.val || 0} SEC)</span>`,
                         `SLOWEST AVG CYCLE TIME (THIS SHIFT): <span>${tk.slow_cyc_shift.mc || '-'} (${tk.slow_cyc_shift.val || 0} SEC)</span>`,
                         `THIS MONTH HIGHEST OUTPUT: <span>${tk.highest_month_record.mc || '-'} (${tk.highest_month_record.val || 0} PCS) ON ${tk.highest_month_record.date || '-'} (${tk.highest_month_record.shift || '-'})</span>`,
                         `THIS MONTH LOWEST OUTPUT: <span>${tk.lowest_month_record.mc || '-'} (${tk.lowest_month_record.val || 0} PCS) ON ${tk.lowest_month_record.date || '-'} (${tk.lowest_month_record.shift || '-'})</span>`,
                         `MOST PRODUCTIVE SHIFT (THIS MONTH): <span>${tk.most_prod_shift.shift || '-'} (AVG ${tk.most_prod_shift.avg || 0} PCS/DAY)</span>`,
                         `LEAST PRODUCTIVE SHIFT (THIS MONTH): <span>${tk.least_prod_shift.shift || '-'} (AVG ${tk.least_prod_shift.avg || 0} PCS/DAY)</span>`,
                         `TOTAL ACTUAL (THIS MONTH): <span>${tk.total_month || 0} PCS</span>`
                    ];
                    document.getElementById('ticker-content').innerHTML = insights.map(i => `<span class="ticker-item">${i.replace('<span>','<span class="ticker-val">')}</span>`).join('');
                    window.globalInsightHtml = insights.map(i => `<div style="padding:10px 0; border-bottom:1px solid #ccc; font-weight:bold; color:#0d47a1;">${i.replace('<span>','<span style="color:#e65100;">')}</div>`).join('');
                    globalMachinesData = res.machines;
                    let htmlAct = '',
                         htmlOff = '',
                         cOff = 0,
                         cAct = 0;
                    res.machines.forEach(m => {
                         if (m.status !== 'OFF') globalGraphData[m.mc] = m.graph_data;
                         let headClass = 'head-run',
                              headText = 'RUNNING';

                         if (m.status == 'OFF') {
                              headClass = 'head-off';
                              headText = 'OFF';
                         } else if (m.status == 'RUNNING_BD') {
                              headClass = 'head-bd';
                              headText = 'BREAKDOWN';
                         }

                         let btnBd = (m.status == 'RUNNING_BD') ? `<button class="btn-action btn-act-bd" onclick="toggleBdRow('${m.mc}')">BREAKDOWN LOG</button>` : '';
                         let btnGraph = (m.status != 'OFF') ? `<button class="btn-action btn-act-graph" onclick="toggleGraphRow('${m.mc}')">SEE GRAPH</button>` : '';

                         let ops = [];
                         for (let i = 1; i <= 4; i++) {
                              if (m.shafts[i].operator && m.shafts[i].operator !== '-') {
                                   if (!ops.includes(m.shafts[i].operator)) ops.push(m.shafts[i].operator);
                              }
                         }

                         let opBadge = ops.map(o => `<span style="border: 1.5px solid white; border-radius: 20px; padding: 2px 10px; font-size: 11px; margin-left: 5px; background: rgba(255,255,255,0.2);">👤 ${o}</span>`).join('');
                         let cardHtml = `
     <div class="machine-block ${m.status == 'OFF' ? 'offline-block' : ''}" style="${m.status == 'OFF' && !isOfflineShown ? 'display:none;' : ''}">
          <div class="m-head ${headClass}">
               ${m.mc} <span>${headText}</span> ${opBadge}
               <div style="margin-left:auto;">${btnBd}${btnGraph}</div>
          </div>
          <div class="table-responsive-wrapper">
               <table class="table-custom">
                    <thead><tr><th>SHAF</th><th>PARTNO</th><th>TARGET (PCS)</th><th>ACTUAL (PCS)</th><th>ACH (%)</th><th>START</th><th>LAST</th></tr></thead>
                    <tbody>`;
                         if (m.status == 'OFF') {
                              cardHtml += `<tr><td colspan="7" style="text-align:center; font-weight:900; color:#7b8794; padding:20px; background:#f5f5f5; letter-spacing:2px;">MACHINE OFF</td></tr>`;
                         } else {
                              for (let i = 1; i <= 4; i++) {
                                   let s = m.shafts[i];
                                   let isUsed = (s.partno !== '-' || s.operator !== '-' || s.target > 0 || s.actual_pcs > 0);
                                   if (!isUsed) {
                                        cardHtml += `<tr><td style="color:#7b8794; font-weight:bold; background:#fafafa;">SHAF ${i}</td><td colspan="6" style="text-align:center; font-weight:bold; color:#b0bec5; background:#fafafa; font-style:italic; letter-spacing:1px;">NOT IN USE</td></tr>`;
                                   } else {
                                        let achC = s.ach >= 100 ? '#2e7d32' : '#c62828';
                                        cardHtml += `<tr><td style="color:#0d47a1; font-weight:bold;">SHAF ${i}</td><td>${s.partno}</td><td style="font-weight:bold;">${s.target}</td><td style="font-weight:900;">${s.actual_pcs}</td><td style="font-weight:900; color:${achC}">${s.ach}%</td><td>${s.start}</td><td>${s.last}</td></tr>`;
                                   }
                              }
                         }

                         let showBd = openBdRows[m.mc] ? 'table-row' : 'none';
                         if (m.status == 'RUNNING_BD' || openBdRows[m.mc]) {
                              let bdLog = m.bd_history.length > 0 ? m.bd_history.join("<br>") : "-";
                              cardHtml += `<tr id="bd-row-${m.mc}" style="display:${showBd}; background:#fff3e0;"><td colspan="7" style="text-align:left; color:#e65100; font-weight:bold;">⏱ DOWNTIME HISTORY:<br>${bdLog}</td></tr>`;
                         }

                         let showG = openGraphRows[m.mc] ? 'table-row' : 'none';
                         if (m.status != 'OFF' || openGraphRows[m.mc]) {
                              cardHtml += `<tr id="graph-row-${m.mc}" style="display:${showG}; background:#e3f2fd;"><td colspan="7"><div id="chartdiv-${m.mc}" style="width:100%; height:250px;"></div><div style="font-weight:bold; color:#0d47a1; margin-top:5px; padding-bottom:5px;">AVERAGE TIME FOR 1 CYCLE ➔ MACHINE TIME: ${m.avg_machine_time}s | CYCLE TIME: ${m.avg_cycle_time}s</div></td></tr>`;
                         }
                         cardHtml += `</tbody></table></div></div>`;
                         if (m.status == 'OFF') {
                              htmlOff += cardHtml;
                              cOff++;
                         } else {
                              htmlAct += cardHtml;
                              cAct++;
                         }
                    });
                    document.getElementById('machine-container').innerHTML = (cAct === 0 ? `<div style="padding:20px; text-align:center; font-weight:bold; color:#c62828;">NO ACTIVE MACHINES</div>` : htmlAct) + htmlOff;
                    res.machines.forEach(m => {
                         if (openGraphRows[m.mc] && m.status !== 'OFF') setTimeout(() => renderChart(m.mc, m.graph_data), 100);
                    });
                    let bOff = document.getElementById('btn-toggle-off');
                    if (cOff > 0) {
                         bOff.style.display = 'block';
                         bOff.dataset.cnt = cOff;
                         bOff.innerHTML = isOfflineShown ? `HIDE ${cOff} OFFLINE MACHINES` : `SHOW ${cOff} OFFLINE MACHINES`;
                    } else bOff.style.display = 'none';

                    if (currentActiveStatus) showStatusDetail(currentActiveStatus, true);
               }).catch(e => console.error(e));
          }

          document.getElementById('filter-date').addEventListener('change', function() {
               updateShiftDropdownOptions();
               loadAll(true);
          });
          document.getElementById('filter-shift').addEventListener('change', () => {
               updateShiftDropdownOptions();
               loadAll(true);
          });
          window.onload = function() {
               const sys = getSystemShiftState();
               sysDateAtLoad = sys.sDate;
               sysShiftAtLoad = sys.sShift;
               document.getElementById('filter-date').value = sysDateAtLoad;
               updateShiftDropdownOptions();
               loadAll(true);
               setTimeout(() => document.getElementById('splash-screen').classList.add('hide'), 3000);
          };
          setInterval(() => {
               if (!isHistoryMode) {
                    checkAutoShiftChange();
                    loadAll(false);
               }
          }, 15000);
     </script>
</body>

</html>