<?php
session_start();
$GLOBALS['AUTH_GUARD_DEPTH'] = 0; // pmc/index.php -> auth/login.php (tanpa naik folder)
include __DIR__ . "/auth/auth_guard.php";
include __DIR__ . "/../database.php"; // root project database.php

date_default_timezone_set('Asia/Jakarta');

// Hanya 3 proses ini yang dibuka di PMC, sesuai cakupan project
$stageList = db_select(
    "SELECT stage_id, stage_code, stage_name
     FROM process_stage
     WHERE stage_code IN ('DRILLING', 'HOTPRESS', 'PREFORMING')
     ORDER BY sequence_order"
);

$selectedStage = $_GET['stage'] ?? '';
$selectedStageCode = '';
foreach ($stageList as $s) {
    if ((string) $s['stage_id'] === (string) $selectedStage) {
        $selectedStageCode = $s['stage_code'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PMC System</title>
    <!-- Pakai logo bersama yang sama dengan Dashboard Monitoring, satu sumber -->
    <link rel="icon" type="image/png" href="assets/img/logo_pmc.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f6f9;
        }

        .navbar {
            width: 100%;
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            gap: 20px;
            flex-wrap: wrap;
            z-index: 1000;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
        }

        .system-title {
            font-weight: bold;
            color: #0a2a66;
            font-size: 20px;
        }

        select.stage-select {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .user-info {
            text-align: right;
            font-size: 14px;
        }

        .role-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 12px;
            background: #e3f2fd;
            color: #0d47a1;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .logout-btn {
            background: #dc2626;
            color: white;
            padding: 9px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }

        .monitoring-link {
            padding: 9px 16px;
            border-radius: 8px;
            border: 1px solid #1e4fbf;
            color: #1e4fbf;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .monitoring-link:hover {
            background: #1e4fbf;
            color: white;
        }

        .content {
            padding: 25px;
        }

        .empty-state {
            background: white;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            color: #7b8794;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* ================= MODAL LOGOUT ================= */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(3px);
            justify-content: center;
            align-items: center;
            z-index: 2000;
            animation: fadeIn 0.2s ease;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: white;
            padding: 28px 30px;
            border-radius: 14px;
            text-align: center;
            width: 320px;
            max-width: 90vw;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            animation: scaleIn 0.2s ease;
        }

        .modal-box h3 {
            font-size: 19px;
            font-weight: 800;
            color: #0a2a66;
            margin-bottom: 8px;
        }

        .modal-box p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 22px;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .modal-actions button,
        .modal-actions a {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-modal-cancel {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-modal-cancel:hover {
            background: #d1d5db;
        }

        .btn-modal-confirm {
            background: #dc2626;
            color: #fff;
        }

        .btn-modal-confirm:hover {
            background: #b91c1c;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: stretch;
            }

            .nav-left,
            .nav-right {
                justify-content: center;
            }

            .logo {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-left">
            <div class="logo">
                <img src="../view/img/logo_perusahaan.png" alt="Logo">
            </div>
            <div class="system-title">Production Management Control</div>
            <form method="GET">
                <select name="stage" class="stage-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Proses --</option>
                    <?php foreach ($stageList as $s): ?>
                        <option value="<?= $s['stage_id'] ?>" <?= $selectedStage == $s['stage_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['stage_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="nav-right">
            <a href="../view/drilling.php" class="monitoring-link">Dashboard Monitoring</a>
            <div class="user-info">
                <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>
                <div class="role-badge"><?= htmlspecialchars($_SESSION['role']) ?></div>
            </div>
            <button type="button" class="logout-btn" onclick="openLogoutModal()">Logout</button>
        </div>
    </div>

    <div class="content">
        <?php if ($selectedStageCode === 'DRILLING'): ?>
            <?php include __DIR__ . "/view/drilling.php"; ?>
        <?php elseif ($selectedStageCode === 'HOTPRESS'): ?>
            <?php include __DIR__ . "/view/hotpress.php"; ?>
        <?php elseif ($selectedStageCode === 'PREFORMING'): ?>
            <?php include __DIR__ . "/view/preforming.php"; ?>
        <?php else: ?>
            <div class="empty-state">Pilih proses dari dropdown di atas untuk mulai input data.</div>
        <?php endif; ?>
    </div>

    <div id="logoutModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Konfirmasi Logout</h3>
            <p>Yakin ingin keluar dari sistem PMC?</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeLogoutModal()">Batal</button>
                <a href="auth/logout.php" class="btn-modal-confirm">Ya, Logout</a>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if (e.target === this) closeLogoutModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLogoutModal();
        });
    </script>
</body>

</html>