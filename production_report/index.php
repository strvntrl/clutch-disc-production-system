<?php
session_start();
setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');

// ================= PARAMETER =================
$granularitas = (!empty($_GET['granularitas'])) ? $_GET['granularitas'] : '';
$date_from    = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01', strtotime('-3 months'));
$date_to      = !empty($_GET['date_to'])   ? $_GET['date_to']   : date('Y-m-d');
$selected_seksi = isset($_GET['seksi']) && is_array($_GET['seksi']) ? $_GET['seksi'] : [];

include "database.php";
include "./view/addon/controller/dashboardController.php";
include "./view/addon/controller/reportController.php";

/**
 * @var array<int, array{id_seksi: int, nama_seksi: string}> $seksiList
 * @var array<int, array<int, array{form_id:int, row_id:int, periode:string, mesin:string, shift:string, iot:int, actual:int, ng:int, target:int}>> $reportBySeksi
 * @var array<int, array{total_iot:int, total_actual:int, total_ng:int, total_target:int, achievement:string}> $kpiBySeksi
 * @var array<int, array{akar_masalah: array, mesin_breakdown: array}> $breakdownBySeksi
 */
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Report - Clutch Tech</title>
    <script src="/assets/amcharts4/core.js"></script>
    <script src="/assets/amcharts4/charts.js"></script>
    <script src="/assets/amcharts4/themes/animated.js"></script>
    <script src="/assets/js/html2canvas.min.js"></script>
    <style>
        :root {
            --navy: #0f2a5c;
            --navy2: #1e4fbf;
            --blue: #2563eb;
            --blue-lt: #eff6ff;
            --green: #16a34a;
            --green-lt: #f0fdf4;
            --orange: #ea580c;
            --orange-lt: #fff7ed;
            --red: #dc2626;
            --red-lt: #fef2f2;
            --yellow: #d97706;
            --yellow-lt: #fffbeb;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --pink: #e11dca;
            --purple: #7c3aed;
            --yellow2: #f59e0b;
            --teal: #0f766e;
            --white: #ffffff;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.10);
        }

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

        /* NAVBAR */
        .navbar {
            width: 100%;
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            gap: 20px;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            min-width: 0;
        }

        .logo img {
            width: 200px;
        }

        .system-title {
            font-weight: bold;
            color: #0a2a66;
            font-size: 20px;
            white-space: nowrap;
        }

        /* MULTI SEKSI */
        .multi-seksi-wrapper {
            position: relative;
            display: inline-block;
        }

        .multi-seksi-trigger {
            appearance: none;
            -webkit-appearance: none;
            padding: 12px 45px 12px 16px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            min-width: 160px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            position: relative;
        }

        .multi-seksi-trigger:hover {
            border-color: #2563eb;
            background: #fff;
        }

        .multi-seksi-trigger::after {
            content: "";
            position: absolute;
            right: 14px;
            top: 50%;
            width: 10px;
            height: 10px;
            border-right: 2px solid #6b7280;
            border-bottom: 2px solid #6b7280;
            transform: translateY(-50%) rotate(45deg);
            pointer-events: none;
        }

        .multi-seksi-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 200px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            z-index: 2000;
            padding: 8px 0;
            overflow: hidden;
        }

        .multi-seksi-dropdown.open {
            display: block;
        }

        .multi-seksi-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            transition: background 0.15s;
        }

        .multi-seksi-option:hover {
            background: #f0f4ff;
        }

        .multi-seksi-option.selected {
            background: #eff6ff;
            color: #2563eb;
        }

        .multi-seksi-option input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
            flex-shrink: 0;
        }

        .seksi-badge {
            display: inline-flex;
            align-items: center;
            background: #eff6ff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 4px;
        }

        /* GRANULARITAS */
        .nav-pill {
            position: relative;
            display: inline-block;
        }

        .nav-pill::after {
            content: "";
            position: absolute;
            right: 14px;
            top: 50%;
            width: 10px;
            height: 10px;
            border-right: 2px solid #6b7280;
            border-bottom: 2px solid #6b7280;
            transform: translateY(-50%) rotate(45deg);
            pointer-events: none;
        }

        .nav-pill select {
            appearance: none;
            -webkit-appearance: none;
            padding: 12px 45px 12px 16px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            font-size: 15px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .nav-pill select:hover {
            border-color: #2563eb;
            background: #fff;
        }

        .nav-pill select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        /* DATE RANGE */
        .nav-daterange {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-daterange input[type="date"] {
            appearance: none;
            -webkit-appearance: none;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #2563eb;
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .nav-daterange input[type="date"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .nav-daterange span {
            color: #2563eb;
            font-size: 14px;
            font-weight: 700;
        }

        /* BUTTONS */
        .btn-filter {
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid #1e4fbf;
            background: white;
            color: #1e4fbf;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s;
            white-space: nowrap;
        }

        .btn-filter:hover {
            background: #1e4fbf;
            color: white;
        }

        /* CLOCK */
        .topbar-clock {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #0f2a5c, #1e4fbf);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .clock-right {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .clock-date {
            font-size: 12px;
            opacity: 0.85;
            white-space: nowrap;
        }

        .clock-time {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
        }

        /* CONTENT */
        .content {
            margin-top: 100px;
            padding: 24px 28px;
            max-width: 2000px;
            margin-left: auto;
            margin-right: auto;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1.5px solid var(--gray-200);
        }

        .page-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 40px;
            font-weight: 700;
            color: var(--navy);
        }

        .page-subtitle {
            font-size: 15px;
            color: var(--gray-400);
            margin-top: 2px;
        }

        /* KPI CARDS */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .kpi-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px 22px;
            border: 1.5px solid var(--gray-200);
            position: relative;
            overflow: hidden;
            transition: all 0.2s;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .kpi-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
        }

        .kpi-card.blue::before {
            background: var(--blue);
        }

        .kpi-card.orange::before {
            background: var(--orange);
        }

        .kpi-card.green::before {
            background: var(--green);
        }

        .kpi-card.red::before {
            background: var(--red);
        }

        .kpi-card.pink::before {
            background: var(--pink);
        }

        .kpi-card.yellow::before {
            background: var(--yellow2);
        }

        .kpi-card.purple::before {
            background: var(--purple);
        }

        .kpi-card.teal::before {
            background: var(--teal);
        }

        .kpi-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--gray-400);
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: -1px;
            line-height: 1;
        }

        .kpi-card.blue .kpi-value {
            color: var(--blue);
        }

        .kpi-card.orange .kpi-value {
            color: var(--orange);
        }

        .kpi-card.green .kpi-value {
            color: var(--green);
        }

        .kpi-card.red .kpi-value {
            color: var(--red);
        }

        .kpi-card.pink .kpi-value {
            color: var(--pink);
        }

        .kpi-card.yellow .kpi-value {
            color: var(--yellow2);
        }

        .kpi-card.purple .kpi-value {
            color: var(--purple);
        }

        .kpi-card.teal .kpi-value {
            color: var(--teal);
        }

        .kpi-sub {
            font-size: 12px;
            color: var(--gray-400);
            margin-top: 6px;
        }

        /* SECTION */
        .section {
            background: var(--white);
            border-radius: var(--radius);
            border: 1.5px solid var(--gray-200);
            margin-top: 20px;
            overflow: hidden;
        }

        .section-header {
            padding: 16px 22px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-body {
            padding: 22px;
        }

        .section-badge {
            font-size: 11px;
            padding: 3px 9px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-blue {
            background: var(--blue-lt);
            color: var(--blue);
        }

        .badge-orange {
            background: var(--orange-lt);
            color: var(--orange);
        }

        .badge-green {
            background: var(--green-lt);
            color: var(--green);
        }

        .badge-red {
            background: var(--red-lt);
            color: var(--red);
        }

        /* CHART */
        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .chart-wrap {
            position: relative;
            height: 220px;
        }

        .chart-wrap-tall {
            position: relative;
            height: 260px;
        }

        .chart-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .meta-box {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            background: var(--gray-50);
            border: 1px solid var(--gray-100);
        }

        .meta-box label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-400);
            display: block;
            margin-bottom: 4px;
        }

        .meta-box .meta-val {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--navy);
        }

        /* RINGKASAN EKSEKUTIF */
        .exec-summary {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: 18px 22px;
            font-size: 13.5px;
            line-height: 1.8;
            color: var(--gray-700);
        }

        .exec-summary p {
            margin-bottom: 10px;
        }

        .exec-summary p:last-child {
            margin-bottom: 0;
        }

        .hl-red {
            color: var(--red);
            font-weight: 700;
        }

        .hl-green {
            color: var(--green);
            font-weight: 700;
        }

        .hl-blue {
            color: var(--blue);
            font-weight: 700;
        }

        .hl-orange {
            color: var(--orange);
            font-weight: 700;
        }

        .hl-yellow {
            color: var(--yellow);
            font-weight: 700;
        }

        .rekomendasi-box {
            margin-top: 14px;
            padding: 12px 16px;
            background: var(--yellow-lt);
            border: 1px solid #fde68a;
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--yellow);
        }

        .rekomendasi-box strong {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .info-statistik {
            margin-top: 12px;
            padding: 10px 16px;
            background: var(--gray-100);
            border-radius: var(--radius-sm);
            font-size: 12px;
            color: var(--gray-400);
            line-height: 1.6;
        }

        /* STAT CARDS IN HEADER */
        .stat-card {
            padding: 10px 18px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
        }

        .stat-card-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-400);
            margin-bottom: 3px;
        }

        .stat-card-val {
            font-size: 22px;
            font-weight: 700;
        }

        /* TABLE */
        .table-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .show-entries {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-600);
        }

        .show-entries select {
            padding: 4px 8px;
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            font-size: 13px;
        }

        .table-search {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-600);
        }

        .table-search input {
            padding: 6px 12px;
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            font-size: 13px;
            outline: none;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .report-table th {
            background: var(--navy);
            color: var(--white);
            padding: 11px 14px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .report-table td {
            padding: 10px 14px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
        }

        .report-table th,
        .report-table td {
            text-align: center;
        }

        .report-table th:nth-child(1),
        .report-table td:nth-child(1),
        .report-table th:nth-child(2),
        .report-table td:nth-child(2) {
            text-align: left;
        }

        .report-table tr:last-child td {
            border-bottom: none;
        }

        .report-table tr:hover td {
            background: var(--gray-50);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-achieve {
            background: var(--green-lt);
            color: var(--green);
        }

        .status-below {
            background: var(--red-lt);
            color: var(--red);
        }

        .status-eval {
            background: var(--yellow-lt);
            color: var(--yellow);
        }

        .table-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 13px;
            color: var(--gray-600);
            flex-wrap: wrap;
            gap: 8px;
        }

        .pagination-btns {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination-btns button {
            padding: 5px 12px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
        }

        .pagination-btns button.active {
            background: var(--navy);
            color: var(--white);
            border-color: var(--navy);
        }

        /* SEKSI BLOCK */
        .seksi-report-block {
            margin-bottom: 32px;
        }

        .seksi-report-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--navy);
            padding: 14px 0 10px 0;
            border-bottom: 2px solid var(--navy2);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seksi-report-title .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--navy2);
            display: inline-block;
        }

        /* ACTION BAR */
        .action-bar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-100);
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 22px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-action img {
            width: 18px;
            height: 18px;
            object-fit: contain;
        }

        .btn-pdf {
            background: var(--red);
            color: var(--white);
        }

        .btn-pdf:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }

        .btn-xlsx {
            background: var(--green);
            color: var(--white);
        }

        .btn-xlsx:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        /* LOADING */
        .loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-200);
            border-top-color: var(--navy);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal.open {
            display: flex;
        }

        .modal-box {
            background: var(--white);
            padding: 28px 32px;
            border-radius: var(--radius);
            text-align: center;
            width: 320px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .modal-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .modal-box p {
            font-size: 13px;
            color: var(--gray-400);
            margin-bottom: 20px;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-cancel {
            background: var(--gray-100);
            color: var(--gray-700);
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-confirm {
            background: var(--red);
            color: var(--white);
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-confirm:hover {
            background: #b91c1c;
        }

        /* EMPTY / ERROR */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-400);
        }

        .empty-state .empty-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--gray-600);
        }

        .empty-state p {
            font-size: 13px;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 14px 18px;
            color: #b91c1c;
            font-size: 13px;
            margin-top: 12px;
        }

        .error-box strong {
            display: block;
            margin-bottom: 4px;
        }

        /* ================= RESPONSIVE 1440px ================= */
        @media screen and (max-width: 1440px) {
            .navbar {
                padding: 12px 20px;
            }

            .logo img {
                width: 160px;
            }

            .system-title {
                font-size: 18px;
            }

            .topbar-clock {
                padding: 8px 12px;
            }

            .clock-time {
                font-size: 16px;
            }

            .content {
                margin-top: 90px;
                padding: 16px 20px;
            }
        }

        /* ================= RESPONSIVE 1024px ================= */
        @media screen and (max-width: 1024px) {
            .navbar {
                position: relative !important;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                padding: 12px 16px;
            }

            .nav-left,
            .nav-right {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
            }

            .system-title {
                text-align: center;
                width: 100%;
                font-size: 16px;
            }

            .content {
                margin-top: 16px !important;
                padding: 14px;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .chart-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .chart-wrap {
                height: 200px;
            }

            .chart-wrap-tall {
                height: 240px;
            }

            .action-bar {
                justify-content: center;
            }

            /* Navbar controls */
            .multi-seksi-wrapper {
                flex: 1;
                min-width: 140px;
            }

            .multi-seksi-trigger {
                width: 100%;
                font-size: 13px;
            }

            .nav-pill {
                flex: 1;
                min-width: 130px;
            }

            .nav-pill select {
                width: 100%;
                font-size: 13px;
            }

            .btn-filter {
                font-size: 13px;
                padding: 10px 16px;
            }

            .topbar-clock {
                flex: 0 0 auto;
            }
        }

        /* ================= RESPONSIVE 768px ================= */
        @media screen and (max-width: 768px) {
            .navbar {
                position: relative !important;
                flex-direction: column;
                align-items: stretch;
                padding: 12px;
                gap: 10px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            /* Logo center */
            .logo {
                width: 100%;
                text-align: center;
            }

            .logo img {
                width: 130px;
            }

            .system-title {
                display: none;
            }

            .nav-left,
            .nav-right {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .multi-seksi-wrapper {
                flex: 1 1 calc(50% - 4px);
                min-width: 0;
            }

            .multi-seksi-trigger {
                width: 100%;
                font-size: 12px;
                padding: 9px 32px 9px 10px;
                min-width: 0;
            }

            .multi-seksi-dropdown {
                min-width: 100%;
                left: 0;
                right: 0;
            }

            .nav-pill {
                flex: 1 1 calc(50% - 4px);
                min-width: 0;
            }

            .nav-pill select {
                width: 100%;
                font-size: 12px;
                padding: 9px 32px 9px 10px;
            }

            .nav-daterange {
                flex: 1 1 100%;
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .nav-daterange input[type="date"] {
                flex: 1;
                font-size: 12px;
                padding: 9px 8px;
                min-width: 0;
            }

            .nav-daterange span {
                flex-shrink: 0;
            }

            .btn-filter {
                flex: 1 1 calc(50% - 4px);
                text-align: center;
                font-size: 12px;
                padding: 9px 10px;
                min-width: 0;
            }

            .topbar-clock {
                flex: 1 1 calc(50% - 4px);
                justify-content: center;
                min-width: 0;
                padding: 8px 10px;
            }

            .clock-time {
                font-size: 14px;
            }

            .clock-date {
                font-size: 10px;
            }

            /* Content */
            .content {
                margin-top: 12px !important;
                padding: 8px;
            }

            .container {
                padding: 12px;
                border-radius: 8px;
            }

            /* KPI */
            .kpi-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 14px;
            }

            .kpi-card {
                padding: 12px 12px;
            }

            .kpi-card::before {
                height: 5px;
            }

            .kpi-value {
                font-size: 20px;
                letter-spacing: -0.5px;
            }

            .kpi-label {
                font-size: 9px;
            }

            .kpi-sub {
                font-size: 10px;
                margin-top: 4px;
            }

            /* Page header */
            .page-header {
                margin-bottom: 14px;
            }

            .page-title {
                font-size: 20px;
            }

            .page-subtitle {
                font-size: 11px;
            }

            /* Seksi block */
            .seksi-report-block {
                margin-bottom: 20px;
            }

            .seksi-report-title {
                font-size: 13px;
                padding: 10px 0 8px;
                margin-bottom: 14px;
            }

            /* Chart */
            .chart-grid {
                grid-template-columns: 1fr;
                gap: 10px;
                margin-top: 12px;
            }

            .chart-wrap {
                height: 170px;
            }

            .chart-wrap-tall {
                height: 200px;
            }

            .chart-meta {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 10px;
            }

            .meta-box {
                padding: 8px 10px;
            }

            .meta-box label {
                font-size: 9px;
            }

            .meta-box .meta-val {
                font-size: 16px;
            }

            /* Section */
            .section {
                border-radius: 8px;
                margin-top: 12px;
            }

            .section-header {
                padding: 10px 14px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .section-title {
                font-size: 12px;
                gap: 6px;
            }

            .section-body {
                padding: 10px 12px;
            }

            .section-badge {
                font-size: 10px;
                padding: 2px 7px;
            }

            .section-header>div:last-child {
                width: 100%;
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .stat-card {
                flex: 1;
                min-width: 0;
                padding: 8px 10px;
            }

            .stat-card-label {
                font-size: 9px;
            }

            .stat-card-val {
                font-size: 16px;
            }

            /* Info statistik */
            .info-statistik {
                font-size: 10px;
                padding: 8px 12px;
                margin-top: 8px;
            }

            /* Exec summary */
            .exec-summary {
                font-size: 11px;
                padding: 12px 14px;
                line-height: 1.7;
            }

            .rekomendasi-box {
                font-size: 11px;
                padding: 10px 12px;
                margin-top: 10px;
            }

            .rekomendasi-box strong {
                font-size: 11px;
            }

            /* Table */
            .table-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 8px;
            }

            .show-entries {
                font-size: 12px;
            }

            .table-search {
                width: 100%;
                font-size: 12px;
            }

            .table-search input {
                width: 100%;
                font-size: 12px;
            }

            /* Tabel scroll horizontal */
            .section-body>div[style*="overflow-x"] {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .report-table {
                min-width: 560px;
            }

            .report-table th {
                padding: 8px 7px;
                font-size: 10px;
            }

            .report-table td {
                padding: 7px 7px;
                font-size: 10px;
            }

            .status-badge {
                font-size: 9px;
                padding: 2px 5px;
                white-space: nowrap;
            }

            /* Pagination */
            .table-pagination {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                margin-top: 8px;
                font-size: 11px;
            }

            .pagination-btns button {
                padding: 4px 9px;
                font-size: 11px;
            }

            /* Action bar */
            .action-bar {
                flex-direction: column;
                gap: 8px;
                padding-top: 14px;
                margin-top: 10px;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
                padding: 11px 16px;
                font-size: 13px;
            }
        }

        /* ================= RESPONSIVE 480px ================= */
        @media screen and (max-width: 480px) {
            .navbar {
                padding: 10px;
            }

            .logo img {
                width: 110px;
            }

            .multi-seksi-wrapper,
            .nav-pill,
            .btn-filter,
            .topbar-clock {
                flex: 1 1 calc(50% - 4px);
                min-width: 0;
            }

            .nav-daterange {
                flex: 1 1 100%;
            }

            .topbar-clock {
                flex: 1 1 100%;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }

            .kpi-value {
                font-size: 17px;
            }

            .kpi-card {
                padding: 10px 8px;
            }

            .kpi-label {
                font-size: 8px;
                letter-spacing: 0.3px;
            }

            .page-title {
                font-size: 17px;
            }

            /* ================= CHART SCROLLABLE ================= */
            .chart-wrap,
            .chart-wrap-tall {
                overflow-x: auto !important;
                overflow-y: visible !important;
                -webkit-overflow-scrolling: touch;
                cursor: grab;
            }

            .chart-wrap:active,
            .chart-wrap-tall:active {
                cursor: grabbing;
            }

            /* Inner chart container */
            .chart-wrap>div,
            .chart-wrap-tall>div {
                min-width: 600px !important;
                height: 100% !important;
            }
        }

        .target-inline-input::-webkit-outer-spin-button,
        .target-inline-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .target-inline-input {
            -moz-appearance: textfield;
            appearance: textfield;
            display: block;
            margin: 0 auto;
            text-align: center;
        }

        .target-inline-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>

<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Memuat laporan...</div>
    </div>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="nav-left">
            <div class="logo"><img src="/assets/img/logo_perusahaan.png" alt="Logo"></div>
            <div class="system-title">Production Report</div>

            <div class="nav-pill">
                <select name="granularitas" id="granularitasSelect">
                    <option value="" disabled selected hidden>GRANULARITAS</option>
                    <option value="harian" <?= $granularitas == 'harian'  ? 'selected' : '' ?>>HARIAN</option>
                    <option value="bulanan" <?= $granularitas == 'bulanan' ? 'selected' : '' ?>>BULANAN</option>
                    <option value="tahunan" <?= $granularitas == 'tahunan' ? 'selected' : '' ?>>TAHUNAN</option>
                </select>
            </div>

            <div class="multi-seksi-wrapper" id="seksiWrapper">
                <button type="button" class="multi-seksi-trigger" id="seksiTrigger" onclick="toggleSeksiDropdown()">
                    <?php
                    $jumlahDipilih = count($selected_seksi);
                    if ($jumlahDipilih === 0) {
                        echo 'PILIH AREA';
                    } elseif ($jumlahDipilih === 1) {
                        foreach ($seksiList as $s) {
                            if ($s['id_seksi'] == $selected_seksi[0]) {
                                echo htmlspecialchars($s['nama_seksi']);
                                break;
                            }
                        }
                    } else {
                        echo 'Seksi';
                    }
                    ?>
                    <?php if ($jumlahDipilih > 1): ?>
                        <span class="seksi-badge"><?= $jumlahDipilih ?></span>
                    <?php endif; ?>
                </button>
                <div class="multi-seksi-dropdown" id="seksiDropdown">
                    <?php foreach ($seksiList as $s): ?>
                        <?php $isChecked = in_array($s['id_seksi'], $selected_seksi); ?>
                        <label class="multi-seksi-option <?= $isChecked ? 'selected' : '' ?>" for="seksi_<?= $s['id_seksi'] ?>">
                            <input type="checkbox" id="seksi_<?= $s['id_seksi'] ?>" value="<?= $s['id_seksi'] ?>"
                                class="seksi-checkbox" <?= $isChecked ? 'checked' : '' ?> onchange="updateSeksiLabel()">
                            <?= htmlspecialchars($s['nama_seksi']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="nav-right">
            <div class="nav-daterange">
                <input type="date" id="dateFrom" value="<?= htmlspecialchars($date_from) ?>" max="<?= date('Y-m-d') ?>">
                <span>–</span>
                <input type="date" id="dateTo" value="<?= htmlspecialchars($date_to)   ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <button class="btn-filter" onclick="applyFilter()">Filter</button>
            <div class="topbar-clock">
                <div class="clock-right">
                    <div class="clock-date" id="clockDate">--</div>
                    <div class="clock-time" id="clockTime">--:--:--</div>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" id="mainFilterForm" style="display:none;">
        <input type="hidden" name="granularitas" id="f_granularitas">
        <input type="hidden" name="date_from" id="f_date_from">
        <input type="hidden" name="date_to" id="f_date_to">
    </form>

    <!-- CONTENT -->
    <div class="content">
        <?php $kpiColors = [
            ['actual' => 'blue',   'achievement' => 'orange'],
            ['actual' => 'pink',   'achievement' => 'red'],
            ['actual' => 'green',  'achievement' => 'yellow'],
            ['actual' => 'purple', 'achievement' => 'teal'],
        ]; ?>

        <div class="kpi-grid">
            <?php foreach ($selected_seksi as $index => $seksiId): ?>
                <?php
                $seksiNamaKpi = '';
                foreach ($seksiList as $s) {
                    if ($s['id_seksi'] == $seksiId) {
                        $seksiNamaKpi = $s['nama_seksi'];
                        break;
                    }
                }
                $colorSet    = $kpiColors[$index % count($kpiColors)];
                $kpi         = $kpiBySeksi[$seksiId] ?? [];
                $totalActual = isset($kpi['total_actual']) ? number_format($kpi['total_actual'], 0, ',', '.') : '–';
                $achievement = isset($kpi['achievement'])  ? $kpi['achievement'] . '%' : '–';
                ?>
                <div class="kpi-card <?= $colorSet['actual'] ?>">
                    <div class="kpi-label"><?= strtoupper($seksiNamaKpi) ?> AKTUAL</div>
                    <div class="kpi-value"><?= $totalActual ?></div>
                    <div class="kpi-sub">Total Aktual Periode</div>
                </div>
                <div class="kpi-card <?= $colorSet['achievement'] ?>">
                    <div class="kpi-label"><?= strtoupper($seksiNamaKpi) ?> PENCAPAIAN</div>
                    <div class="kpi-value"><?= $achievement ?></div>
                    <div class="kpi-sub">Achievement (Actual / Target)</div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="container">
            <div class="page-header">
                <div class="page-title">LAPORAN EVALUASI PRODUKSI</div>
                <div class="page-subtitle">
                    <?php if (empty($selected_seksi)): ?>
                        Pilih area untuk melihat laporan
                    <?php else: ?>
                        <?php
                        $bulan_id = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ];
                        $fmt = fn($tgl) => date('d', strtotime($tgl)) . ' ' . $bulan_id[(int)date('n', strtotime($tgl))] . ' ' . date('Y', strtotime($tgl));
                        ?>
                        Periode Analisis: <?= $fmt($date_from) ?> s/d <?= $fmt($date_to) ?> &nbsp;|&nbsp; Granularitas: <strong><?= ucfirst($granularitas) ?></strong>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($selected_seksi)): ?>
                <div class="section">
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <h3>Belum ada area dipilih</h3>
                        <p>Pilih area produksi dari dropdown di navbar untuk melihat laporan.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($selected_seksi as $seksiId):
                    $seksiNama = '';
                    foreach ($seksiList as $s) {
                        if ($s['id_seksi'] == $seksiId) {
                            $seksiNama = $s['nama_seksi'];
                            break;
                        }
                    }
                    if (!$seksiNama) continue;

                    $reportData = $reportBySeksi[$seksiId] ?? [];
                    $kpi        = $kpiBySeksi[$seksiId]    ?? [];
                    $hasError   = !empty($kpi['error']);

                    // Chart aggregate per periode
                    $chartData = [];
                    foreach ($reportData as $row) {
                        $p = $row['periode'];
                        if (!isset($chartData[$p])) $chartData[$p] = ['iot' => 0, 'actual' => 0, 'ng' => 0];
                        $chartData[$p]['iot']    += $row['iot'];
                        $chartData[$p]['actual'] += $row['actual'];
                        $chartData[$p]['ng']     += $row['ng'];
                    }
                    $chartLabels = array_keys($chartData);
                    $chartIot    = array_column(array_values($chartData), 'iot');
                    $chartActual = array_column(array_values($chartData), 'actual');
                    $chartNg     = array_column(array_values($chartData), 'ng');

                    // Achievement per periode
                    $achValues = [];
                    foreach ($chartData as $d) {
                        $achValues[] = $d['iot'] > 0 ? round(($d['actual'] / $d['iot']) * 100, 1) : 0;
                    }
                    $n      = count($achValues);
                    $avgAch = $n > 0 ? round(array_sum($achValues) / $n, 1) : 0;

                    // Regresi linear
                    $regresiTren = 'Stabil';
                    $regresiLine = [];
                    if ($n >= 2) {
                        $sumX = $sumY = $sumXY = $sumX2 = 0;
                        for ($i = 0; $i < $n; $i++) {
                            $sumX += $i;
                            $sumY += $achValues[$i];
                            $sumXY += $i * $achValues[$i];
                            $sumX2 += $i * $i;
                        }
                        $denom = ($n * $sumX2 - $sumX * $sumX);
                        $b = $denom != 0 ? ($n * $sumXY - $sumX * $sumY) / $denom : 0;
                        $a = ($sumY - $b * $sumX) / $n;
                        for ($i = 0; $i < $n; $i++) $regresiLine[] = round($a + $b * $i, 2);
                        if ($b > 0.3)      $regresiTren = 'Meningkat';
                        elseif ($b < -0.3) $regresiTren = 'Menurun';
                        else               $regresiTren = 'Stabil';
                    } else {
                        $regresiLine = $achValues;
                    }

                    // Warna tren
                    $warnaTren = $regresiTren === 'Meningkat' ? 'var(--green)' : ($regresiTren === 'Menurun' ? 'var(--red)' : 'var(--blue)');
                    $warnaAch  = $avgAch >= 100 ? 'var(--green)' : ($avgAch >= 90 ? 'var(--yellow)' : 'var(--red)');

                    // KPI ringkasan
                    $totalNgRing     = $kpi['total_ng']     ?? 0;
                    $totalActualRing = $kpi['total_actual'] ?? 0;
                    $ngRateRing      = $totalActualRing > 0 ? round(($totalNgRing / $totalActualRing) * 100, 2) : 0;

                    // Status & kalimat
                    if ($avgAch >= 100) {
                        $statusAch = 'tercapai';
                        $clsAch = 'hl-green';
                    } elseif ($avgAch >= 90) {
                        $statusAch = 'evaluasi';
                        $clsAch = 'hl-yellow';
                    } else {
                        $statusAch = 'di bawah target';
                        $clsAch = 'hl-red';
                    }

                    if ($regresiTren === 'Meningkat')
                        $kalimatTren = 'menunjukkan tren <span class="hl-green">meningkat</span>, mengindikasikan perbaikan kinerja yang konsisten.';
                    elseif ($regresiTren === 'Menurun')
                        $kalimatTren = 'menunjukkan tren <span class="hl-red">menurun</span>, yang memerlukan tindakan korektif segera.';
                    else
                        $kalimatTren = 'menunjukkan tren <span class="hl-blue">stabil</span> tanpa perubahan signifikan.';

                    if ($ngRateRing > 10)
                        $kalimatNg = 'NG rate sebesar <span class="hl-red">' . $ngRateRing . '%</span> tergolong <strong>tinggi</strong> dan memerlukan investigasi mendalam.';
                    elseif ($ngRateRing > 5)
                        $kalimatNg = 'NG rate sebesar <span class="hl-yellow">' . $ngRateRing . '%</span> tergolong <strong>sedang</strong>, perlu pemantauan lebih ketat.';
                    elseif ($ngRateRing > 0)
                        $kalimatNg = 'NG rate sebesar <span class="hl-green">' . $ngRateRing . '%</span> tergolong <strong>rendah</strong> dan masih dalam batas toleransi.';
                    else
                        $kalimatNg = 'tidak terdapat data NG yang tercatat pada periode ini.';

                    if ($avgAch < 90 && $ngRateRing > 10)
                        $rekomendasi = 'Lakukan investigasi menyeluruh terhadap penyebab rendahnya pencapaian dan tingginya NG. Prioritaskan analisis 5-Why pada mesin dan operator dengan frekuensi masalah tertinggi.';
                    elseif ($avgAch < 90)
                        $rekomendasi = 'Evaluasi kapasitas mesin, jadwal perawatan, dan kecukupan tenaga operator. Pastikan target IOT sudah realistis berdasarkan kondisi aktual lantai produksi.';
                    elseif ($ngRateRing > 10)
                        $rekomendasi = 'Meskipun pencapaian produksi baik, tingkat NG yang tinggi menggerus kualitas output. Tingkatkan inspeksi awal shift dan review parameter proses secara berkala.';
                    elseif ($regresiTren === 'Menurun')
                        $rekomendasi = 'Pencapaian masih dalam batas, namun tren menurun perlu diwaspadai. Lakukan review mingguan dan identifikasi potensi bottleneck sejak dini.';
                    else
                        $rekomendasi = 'Pertahankan performa yang sudah baik. Dokumentasikan best practice yang berjalan dan jadikan acuan untuk area lain.';
                ?>

                    <div class="seksi-report-block">
                        <div class="seksi-report-title">
                            <span class="dot"></span>
                            ANALISIS PRODUKSI – <?= htmlspecialchars(strtoupper($seksiNama)) ?>
                        </div>

                        <?php if ($hasError): ?>
                            <div class="error-box">
                                <strong>⚠ Gagal mengambil data</strong>
                                <?= htmlspecialchars($kpi['error']) ?>
                            </div>
                        <?php elseif (empty($reportData)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">🔍</div>
                                <h3>Tidak ada data</h3>
                                <p>Tidak ditemukan data produksi <?= htmlspecialchars($seksiNama) ?> pada periode yang dipilih.</p>
                            </div>
                        <?php else: ?>

                            <!-- CHART TREND PRODUKSI + NG -->
                            <div class="chart-grid">
                                <div class="section">
                                    <div class="section-header">
                                        <div class="section-title">
                                            📈 Trend Produksi
                                            <span class="section-badge badge-blue"><?= ucfirst($granularitas) ?></span>
                                        </div>
                                    </div>
                                    <div class="section-body">
                                        <div class="chart-meta">
                                            <div class="meta-box">
                                                <label>Rata-rata IOT</label>
                                                <div class="meta-val"><?= count($chartData) > 0 ? number_format(array_sum($chartIot) / count($chartData), 0, ',', '.') : '–' ?></div>
                                            </div>
                                            <div class="meta-box">
                                                <label>Rata-rata Actual</label>
                                                <div class="meta-val"><?= count($chartData) > 0 ? number_format(array_sum($chartActual) / count($chartData), 0, ',', '.') : '–' ?></div>
                                            </div>
                                        </div>
                                        <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                                            <div class="chart-wrap" id="chartProduksi_<?= $seksiId ?>"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="section">
                                    <div class="section-header">
                                        <div class="section-title">
                                            🔴 Trend NG
                                            <span class="section-badge badge-red">Defect Rate</span>
                                        </div>
                                    </div>
                                    <div class="section-body">
                                        <div class="chart-meta">
                                            <div class="meta-box">
                                                <label>Total NG</label>
                                                <div class="meta-val"><?= number_format($kpi['total_ng'] ?? 0, 0, ',', '.') ?></div>
                                            </div>
                                            <div class="meta-box">
                                                <label>NG Rate</label>
                                                <div class="meta-val">
                                                    <?= ($kpi['total_actual'] ?? 0) > 0 ? number_format(($kpi['total_ng'] / $kpi['total_actual']) * 100, 2, '.', '') . '%' : '0%' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                                            <div class="chart-wrap" id="chartNg_<?= $seksiId ?>"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CHART ACHIEVEMENT + REGRESI -->
                            <div class="section" style="margin-top:20px;">
                                <div class="section-header">
                                    <div class="section-title">
                                        📊 Visualisasi Tren Performa
                                        <span class="section-badge badge-blue"><?= ucfirst($granularitas) ?></span>
                                    </div>
                                    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                                        <div class="stat-card">
                                            <div class="stat-card-label">Rata-rata Pencapaian</div>
                                            <div class="stat-card-val" style="color:<?= $warnaAch ?>;"><?= $avgAch ?>%</div>
                                        </div>
                                        <div class="stat-card">
                                            <div class="stat-card-label">Tren Produksi (Analisis Regresi)</div>
                                            <div class="stat-card-val" style="color:<?= $warnaTren ?>;"><?= $regresiTren ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                                        <div class="chart-wrap-tall" id="chartAch_<?= $seksiId ?>"></div>
                                    </div>
                                    <div class="info-statistik">
                                        Garis oranye putus-putus menunjukkan tren jangka panjang melalui &lsquo;Analisis Regresi Linear&rsquo;.
                                        Meskipun performa harian naik-turun, arah garis ini memberikan kesimpulan apakah kinerja secara keseluruhan cenderung membaik atau menurun.
                                    </div>
                                </div>
                            </div>

                            <!-- CHART BREAKDOWN KETERANGAN -->
                            <?php
                            $breakdown = $breakdownBySeksi[$seksiId] ?? ['akar_masalah' => [], 'mesin_breakdown' => []];
                            $hasBreakdown = !empty($breakdown['akar_masalah']) || !empty($breakdown['mesin_breakdown']);
                            ?>
                            <?php if ($hasBreakdown): ?>
                                <div class="section" style="margin-top:20px;">
                                    <div class="section-header">
                                        <div class="section-title">
                                            🔧 Rekap Breakdown – Akar Masalah & Mesin Bermasalah
                                            <span class="section-badge badge-red">Keterangan</span>
                                        </div>
                                    </div>
                                    <div class="section-body">
                                        <div class="chart-grid">
                                            <div class="section">
                                                <div class="section-header">
                                                    <div class="section-title">📋 Akar Masalah</div>
                                                </div>
                                                <div class="section-body">
                                                    <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                                                        <div class="chart-wrap" id="chartAkar_<?= $seksiId ?>" style="height:<?= max(200, count($breakdown['akar_masalah']) * 40) ?>px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="section">
                                                <div class="section-header">
                                                    <div class="section-title">🔩 Mesin Bermasalah</div>
                                                </div>
                                                <div class="section-body">
                                                    <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                                                        <div class="chart-wrap" id="chartMesin_<?= $seksiId ?>" style="height:<?= max(200, count($breakdown['mesin_breakdown']) * 40) ?>px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- RINGKASAN EKSEKUTIF -->
                            <div class="section">
                                <div class="section-header">
                                    <div class="section-title">📝 Ringkasan Eksekutif</div>
                                </div>
                                <div class="section-body">
                                    <div class="exec-summary">
                                        <p>Sepanjang periode ini, efisiensi operasional mencapai angka
                                            <span class="<?= $clsAch ?>"><?= $avgAch ?>%</span>
                                            dengan status <span class="<?= $clsAch ?>"><?= $statusAch ?></span>.
                                            Performa <?= $kalimatTren ?>
                                        </p>
                                        <p>Dari sisi kualitas, <?= $kalimatNg ?></p>
                                        <div class="rekomendasi-box">
                                            <strong>💡 Rekomendasi Tindakan:</strong>
                                            <?= $rekomendasi ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TABEL DETAIL -->
                            <div class="section" style="margin-top:20px;">
                                <div class="section-header">
                                    <div class="section-title">
                                        📋 Detail Laporan – <?= htmlspecialchars($seksiNama) ?>
                                        <span class="section-badge badge-blue" id="badge_<?= $seksiId ?>"><?= count($reportData) ?> data</span>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="table-controls">
                                        <div class="show-entries">
                                            Tampilkan
                                            <select id="entries_<?= $seksiId ?>" onchange="renderTable('<?= $seksiId ?>')">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                            entri
                                        </div>
                                        <div class="table-search">
                                            Cari: <input type="text" id="search_<?= $seksiId ?>" placeholder="Ketik untuk mencari..." oninput="renderTable('<?= $seksiId ?>')">
                                        </div>
                                    </div>
                                    <div style="overflow-x:auto;">
                                        <table class="report-table">
                                            <thead>
                                                <tr>
                                                    <th>Periode</th>
                                                    <th>Mesin</th>
                                                    <th>Shift</th>
                                                    <th>Target</th>
                                                    <th>IOT</th>
                                                    <th>Actual</th>
                                                    <th>NG</th>
                                                    <th>Achievement</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_<?= $seksiId ?>"></tbody>
                                        </table>
                                    </div>
                                    <div class="table-pagination">
                                        <div id="pageInfo_<?= $seksiId ?>">–</div>
                                        <div class="pagination-btns" id="pageBtns_<?= $seksiId ?>"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="action-bar">
                                <button class="btn-action btn-pdf" onclick="exportPdf('<?= $seksiId ?>')">
                                    <img src="/assets/icon/pdf.png" alt="PDF"> Download PDF
                                </button>
                                <button class="btn-action btn-xlsx" onclick="exportExcel('<?= $seksiId ?>')">
                                    <img src="/assets/icon/xlsx.png" alt="Excel"> Download (.xlsx)
                                </button>
                            </div>

                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="logoutModal" class="modal">
        <div class="modal-box">
            <h3>Konfirmasi Logout</h3>
            <p>Yakin ingin keluar dari sistem?</p>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
                <a href="auth/logout.php" class="btn-confirm">Ya, Logout</a>
            </div>
        </div>
    </div>

    <script>
        const reportDataMap = <?= json_encode($reportBySeksi ?? [], JSON_UNESCAPED_UNICODE) ?>;
        const pageMap = {};
        const breakdownMap = <?= json_encode($breakdownBySeksi ?? [], JSON_UNESCAPED_UNICODE) ?>;

        // chartDataMap: per seksi → { labels, iot, actual, ng, ach, regresi }
        const chartDataMap = <?= json_encode(
                                    array_combine(
                                        $selected_seksi,
                                        array_map(function ($seksiId) use ($reportBySeksi) {
                                            $data      = $reportBySeksi[$seksiId] ?? [];
                                            $byPeriode = [];
                                            foreach ($data as $row) {
                                                $p = $row['periode'];
                                                if (!isset($byPeriode[$p])) $byPeriode[$p] = ['iot' => 0, 'actual' => 0, 'ng' => 0];
                                                $byPeriode[$p]['iot']    += $row['iot'];
                                                $byPeriode[$p]['actual'] += $row['actual'];
                                                $byPeriode[$p]['ng']     += $row['ng'];
                                            }
                                            $labels  = array_keys($byPeriode);
                                            $iotArr  = array_column(array_values($byPeriode), 'iot');
                                            $actArr  = array_column(array_values($byPeriode), 'actual');
                                            $ngArr   = array_column(array_values($byPeriode), 'ng');
                                            $n       = count($labels);

                                            // Achievement per periode
                                            $achArr = [];
                                            foreach ($byPeriode as $d) {
                                                $achArr[] = $d['iot'] > 0 ? round(($d['actual'] / $d['iot']) * 100, 1) : 0;
                                            }

                                            // Regresi linear
                                            $regArr = [];
                                            if ($n >= 2) {
                                                $sumX = $sumY = $sumXY = $sumX2 = 0;
                                                for ($i = 0; $i < $n; $i++) {
                                                    $sumX += $i;
                                                    $sumY += $achArr[$i];
                                                    $sumXY += $i * $achArr[$i];
                                                    $sumX2 += $i * $i;
                                                }
                                                $denom = $n * $sumX2 - $sumX * $sumX;
                                                $b = $denom != 0 ? ($n * $sumXY - $sumX * $sumY) / $denom : 0;
                                                $a = ($sumY - $b * $sumX) / $n;
                                                for ($i = 0; $i < $n; $i++) $regArr[] = round($a + $b * $i, 2);
                                            } else {
                                                $regArr = $achArr;
                                            }

                                            return ['labels' => $labels, 'iot' => $iotArr, 'actual' => $actArr, 'ng' => $ngArr, 'ach' => $achArr, 'regresi' => $regArr];
                                        }, $selected_seksi)
                                    ) ?: new stdClass(),
                                    JSON_UNESCAPED_UNICODE
                                ) ?>;

        <?php foreach ($selected_seksi as $seksiId): ?>
            pageMap['<?= $seksiId ?>'] = 1;
        <?php endforeach; ?>

        // CLOCK
        function updateClock() {
            const now = new Date();
            document.getElementById('clockDate').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('clockTime').textContent = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // MODAL
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('open');
        }

        // MULTI SEKSI
        function toggleSeksiDropdown() {
            document.getElementById('seksiDropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const w = document.getElementById('seksiWrapper');
            if (w && !w.contains(e.target)) document.getElementById('seksiDropdown').classList.remove('open');
        });

        function updateSeksiLabel() {
            const checked = document.querySelectorAll('.seksi-checkbox:checked');
            const trigger = document.getElementById('seksiTrigger');
            document.querySelectorAll('.seksi-checkbox').forEach(cb => cb.closest('.multi-seksi-option').classList.toggle('selected', cb.checked));
            if (checked.length === 0) trigger.innerHTML = 'PILIH AREA';
            else if (checked.length === 1) trigger.innerHTML = checked[0].closest('label').textContent.trim();
            else trigger.innerHTML = `Area <span class="seksi-badge">${checked.length}</span>`;
        }

        // APPLY FILTER
        function applyFilter() {
            const form = document.getElementById('mainFilterForm');
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const gran = document.getElementById('granularitasSelect').value;
            if (!gran) {
                alert('Pilih granularitas terlebih dahulu!');
                return;
            }
            if (!dateFrom || !dateTo) {
                alert('Tanggal harus diisi!');
                return;
            }
            if (dateFrom > dateTo) {
                alert('Tanggal mulai tidak boleh lebih dari tanggal akhir!');
                return;
            }
            document.getElementById('f_granularitas').value = gran;
            document.getElementById('f_date_from').value = dateFrom;
            document.getElementById('f_date_to').value = dateTo;
            form.querySelectorAll('input[name="seksi[]"]').forEach(el => el.remove());
            document.querySelectorAll('.seksi-checkbox:checked').forEach(cb => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'seksi[]';
                inp.value = cb.value;
                form.appendChild(inp);
            });
            document.getElementById('seksiDropdown').classList.remove('open');
            document.getElementById('loadingOverlay').classList.add('active');
            form.submit();
        }

        // TABLE RENDER
        function renderTable(seksiId) {
            const data = reportDataMap[seksiId] || [];
            const search = (document.getElementById('search_' + seksiId)?.value || '').toLowerCase();
            const perPage = parseInt(document.getElementById('entries_' + seksiId)?.value || 10);
            const tbody = document.getElementById('tbody_' + seksiId);
            if (!tbody) return;

            const filtered = data.filter(row => Object.values(row).some(v => String(v).toLowerCase().includes(search)));
            const totalPages = Math.ceil(filtered.length / perPage) || 1;
            if ((pageMap[seksiId] || 1) > totalPages) pageMap[seksiId] = 1;
            const start = ((pageMap[seksiId] || 1) - 1) * perPage;
            const slice = filtered.slice(start, start + perPage);

            const badge = document.getElementById('badge_' + seksiId);
            const pageInfo = document.getElementById('pageInfo_' + seksiId);
            if (badge) badge.textContent = filtered.length + ' data';
            if (pageInfo) pageInfo.textContent = `Menampilkan ${filtered.length ? start+1 : 0}–${Math.min(start+perPage, filtered.length)} dari ${filtered.length} entri`;

            tbody.innerHTML = !slice.length ?
                `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-400);">Tidak ada data untuk ditampilkan</td></tr>` :
                slice.map(row => {
                    const target = row.target ?? 0;
                    const ach = target > 0 ? ((row.actual / target) * 100).toFixed(1) : '0.0';
                    const achNum = parseFloat(ach);
                    const cls = achNum >= 100 ? 'status-achieve' : achNum >= 90 ? 'status-eval' : 'status-below';
                    const txt = achNum >= 100 ? '✔ Tercapai' : achNum >= 90 ? '⚠ Evaluasi' : '✘ Di Bawah Target';

                    // Key unik per periode+mesin+shift untuk identifikasi saat save
                    const rowKey = `${row.periode}|${row.mesin}|${row.shift}`;

                    return `<tr>
                        <td>${row.periode??'-'}</td>
                        <td>${row.mesin??'-'}</td>
                        <td>Shift ${row.shift??'-'}</td>
                        <td style="text-align:center;">
                            <input type="number"
                                class="target-inline-input"
                                value="${target > 0 ? target : ''}"
                                placeholder="–"
                                min="0"
                                data-row-id="${row.row_id}"
                                data-periode="${row.periode??''}"
                                data-mesin="${row.mesin??''}"
                                data-shift="${row.shift??''}"
                                data-seksi-id="${seksiId}"
                                style="width:80px;padding:3px 6px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;">
                        </td>
                        <td>${(row.iot??0).toLocaleString('id-ID')}</td>
                        <td>${(row.actual??0).toLocaleString('id-ID')}</td>
                        <td>${(row.ng??0).toLocaleString('id-ID')}</td>
                        <td class="ach-cell">${ach}%</td>
                        <td><span class="status-badge ${cls}">${txt}</span></td>
                    </tr>`;
                }).join('');

            // Pagination
            const btnsEl = document.getElementById('pageBtns_' + seksiId);
            if (btnsEl) {
                btnsEl.innerHTML = '';
                const cur = pageMap[seksiId] || 1;
                if (totalPages > 1) {
                    const prev = document.createElement('button');
                    prev.textContent = '‹';
                    prev.disabled = cur <= 1;
                    prev.onclick = () => {
                        pageMap[seksiId]--;
                        renderTable(seksiId);
                    };
                    btnsEl.appendChild(prev);
                }
                const startP = Math.max(1, cur - 3),
                    endP = Math.min(totalPages, startP + 6);
                for (let i = startP; i <= endP; i++) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    if (i === cur) btn.classList.add('active');
                    btn.onclick = () => {
                        pageMap[seksiId] = i;
                        renderTable(seksiId);
                    };
                    btnsEl.appendChild(btn);
                }
                if (totalPages > 1) {
                    const next = document.createElement('button');
                    next.textContent = '›';
                    next.disabled = cur >= totalPages;
                    next.onclick = () => {
                        pageMap[seksiId]++;
                        renderTable(seksiId);
                    };
                    btnsEl.appendChild(next);
                }
            }
        }

        // AUTO SAVE TARGET INLINE
        let targetSaveDebounce = {};

        document.addEventListener('change', async function(e) {
            const input = e.target;
            if (!input.classList.contains('target-inline-input')) return;

            const rowId = input.dataset.rowId;
            const periode = input.dataset.periode;
            const mesin = input.dataset.mesin;
            const shift = input.dataset.shift;
            const seksiId = input.dataset.seksiId;
            const newTarget = parseInt(input.value) || 0;

            const key = `row_${rowId}`;
            clearTimeout(targetSaveDebounce[key]);
            targetSaveDebounce[key] = setTimeout(async () => {
                try {
                    const fd = new FormData();
                    fd.append('action', 'save_target');
                    fd.append('row_id', rowId);
                    fd.append('target', newTarget);

                    const res = await fetch('view/addon/controller/reportController.php', {
                        method: 'POST',
                        body: fd
                    });

                    const text = await res.text();
                    let json;
                    try {
                        json = JSON.parse(text);
                    } catch (parseErr) {
                        console.error('Non-JSON response:', text);
                        input.style.borderColor = '#dc2626';
                        return;
                    }

                    if (json.status === 'success') {
                        const data = reportDataMap[seksiId] || [];
                        data.forEach(row => {
                            if (String(row.row_id) === String(rowId)) {
                                row.target = newTarget;
                            }
                        });
                        renderTable(seksiId);

                        console.log(`target telah tersimpan: ${newTarget}`);

                        input.style.borderColor = '#16a34a';
                        setTimeout(() => input.style.borderColor = '#d1d5db', 1500);
                    } else {
                        input.style.borderColor = '#dc2626';
                        console.error('Gagal simpan target:', json.message);
                    }
                } catch (err) {
                    console.error('Save target error:', err);
                    input.style.borderColor = '#dc2626';
                }
            }, 800);
        });

        // amCharts 4 THEME
        am4core.useTheme(am4themes_animated);

        const _charts = {};

        function disposeChart(id) {
            if (_charts[id]) {
                _charts[id].dispose();
                delete _charts[id];
            }
        }

        function wrapChartScrollable(el, dataLength) {
            // Hitung lebar minimum berdasarkan jumlah data
            const minW = Math.max(600, dataLength * 50);
            el.style.minWidth = minW + 'px';
        }

        // CHART PRODUKSI (Bar Actual + Line IOT)
        function buildChartProduksi(seksiId) {
            const el = document.getElementById('chartProduksi_' + seksiId);
            if (!el) return;
            disposeChart('prod_' + seksiId);
            const cd = chartDataMap[seksiId];
            if (!cd || !cd.labels.length) return;

            wrapChartScrollable(el, cd.labels.length)

            const chart = am4core.create(el, am4charts.XYChart);
            chart.paddingRight = 20;
            chart.language.locale['_thousandSeparator'] = '.';
            chart.language.locale['_decimalSeparator'] = ',';

            // Data
            chart.data = cd.labels.map((l, i) => ({
                periode: l,
                iot: cd.iot[i],
                actual: cd.actual[i]
            }));

            // Axes
            const catAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            catAxis.dataFields.category = 'periode';
            catAxis.renderer.minGridDistance = 30;
            catAxis.renderer.labels.template.fontSize = 11;
            catAxis.renderer.labels.template.rotation = cd.labels.length > 10 ? -45 : 0;
            catAxis.renderer.labels.template.horizontalCenter = cd.labels.length > 10 ? 'right' : 'middle';

            const valAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valAxis.renderer.labels.template.fontSize = 11;
            valAxis.min = 0;

            // Series Actual (Bar)
            const barSeries = chart.series.push(new am4charts.ColumnSeries());
            barSeries.dataFields.valueY = 'actual';
            barSeries.dataFields.categoryX = 'periode';
            barSeries.name = 'Actual';
            barSeries.fill = am4core.color('#16a34a');
            barSeries.stroke = am4core.color('#16a34a');
            barSeries.fillOpacity = 0.8;
            barSeries.columns.template.tooltipText = '{categoryX}\nActual: [bold]{valueY.value.formatNumber("#,###")}[/]';
            barSeries.columns.template.cornerRadiusTopLeft = 3;
            barSeries.columns.template.cornerRadiusTopRight = 3;
            barSeries.columns.template.width = am4core.percent(60);

            // Series IOT (Line)
            const lineSeries = chart.series.push(new am4charts.LineSeries());
            lineSeries.dataFields.valueY = 'iot';
            lineSeries.dataFields.categoryX = 'periode';
            lineSeries.name = 'IOT';
            lineSeries.stroke = am4core.color('#2563eb');
            lineSeries.strokeWidth = 2.5;
            lineSeries.fill = am4core.color('#2563eb');
            lineSeries.fillOpacity = 0.06;
            lineSeries.tensionX = 0.8;
            lineSeries.tooltipText = 'IOT: [bold]{valueY.value.formatNumber("#,###")}[/]';
            const bullet = lineSeries.bullets.push(new am4charts.CircleBullet());
            bullet.circle.radius = 4;
            bullet.circle.fill = am4core.color('#2563eb');
            bullet.circle.stroke = am4core.color('#ffffff');
            bullet.circle.strokeWidth = 2;

            // Legend & cursor
            chart.legend = new am4charts.Legend();
            chart.legend.labels.template.fontSize = 11;
            chart.cursor = new am4charts.XYCursor();
            chart.cursor.lineX.stroke = am4core.color('#2563eb');

            _charts['prod_' + seksiId] = chart;
        }

        // CHART NG (Bar NG + Line NG Rate %) 
        function buildChartNg(seksiId) {
            const el = document.getElementById('chartNg_' + seksiId);
            if (!el) return;
            disposeChart('ng_' + seksiId);
            const cd = chartDataMap[seksiId];
            if (!cd || !cd.labels.length) return;

            wrapChartScrollable(el, cd.labels.length);

            const chart = am4core.create(el, am4charts.XYChart);
            chart.paddingRight = 40;

            chart.data = cd.labels.map((l, i) => ({
                periode: l,
                ng: cd.ng[i],
                ngRate: cd.actual[i] > 0 ? parseFloat(((cd.ng[i] / cd.actual[i]) * 100).toFixed(2)) : 0
            }));

            // X Axis
            const catAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            catAxis.dataFields.category = 'periode';
            catAxis.renderer.minGridDistance = 30;
            catAxis.renderer.labels.template.fontSize = 11;
            catAxis.renderer.labels.template.rotation = cd.labels.length > 10 ? -45 : 0;
            catAxis.renderer.labels.template.horizontalCenter = cd.labels.length > 10 ? 'right' : 'middle';

            // Y Axis kiri (jumlah NG)
            const valAxisNg = chart.yAxes.push(new am4charts.ValueAxis());
            valAxisNg.renderer.labels.template.fontSize = 11;
            valAxisNg.min = 0;
            valAxisNg.title.text = 'Jumlah NG';
            valAxisNg.title.fontSize = 11;

            // Y Axis kanan (NG Rate %)
            const valAxisRate = chart.yAxes.push(new am4charts.ValueAxis());
            valAxisRate.renderer.opposite = true;
            valAxisRate.renderer.labels.template.fontSize = 11;
            valAxisRate.renderer.labels.template.adapter.add('text', (t, target) => target.dataItem ? target.dataItem.value + '%' : t);
            valAxisRate.renderer.grid.template.disabled = true;
            valAxisRate.min = 0;

            // Series NG Bar
            const barSeries = chart.series.push(new am4charts.ColumnSeries());
            barSeries.dataFields.valueY = 'ng';
            barSeries.dataFields.categoryX = 'periode';
            barSeries.yAxis = valAxisNg;
            barSeries.name = 'NG';
            barSeries.fill = am4core.color('#dc2626');
            barSeries.stroke = am4core.color('#dc2626');
            barSeries.fillOpacity = 0.75;
            barSeries.columns.template.tooltipText = '{categoryX}\nNG: [bold]{valueY}[/]';
            barSeries.columns.template.cornerRadiusTopLeft = 3;
            barSeries.columns.template.cornerRadiusTopRight = 3;
            barSeries.columns.template.width = am4core.percent(60);

            // Series NG Rate Line
            const lineSeries = chart.series.push(new am4charts.LineSeries());
            lineSeries.dataFields.valueY = 'ngRate';
            lineSeries.dataFields.categoryX = 'periode';
            lineSeries.yAxis = valAxisRate;
            lineSeries.name = 'NG Rate (%)';
            lineSeries.stroke = am4core.color('#ea580c');
            lineSeries.strokeWidth = 2.5;
            lineSeries.fill = am4core.color('#ea580c');
            lineSeries.fillOpacity = 0.07;
            lineSeries.tensionX = 0.8;
            lineSeries.tooltipText = 'NG Rate: [bold]{valueY}%[/]';
            const bullet2 = lineSeries.bullets.push(new am4charts.CircleBullet());
            bullet2.circle.radius = 4;
            bullet2.circle.fill = am4core.color('#ea580c');
            bullet2.circle.stroke = am4core.color('#ffffff');
            bullet2.circle.strokeWidth = 2;

            chart.legend = new am4charts.Legend();
            chart.legend.labels.template.fontSize = 11;
            chart.cursor = new am4charts.XYCursor();

            _charts['ng_' + seksiId] = chart;
        }

        // CHART ACHIEVEMENT + REGRESI
        function buildChartAch(seksiId) {
            const el = document.getElementById('chartAch_' + seksiId);
            if (!el) return;
            disposeChart('ach_' + seksiId);
            const cd = chartDataMap[seksiId];
            if (!cd || !cd.labels.length) return;

            wrapChartScrollable(el, cd.labels.length);


            const chart = am4core.create(el, am4charts.XYChart);
            chart.paddingRight = 20;

            chart.data = cd.labels.map((l, i) => ({
                periode: l,
                ach: cd.ach[i],
                regresi: cd.regresi[i]
            }));

            // X Axis
            const catAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            catAxis.dataFields.category = 'periode';
            catAxis.renderer.minGridDistance = 30;
            catAxis.renderer.labels.template.fontSize = 11;
            catAxis.renderer.labels.template.rotation = cd.labels.length > 10 ? -45 : 0;
            catAxis.renderer.labels.template.horizontalCenter = cd.labels.length > 10 ? 'right' : 'middle';

            // Y Axis
            const valAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valAxis.renderer.labels.template.fontSize = 11;
            valAxis.renderer.labels.template.adapter.add('text', (t, target) => target.dataItem ? target.dataItem.value + '%' : t);
            valAxis.min = 0;
            valAxis.max = 110;

            // Garis target 90%
            const range = valAxis.axisRanges.create();
            range.value = 90;
            range.grid.stroke = am4core.color('#d97706');
            range.grid.strokeWidth = 1.5;
            range.grid.strokeDasharray = '4,4';
            range.grid.strokeOpacity = 0.8;
            range.label.text = 'Target 90%';
            range.label.fontSize = 10;
            range.label.fill = am4core.color('#d97706');
            range.label.fontWeight = '700';
            range.label.horizontalCenter = 'left';

            // Series Achievement (Line + area fill)
            const achSeries = chart.series.push(new am4charts.LineSeries());
            achSeries.dataFields.valueY = 'ach';
            achSeries.dataFields.categoryX = 'periode';
            achSeries.name = 'Achievement (%)';
            achSeries.stroke = am4core.color('#7c3aed');
            achSeries.strokeWidth = 2.5;
            achSeries.fill = am4core.color('#7c3aed');
            achSeries.fillOpacity = 0.08;
            achSeries.tensionX = 0.8;
            achSeries.tooltipText = '{categoryX}\nAchievement: [bold]{valueY}%[/]';

            const bullet3 = achSeries.bullets.push(new am4charts.LabelBullet());
            bullet3.label.text = '{valueY}%';
            bullet3.label.fontSize = 10;
            bullet3.label.dy = -14;
            bullet3.label.fontWeight = '700';
            bullet3.label.fill = am4core.color('#7c3aed');

            const circle3 = achSeries.bullets.push(new am4charts.CircleBullet());
            circle3.circle.radius = 4;
            circle3.circle.fill = am4core.color('#7c3aed');
            circle3.circle.stroke = am4core.color('#ffffff');
            circle3.circle.strokeWidth = 2;

            // Series Regresi (Line putus-putus orange)
            const regSeries = chart.series.push(new am4charts.LineSeries());
            regSeries.dataFields.valueY = 'regresi';
            regSeries.dataFields.categoryX = 'periode';
            regSeries.name = 'Tren Regresi';
            regSeries.stroke = am4core.color('#ea580c');
            regSeries.strokeWidth = 2;
            regSeries.strokeDasharray = '8,4';
            regSeries.fill = am4core.color('#ea580c');
            regSeries.fillOpacity = 0;
            regSeries.tensionX = 1;
            regSeries.tooltipText = 'Regresi: [bold]{valueY}%[/]';

            chart.legend = new am4charts.Legend();
            chart.legend.labels.template.fontSize = 11;
            chart.cursor = new am4charts.XYCursor();
            chart.cursor.lineX.stroke = am4core.color('#7c3aed');

            _charts['ach_' + seksiId] = chart;
        }

        // CHART AKAR MASALAH (Horizontal Bar)
        function buildChartAkar(seksiId) {
            const el = document.getElementById('chartAkar_' + seksiId);
            if (!el) return;
            disposeChart('akar_' + seksiId);
            const bd = breakdownMap[seksiId];
            if (!bd || !bd.akar_masalah.length) return;

            const chart = am4core.create(el, am4charts.XYChart);
            chart.paddingRight = 30;
            chart.paddingLeft = 10;

            chart.data = bd.akar_masalah.map(d => ({
                label: d.label,
                count: d.count
            }));

            // Y Axis = kategori (horizontal bar → Y adalah kategori)
            const catAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            catAxis.dataFields.category = 'label';
            catAxis.renderer.inversed = true;
            catAxis.renderer.labels.template.fontSize = 11;
            catAxis.renderer.labels.template.maxWidth = 180;
            catAxis.renderer.labels.template.truncated = true;
            catAxis.renderer.minGridDistance = 10;
            catAxis.renderer.grid.template.disabled = true;

            // X Axis = nilai
            const valAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valAxis.renderer.labels.template.fontSize = 11;
            valAxis.min = 0;
            valAxis.strictMinMax = true;
            valAxis.renderer.grid.template.strokeDasharray = '3,3';

            // Series
            const series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueX = 'count';
            series.dataFields.categoryY = 'label';
            series.name = 'Frekuensi';
            series.columns.template.fill = am4core.color('#ea580c');
            series.columns.template.stroke = am4core.color('#ea580c');
            series.columns.template.fillOpacity = 0.85;
            series.columns.template.height = am4core.percent(60);
            series.columns.template.cornerRadiusTopRight = 4;
            series.columns.template.cornerRadiusBottomRight = 4;
            series.columns.template.tooltipText = '{categoryY}: [bold]{valueX}[/] kali';

            // Label di ujung bar
            const labelBullet = series.bullets.push(new am4charts.LabelBullet());
            labelBullet.label.text = '{valueX}';
            labelBullet.label.fontSize = 11;
            labelBullet.label.fontWeight = '700';
            labelBullet.label.fill = am4core.color('#374151');
            labelBullet.label.dx = 16;
            labelBullet.label.horizontalCenter = 'left';

            chart.cursor = new am4charts.XYCursor();
            chart.cursor.lineY.disabled = true;

            _charts['akar_' + seksiId] = chart;
        }

        // CHART MESIN BERMASALAH (Horizontal Bar)
        function buildChartMesin(seksiId) {
            const el = document.getElementById('chartMesin_' + seksiId);
            if (!el) return;
            disposeChart('mesin_' + seksiId);
            const bd = breakdownMap[seksiId];
            if (!bd || !bd.mesin_breakdown.length) return;

            const chart = am4core.create(el, am4charts.XYChart);
            chart.paddingRight = 30;
            chart.paddingLeft = 10;

            chart.data = bd.mesin_breakdown.map(d => ({
                mesin: d.mesin,
                count: d.count
            }));

            // Y Axis = mesin
            const catAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            catAxis.dataFields.category = 'mesin';
            catAxis.renderer.inversed = true;
            catAxis.renderer.labels.template.fontSize = 11;
            catAxis.renderer.minGridDistance = 10;
            catAxis.renderer.grid.template.disabled = true;

            // X Axis = nilai
            const valAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valAxis.renderer.labels.template.fontSize = 11;
            valAxis.min = 0;
            valAxis.strictMinMax = true;
            valAxis.renderer.grid.template.strokeDasharray = '3,3';

            // Series
            const series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueX = 'count';
            series.dataFields.categoryY = 'mesin';
            series.name = 'Frekuensi';
            series.columns.template.fill = am4core.color('#dc2626');
            series.columns.template.stroke = am4core.color('#dc2626');
            series.columns.template.fillOpacity = 0.85;
            series.columns.template.height = am4core.percent(60);
            series.columns.template.cornerRadiusTopRight = 4;
            series.columns.template.cornerRadiusBottomRight = 4;
            series.columns.template.tooltipText = '{categoryY}: [bold]{valueX}[/] kali';

            // Label
            const labelBullet = series.bullets.push(new am4charts.LabelBullet());
            labelBullet.label.text = '{valueX}';
            labelBullet.label.fontSize = 11;
            labelBullet.label.fontWeight = '700';
            labelBullet.label.fill = am4core.color('#374151');
            labelBullet.label.dx = 16;
            labelBullet.label.horizontalCenter = 'left';

            chart.cursor = new am4charts.XYCursor();
            chart.cursor.lineY.disabled = true;

            _charts['mesin_' + seksiId] = chart;
        }

        // INIT
        document.addEventListener('DOMContentLoaded', function() {
            Object.keys(reportDataMap).forEach(seksiId => {
                pageMap[seksiId] = pageMap[seksiId] || 1;
                renderTable(seksiId);
                buildChartProduksi(seksiId);
                buildChartNg(seksiId);
                buildChartAch(seksiId);
                buildChartAkar(seksiId);
                buildChartMesin(seksiId);
            });
        });

        // EXPORT
        function buildExportUrl(seksiId, type) {
            const current = new URLSearchParams(window.location.search);
            const params = new URLSearchParams();
            params.set('export', type);
            params.set('granularitas', current.get('granularitas') || '');
            params.set('date_from', current.get('date_from') || '');
            params.set('date_to', current.get('date_to') || '');
            params.append('seksi[]', seksiId);
            return 'view/addon/proses/print.php?' + params.toString();
        }

        function captureChartElement(elementId) {
            return new Promise((resolve) => {
                const el = document.getElementById(elementId);
                if (!el) return resolve('');

                const svg = el.querySelector('svg');
                if (!svg) return resolve('');

                html2canvas(el, {
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    scale: 1.5,
                    logging: false,
                }).then(canvas => {
                    resolve(canvas.toDataURL('image/png'));
                }).catch(() => resolve(''));
            });
        }

        async function captureCharts(seksiId) {
            await new Promise(r => setTimeout(r, 800));

            const images = await Promise.all([
                captureChartElement('chartProduksi_' + seksiId),
                captureChartElement('chartNg_' + seksiId),
                captureChartElement('chartAch_' + seksiId),
                captureChartElement('chartAkar_' + seksiId),
                captureChartElement('chartMesin_' + seksiId),
            ]);

            return images;
        }

        function buildPostForm(seksiId, type, chartImages) {
            const current = new URLSearchParams(window.location.search);

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'view/addon/proses/print.php';
            if (type === 'pdf') form.target = '_blank';

            const fields = {
                'export': type,
                'granularitas': current.get('granularitas') || '',
                'date_from': current.get('date_from') || '',
                'date_to': current.get('date_to') || '',
                'chart_images': JSON.stringify(chartImages),
            };

            for (const [name, value] of Object.entries(fields)) {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = name;
                inp.value = value;
                form.appendChild(inp);
            }

            const seksiInp = document.createElement('input');
            seksiInp.type = 'hidden';
            seksiInp.name = 'seksi[]';
            seksiInp.value = seksiId;
            form.appendChild(seksiInp);

            document.body.appendChild(form);
            return form;
        }

        async function exportPdf(seksiId) {
            const overlay = document.getElementById('loadingOverlay');
            const loadingText = overlay.querySelector('.loading-text');
            loadingText.textContent = 'Mendownload PDF...';
            overlay.classList.add('active');

            try {
                const chartImages = await captureCharts(seksiId);
                loadingText.textContent = 'Membuat PDF...';
                const form = buildPostForm(seksiId, 'pdf', chartImages);
                form.submit();
                document.body.removeChild(form);
            } catch (e) {
                console.error(e);
                alert('Gagal mengekspor PDF.');
            } finally {
                setTimeout(() => {
                    loadingText.textContent = 'Memuat laporan...';
                    overlay.classList.remove('active');
                }, 2000);
            }
        }

        async function exportExcel(seksiId) {
            const overlay = document.getElementById('loadingOverlay');
            const loadingText = overlay.querySelector('.loading-text');
            loadingText.textContent = 'Mendownload Excel...';
            overlay.classList.add('active');

            try {
                const chartImages = await captureCharts(seksiId);
                loadingText.textContent = 'Membuat file Excel...';
                const form = buildPostForm(seksiId, 'xlsx', chartImages);
                form.submit();
                document.body.removeChild(form);
            } catch (e) {
                console.error(e);
                alert('Gagal mengekspor Excel.');
            } finally {
                setTimeout(() => {
                    loadingText.textContent = 'Memuat laporan...';
                    overlay.classList.remove('active');
                }, 2000);
            }
        }
    </script>
</body>

</html>