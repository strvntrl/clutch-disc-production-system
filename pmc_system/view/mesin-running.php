<?php
session_start();
include "../database.php";

if (!isset($_GET['seksi'])) {
    header("Location: index.php");
    exit;
}

$id_seksi = $_GET['seksi'];

$query = mysqli_query($con, "
SELECT 
    mesin.nama_mesin,
    produksi.mould,
    produksi.operator,
    produksi.start_produksi,
    produksi.lot_number,
    produksi.model,
    produksi.target,
    produksi.ok
FROM mesin
LEFT JOIN produksi 
    ON mesin.id_mesin = produksi.mesin_id
WHERE mesin.seksi_id='$id_seksi'
ORDER BY mesin.nama_mesin ASC
");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Mesin Running</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ================= GLOBAL ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f6f9;
        }

        /* ================= CONTENT ================= */
        .content {
            padding: 25px;
        }

        /* ================= PAGE HEADER ================= */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
        }

        /* ================= BACK BUTTON ================= */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #ffffff;
            color: #1e4fbf;
            border: 1px solid #1e4fbf;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: #1e4fbf;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(30, 79, 191, 0.3);
        }

        .back-btn:active {
            transform: scale(0.96);
        }

        /* ================= TOPBAR CLOCK ================= */
        .topbar-clock {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 16px;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #0f2a5c, #1e4fbf);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .clock-shift {
            font-size: 13px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 20px;
        }

        .clock-right {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .clock-date {
            font-size: 12px;
            opacity: 0.85;
        }

        .clock-time {
            font-size: 18px;
            font-weight: bold;
        }

        /* ================= SHIFT COLORS ================= */
        .shift-1 {
            background: linear-gradient(135deg, #2e7d32, #66bb6a);
        }

        .shift-2 {
            background: linear-gradient(135deg, #ef6c00, #ff9800);
        }

        .shift-3 {
            background: linear-gradient(135deg, #283593, #5c6bc0);
        }

        /* ================= TABLE CONTAINER ================= */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* ================= TABLE ================= */
        .running-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .running-table th {
            background: #0a2a66;
            color: white;
            padding: 12px 10px;
            font-size: 13px;
            text-align: center;
            white-space: nowrap;
        }

        .running-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            text-align: center;
            vertical-align: middle;
        }

        .running-table tr:last-child td {
            border-bottom: none;
        }

        /* ================= STATUS ROW ================= */
        .running-table tr.running td {
            background-color: #f0fdf4;
        }

        .running-table tr.ready td {
            background-color: #fefce8;
        }

        .running-table tr.running td:nth-child(3) {
            color: #16a34a;
            font-weight: 700;
        }

        .running-table tr.ready td:nth-child(3) {
            color: #ca8a04;
            font-weight: 700;
        }

        /* ================= KOLOM KHUSUS ================= */
        .number-col {
            width: 40px;
        }

        .machine-col {
            font-weight: 700;
            color: #1e3a8a;
        }

        .model-text {
            max-width: 180px;
            word-break: break-word;
            white-space: normal;
        }

        .small-number {
            width: 60px;
            font-weight: 700;
        }

        /* ================= RESPONSIVE 768px ================= */
        @media screen and (max-width: 768px) {
            .content {
                padding: 15px;
            }

            .page-header {
                flex-wrap: wrap;
                gap: 10px;
            }

            .page-title {
                font-size: 16px;
            }

            .topbar-clock {
                flex: 1;
                justify-content: center;
                min-width: 180px;
            }

            .clock-time {
                font-size: 14px;
            }

            .running-table th,
            .running-table td {
                font-size: 12px;
                padding: 8px 6px;
            }
        }

        /* ================= RESPONSIVE 480px ================= */
        @media screen and (max-width: 480px) {
            .back-btn {
                font-size: 12px;
                padding: 6px 10px;
            }

            .page-title {
                font-size: 14px;
            }

            .clock-time {
                font-size: 13px;
            }

            .clock-date {
                font-size: 10px;
            }

            .clock-shift {
                font-size: 10px;
            }
        }
    </style>
</head>

<body class="dashboard">
    <div class="content">
        <div class="page-header">
            <a href="../index.php?seksi=<?php echo $id_seksi; ?>" class="back-btn">
                ← KEMBALI
            </a>

            <div class="page-title">Mesin Running</div>

            <div class="topbar-clock">
                <div class="clock-shift" id="clockShift">Shift -</div>
                <div class="clock-right">
                    <div class="clock-date" id="clockDate">--</div>
                    <div class="clock-time" id="clockTime">--:--:--</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="running-table">
                <tr>
                    <th class="number-col">No</th>
                    <th>Mesin</th>
                    <th>Status</th>
                    <th>Mould</th>
                    <th>Operator</th>
                    <th>Start</th>
                    <th>LOT Number</th>
                    <th>Model</th>
                    <th>Kuota</th>
                    <th>PCS</th>
                </tr>

                <?php
                $no = 1;
                while ($row = odbc_fetch_array($query)) {
                    $status = "READY";
                    if ($row['operator'] != NULL) {
                        $status = "RUNNING";
                    }
                ?>
                    <tr class="<?php echo strtolower($status); ?>">
                        <td><?php echo $no++; ?></td>
                        <td class="machine-col"><?php echo $row['nama_mesin']; ?></td>
                        <td><?php echo $status; ?></td>
                        <td><?php echo $row['mould'] ?? "-"; ?></td>
                        <td><?php echo $row['operator'] ?? "-"; ?></td>
                        <td>
                            <?php
                            if ($row['start_produksi'] != NULL) {
                                echo date("d-m-Y H:i:s", strtotime($row['start_produksi']));
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td><?php echo $row['lot_number'] ?? "-"; ?></td>
                        <td class="model-text"><?php echo $row['model'] ?? "-"; ?></td>
                        <td class="small-number"><?php echo $row['target'] ?? "0"; ?></td>
                        <td class="small-number"><?php echo $row['ok'] ?? "0"; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();

            document.getElementById('clockDate').textContent =
                now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });

            document.getElementById('clockTime').textContent =
                now.toLocaleTimeString('id-ID');

            const h = now.getHours().toString().padStart(2, '0');
            const m = now.getMinutes().toString().padStart(2, '0');
            const s = now.getSeconds().toString().padStart(2, '0');
            const currentTime = `${h}:${m}:${s}`;

            let shift, shiftClass;

            if (currentTime >= "07:30:00" && currentTime < "15:30:00") {
                shift = 1;
                shiftClass = 'shift-1';
            } else if (currentTime >= "15:30:00" && currentTime < "23:30:00") {
                shift = 2;
                shiftClass = 'shift-2';
            } else {
                shift = 3;
                shiftClass = 'shift-3';
            }

            const shiftEl = document.getElementById('clockShift');
            const clockBox = document.querySelector('.topbar-clock');

            shiftEl.textContent = 'SHIFT ' + shift;
            clockBox.classList.remove('shift-1', 'shift-2', 'shift-3');
            clockBox.classList.add(shiftClass);
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>

</body>

</html>