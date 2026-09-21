<?php
$drillingMachines = [
    'DR01',
    'DR02',
    'DR03',
    'DR04',
    'DR05',
    'BOR ULANG'
];

$keteranganOptions = [
    '',
    'Mesin Error: Breakdown',
    'Quality: Cacat/Rework',
    'Quality: Sortir & Variasi',
    'Tooling: Mata Bor & Plong',
    'Tooling: Spindle/Joint',
    'Tooling: TC/Blushing/Upper-Bottom',
    'Setting Atas',
    'Setting Bawah',
    'Lainnya'
];
?>

<style>
    /* ================= TABLE ================= */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    table th {
        background: #0a2a66;
        color: white;
        padding: 10px;
        font-size: 13px;
        text-align: center;
    }

    table td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        font-size: 12px;
        text-align: center;
    }

    /* ================= INPUT ================= */
    input[type="number"] {
        width: 60px;
        padding: 15px;
        text-align: center;
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        font-weight: 700;
    }

    /* ================= DRILLING ================= */
    .page-wrapper {
        display: flex;
        flex-direction: column;
        height: 109vh;
        overflow: hidden;
    }

    .header-container {
        flex-shrink: 0;
        position: sticky;
        top: 0;
        z-index: 100;
        margin: 15px;
        background: var(--bg, #fff);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        padding: 8px 16px 4px;
    }

    .table-container {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        padding: 0 20px;
    }

    .bottom-container {
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        z-index: 100;
        background: var(--bg, #fff);
        box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.08);
        padding: 12px 16px;
    }

    .table-header-sticky {
        position: static !important;
        box-shadow: none !important;
    }

    #shift-mode-label {
        text-align: left;
        font-weight: 600;
        padding-left: 8px;
    }

    .active-shift-label {
        color: #35dc26;
        font-weight: 600;
        padding-left: 10px;
    }

    .view-only-label {
        color: #dc2626;
        font-weight: 600;
        padding-left: 8px;
    }

    .drilling-table th {
        border: 1px solid #000;
        padding: 15px;
        text-align: center;
        font-size: 15px;
    }

    .drilling-table td {
        border: 1px solid #000;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        height: auto;
        vertical-align: middle;
    }

    /* ================= STICKY HEADER & KOLOM MESIN ================= */
    .drilling-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .drilling-table tr:first-child th {
        position: sticky;
        top: 0;
        z-index: 20;
        font-size: 17px;
        background: #0a2a66;
    }

    .drilling-table tr:nth-child(2) th {
        position: sticky;
        top: 0;
        z-index: 20;
        font-size: 15px;
        background: #0a2a66;
    }

    .drilling-table tr:first-child th.col-mesin {
        position: sticky;
        top: 0;
        left: 0;
        z-index: 40;
        background: #0a2a66;
        box-shadow: 3px 0 6px rgba(0, 0, 0, 0.15);
    }

    .drilling-table td.col-mesin {
        position: sticky;
        left: 0;
        z-index: 10;
        font-size: 15px;
        background: #f0f4ff;
        color: #0a2a66;
        font-weight: 700;
        border: 1.5px solid #0a2a66 !important;
        box-shadow: 3px 0 6px rgba(0, 0, 0, 0.08);
        white-space: nowrap;
        min-width: 90px;
    }

    .locked-row td.col-mesin,
    .full-locked td.col-mesin {
        background: #dde3f0;
    }

    /* ================= CELL INPUT ================= */
    .cell-input {
        width: 100%;
        min-height: 40px;
        height: auto;
        border: none;
        outline: none;
        text-align: center;
        font-weight: 700;
        font-size: 15px;
        background: transparent;
        overflow: hidden;
        resize: none;
        box-sizing: border-box;
    }

    .cell-select {
        width: 100%;
    }

    /* ================= KETERANGAN ================= */
    .keterangan-select {
        width: 100%;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }

    .keterangan-text {
        margin-top: 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .ket-tag {
        padding: 4px 6px;
        border-radius: 5px;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        text-align: center;
        width: 100%;
    }

    .ket-0 {
        background: #daf5ff;
    }

    .ket-1 {
        background: #d1f7d6;
    }

    .ket-2 {
        background: #f9ffd6;
    }

    .ket-3 {
        background: #ffcdcd;
    }

    .remove {
        cursor: pointer;
        margin-left: 8px;
        color: red;
        font-weight: bold;
    }

    /* ================= LOCKED ROW ================= */
    .locked-row {
        background-color: transparent;
        opacity: 1;
    }

    .locked-row input[name="cf_no[]"],
    .locked-row textarea[name="cf_no[]"],
    .locked-row input[name="customer[]"],
    .locked-row textarea[name="customer[]"],
    .locked-row input[name="diameter[]"],
    .locked-row textarea[name="diameter[]"],
    .locked-row input[name="mata_bor[]"],
    .locked-row textarea[name="mata_bor[]"],
    .locked-row input[name="ukuran_bor[]"],
    .locked-row textarea[name="ukuran_bor[]"],
    .locked-row input[name="jam[]"],
    .locked-row input[name="actual[]"],
    .locked-row input[name="ng[]"],
    .locked-row input[name="operator[]"],
    .locked-row .validasi {
        background-color: #e9ecef;
        opacity: 0.8;
        text-align: center;
    }

    .full-locked {
        background-color: #e9ecef;
        opacity: 0.8;
    }

    /* ================= INPUT SIZING ================= */
    textarea,
    input,
    select {
        max-width: 100%;
    }

    input[name="cf_no[]"],
    input[name="diameter_cf[]"],
    input[name="ukuran_bor[]"] {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: clip;
    }

    input[name="cf_no[]"] {
        min-width: 260px !important;
    }

    input[name="diameter_cf[]"] {
        min-width: 180px !important;
    }

    input[name="ukuran_bor[]"] {
        min-width: 220px !important;
    }

    input[name="operator[]"],
    .keterangan-select {
        min-width: 160px !important;
    }

    input[name="actual[]"],
    input[name="ng[]"],
    input[name="iot[]"] {
        min-width: 55px !important;
        width: 55px !important;
    }

    input[name="target_mesin[]"] {
        min-width: 55px !important;
        width: 55px !important;
        text-align: center;
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        font-weight: 700;
    }

    input[name="jumlah_mata_bor[]"] {
        min-width: 80px !important;
    }

    /* ================= VERTICAL INPUT ================= */
    .drilling-table td .cell-input.vertical {
        width: 100%;
        min-height: 36px;
        height: auto;
        white-space: pre-wrap;
        overflow-wrap: break-word;
        resize: none;
        padding: 4px 6px;
        line-height: 1.4;
        display: block;
    }

    .drilling-table textarea,
    .drilling-table .cell-input.vertical {
        overflow: hidden !important;
        overflow-y: hidden !important;
        overflow-x: hidden !important;
        resize: none;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .drilling-table textarea::-webkit-scrollbar,
    .drilling-table .cell-input.vertical::-webkit-scrollbar {
        display: none;
    }

    .drilling-table textarea[readonly],
    .drilling-table input[readonly]:not(.jam):not(.iot-mesin) {
        background: #f8fafc;
        color: #6b7280;
        cursor: default;
    }

    .drilling-table input[type="number"] {
        text-align: center !important;
        width: 100% !important;
        display: block !important;
    }

    /* ================= VALIDASI CHECKBOX ================= */
    .validasi {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: green;
    }

    /* ================= JAM ================= */
    .jam {
        width: 100%;
        border: none;
        outline: none;
        text-align: center;
        background: transparent;
        font-weight: bold;
    }

    .cell-input:invalid {
        border: 1px solid red;
    }

    /* ================= BOTTOM SECTION ================= */
    .bottom-section {
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        gap: 20px;
        margin-top: 25px;
    }

    .left-section {
        display: flex;
        flex-direction: row;
        align-items: stretch;
        gap: 15px;
    }

    /* ================= SUMMARY BOX ================= */
    .target-input {
        width: 120px;
        min-width: 120px;
        border: none;
        outline: none;
        background: transparent;
        text-align: right;
        padding: 0 6px 0 0;
        font-size: 20px !important;
        font-weight: 700 !important;
        font-family: inherit !important;
        line-height: 1 !important;
        color: #000 !important;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: textfield;
    }

    .target-input::-webkit-outer-spin-button,
    .target-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    #sumTarget {
        font-size: 20px;
        font-weight: 700;
        line-height: 1;
        color: #000;
        display: inline-block;
        min-width: 120px;
        text-align: center;
    }

    #sumActual {
        font-size: 20px;
        font-weight: 700;
        line-height: 1;
        color: #000;
        display: inline-block;
        min-width: 120px;
        text-align: center;
    }

    .target-input:focus {
        border-color: #1e4fbf;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        background: #ffffff;
    }

    .target-input:disabled {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #6b7280;
        cursor: not-allowed;
    }

    .summary-box {
        min-width: 280px;
        background: #ffffff;
        border-radius: 12px;
        padding: 20px 24px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
        transition: 0.2s;
    }

    .summary-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }

    .summary-box h4 {
        text-align: center;
        font-size: 15px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 18px;
        align-items: center;
        gap: 12px;
        padding: 6px 0;
    }

    .summary-row span {
        color: #6b7280;
        font-size: 16px;
        font-weight: 500;
    }

    .summary-row strong {
        font-size: 22px;
        font-weight: 700;
        color: #1e3a8a;
    }

    .summary-box.percent {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border: none;
    }

    .percent-value {
        font-size: 42px;
        font-weight: 800;
        color: #2e7d32;
    }

    /* ================= PRINT HEADER (SCREEN: HIDDEN) ================= */
    .print-header-info,
    .code-header-info {
        display: none;
    }

    /* ================= VALIDASI BOX ================= */
    .validasi-box {
        width: 260px;
        background: #ffffff;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .validasi-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 17px;
        font-weight: 600;
        color: #374151;
        padding-bottom: 6px;
        border-bottom: 1px dashed #ddd;
    }

    .validasi-item:last-child {
        border-bottom: none;
    }

    .approve {
        width: 22px;
        height: 22px;
        cursor: pointer;
        accent-color: #2e7d32;
    }

    .approve:hover {
        transform: scale(1.1);
    }

    /* ================= PRINT TTD BOX (SCREEN: HIDDEN) ================= */
    .print-ttd-box {
        display: none;
    }

    /* ================= PRINT PDF ================= */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box !important;
            float: none !important;
        }

        html {
            zoom: 1 !important;
            transform: none !important;
        }

        .page-wrapper,
        .header-container,
        .table-container,
        .bottom-container {
            position: static !important;
            display: block !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            width: 100% !important;
            transform: none !important;
            will-change: auto !important;
        }

        .header-container {
            margin-bottom: 30px;
        }

        .page-wrapper {
            display: block !important;
            height: auto !important;
        }

        .bottom-container {
            padding: 4px 8px !important;
            margin-top: 0 !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .bottom-section {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-top: 2px !important;
        }

        .drilling-table {
            min-width: 0 !important;
            width: 100% !important;
            table-layout: fixed !important;
        }

        .table-container {
            min-width: 0 !important;
            overflow: visible !important;
        }

        html,
        body {
            height: auto !important;
            overflow: visible !important;
            max-height: none !important;
            width: 100% !important;
        }

        .bottom-section {
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
            break-before: avoid !important;
            break-inside: avoid !important;
        }

        .no-print,
        .navbar,
        .ac-dropdown,
        .keterangan-select {
            display: none !important;
        }

        @page {
            size: A4 landscape;
            margin: 3mm 3mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box !important;
        }

        html,
        body {
            background: #fff !important;
            font-family: Arial, sans-serif !important;
            font-size: 8px !important;
            margin: 0 !important;
            padding: 0 !important;
            align-content: center;
            width: 100% !important;
            height: 100% !important;
        }

        .content {
            margin-top: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .print-header-info {
            display: block !important;
            text-align: right !important;
            font-size: 10px !important;
            font-weight: bold !important;
            color: #000 !important;
            margin-top: 40px !important;
            margin-bottom: -2px !important;
            padding-right: 28px !important;
            line-height: 1.4 !important;
        }

        .code-header-info {
            display: block !important;
            text-align: left !important;
            font-size: 10px !important;
            font-weight: bold !important;
            color: #000 !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            margin-top: 25px;
            line-height: 1.4 !important;
            padding-left: 14px !important;
        }

        .page-wrapper {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
        }

        .header-container,
        .table-container,
        .bottom-container {
            position: static !important;
            overflow: visible !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .table-container {
            width: 100% !important;
            max-width: 100% !important;
            max-height: none !important;
            overflow: visible !important;
            padding: 0 !important;
            margin-bottom: 20px !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .table-header-sticky {
            position: relative !important;
            top: auto !important;
            margin-bottom: 40px !important;
            box-shadow: none !important;
            text-align: center !important;
        }

        .table-header-sticky h3 {
            font-size: 18px !important;
            margin: 0 0 1px 0 !important;
            line-height: 1.1 !important;
            text-align: center !important;
        }

        #shift-mode-label {
            display: none !important;
        }

        .drilling-table {
            width: 98% !important;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            table-layout: fixed !important;
            overflow: visible !important;
            max-height: none !important;
            margin: 0 auto !important;
        }

        .drilling-table th,
        .drilling-table td {
            border: 0.5px solid #555 !important;
            padding: 0px 1px !important;
            line-height: 1 !important;
            height: auto !important;
            min-height: 0 !important;
            font-size: 8px !important;
            text-align: center !important;
            vertical-align: middle !important;
            color: #000 !important;
            word-wrap: break-word !important;
            white-space: normal !important;
            position: static !important;
            background: #fff !important;
            overflow: hidden !important;
        }

        .drilling-table th {
            background: #c8d6f0 !important;
            font-weight: bold !important;
            color: #000 !important;
            font-size: 8px !important;
            padding: 1px !important;
            text-align: center !important;
        }

        .drilling-table th:nth-child(1),
        .drilling-table td:nth-child(1) {
            width: 4% !important;
        }

        .drilling-table th:nth-child(2),
        .drilling-table td:nth-child(2) {
            width: 6% !important;
        }

        .drilling-table th:nth-child(3),
        .drilling-table td:nth-child(3) {
            width: 5% !important;
        }

        .drilling-table th:nth-child(4),
        .drilling-table td:nth-child(4) {
            width: 6% !important;
        }

        .drilling-table th:nth-child(5),
        .drilling-table td:nth-child(5) {
            width: 4% !important;
        }

        .drilling-table th:nth-child(6),
        .drilling-table td:nth-child(6) {
            width: 6% !important;
        }

        .drilling-table th:nth-child(7),
        .drilling-table td:nth-child(7) {
            width: 4% !important;
        }

        .drilling-table th:nth-child(8),
        .drilling-table td:nth-child(8) {
            width: 7% !important;
        }

        .drilling-table th:nth-child(9),
        .drilling-table td:nth-child(9) {
            width: 7% !important;
        }

        .drilling-table th:nth-child(10),
        .drilling-table td:nth-child(10) {
            width: 5% !important;
        }

        .drilling-table th:nth-child(11),
        .drilling-table td:nth-child(11) {
            width: 9% !important;
        }

        .drilling-table th:nth-child(12),
        .drilling-table td:nth-child(12) {
            width: 11% !important;
        }

        .col-validasi {
            display: none !important;
        }

        .drilling-table td:nth-child(5),
        .drilling-table td:nth-child(7),
        .drilling-table td:nth-child(8),
        .drilling-table td:nth-child(9),
        .drilling-table td:nth-child(10) {
            vertical-align: middle !important;
            text-align: center !important;
        }

        .drilling-table td:nth-child(8) input {
            text-align: center !important;
            width: 100% !important;
            display: block !important;
            margin: 0 auto !important;
        }

        .drilling-table th,
        .drilling-table td {
            padding: 1px !important;
            line-height: 1.1 !important;
            height: 20px !important;
        }

        .drilling-table input,
        .drilling-table textarea {
            border: none !important;
            background: transparent !important;
            font-size: 8px !important;
            line-height: 1 !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 auto !important;
            text-align: center !important;
            color: #000 !important;
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
            resize: none !important;
            height: auto !important;
            box-shadow: none !important;
            outline: none !important;
            min-width: 0 !important;
            min-height: 0 !important;
            -webkit-appearance: none !important;
            appearance: none !important;
        }

        .drilling-table input[type="number"],
        .drilling-table input[type="text"] {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
        }

        .print-cell-value {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            font-size: 8px !important;
            font-weight: bold !important;
            color: #000 !important;
        }

        .drilling-table input::placeholder,
        .drilling-table textarea::placeholder {
            color: transparent !important;
        }

        .drilling-table td.col-mesin {
            background: #e8eef8 !important;
            font-weight: bold !important;
            font-size: 8px !important;
            padding: 1px !important;
            vertical-align: middle !important;
            text-align: center !important;
        }

        .drilling-table td:nth-child(12) {
            font-size: 8px !important;
            line-height: 1 !important;
            text-align: center !important;
            padding: 1px !important;
            vertical-align: middle !important;
        }

        .keterangan-text {
            font-size: 8px !important;
            line-height: 1 !important;
            text-align: center !important;
            padding: 0 !important;
            gap: 1px !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .ket-tag {
            font-size: 8px !important;
            padding: 0 1px !important;
            line-height: 1 !important;
            border-radius: 2px !important;
            margin-bottom: 0px !important;
        }

        .drilling-table tr {
            height: auto !important;
            line-height: 1 !important;
        }

        .drilling-table td input[value=""],
        .drilling-table td textarea:empty {
            height: 6px !important;
            min-height: 0 !important;
        }

        .drilling-table tr:last-child td {
            font-weight: bold !important;
            background: #f0f0f0 !important;
            font-size: 8px !important;
            padding: 1px !important;
            text-align: center !important;
        }

        tr {
            page-break-inside: avoid !important;
        }

        thead {
            display: table-header-group !important;
        }

        .bottom-section {
            display: flex !important;
            flex-direction: row !important;
            justify-content: center !important;
            align-items: flex-start !important;
            gap: 4px !important;
            margin-top: 3px !important;
            page-break-inside: avoid !important;
            width: 99% !important;
            padding-left: 10.5px !important;
        }

        .left-section {
            display: flex !important;
            flex-direction: row !important;
            justify-content: center !important;
            gap: 4px !important;
            flex: 1 !important;
        }

        .summary-box {
            flex: 1 !important;
            border: 0.5px solid #000 !important;
            border-radius: 2px !important;
            padding: 4px 8px !important;
            background: #fff !important;
            box-shadow: none !important;
            min-width: 180px !important;
            min-height: 50px !important;
            text-align: center !important;
        }

        .summary-box h4 {
            font-size: 8px !important;
            font-weight: bold !important;
            text-align: center !important;
            margin: 0 0 5px 0 !important;
            color: #000 !important;
        }

        .summary-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 8px !important;
            padding: 0px 0 !important;
        }

        .summary-row span {
            font-size: 10px !important;
            color: #000 !important;
            text-align: left !important;
            gap: 10px !important;
            flex: 1 !important;
            white-space: nowrap !important;
        }

        .target-input {
            font-size: 10px !important;
            font-weight: bold !important;
            color: #000 !important;
            min-width: 0 !important;
            text-align: right !important;
            width: auto !important;
        }

        #sumTarget {
            font-size: 10px !important;
            font-weight: bold !important;
            color: #000 !important;
            min-width: 0 !important;
            text-align: right !important;
        }

        #sumActual {
            font-size: 10px !important;
            font-weight: bold !important;
            color: #000 !important;
            min-width: 0 !important;
            text-align: right !important;
        }

        .target-input,
        input#sumTarget {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .summary-box.percent {
            background: #e8f4fd !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
        }

        .percent-value {
            font-size: 14px !important;
            font-weight: bold !important;
            color: #000 !important;
            text-align: center !important;
        }

        .print-ttd-box {
            display: block !important;
            width: 75% !important;
            text-align: center !important;
        }

        .print-ttd-cols {
            display: flex !important;
            flex-direction: row !important;
            justify-content: center !important;
            gap: 2px !important;
            width: 100% !important;
        }

        .print-ttd-col {
            flex: 1 !important;
            border: 0.5px solid #ccc !important;
            padding: 2px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 50px !important;
            text-align: center !important;
        }

        .print-ttd-label {
            font-size: 12px !important;
            font-weight: bold !important;
            color: #000 !important;
            text-align: center !important;
            margin-bottom: 2px !important;
        }

        .print-ttd-space {
            flex: 1 !important;
            min-height: 22px !important;
        }

        .print-ttd-line {
            width: 80% !important;
            border-top: 0.5px solid #000 !important;
            margin-top: 2px !important;
        }

        .validasi-box,
        .validasi-item,
        .approve {
            display: none !important;
        }
    }

    /* ================= RESPONSIVE 1025px+ ================= */
    @media screen and (min-width: 1025px) {
        .table-container {
            overflow-x: auto !important;
            overflow-y: auto !important;
        }

        .drilling-table {
            width: max-content !important;
            table-layout: auto !important;
        }
    }

    /* ================= RESPONSIVE 1024px ================= */
    @media only screen and (max-width: 1024px) {
        .drilling-table {
            min-width: 1400px !important;
            width: max-content !important;
            table-layout: auto !important;
        }

        .bottom-section {
            flex-direction: column;
        }

        .left-section {
            width: 100%;
            flex-wrap: wrap;
        }

        .summary-box {
            flex: 1;
            min-width: 220px;
        }

        .validasi-box {
            width: 100%;
        }
    }

    /* ================= RESPONSIVE 768px ================= */
    @media only screen and (max-width: 768px) {
        .drilling-table {
            min-width: 1300px !important;
            width: max-content !important;
            table-layout: auto !important;
        }

        .drilling-table th,
        .drilling-table td,
        .cell-input {
            font-size: 15px !important;
            padding: 6px;
        }

        .jam,
        .keterangan-select,
        textarea {
            max-width: 100%;
            width: 100%;
            font-size: 15px;
        }

        textarea::-webkit-scrollbar,
        input::-webkit-scrollbar {
            display: none;
        }

        textarea,
        input {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .bottom-section {
            flex-direction: column;
        }

        .left-section {
            width: 100%;
            flex-wrap: wrap;
        }

        .summary-box,
        .validasi-box {
            width: 100%;
        }

        .percent-value {
            font-size: 30px;
        }

        .table-header-sticky {
            top: 10px;
        }

        .table-header-sticky h3 {
            font-size: 18px;
        }
    }

    /* ================= RESPONSIVE HEADER & PAGE WRAPPER ================= */
    @media screen and (max-width: 1024px) {
        .page-wrapper {
            height: 150dvh;
        }

        .header-container {
            margin: 6px 8px;
            padding: 6px 12px 4px;
        }

        .header-container h3 {
            font-size: 18px !important;
            line-height: 1.2 !important;
            margin-bottom: 2px !important;
        }

        #shift-mode-label {
            font-size: 12px;
            padding-left: 4px;
        }

        .bottom-container {
            padding: 8px 10px;
        }

        .bottom-section {
            margin-top: 8px;
            gap: 10px;
        }
    }

    @media screen and (max-width: 768px) {
        .page-wrapper {
            height: 100dvh;
        }

        .header-container {
            margin: 4px 6px;
            padding: 4px 10px 3px;
        }

        .header-container h3 {
            font-size: 14px !important;
            line-height: 1.2 !important;
            margin-bottom: 1px !important;
        }

        #shift-mode-label {
            font-size: 11px;
            padding-left: 2px;
        }

        .bottom-container {
            padding: 6px 8px;
        }

        .summary-box {
            padding: 10px 14px;
            min-width: 0;
        }

        .summary-box h4 {
            font-size: 12px;
            margin-bottom: 6px;
        }

        .summary-row {
            font-size: 13px;
            gap: 6px;
            padding: 3px 0;
        }

        .summary-row span {
            font-size: 12px;
        }

        .target-input,
        #sumTarget,
        #sumActual {
            font-size: 15px !important;
        }

        .percent-value {
            font-size: 24px;
        }

        .summary-box.percent h4 {
            font-size: 11px;
        }

        .validasi-box {
            padding: 10px 12px;
            gap: 8px;
        }

        .validasi-item {
            font-size: 13px;
        }
    }

    @media screen and (max-width: 480px) {
        .page-wrapper {
            height: 100dvh;
        }

        .header-container {
            margin: 2px 4px;
            padding: 3px 8px 2px;
        }

        .header-container h3 {
            font-size: 12px !important;
            line-height: 1.1 !important;
            margin-bottom: 0 !important;
        }

        #shift-mode-label {
            font-size: 10px;
        }

        .bottom-container {
            padding: 4px 6px;
        }

        .bottom-section {
            gap: 6px;
            margin-top: 4px;
        }

        .summary-box {
            padding: 8px 10px;
        }

        .summary-box h4 {
            font-size: 11px;
        }

        .summary-row span {
            font-size: 11px;
        }

        .target-input,
        #sumTarget,
        #sumActual {
            font-size: 13px !important;
        }

        .percent-value {
            font-size: 20px;
        }
    }
</style>

<div class="page-wrapper">
    <!-- ================= CONTAINER 1: HEADER ================= -->
    <div class="header-container">
        <div class="code-header-info" id="printCodeInfo"></div>
        <div class="print-header-info" id="printHeaderInfo"></div>
        <h3 style="text-align:center; font-size:25px; margin:0 0 4px 0;">
            PT. EXEDY PRIMA INDONESIA<br>LAPORAN HARIAN DRILLING
        </h3>
        <div id="shift-mode-label"></div>
    </div>

    <!-- ================= CONTAINER 2: TABEL ================= -->
    <div class="table-container">
        <form id="drillingForm">
            <div id="shift-mode-label"></div>

            <table class="drilling-table">
                <tr>
                    <th rowspan="2" class="col-mesin">MESIN</th>
                    <th rowspan="2">PART NO.</th>
                    <th rowspan="2">CUSTOMER</th>
                    <th rowspan="2">SIZE CF</th>
                    <th rowspan="2">JUMLAH MATA BOR</th>
                    <th rowspan="2">UKURAN MATA BOR</th>
                    <th rowspan="2">JAM OPERASIONAL</th>
                    <th colspan="4">JUMLAH</th>
                    <th rowspan="2">NAMA OPERATOR</th>
                    <th rowspan="2">KETERANGAN</th>
                    <th rowspan="2" class="col-validasi">VALIDASI</th>
                </tr>
                <tr>
                    <th>IOT</th>
                    <th>TARGET</th>
                    <th>ACTUAL</th>
                    <th>NG</th>
                </tr>

                <?php foreach ($drillingMachines as $mIndex => $machine) { ?>
                    <?php for ($i = 0; $i < 4; $i++) { ?>
                        <tr>
                            <!-- MESIN -->
                            <?php if ($i == 0) { ?>
                                <td rowspan="4" class="col-mesin">
                                    <?= $machine ?>
                                    <?php for ($j = 0; $j < 4; $j++) { ?>
                                        <input type="hidden" name="mesin[]" value="<?= $machine ?>">
                                    <?php } ?>
                                </td>
                            <?php } ?>

                            <!-- PART NO -->
                            <td>
                                <textarea name="cf_no[]" class="cell-input cf-input vertical"
                                    placeholder="isi part no."
                                    autocomplete="off"
                                    rows="1"
                                    style="resize:none;"></textarea>
                            </td>

                            <!-- CUSTOMER -->
                            <td>
                                <textarea name="customer[]" class="cell-input vertical readonly-field"
                                    rows="1"
                                    style="resize:none;"
                                    readonly></textarea>
                            </td>

                            <!-- SIZE CF -->
                            <td>
                                <textarea name="diameter[]" class="cell-input vertical readonly-field"
                                    rows="1"
                                    style="resize:none;"
                                    readonly></textarea>
                            </td>

                            <!-- JUMLAH MATA BOR -->
                            <td><input type="text" name="mata_bor[]" class="cell-input" placeholder="isi jumlah mata bor"></td>

                            <!-- UKURAN MATA BOR -->
                            <td>
                                <textarea name="ukuran_bor[]" class="cell-input vertical readonly-field"
                                    rows="1"
                                    style="resize:none;"
                                    readonly></textarea>
                            </td>

                            <!-- JAM OPERASIONAL -->
                            <td><input type="text" name="jam[]" class="jam" readonly></td>

                            <!-- IOT per mesin -->
                            <?php if ($i == 0) { ?>
                                <td rowspan="4">
                                    <input type="number"
                                        name="iot_mesin[]"
                                        class="iot-mesin"
                                        id="iot-<?php echo $mIndex; ?>"
                                        readonly>
                                </td>
                            <?php } ?>

                            <!-- TARGET -->
                            <td>
                                <input type="number" class="target-row" name="target_row[]"
                                    min="0" placeholder="isi target" style="width:60px; text-align:center;">
                            </td>

                            <!-- ACTUAL & NG -->
                            <td><input type="number" name="actual[]" class="actual" placeholder="isi actual" min="0"></td>
                            <td><input type="number" name="ng[]" class="ng" placeholder="isi ng" min="0"></td>

                            <!-- NAMA OPERATOR -->
                            <td><input type="text" name="operator[]" class="cell-input" placeholder="isi nama operator"></td>

                            <!-- KETERANGAN -->
                            <?php if ($i == 0) { ?>
                                <td rowspan="4">
                                    <select class="keterangan-select" data-target="ket-<?php echo $mIndex; ?>">
                                        <?php foreach ($keteranganOptions as $option) { ?>
                                            <option value="<?php echo $option; ?>">
                                                <?php echo $option == '' ? '-- Pilih --' : $option; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <div id="ket-<?php echo $mIndex; ?>" class="keterangan-text"></div>
                                </td>
                            <?php } ?>

                            <input type="hidden" name="keterangan[]" class="ket-input">
                            <input type="hidden" name="iot_hidden[]" class="iot-hidden">
                            <input type="hidden" name="target_hidden[]" class="target-hidden">

                            <!-- VALIDASI -->
                            <td class="col-validasi">
                                <input type="checkbox"
                                    name="validasi[<?= ($mIndex * 4) + $i ?>]"
                                    class="validasi">
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>

                <!-- TOTAL ROW -->
                <tr>
                    <td colspan="7">TOTAL</td>
                    <td id="totalIot">0</td>
                    <td id="totalTarget">0</td>
                    <td id="totalActual">0</td>
                    <td id="totalNg">0</td>
                </tr>
            </table>
        </form>
    </div>

    <datalist id="partno-list"></datalist>

    <!-- ================= CONTAINER 3: BOTTOM SECTION ================= -->
    <div class="bottom-container">
        <div class="bottom-section">
            <div class="left-section">
                <div class="summary-box">
                    <h4>SUMMARY PRODUKSI</h4>
                    <div class="summary-row">
                        <span>Total Target</span>
                        <div id="sumTarget">0</div
                            <?php // if (!in_array($_SESSION['role'], ['supervisor', 'ass_manager'])) echo 'disabled'; 
                            ?>>
                    </div>
                    <div class="summary-row">
                        <span>Total Actual</span>
                        <div id="sumActual">0</div>
                    </div>
                </div>
                <div class="summary-box percent">
                    <h4>ACHIEVEMENT</h4>
                    <div class="percent-value" id="sumPercent">0%</div>
                </div>
            </div>

            <!-- ================= PRINT PDF BUTTON ================= -->
            <div class="no-print" style="margin-bottom: 15px; text-align: right;">
                <button type="button" onclick="prepareAndPrint()"
                    style="padding: 10px 20px; background-color: #1d4ed8; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    🖨️ Cetak Laporan & Summary
                </button>
            </div>

            <!-- ================= VALIDASI BOX ================= -->
            <div class="validasi-box">
                <div id="approve-window-info" style="font-size:12px; text-align:center; padding-bottom:6px;"></div>
                <div class="validasi-item">
                    <label>Foreman</label>
                    <input type="checkbox" class="approve"
                        <?php if ($_SESSION['role'] != 'foreman') echo 'disabled'; ?>
                        <?php if ($_SESSION['role'] == 'foreman') echo 'data-role-ok="1"'; ?>>
                </div>
                <div class="validasi-item">
                    <label>Supervisor</label>
                    <input type="checkbox" class="approve"
                        <?php if ($_SESSION['role'] != 'supervisor') echo 'disabled'; ?>
                        <?php if ($_SESSION['role'] == 'supervisor') echo 'data-role-ok="1"'; ?>>
                </div>
                <div class="validasi-item">
                    <label>Ast. Manager</label>
                    <input type="checkbox" class="approve"
                        <?php if ($_SESSION['role'] != 'ass_manager') echo 'disabled'; ?>
                        <?php if ($_SESSION['role'] == 'ass_manager') echo 'data-role-ok="1"'; ?>>
                </div>
            </div>

            <!-- ================= PRINT ONLY: TTD BOX ================= -->
            <div class="print-ttd-box">
                <div class="print-ttd-cols">
                    <div class="print-ttd-col">
                        <div class="print-ttd-label">Foreman</div>
                        <div class="print-ttd-space"></div>
                        <div class="print-ttd-line"></div>
                    </div>
                    <div class="print-ttd-col">
                        <div class="print-ttd-label">Supervisor</div>
                        <div class="print-ttd-space"></div>
                        <div class="print-ttd-line"></div>
                    </div>
                    <div class="print-ttd-col">
                        <div class="print-ttd-label">Ast. Manager</div>
                        <div class="print-ttd-space"></div>
                        <div class="print-ttd-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ================= READONLY FIELDS =================
    const ALWAYS_READONLY_FIELDS = ['customer[]', 'diameter[]', 'ukuran_bor[]'];

    function isAlwaysReadonly(el) {
        const name = el.getAttribute('name');
        return ALWAYS_READONLY_FIELDS.includes(name);
    }

    function relockReadonlyFields() {
        document.querySelectorAll('[name="customer[]"], [name="diameter[]"], [name="ukuran_bor[]"]')
            .forEach(el => {
                el.readOnly = true;
            });
    }

    /// ================= DRILLING CORE =================
    let operatorCounter = {};
    let isSaving = false;
    let pendingSave = false;

    const _restoredChangeTime = localStorage.getItem('shiftChangeTime') ?
        parseInt(localStorage.getItem('shiftChangeTime')) : null;
    const _restoredChangedFrom = localStorage.getItem('shiftChangedFrom') ?? null;
    const _stillInTolerance = _restoredChangeTime !== null &&
        (Date.now() - _restoredChangeTime) / 1000 / 60 < 30;

    function getShiftStartTime() {
        const now = new Date();
        const time = now.toTimeString().split(" ")[0];
        const today = now.toISOString().split('T')[0];

        let shiftStart = null;
        if (time >= "07:30:00" && time < "15:30:00") {
            shiftStart = new Date(today + 'T07:30:00');
        } else if (time >= "15:30:00" && time < "23:30:00") {
            shiftStart = new Date(today + 'T15:30:00');
        } else if (time >= "23:30:00") {
            shiftStart = new Date(today + 'T23:30:00');
        } else if (time < "07:30:00") {
            // Shift-3 dari kemarin
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            shiftStart = new Date(yesterday.toISOString().split('T')[0] + 'T23:30:00');
        }
        return shiftStart;
    }

    function getPrevShift(currentShift) {
        if (currentShift === 'Shift-1') return 'Shift-3';
        if (currentShift === 'Shift-2') return 'Shift-1';
        return 'Shift-2';
    }

    let _autoTolerance = false;
    let _autoToleranceFrom = null;

    if (!_stillInTolerance) {
        const shiftStart = getShiftStartTime();
        if (shiftStart) {
            const msSinceStart = Date.now() - shiftStart.getTime();
            const minSinceStart = msSinceStart / 1000 / 60;
            if (minSinceStart >= 0 && minSinceStart < 30) {
                // Shift baru mulai < 30 menit lalu → masih toleransi
                _autoTolerance = true;
                _autoToleranceFrom = getPrevShift(getShift() ?? 'Shift-1');
                if (!localStorage.getItem('shiftChangeTime')) {
                    localStorage.setItem('shiftChangeTime', shiftStart.getTime().toString());
                    localStorage.setItem('shiftChangedFrom', _autoToleranceFrom);
                    shiftChangeTime = shiftStart.getTime();
                    shiftChangedFrom = _autoToleranceFrom;
                }
            }
        }
    }

    const inToleranceOnLoad = _stillInTolerance || _autoTolerance;
    const toleranceFromOnLoad = _restoredChangedFrom || _autoToleranceFrom;

    let lastShift = (inToleranceOnLoad && toleranceFromOnLoad) ?
        toleranceFromOnLoad :
        getShift();

    let saveTimeout;
    let isLoaded = false;
    let cachedTotalActual = 0;
    let loadRequestId = 0;
    let totalNgFromDB = 0;
    let totalActualFromDB = 0;
    let shiftChangeTime = localStorage.getItem('shiftChangeTime') ?
        parseInt(localStorage.getItem('shiftChangeTime')) :
        null;
    let shiftChangedFrom = localStorage.getItem('shiftChangedFrom') ?? null;
    const _savedChangeTime = shiftChangeTime;
    const _inTolerance = _savedChangeTime !== null &&
        (Date.now() - _savedChangeTime) / 1000 / 60 < 30;
    const userRole = "<?= $_SESSION['role'] ?>";

    // ================= SHIFT TABS =================
    let activeViewShift = (inToleranceOnLoad && toleranceFromOnLoad) ?
        toleranceFromOnLoad :
        (getShift() ?? "Shift-1");

    function getCurrentShiftRealtime() {
        return getShift() ?? "Shift-1";
    }

    function initShiftTabs() {
        const dropdown = document.getElementById('shiftDropdown');
        if (!dropdown) return;

        // Saat toleransi: tampilkan shift lama di dropdown, user bisa ganti
        if (inToleranceOnLoad && toleranceFromOnLoad) {
            dropdown.value = toleranceFromOnLoad;
        } else {
            dropdown.value = getCurrentShiftRealtime();
        }

        dropdown.addEventListener('change', function() {
            activeViewShift = this.value;
            const currentShiftNow = getCurrentShiftRealtime();

            if (this.value === toleranceFromOnLoad || this.value === shiftChangedFrom) {
                // User pilih shift lama → pakai tanggal shift lama
                activeViewDate = getShiftDateFor(this.value, todayDate);
            } else {
                // User pilih shift baru → pakai tanggal hari ini
                const filterTgl = document.getElementById('filterTanggal')?.value;
                activeViewDate = (filterTgl && filterTgl !== todayDate) ? filterTgl : getShiftDate();
            }
            loadDrillingByShift(activeViewShift);
            updateShiftModeLabel();
        });
    }

    // ================= SET FORM READ ONLY =================
    function setFormReadOnly(isReadOnly) {
        document.querySelectorAll('#drillingForm input, #drillingForm select, #drillingForm textarea')
            .forEach(el => {
                if (el.type === 'hidden') return;
                if (el.classList.contains('jam')) return;

                if (isAlwaysReadonly(el)) {
                    el.readOnly = true;
                    el.disabled = false;
                    return;
                }

                const isValidasiCb = el.classList.contains('validasi');

                // ================= SUPERVISOR / AST. MANAGER =================
                if (userRole === 'supervisor' || userRole === 'ass_manager') {
                    if (el.classList.contains('iot-mesin')) {
                        el.disabled = false;
                        el.readOnly = true;
                        return;
                    }

                    if (el.classList.contains('target-mesin')) {
                        el.disabled = false;
                        el.readOnly = isReadOnly;
                        return;
                    }

                    const row = el.closest('tr');
                    const cb = row?.querySelector('.validasi');

                    if (cb && cb.checked) {
                        if (el.classList.contains('validasi')) {
                            el.disabled = false;
                        } else {
                            el.disabled = false;
                            if (el.tagName === 'INPUT' ||
                                el.tagName === 'TEXTAREA') el.readOnly = true;
                            if (el.tagName === 'SELECT' && !el.classList.contains('keterangan-select')) {
                                el.disabled = true;
                            }
                        }
                    } else {
                        el.disabled = false;
                        if (el.tagName === 'SELECT') el.disabled = false;
                        if ((el.tagName === 'INPUT' ||
                                el.tagName === 'TEXTAREA') && el.type !== 'checkbox') {
                            el.readOnly = false;
                        }
                        if (el.classList.contains('iot-mesin')) el.readOnly = true;
                    }
                    return;
                }

                // ================= SHIFT LAMA / READ ONLY =================
                if (isReadOnly) {
                    el.disabled = true;
                    return;
                }

                // ================= SHIFT AKTIF =================
                const row = el.closest('tr');

                if (el.classList.contains('keterangan-select')) {
                    const mesinIndex = parseInt(el.dataset.target?.split('-')[1]);
                    if (!isNaN(mesinIndex)) {
                        let allChecked = true;
                        for (let i = 0; i < 4; i++) {
                            const c = document.querySelectorAll('.validasi')[mesinIndex * 4 + i];
                            if (!c || !c.checked) {
                                allChecked = false;
                                break;
                            }
                        }
                        el.disabled = allChecked || isReadOnly;
                    }
                    return;
                }

                const isLockedRow = row && row.classList.contains('locked-row');
                if (!isLockedRow && !isValidasiCb) {
                    el.disabled = false;
                    if (el.classList.contains('iot-mesin')) {
                        el.readOnly = true;
                    } else if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.readOnly = false;
                    } else if (el.tagName === 'SELECT' && el.classList.contains('keterangan-select')) {
                        const mesinIndex = parseInt(el.dataset.target?.split('-')[1]);
                        if (!isNaN(mesinIndex)) {
                            let allChecked = true;
                            for (let i = 0; i < 4; i++) {
                                const c = document.querySelectorAll('.validasi')[mesinIndex * 4 + i];
                                if (!c || !c.checked) {
                                    allChecked = false;
                                    break;
                                }
                            }
                            el.disabled = allChecked;
                        } else {
                            el.disabled = false;
                        }
                    }
                }

                if (isValidasiCb) {
                    el.disabled = el.checked ? true : false;
                }
            });

        relockReadonlyFields();
    }

    /// ================= SYNC KETERANGAN PER MESIN =================
    function syncKeteranganPerMesin() {
        document.querySelectorAll('.keterangan-text').forEach((ketBox, mesinIndex) => {
            const start = mesinIndex * 4;
            const ketPerRow = {
                0: [],
                1: [],
                2: [],
                3: []
            };

            ketBox.querySelectorAll('.ket-tag').forEach(tag => {
                const classList = [...tag.classList];
                const ketClass = classList.find(c => /^ket-\d$/.test(c));
                const pos = ketClass ? parseInt(ketClass.replace('ket-', '')) : 0;
                const text = tag.innerText.replace('✖', '').trim();
                if (text) ketPerRow[pos].push(text);
            });

            for (let i = 0; i < 4; i++) {
                const input = document.querySelectorAll('.ket-input')[start + i];
                if (input) input.value = ketPerRow[i].join(',');
            }
        });
    }

    /// ================= AUTO RESIZE =================
    function autoResize(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    document.querySelectorAll('.cell-input').forEach(el => {
        el.addEventListener('input', function() {
            autoResize(this);
        });
        autoResize(el);
    });

    /// ================= BALANCE ROW =================
    function balanceRows() {
        document.querySelectorAll('.drilling-table').forEach(table => {
            const rows = table.querySelectorAll('tr');
            for (let i = 2; i < rows.length; i += 4) {
                let group = [rows[i], rows[i + 1], rows[i + 2], rows[i + 3]];
                let maxHeight = 0;
                group.forEach(r => {
                    if (r) {
                        r.style.height = 'auto';
                        if (r.offsetHeight > maxHeight) maxHeight = r.offsetHeight;
                    }
                });
                group.forEach(r => {
                    if (r) r.style.height = maxHeight + 'px';
                });
            }
        });
    }

    window.addEventListener('load', balanceRows);
    document.addEventListener('input', balanceRows);

    // ================= AUTOCOMPLETE PART NO =================
    function initAutocomplete() {
        const cfInputs = document.querySelectorAll('[name="cf_no[]"]');

        cfInputs.forEach((input, index) => {
            const td = input.parentElement;
            td.style.position = 'relative';

            const dropdown = document.createElement('div');
            dropdown.className = 'ac-dropdown';
            dropdown.style.cssText = `
                display: none; 
                position: absolute; 
                top: 100%; 
                left: 0;
                min-width: 300px; 
                background: #fff; 
                border: 1px solid #d1d5db;
                border-radius: 6px; 
                box-shadow: 0 4px 12px rgba(0,0,0,.15);
                z-index: 9999; 
                max-height: 220px; 
                overflow-y: auto;
            `;
            td.appendChild(dropdown);

            let debounceTimer;
            let activeIndex = -1;

            function setActiveItem(idx) {
                const items = dropdown.querySelectorAll('.ac-item');
                if (!items.length) return;
                items.forEach(item => {
                    item.style.background = '#fff';
                    item.removeAttribute('data-active');
                });
                if (idx < 0) idx = 0;
                if (idx >= items.length) idx = items.length - 1;
                activeIndex = idx;
                items[activeIndex].style.background = '#eff6ff';
                items[activeIndex].setAttribute('data-active', 'true');
                items[activeIndex].scrollIntoView({
                    block: 'nearest'
                });
            }

            function selectActiveItem() {
                const items = dropdown.querySelectorAll('.ac-item');
                if (activeIndex < 0 || activeIndex >= items.length) return;
                const item = items[activeIndex];

                input.value = item.dataset.partno;
                autoResize(input);

                const allCf = [...document.querySelectorAll('[name="cf_no[]"]')];
                const rowIndex = allCf.indexOf(input);

                const diameterInput = document.querySelectorAll('[name="diameter[]"]')[rowIndex];
                if (diameterInput) {
                    diameterInput.value = item.dataset.size ?? '';
                    autoResize(diameterInput);
                }

                const ukuranInput = document.querySelectorAll('[name="ukuran_bor[]"]')[rowIndex];
                if (ukuranInput) {
                    ukuranInput.value = item.dataset.mataborstandar ?? '';
                    autoResize(ukuranInput);
                }

                const customerInput = document.querySelectorAll('[name="customer[]"]')[rowIndex];
                if (customerInput) {
                    customerInput.value = item.dataset.customer ?? '';
                    autoResize(customerInput);
                }

                dropdown.style.display = 'none';
                activeIndex = -1;
                //clearTimeout(saveTimeout);
                // saveTimeout = setTimeout(() => saveDrilling(), 1000);
            }

            input.addEventListener('input', function() {
                const q = this.value.trim();
                clearTimeout(debounceTimer);
                activeIndex = -1;

                // partno dikosongkan, clear semua field auto-fill
                if (q.length === 0) {
                    dropdown.style.display = 'none';
                    const rowIndex = [...document.querySelectorAll('[name="cf_no[]"]')].indexOf(input);

                    const diameterInput = document.querySelectorAll('[name="diameter[]"]')[rowIndex];
                    if (diameterInput) {
                        diameterInput.value = '';
                        autoResize(diameterInput);
                    }

                    const ukuranInput = document.querySelectorAll('[name="ukuran_bor[]"]')[rowIndex];
                    if (ukuranInput) {
                        ukuranInput.value = '';
                        autoResize(ukuranInput);
                    }

                    const customerInput = document.querySelectorAll('[name="customer[]"]')[rowIndex];
                    if (customerInput) {
                        customerInput.value = '';
                        autoResize(customerInput);
                    }

                    //clearTimeout(saveTimeout);
                    // saveTimeout = setTimeout(() => saveDrilling(), 800);
                    return;
                }

                if (q.length < 1) {
                    dropdown.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`view/addon/controller/get_partno.php?q=${encodeURIComponent(q)}`);
                        if (!res.ok) return;
                        const json = await res.json();
                        const items = json.data || [];
                        if (!items.length) {
                            dropdown.style.display = 'none';
                            return;
                        }

                        dropdown.innerHTML = items.map(item => `
                            <div class="ac-item"
                                data-partno="${item.partno.replace(/"/g, '&quot;')}"
                                data-size="${item.size.replace(/"/g, '&quot;')}"
                                data-mataborstandar="${item.mataborstandar.replace(/"/g, '&quot;')}"
                                data-customer="${(item.customer || '').replace(/"/g, '&quot;')}"
                                style="padding:8px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #f1f5f9; line-height:1.4;">
                                <div><strong>${highlightMatch(item.partno, q)}</strong></div>
                                <div style="color:#94a3b8; font-size:11px;">
                                    ${item.customer ? item.customer + ' &nbsp;|&nbsp; ' : ''}
                                    Size: ${item.size || '-'} &nbsp;|&nbsp; Mata Bor: ${item.mataborstandar || '-'}
                                </div>
                            </div>
                        `).join('');

                        dropdown.querySelectorAll('.ac-item').forEach((item, i) => {
                            item.addEventListener('mouseenter', () => setActiveItem(i));
                            item.addEventListener('mouseleave', () => {
                                item.style.background = '#fff';
                                item.removeAttribute('data-active');
                                activeIndex = -1;
                            });
                            item.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                activeIndex = i;
                                selectActiveItem();
                            });
                        });
                        dropdown.style.display = 'block';
                        activeIndex = -1;
                    } catch (err) {
                        console.error('Autocomplete error:', err);
                    }
                }, 300);
            });

            input.addEventListener('keydown', function(e) {
                const items = dropdown.querySelectorAll('.ac-item');
                const isOpen = dropdown.style.display !== 'none' && items.length > 0;
                if (!isOpen) return;
                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        setActiveItem(activeIndex + 1);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        setActiveItem(activeIndex - 1);
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (activeIndex >= 0) selectActiveItem();
                        break;
                    case 'Escape':
                        dropdown.style.display = 'none';
                        activeIndex = -1;
                        break;
                    case 'Tab':
                        if (activeIndex >= 0) {
                            e.preventDefault();
                            selectActiveItem();
                        } else {
                            dropdown.style.display = 'none';
                        }
                        break;
                }
            });

            input.addEventListener('blur', () => {
                setTimeout(() => {
                    dropdown.style.display = 'none';
                    activeIndex = -1;
                }, 200);
            });
        });
    }

    // ================= ENTER TO NEXT FIELD =================
    function initEnterToNextField() {
        const selector = [
            '[name="cf_no[]"]',
            '[name="mata_bor[]"]',
            '[name="target_row[]"]',
            '[name="actual[]"]',
            '[name="ng[]"]',
            '[name="operator[]"]'
        ].join(', ');

        const allFields = [...document.querySelectorAll(selector)];
        allFields.forEach((field, idx) => {
            field.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                const td = this.closest('td');
                if (td) {
                    const dropdown = td.querySelector('.ac-dropdown');
                    if (dropdown && dropdown.style.display !== 'none') return;
                }
                e.preventDefault();
                let next = null;
                for (let i = idx + 1; i < allFields.length; i++) {
                    const f = allFields[i];
                    if (!f.disabled && !f.readOnly) {
                        next = f;
                        break;
                    }
                }
                if (next) {
                    next.focus();
                    if (next.tagName === 'TEXTAREA' || next.type === 'text') next.select();
                }
            });
        });
    }

    function highlightMatch(text, query) {
        const idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) return text;
        return text.substring(0, idx) +
            `<strong style="color:#1d4ed8;">${text.substring(idx, idx + query.length)}</strong>` +
            text.substring(idx + query.length);
    }

    /// ================= KETERANGAN =================
    document.querySelectorAll('.keterangan-select').forEach((select, mesinIndex) => {
        select.addEventListener('change', function() {
            const mesinId = this.dataset.target;
            const target = document.getElementById(mesinId);
            if (this.value === '') return;

            let text = this.value;
            if (this.value === 'Lainnya') {
                const custom = prompt("Masukkan keterangan:");
                if (!custom) return;
                text = custom;
            }

            const start = mesinIndex * 4;
            let rowPosition = -1;
            for (let i = 0; i < 4; i++) {
                const row = document.querySelectorAll('.validasi')[start + i]?.closest('tr');
                if (!row) continue;
                const cf = row.querySelector('[name="cf_no[]"]')?.value.trim();
                if (cf !== '') {
                    rowPosition = i;
                }
            }

            if (rowPosition === -1) rowPosition = 0;
            for (let i = 0; i < 4; i++) {
                const row = document.querySelectorAll('.validasi')[start + i]?.closest('tr');
                if (!row) continue;
                const cb = row.querySelector('.validasi');
                if (cb && !cb.checked) {
                    rowPosition = i;
                    break;
                }
            }

            const tag = document.createElement('div');
            tag.className = 'ket-tag ket-' + rowPosition;
            tag.innerHTML = `${text} <span class="remove">✖</span>`;
            tag.querySelector('.remove').onclick = function() {
                tag.remove();
                updateKeteranganToRows(mesinId);
                //saveDrilling();
            };

            // ================= INSERT SESUAI URUTAN ROW =================
            const existingTags = [...target.querySelectorAll('.ket-tag')];
            let inserted = false;
            for (const existing of existingTags) {
                const existingClass = [...existing.classList]
                    .find(c => /^ket-\d$/.test(c));
                const existingPos = existingClass ?
                    parseInt(existingClass.replace('ket-', '')) :
                    0;
                if (rowPosition < existingPos) {
                    target.insertBefore(tag, existing);
                    inserted = true;
                    break;
                }
            }
            if (!inserted) {
                target.appendChild(tag);
            }
            updateKeteranganToRows(mesinId);
            this.value = '';
            // saveDrilling();
        });
    });

    /// ================= UPDATE KETERANGAN TO ROWS =================
    function updateKeteranganToRows(mesinId) {
        const ketBox = document.getElementById(mesinId);
        const mIndex = parseInt(mesinId.split('-')[1]);
        const start = mIndex * 4;
        const ketPerRow = {
            0: [],
            1: [],
            2: [],
            3: []
        };

        ketBox.querySelectorAll('.ket-tag').forEach(tag => {
            const classList = [...tag.classList];
            const ketClass = classList.find(c => /^ket-\d$/.test(c));
            const pos = ketClass ? parseInt(ketClass.replace('ket-', '')) : 0;
            const text = tag.innerText.replace('✖', '').trim();
            if (text) ketPerRow[pos].push(text);
        });

        for (let i = 0; i < 4; i++) {
            const input = document.querySelectorAll('.ket-input')[start + i];
            if (input) input.value = ketPerRow[i].join(',');
        }
    }

    /// ================= VALIDASI =================
    document.querySelectorAll('.validasi').forEach(cb => {
        cb.addEventListener('change', async function(e) {
            const role = "<?= strtolower($_SESSION['role']) ?>";
            const row = this.closest('tr');

            // ================= UNCHECK (BATALKAN VALIDASI) =================
            if (!this.checked) {
                if (!['supervisor', 'ass_manager'].includes(role)) {
                    alert("Hanya Supervisor / Ast. Manager yang bisa membatalkan validasi.");
                    this.checked = true;
                    return;
                }

                row.classList.remove('locked-row');

                row.querySelectorAll('input, textarea').forEach(el => {
                    if (el.type === 'hidden') return;
                    if (el.classList.contains('jam') || el.classList.contains('iot-mesin')) return;
                    if (isAlwaysReadonly(el)) {
                        el.readOnly = true;
                    } else {
                        el.readOnly = false;
                    }
                });
                this.disabled = false;

                const allRows = document.querySelectorAll('#drillingForm tbody tr, #drillingForm table tr');
                const dataRows = [...allRows].filter(tr => tr.querySelector('.validasi'));
                const rowIndex = dataRows.indexOf(row);
                const mIndex = Math.floor(rowIndex / 4);
                const rowPos = rowIndex % 4;

                const select = document.querySelector(`[data-target="ket-${mIndex}"]`);
                if (select) select.disabled = false;

                const ketBox = document.getElementById('ket-' + mIndex);
                if (ketBox) {
                    ketBox.querySelectorAll('.ket-tag.ket-' + rowPos).forEach(tag => {
                        if (!tag.querySelector('.remove')) {
                            tag.innerHTML += ` <span class="remove">✖</span>`;
                            const mesinIdLocal = 'ket-' + mIndex;
                            tag.querySelector('.remove').onclick = function() {
                                tag.remove();
                                updateKeteranganToRows(mesinIdLocal);
                                setTimeout(() => saveDrilling(), 50);
                            };
                        }
                    });
                }

                await saveDrilling();
                return;
            }

            // ================= CHECK (VALIDASI) =================
            const cf = row.querySelector('[name="cf_no[]"]').value.trim();
            const customer = row.querySelector('[name="customer[]"]').value.trim();
            const diameter = row.querySelector('[name="diameter[]"]').value.trim();
            const mataBor = row.querySelector('[name="mata_bor[]"]').value.trim();
            const ukuranBor = row.querySelector('[name="ukuran_bor[]"]').value.trim();
            const actual = row.querySelector('[name="actual[]"]').value.trim();
            const operator = row.querySelector('[name="operator[]"]').value.trim();

            if (!cf || !actual || !operator) {
                alert("Semua data pada row ini harus diisi sebelum validasi!");
                this.checked = false;
                return;
            }

            syncKeteranganPerMesin();

            const jam = row.querySelector('.jam');
            if (jam) {
                const d = new Date();
                jam.value = d.getHours().toString().padStart(2, '0') + ':' +
                    d.getMinutes().toString().padStart(2, '0') + ':' +
                    d.getSeconds().toString().padStart(2, '0');
            }

            syncIotToRows();
            await saveDrilling();
            lockRowByElement(row);
            this.disabled = (role !== 'supervisor' && role !== 'ass_manager');

            const allRows2 = document.querySelectorAll('#drillingForm tbody tr, #drillingForm table tr');
            const dataRows2 = [...allRows2].filter(tr => tr.querySelector('.validasi'));
            const rowIndex2 = dataRows2.indexOf(row);
            const mIndex2 = Math.floor(rowIndex2 / 4);

            const ketBox2 = document.getElementById('ket-' + mIndex2);
            if (ketBox2) {
                ketBox2.querySelectorAll('.ket-tag .remove').forEach(btn => btn.remove());
            }

            const start = mIndex2 * 4;
            let allChecked = true;
            for (let i = 0; i < 4; i++) {
                const c = document.querySelectorAll('.validasi')[start + i];
                if (!c || !c.checked) {
                    allChecked = false;
                    break;
                }
            }
            const select2 = document.querySelector(`[data-target="ket-${mIndex2}"]`);
            if (select2) select2.disabled = allChecked;
        });
    });

    /// ================= LOCK ROW =================
    function lockRowByElement(row) {
        row.querySelectorAll('input, textarea').forEach(el => {
            if (el.type === 'hidden') return;
            el.readOnly = true;
        });
        row.classList.add('locked-row');
    }

    function checkMesinFullValidated(row) {
        let rows = [],
            current = row;
        for (let i = 0; i < 4; i++) {
            if (!current) break;
            rows.push(current);
            current = current.nextElementSibling;
        }
        if (rows.every(r => {
                const cb = r.querySelector('.validasi');
                return cb && cb.checked;
            })) {
            rows.forEach(r => r.classList.add('full-locked'));
        }
    }

    /// ================= TOTAL =================
    function hitungTotal() {
        let totalIot = 0,
            totalTarget = 0,
            totalActual = 0,
            totalNg = 0;
        document.querySelectorAll('.iot-mesin').forEach(e => totalIot += Number(e.value) || 0);
        document.querySelectorAll('.target-row').forEach(e => totalTarget += Number(e.value) || 0);
        document.querySelectorAll('.actual').forEach(e => totalActual += Number(e.value) || 0);
        document.querySelectorAll('.ng').forEach(e => totalNg += Number(e.value) || 0);
        document.getElementById('totalIot').innerText = totalIot;
        document.getElementById('totalTarget').innerText = totalTarget;
        document.getElementById('totalActual').innerText = totalActual;
        document.getElementById('totalNg').innerText = totalNg;
    }

    document.querySelectorAll('.iot-mesin, .target_row, .actual, .ng').forEach(el => el.addEventListener('input', hitungTotal));

    // ================= SYNC IOT FROM ACTUAL (tanpa save) =================
    function syncIotFromActualNoSave() {
        const mesinCount = 6;
        let totalIotBaru = 0;

        for (let mIndex = 0; mIndex < mesinCount; mIndex++) {
            const start = mIndex * 4;
            let totalActualMesin = 0;

            for (let i = 0; i < 4; i++) {
                const actualEl = document.querySelectorAll('[name="actual[]"]')[start + i];
                totalActualMesin += Number(actualEl?.value) || 0;
            }

            const iotEl = document.getElementById('iot-' + mIndex);
            if (iotEl) iotEl.value = totalActualMesin;
            totalIotBaru += totalActualMesin;

            for (let i = 0; i < 4; i++) {
                const hidden = document.querySelectorAll('.iot-hidden')[start + i];
                if (hidden) hidden.value = totalActualMesin;
            }
        }

        document.getElementById('totalIot').innerText = totalIotBaru;

        const totalActualNow = getTotalActualFromFields();
        cachedTotalActual = totalActualNow;
        updateSummary(totalActualNow);
    }

    // ================= SYNC IOT FROM ACTUAL =================
    function syncIotFromActual() {
        syncIotFromActualNoSave();
        const mesinCount = 6;

        let totalIotBaru = 0;

        for (let mIndex = 0; mIndex < mesinCount; mIndex++) {
            const start = mIndex * 4;
            let totalActualMesin = 0;

            for (let i = 0; i < 4; i++) {
                const actualEl = document.querySelectorAll('[name="actual[]"]')[start + i];
                totalActualMesin += Number(actualEl?.value) || 0;
            }

            // Set IOT mesin = total actual mesin
            const iotEl = document.getElementById('iot-' + mIndex);
            if (iotEl) iotEl.value = totalActualMesin;

            totalIotBaru += totalActualMesin;

            for (let i = 0; i < 4; i++) {
                const hidden = document.querySelectorAll('.iot-hidden')[start + i];
                if (hidden) hidden.value = totalActualMesin;
            }
        }

        // Update total IOT di tabel
        document.getElementById('totalIot').innerText = totalIotBaru;

        const totalActualNow = getTotalActualFromFields();
        cachedTotalActual = totalActualNow;
        updateSummary(totalActualNow);

        //clearTimeout(saveTimeout);
        //saveTimeout = setTimeout(() => saveDrilling(), 500);

        console.log('IOT disinkronkan dari actual:', totalIotBaru);
    }

    /// ================= SHIFT =================
    function getShift() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(" ")[0];
        if (day === 0) return null;
        if (day === 6) {
            if (time >= "07:30:00" && time < "12:30:00") return "Shift-1";
            if (time >= "12:30:00" && time < "17:30:00") return "Shift-2";
            if (time >= "17:30:00" && time < "23:30:00") return "Shift-3";
            return null;
        }
        if (time >= "07:30:00" && time < "15:30:00") return "Shift-1";
        if (time >= "15:30:00" && time < "23:30:00") return "Shift-2";
        if (time >= "23:30:00" || time < "07:30:00") return "Shift-3";
        return null;
    }

    function getShiftDate() {
        const now = new Date();
        const time = now.toTimeString().split(" ")[0];

        // Jika jam 00:00 - 07:29 → masih Shift-3 dari hari sebelumnya
        if (time >= "00:00:00" && time < "07:30:00") {
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            return yesterday.toISOString().split('T')[0];
        }

        return now.toISOString().split('T')[0];
    }

    // Tanggal yang dipakai untuk save/load — Shift-3 pakai tanggal MULAI shift
    function getShiftDateFor(shiftName, referenceDate) {
        // Shift-3 yang mulai jam 23:30 → data tersimpan di tanggal mulai (kemarin jika sekarang dini hari)
        // Jika shift yang ditoleransi adalah Shift-3, sekarang jam 00:00-08:00 maka tanggal shift lama = kemarin
        const now = new Date();
        const time = now.toTimeString().split(" ")[0];

        if (shiftName === 'Shift-3' && time >= "00:00:00" && time < "08:00:00") {
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            return yesterday.toISOString().split('T')[0];
        }
        return referenceDate ?? now.toISOString().split('T')[0];
    }

    /// ================= SAVE =================
    async function saveDrilling() {
        const canEditPast = userRole === 'supervisor' || userRole === 'ass_manager';
        if (!canEditPast) {
            const latestShift = getShift();
            if (latestShift === null) return;
            const inTolerance = shiftChangeTime !== null &&
                (Date.now() - shiftChangeTime) / 1000 / 60 < 30;
            if (!inTolerance) {
                if (activeViewShift !== latestShift) return;
                if (activeViewDate !== todayDate) return;
            }
        }

        const cfInputs = document.querySelectorAll('[name="cf_no[]"]');
        const formData = new FormData();
        formData.append("shift", activeViewShift);
        formData.append("tanggal", activeViewDate);

        document.querySelectorAll('.approve').forEach((cb, i) => {
            formData.append(`approve[${i}]`, cb.checked ? "1" : "0");
        });

        cfInputs.forEach((cf, index) => {
            const actual = document.querySelectorAll('[name="actual[]"]')[index].value;
            const operator = document.querySelectorAll('[name="operator[]"]')[index].value;
            const validasi = document.querySelectorAll('.validasi')[index];
            const isChecked = validasi && validasi.checked;

            const ng = document.querySelectorAll('[name="ng[]"]')[index].value;
            const mataBor = document.querySelectorAll('[name="mata_bor[]"]')[index].value;

            const hasData = cf.value.trim() !== "" ||
                actual.trim() !== "" ||
                operator.trim() !== "" ||
                ng.trim() !== "" ||
                mataBor.trim() !== "";

            const rowEl = validasi?.closest('tr');
            const wasLoaded = rowEl?.dataset.wasLoaded === '1';

            const shouldSend = isChecked || hasData || wasLoaded;

            if (shouldSend) {
                formData.append("row_id[]", index);
                formData.append("mesin[]", document.querySelectorAll('[name="mesin[]"]')[index].value);
                formData.append("cf_no[]", cf.value);
                formData.append("customer[]", document.querySelectorAll('[name="customer[]"]')[index].value);
                formData.append("diameter[]", document.querySelectorAll('[name="diameter[]"]')[index].value);
                formData.append("mata_bor[]", document.querySelectorAll('[name="mata_bor[]"]')[index].value);
                formData.append("ukuran_bor[]", document.querySelectorAll('[name="ukuran_bor[]"]')[index].value);
                formData.append("jam[]", document.querySelectorAll('[name="jam[]"]')[index].value);
                formData.append("iot_hidden[]", document.querySelectorAll('[name="iot_hidden[]"]')[index].value);
                formData.append('target_row[]', document.querySelectorAll('.target-row')[index]?.value ?? '');
                formData.append("actual[]", actual);
                formData.append("ng[]", document.querySelectorAll('[name="ng[]"]')[index].value);
                formData.append("operator[]", document.querySelectorAll('[name="operator[]"]')[index].value);
                formData.append("keterangan[]", document.querySelectorAll('.ket-input')[index].value);
                formData.append(`validasi[${index}]`, isChecked ? "1" : "0");
            }
        });

        if (isSaving) {
            pendingSave = true;
            return;
        }

        try {
            isSaving = true;
            pendingSave = false;
            const res = await fetch("view/addon/proses/save_drilling.php", {
                method: "POST",
                body: formData
            });
            const text = await res.text();
            if (!res.ok) {
                try {
                    const e = JSON.parse(text);
                    console.error(`❌ SAVE ERROR [${e.file} line ${e.line}]: ${e.message}`);
                } catch {
                    console.error(`❌ SAVE ERROR (HTTP ${res.status}):`, text);
                }
            } else {
                console.log("SAVE RESULT:", text);
            }
        } catch (err) {
            console.error("Fetch Error:", err);
        } finally {
            isSaving = false;
            if (pendingSave) {
                pendingSave = false;
                setTimeout(() => saveDrilling(), 100);
            }
        }
    }

    /// ================= AUTO SAVE INPUT =================
    // ["input", "change"].forEach(evt => {
    // document.addEventListener(evt, function(e) {
    //   if (e.target.closest("#drillingForm") && isLoaded) {
    //     clearTimeout(saveTimeout);
    //   saveTimeout = setTimeout(() => saveDrilling(), 1000);
    // }
    // });
    // }); 

    /// ================= RESET FORM =================
    function resetFormDrilling() {
        const form = document.getElementById("drillingForm");

        form.querySelectorAll("input[type=text], input[type=number]").forEach(el => {
            if (el.classList.contains('iot-mesin')) return;
            el.value = "";
            el.readOnly = false;
        });

        form.querySelectorAll('.target-mesin').forEach(el => {
            el.value = '';
            el.readOnly = false;
        });
        document.querySelectorAll('.target-hidden').forEach(el => el.value = '');
        document.getElementById('sumTarget').innerText = '0';

        form.querySelectorAll("textarea").forEach(el => {
            el.value = "";
            el.readOnly = false;
            el.style.height = 'auto';
        });
        form.querySelectorAll(".jam").forEach(j => j.value = "");
        form.querySelectorAll(".keterangan-select").forEach(el => el.disabled = false);
        document.querySelectorAll(".keterangan-text").forEach(div => div.innerHTML = "");
        document.querySelectorAll('.ket-input').forEach(el => el.value = "");
        document.querySelectorAll('#drillingForm tr').forEach(tr => {
            tr.classList.remove('locked-row', 'full-locked');
            delete tr.dataset.wasLoaded;
        });

        operatorCounter = {};
        document.getElementById("totalIot").innerText = 0;
        document.getElementById("totalTarget").innerText = 0;
        document.getElementById("totalActual").innerText = 0;
        document.getElementById("totalNg").innerText = 0;
        document.getElementById('sumTarget').innerText = '0';
        document.querySelectorAll('.target-row').forEach(inp => inp.value = '');
        document.getElementById("sumActual").innerText = 0;
        document.getElementById("sumPercent").innerText = '–';
        document.getElementById("sumPercent").style.color = '#9ca3af';

        relockReadonlyFields();
        console.log("FORM RESET SHIFT");
    }

    /// ================= SHIFT CHANGE =================
    function checkShiftChange() {
        const newShift = getShift();
        if (newShift === null) {
            const label = document.getElementById('shift-mode-label');
            if (label) {
                label.innerHTML = `<span class="view-only-label">Tidak ada shift aktif hari ini</span>`;
            }
            setFormReadOnly(true);
            return;
        }

        if (newShift !== lastShift) {
            // Toleransi 30 menit setelah shift berganti
            if (!shiftChangeTime) {
                shiftChangeTime = Date.now();
                shiftChangedFrom = lastShift; // shift yang sedang berjalan sebelum berganti ke shift baru
                localStorage.setItem('shiftChangeTime', shiftChangeTime);
                localStorage.setItem('shiftChangedFrom', shiftChangedFrom);
            }

            const elapsedMinutes = (Date.now() - shiftChangeTime) / 1000 / 60;

            if (elapsedMinutes < 30) {
                const label = document.getElementById('shift-mode-label');
                const sisaMenit = Math.ceil(30 - elapsedMinutes);
                if (label) {
                    label.innerHTML = `
                    <span class="view-only-label" style="color:#ea580c;">
                        ⚠️ Shift berganti ke ${newShift} — Toleransi pengisian: ${sisaMenit} menit lagi
                    </span>
                `;
                }
                setFormReadOnly(false);
                return;
            }

            // Toleransi habis, baru reset dan pindah shift
            lastShift = newShift;
            localStorage.removeItem(getTargetStorageKey());
            activeViewShift = newShift;
            activeViewDate = getShiftDateFor();
            shiftChangeTime = null;
            shiftChangedFrom = null;
            localStorage.removeItem('shiftChangeTime');
            localStorage.removeItem('shiftChangedFrom');

            activeViewDate = getShiftDate();

            resetFormDrilling();

            cachedTotalActual = 0;

            const dropdown = document.getElementById('shiftDropdown');
            if (dropdown) dropdown.value = newShift;

            loadDrillingByShift(newShift);
        } else {
            if (shiftChangeTime !== null) {
                shiftChangeTime = null;
                shiftChangedFrom = null;
                localStorage.removeItem('shiftChangeTime');
                localStorage.removeItem('shiftChangedFrom');
            }
        }
    }

    setInterval(checkShiftChange, 1000);

    /// ================= BACKUP SAVE =================
    //setInterval(() => {
    //  if (isLoaded) saveDrilling();
    // }, 30000);

    /// ================= LOAD DATA DRILLING =================
    function loadDrilling() {
        const shift = getShift();
        if (!shift) {
            const label = document.getElementById('shift-mode-label');
            if (label) label.innerHTML = `<span class="view-only-label">Tidak ada shift aktif hari ini</span>`;
            return;
        }
        loadDrillingByShift(activeViewShift);
    }

    /// ================= FILTER TANGGAL =================
    const todayDate = getShiftDate();
    let activeViewDate = (inToleranceOnLoad && toleranceFromOnLoad) ?
        getShiftDateFor(toleranceFromOnLoad, todayDate) :
        todayDate;


    function initFilterTanggal() {
        const input = document.getElementById('filterTanggal');
        if (!input) return;
        input.value = todayDate;
        input.max = todayDate;
        input.addEventListener('change', function() {
            activeViewDate = this.value;
            const dropdown = document.getElementById('shiftDropdown');
            if (dropdown) activeViewShift = dropdown.value;
            loadDrillingByShift(activeViewShift);
            updateShiftModeLabel();
        });
    }

    /// ================= UPDATE SHIFT LABEL =================
    function updateShiftModeLabel() {
        const latestShift = getShift();
        const label = document.getElementById('shift-mode-label');
        const isToday = activeViewDate === todayDate;
        const isCurrent = activeViewShift === latestShift;

        const inTolerance = shiftChangeTime !== null &&
            (Date.now() - shiftChangeTime) / 1000 / 60 < 30 ||
            (inToleranceOnLoad && toleranceFromOnLoad !== null);

        const activeTolerantShift = shiftChangedFrom ?? toleranceFromOnLoad;

        if (inTolerance && activeTolerantShift) {
            const elapsed = getToleranceElapsedMinutes();
            const sisaMenit = Math.ceil(30 - elapsed);

            if (activeViewShift === activeTolerantShift) {
                // Masih di shift lama → label toleransi, form bisa diisi
                label.innerHTML = `
                    <span class="view-only-label" style="color:#ea580c;">
                        ⚠️ Shift berganti ke ${latestShift} — Toleransi pengisian: ${sisaMenit} menit lagi
                        &nbsp;|&nbsp; <strong>Sedang lihat: ${activeViewShift}</strong>
                    </span>
                `;
                setFormReadOnly(false);
            } else {
                // User sudah pindah ke shift baru → label shift baru aktif
                label.innerHTML = `<span class="active-shift-label">${activeViewShift} - Sedang Aktif</span>`;
                setFormReadOnly(false);
            }
            return;
        }

        if (!isToday || !isCurrent) {
            label.innerHTML = `<span class="view-only-label">${activeViewShift} - ${formatTanggal(activeViewDate)} (read-only)</span>`;
        } else {
            label.innerHTML = `<span class="active-shift-label">${latestShift} - Sedang Aktif</span>`;
        }
        setFormReadOnly(!isToday || !isCurrent);
    }

    function getToleranceElapsedMinutes() {
        if (shiftChangeTime !== null) {
            return (Date.now() - shiftChangeTime) / 1000 / 60;
        }
        const shiftStart = getShiftStartTime();
        if (shiftStart) {
            return (Date.now() - shiftStart.getTime()) / 1000 / 60;
        }
        return 30;
    }

    function formatTanggal(dateStr) {
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    function getTotalActualFromFields() {
        let total = 0;
        document.querySelectorAll('[name="actual[]"]').forEach(el => {
            total += Number(el.value) || 0;
        });
        return total;
    }

    /// ================= LOAD DRILLING BY SHIFT =================
    function loadDrillingByShift(shift) {
        if (!shift) return;

        const tanggal = activeViewDate;
        const myRequestId = ++loadRequestId;

        totalNgFromDB = 0;
        totalActualFromDB = 0;

        clearFormDisplay();

        const isPastView = tanggal !== todayDate || shift !== getCurrentShiftRealtime();
        Promise.all([
            isPastView ? Promise.resolve(0) : loadIotMesin(tanggal, shift),
            fetch(`view/addon/proses/load_drilling.php?tanggal=${tanggal}&shift=${shift}`)
            .then(async r => {
                if (!r.ok) {
                    const errText = await r.text();
                    try {
                        const e = JSON.parse(errText);
                        console.error(`❌ LOAD ERROR [${e.file} line ${e.line}]: ${e.message}`);
                    } catch {
                        console.error(`❌ LOAD ERROR (HTTP ${r.status}):`, errText);
                    }
                    throw new Error('load_drilling failed');
                }
                return r.json();
            })
        ]).then(([totalIotFromAchievement, res]) => {

            if (myRequestId !== loadRequestId) return;

            const data = res.data || [];

            if (data.length === 0) {
                document.getElementById('totalIot').innerText = totalIotFromAchievement;
                document.getElementById('totalActual').innerText = 0;
                document.getElementById('totalNg').innerText = 0;

                hitungTotalTarget();
                restoreApproveStatus(res.approve);
                const totalActualNow = getTotalActualFromFields();
                cachedTotalActual = totalActualNow;
                updateSummary(totalActualNow);
                updateShiftModeLabel();

                const codeEl = document.getElementById('printCodeInfo');
                if (codeEl) {
                    codeEl.innerHTML = `EPI-FORM/PROD/01/11/03<br>REV &nbsp;&nbsp;: 0<br>DATE : 09.09.2021`;
                }

                const infoEl = document.getElementById('printHeaderInfo');
                if (infoEl) {
                    const [y, m, d] = activeViewDate.split('-');
                    infoEl.innerHTML = `Tanggal: ${d}/${m}/${y}<br>${activeViewShift}`;
                }

                if (res && res.status == 1) lockAllForm();
                isLoaded = true;
                return;
            }

            const mesinMapping = {
                'DR01': 0,
                'DR02': 1,
                'DR03': 2,
                'DR04': 3,
                'DR05': 4,
                'BOR ULANG': 5
            };

            const ketPerMesin = {};

            data.forEach(row => {
                const absoluteIndex = parseInt(row.row_id);
                const mIndex = mesinMapping[row.mesin];
                if (mIndex === undefined) return;

                totalNgFromDB += Number(row.ng) || 0;
                totalActualFromDB += Number(row.actual) || 0;

                const fill = (sel, val, resize = false) => {
                    const el = document.querySelectorAll(sel)[absoluteIndex];
                    if (el) {
                        el.value = val || '';
                        if (resize) autoResize(el);
                    }
                };

                fill('[name="cf_no[]"]', row.cf_no, true);
                fill('[name="customer[]"]', row.customer, true);
                fill('[name="diameter[]"]', row.diameter_cf, true);
                fill('[name="mata_bor[]"]', row.jumlah_mata_bor, false);
                fill('[name="ukuran_bor[]"]', row.ukuran_bor, true);
                fill('[name="jam[]"]', row.jam, false);
                fill('[name="actual[]"]', row.actual, false);
                fill('[name="ng[]"]', row.ng, false);
                fill('[name="operator[]"]', row.operator, false);

                const targetRowEl = document.querySelectorAll('.target-row')[absoluteIndex];
                if (targetRowEl) targetRowEl.value = Number(row.target) > 0 ? row.target : '';

                const checkbox = document.querySelectorAll('.validasi')[absoluteIndex];
                if (checkbox) {
                    checkbox.checked = (parseInt(row.validasi) === 1);
                    if (checkbox.checked) lockRowByIndex(absoluteIndex);

                    const tr = checkbox.closest('tr');
                    if (tr) tr.dataset.wasLoaded = '1';
                }

                if (row.keterangan && row.keterangan.trim() !== '') {
                    if (!ketPerMesin[mIndex]) ketPerMesin[mIndex] = [];
                    const pos = absoluteIndex % 4;
                    row.keterangan.split(',').forEach(entry => {
                        entry = entry.trim();
                        if (entry) ketPerMesin[mIndex].push({
                            pos,
                            text: entry,
                            validasi: parseInt(row.validasi)
                        });
                    });
                }
            });

            Object.entries(ketPerMesin).forEach(([mIndex, tags]) => {
                const ketBox = document.getElementById("ket-" + mIndex);
                if (!ketBox) return;
                ketBox.innerHTML = "";

                tags.forEach(({
                    pos,
                    text,
                    validasi
                }) => {
                    const div = document.createElement('div');
                    div.className = 'ket-tag ket-' + pos;
                    const isPast = activeViewShift !== getCurrentShiftRealtime() || activeViewDate !== todayDate;
                    const isValidated = validasi === 1;

                    if (isPast || isValidated) {
                        div.innerHTML = text;
                    } else {
                        div.innerHTML = `${text} <span class="remove">✖</span>`;
                        //div.querySelector('.remove').onclick = function() {
                        //  div.remove();
                        // updateKeteranganToRows('ket-' + mIndex);
                        // saveDrilling();
                        //};
                    }
                    ketBox.appendChild(div);
                });

                const start = parseInt(mIndex) * 4;
                let allValidated = true;
                for (let i = 0; i < 4; i++) {
                    const cb = document.querySelectorAll('.validasi')[start + i];
                    if (!cb || !cb.checked) {
                        allValidated = false;
                        break;
                    }
                }

                const isPast = activeViewShift !== getCurrentShiftRealtime() || activeViewDate !== todayDate;
                const select = document.querySelector(`[data-target="ket-${mIndex}"]`);
                if (!select) return;

                if (userRole === 'supervisor' || userRole === 'ass_manager') {
                    let allChecked = true;
                    for (let i = 0; i < 4; i++) {
                        const cb = document.querySelectorAll('.validasi')[start + i];
                        if (!cb || !cb.checked) {
                            allChecked = false;
                            break;
                        }
                    }
                    select.disabled = allChecked || isPast;
                } else {
                    select.disabled = allValidated || isPast;
                }
            });

            document.getElementById('totalIot').innerText = totalIotFromAchievement;
            document.getElementById('totalActual').innerText = totalActualFromDB;
            document.getElementById('totalNg').innerText = totalNgFromDB;

            restoreApproveStatus(res.approve);
            const totalActualNow = getTotalActualFromFields();
            cachedTotalActual = totalActualNow;
            updateSummary(totalActualNow);
            updateShiftModeLabel();
            hitungTotalTarget();


            const codeEl = document.getElementById('printCodeInfo');
            if (codeEl) {
                codeEl.innerHTML = `EPI-FORM/PROD/01/11/03<br>REV &nbsp;&nbsp;: 0<br>DATE : 09.09.2021`;
            }

            const infoEl = document.getElementById('printHeaderInfo');
            if (infoEl) {
                const [y, m, d] = activeViewDate.split('-');
                infoEl.innerHTML = `Tanggal: ${d}/${m}/${y}<br>${activeViewShift}`;
            }

            if (res.status == 1) lockAllForm();
            isLoaded = true;

            relockReadonlyFields();

        }).catch(err => console.error("Gagal memuat:", err));

        document.querySelectorAll('.iot-mesin').forEach(el => {
            el.setAttribute('style',
                (el.getAttribute('style') || '') +
                '; text-align: center !important;'
            );
        });
    }

    // ================= CLEAR FORM DISPLAY =================
    function clearFormDisplay() {
        isLoaded = false;
        cachedTotalActual = 0;
        totalActualFromDB = 0;
        totalNgFromDB = 0;

        document.querySelectorAll('[name="cf_no[]"], [name="customer[]"], [name="diameter[]"], [name="mata_bor[]"], [name="ukuran_bor[]"], [name="operator[]"]')
            .forEach(el => {
                el.value = '';
                if (!isAlwaysReadonly(el)) el.readOnly = false;
                if (el.tagName === 'TEXTAREA') el.style.height = 'auto';
            });

        document.querySelectorAll('.target-row').forEach(el => {
            el.value = '';
            el.readOnly = false;
        });
        document.querySelectorAll('.target-hidden').forEach(el => el.value = '');
        document.getElementById('sumTarget').innerText = '0';

        document.querySelectorAll('[name="actual[]"], [name="ng[]"]').forEach(el => {
            el.value = '';
            el.readOnly = false;
        });
        document.querySelectorAll('.jam').forEach(el => el.value = '');
        document.querySelectorAll('.validasi').forEach(cb => {
            cb.checked = false;
            cb.disabled = false;
        });
        document.querySelectorAll('.keterangan-text').forEach(div => div.innerHTML = '');
        document.querySelectorAll('.ket-input').forEach(el => el.value = '');
        document.querySelectorAll('.keterangan-select').forEach(el => el.disabled = false);
        document.querySelectorAll('#drillingForm tr').forEach(tr => {
            tr.classList.remove('locked-row', 'full-locked');
            delete tr.dataset.wasLoaded;
        });

        document.getElementById('sumTarget').value = '';
        document.getElementById('sumActual').innerText = 0;
        document.getElementById('sumPercent').innerText = '–';
        document.getElementById('sumPercent').style.color = '#9ca3af';

        relockReadonlyFields();
    }

    // ================= LOCK 1 ROW =================
    function lockRowByIndex(index) {
        const fields = [
            '[name="cf_no[]"]',
            '[name="customer[]"]',
            '[name="diameter[]"]',
            '[name="mata_bor[]"]',
            '[name="ukuran_bor[]"]',
            '[name="actual[]"]',
            '[name="ng[]"]',
            '[name="operator[]"]'
        ];
        fields.forEach(sel => {
            const el = document.querySelectorAll(sel)[index];
            if (el) el.readOnly = true;
        });

        const cb = document.querySelectorAll('.validasi')[index];
        if (cb) cb.disabled = (userRole !== 'supervisor' && userRole !== 'ass_manager');

        const allRows = document.querySelectorAll('#drillingForm tbody tr, #drillingForm table tr');
        const dataRows = [...allRows].filter(tr => tr.querySelector('.validasi'));
        if (dataRows[index]) dataRows[index].classList.add('locked-row');
    }

    // ================= LOCK SEMUA FORM =================
    function lockAllForm() {
        document.querySelectorAll('#drillingForm input, #drillingForm select, #drillingForm textarea')
            .forEach(el => {
                el.disabled = true;
            });
        console.log("FORM DI LOCK (APPROVED)");
    }

    /// ================= VALIDASI ANGKA =================
    document.querySelectorAll('.actual, .ng').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
            if (this.value > 999999) this.value = 999999;
        });
    });

    /// ================= LOAD IOT =================
    async function loadIotMesin(tanggal = null, shift = null) {
        const tgl = tanggal ?? activeViewDate;
        const sft = shift ?? activeViewShift;

        try {
            const res = await fetch(`view/addon/controller/get_achievement.php?date=${tgl}&shift=${sft}`);
            const data = await res.json();

            let mesinMap = {
                "DR01": 0,
                "DR02": 0,
                "DR03": 0,
                "DR04": 0,
                "DR05": 0
            };
            let totalIot = 0;

            data.data.forEach(row => {
                if (mesinMap[row.mc] !== undefined) {
                    mesinMap[row.mc] += row.iot;
                    totalIot += row.iot;
                }
            });

            const mapping = ['DR01', 'DR02', 'DR03', 'DR04', 'DR05', 'BOR ULANG'];
            mapping.forEach((mc, index) => {
                const el = document.getElementById('iot-' + index);
                if (el) el.value = mesinMap[mc] || 0;
            });

            document.querySelectorAll('.iot-mesin').forEach(el => el.dispatchEvent(new Event('input')));
            syncIotToRows();
            return totalIot;

        } catch (err) {
            console.error("Gagal load IOT:", err);
            return 0;
        }
    }

    setInterval(async () => {
        const realtimeShift = getShift();
        const isApproved = [...document.querySelectorAll('.approve')].some(cb => cb.checked);
        if (isApproved) return;

        if (activeViewDate === todayDate && activeViewShift === realtimeShift && isLoaded && realtimeShift !== null) {
            const totalIot = await loadIotMesin();
            document.getElementById('totalIot').innerText = totalIot;

            const totalActualNow = getTotalActualFromFields();
            cachedTotalActual = totalActualNow;
            updateSummary(totalActualNow);
        }
    }, 5000);

    /// ================= SUMMARY PRODUKSI =================
    function updateSummary(totalActualParam = null) {
        const totalTarget = parseInt(document.getElementById('sumTarget').innerText) || 0;

        if (totalActualParam !== null) {
            cachedTotalActual = totalActualParam;
        }

        const totalActual = cachedTotalActual;

        // sumActual mengikuti totalIot jika approve belum dicentang
        const isApproved = [...document.querySelectorAll('.approve')].some(cb => cb.checked);
        if (!isApproved) {
            const totalIotNow = parseInt(document.getElementById('totalIot').innerText) || 0;
            document.getElementById('sumActual').innerText = totalIotNow;

            if (totalTarget <= 0) {
                document.getElementById('sumPercent').innerText = '–';
                document.getElementById('sumPercent').style.color = '#9ca3af';
                return;
            }
            const percent = ((totalIotNow / totalTarget) * 100).toFixed(1);
            document.getElementById('sumPercent').innerText = percent + '%';
            document.getElementById('sumPercent').style.color =
                percent >= 100 ? 'green' : percent >= 80 ? 'orange' : 'red';
            return;
        }

        // Sudah approve, tampilkan actual dari field
        document.getElementById('sumActual').innerText = totalActual;

        if (totalTarget <= 0) {
            document.getElementById('sumPercent').innerText = '–';
            document.getElementById('sumPercent').style.color = '#9ca3af';
            return;
        }

        const percent = ((totalActual / totalTarget) * 100).toFixed(1);
        document.getElementById('sumPercent').innerText = percent + '%';
        document.getElementById('sumPercent').style.color =
            percent >= 100 ? 'green' : percent >= 80 ? 'orange' : 'red';
    }

    /// ================= RESTORE APPROVE STATUS =================
    function restoreApproveStatus(approveData) {
        if (!approveData) return;
        const cbs = document.querySelectorAll('.approve');
        let anyChecked = false;
        cbs.forEach((cb, i) => {
            const val = parseInt(approveData[i]) || 0;
            cb.checked = val === 1;
            if (cb.checked) anyChecked = true;
        });

        if (anyChecked) {
            // Hanya update tampilan IOT, JANGAN trigger save
            const mesinCount = 6;
            let totalIotBaru = 0;
            for (let mIndex = 0; mIndex < mesinCount; mIndex++) {
                const start = mIndex * 4;
                let totalActualMesin = 0;
                for (let i = 0; i < 4; i++) {
                    const actualEl = document.querySelectorAll('[name="actual[]"]')[start + i];
                    totalActualMesin += Number(actualEl?.value) || 0;
                }
                const iotEl = document.getElementById('iot-' + mIndex);
                if (iotEl) iotEl.value = totalActualMesin;
                totalIotBaru += totalActualMesin;
                for (let i = 0; i < 4; i++) {
                    const hidden = document.querySelectorAll('.iot-hidden')[start + i];
                    if (hidden) hidden.value = totalActualMesin;
                }
            }
            document.getElementById('totalIot').innerText = totalIotBaru;
            // update summary pakai actual dari fields
            const totalActualNow = getTotalActualFromFields();
            cachedTotalActual = totalActualNow;
            updateSummary(totalActualNow);
        }
    }

    // ================= TARGET STORAGE =================
    function getTargetStorageKey() {
        return 'drilling_target_' + activeViewDate + '_' + activeViewShift;
    }

    function restoreTargetFromStorage() {
        const stored = localStorage.getItem(getTargetStorageKey());
        if (stored && parseInt(stored) > 0) {
            const targetInput = document.getElementById('sumTarget');
            if (!targetInput.value || parseInt(targetInput.value) <= 0) {
                targetInput.value = stored;
                updateSummary();
            }
        }
    }

    // ================= TARGET INPUT =================
    let targetSaveTimeout;

    // ================= HITUNG TOTAL TARGET =================
    function hitungTotalTarget() {
        let totalTarget = 0;
        document.querySelectorAll('.target-row').forEach(e => {
            totalTarget += Number(e.value) || 0;
        });
        document.getElementById('sumTarget').innerText = totalTarget;
        document.getElementById('totalTarget').innerText = totalTarget;
        updateSummary();
    }

    document.querySelectorAll('.target-row').forEach(el => {
        el.addEventListener('input', function() {
            hitungTotalTarget();
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => saveDrilling(), 1000);
        });
    });

    // ================= SYNC TARGET KE ROWS =================
    function syncTargetToRows() {
        document.querySelectorAll('.target-mesin').forEach((targetInput, mesinIndex) => {
            const value = targetInput.value;
            const start = mesinIndex * 4;
            for (let i = 0; i < 4; i++) {
                const hidden = document.querySelectorAll('.target-hidden')[start + i];
                if (hidden) hidden.value = value;
            }
        });
    }

    /// ================= VALIDASI IOT =================
    function validateIotPerMesin(mesinId) {
        const ketBox = document.getElementById(mesinId);
        const firstRow = ketBox.closest('tr');
        let current = firstRow;
        let total = 0;

        for (let i = 0; i < 4; i++) {
            if (!current) break;
            total += (Number(current.querySelector('.actual')?.value) || 0) +
                (Number(current.querySelector('.ng')?.value) || 0);
            current = current.nextElementSibling;
        }

        const mesinIndex = mesinId.split('-')[1];
        const iot = Number(document.getElementById('iot-' + mesinIndex).value) || 0;
        if (total > iot) {
            alert("Total Actual + NG melebihi IOT!");
            return false;
        }
        return true;
    }

    // ================= CEK APAKAH BOLEH APPROVE =================
    function isApproveAllowed() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(" ")[0];

        // Minggu tidak ada shift
        if (day === 0) return false;

        // Sabtu — shift pendek
        if (day === 6) {
            // Shift-1 sabtu: 07:30–12:30 → approve mulai 12:00
            if (time >= "12:00:00" && time <= "12:30:00") return true;
            if (time > "12:30:00" && time < "13:00:00") return true;

            // Shift-2 sabtu: 12:30–17:30 → approve mulai 17:00
            if (time >= "17:00:00" && time <= "17:30:00") return true;
            if (time > "17:30:00" && time < "18:00:00") return true;

            // Shift-3 sabtu: 17:30–23:30 → approve mulai 23:00
            if (time >= "23:00:00") return true;
            return false;
        }

        // Senin–Jumat
        // Shift-1: 07:30–15:30 → approve mulai 15:00
        if (time >= "15:00:00" && time <= "15:30:00") return true;
        if (time > "15:30:00" && time < "16:00:00") return true;

        // Shift-2: 15:30–23:30 → approve mulai 23:00
        if (time >= "23:00:00") return true;

        // Shift-3: 23:30–07:30 → approve mulai 07:00
        if (time >= "00:00:00" && time <= "07:30:00") return true;
        if (time > "07:30:00" && time < "08:00:00") return true;

        return false;
    }

    function getApproveWindowInfo() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(" ")[0];

        if (day === 0) return {
            openAt: null,
            shiftName: null
        };

        let openAt = null,
            shiftName = null;

        if (day === 6) {
            if (time >= "07:30:00" && time < "12:00:00") {
                openAt = "12:00";
                shiftName = "Shift-1";
            } else if (time >= "12:30:00" && time < "17:00:00") {
                openAt = "17:00";
                shiftName = "Shift-2";
            } else if (time >= "17:30:00" && time < "23:00:00") {
                openAt = "23:00";
                shiftName = "Shift-3";
            }
        } else {
            if (time >= "07:30:00" && time < "15:00:00") {
                openAt = "15:00";
                shiftName = "Shift-1";
            } else if (time >= "15:30:00" && time < "23:00:00") {
                openAt = "23:00";
                shiftName = "Shift-2";
            } else if (time >= "00:00:00" && time < "07:00:00") {
                openAt = "07:00";
                shiftName = "Shift-3";
            }
        }

        return {
            openAt,
            shiftName
        };
    }

    // ================= UPDATE TAMPILAN APPROVE CHECKBOX =================
    function updateApproveCheckboxState() {
        const allowed = isApproveAllowed();
        const info = getApproveWindowInfo();
        const infoEl = document.getElementById('approve-window-info');

        document.querySelectorAll('.approve').forEach(cb => {
            if (cb.checked) return;
            if (!cb.dataset.roleOk) return;

            cb.disabled = !allowed;
        });

        if (infoEl) {
            if (allowed) {
                infoEl.textContent = '✅ Approve tersedia';
                infoEl.style.color = '#16a34a';
            } else if (info.openAt) {
                infoEl.textContent = `🔒 Dibuka jam ${info.openAt} (${info.shiftName})`;
                infoEl.style.color = '#dc2626';
            } else {
                infoEl.textContent = '';
            }
        }
    }

    updateApproveCheckboxState();
    setInterval(updateApproveCheckboxState, 5000);

    // ================= AUTO REFRESH SAAT TOLERANSI HABIS =================
    function scheduleToleranceRefresh() {
        const now = new Date();
        const day = now.getDay();
        const totalSec = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

        const ends = day === 6 ?
            [13 * 3600, 18 * 3600] // Sabtu: 13:00, 18:00
            :
            [16 * 3600, 8 * 3600]; // Senin–Jumat: 16:00, 08:00
        // 00:30 (Shift-2 Senin-Jumat) di-skip karena tengah malam

        let msUntilRefresh = null;

        for (const endSec of ends) {
            if (endSec <= totalSec) continue; 
            const diff = (endSec - totalSec) * 1000;
            if (msUntilRefresh === null || diff < msUntilRefresh) {
                msUntilRefresh = diff;
            }
        }

        // Hanya schedule kalau dalam 35 menit ke depan (masih di window toleransi)
        if (msUntilRefresh !== null && msUntilRefresh <= 35 * 60 * 1000) {
            console.log(`🔄 Auto-refresh toleransi dalam ${Math.round(msUntilRefresh / 1000)} detik`);
            setTimeout(async () => {
                console.log('🔄 Toleransi habis — refresh data via AJAX');

                // 1. Update state shift ke shift yang baru aktif
                const newShift = getShift();
                activeViewShift = newShift;
                activeViewDate = getShiftDate();
                shiftChangedFrom = null;
                shiftChangeTime = null;
                inToleranceOnLoad = false;
                toleranceFromOnLoad = null;

                // 2. Sync dropdown
                const dropdown = document.getElementById('shiftDropdown');
                if (dropdown) dropdown.value = newShift;

                // 3. Sync tanggal
                const filterTgl = document.getElementById('filterTanggal');
                if (filterTgl) filterTgl.value = activeViewDate;

                // 4. Load ulang data shift baru via AJAX
                await loadDrillingByShift(newShift);

                // 5. Update label dan approve
                updateShiftModeLabel();
                updateApproveCheckboxState();

                console.log(`✅ Pindah ke ${newShift} tanggal ${activeViewDate}`);
            }, msUntilRefresh + 1500); 
        }
    }

    scheduleToleranceRefresh();

    /// ================= VALIDASI BOX =================
    document.querySelectorAll('.approve').forEach(cb => {
        cb.addEventListener('change', async function(e) {
            const role = "<?= $_SESSION['role'] ?>";
            const label = this.closest('.validasi-item').querySelector('label').innerText.toLowerCase();

            const roleMatch =
                (label.includes('foreman') && role === 'foreman') ||
                (label.includes('supervisor') && role === 'supervisor') ||
                (label.includes('ast') && role === 'ass_manager');

            if (!roleMatch) {
                e.preventDefault();
                this.checked = false;
                alert("Role anda tidak memiliki akses validasi ini.");
                return;
            }

            // ======= CEK WINDOW APPROVE =======
            if (this.checked && !isApproveAllowed()) {
                e.preventDefault();
                this.checked = false;
                const info = getApproveWindowInfo();
                const msg = info.openAt ?
                    `Validasi approve hanya bisa dilakukan 30 menit sebelum shift berakhir.\nBuka mulai jam ${info.openAt} (${info.shiftName}).` :
                    `Tidak ada shift aktif saat ini.`;
                alert(msg);
                return;
            }

            clearTimeout(saveTimeout);
            clearTimeout(targetSaveTimeout);

            if (this.checked) {
                syncIotFromActualNoSave();
                await saveDrilling();
            } else {
                await saveDrilling();
                const totalIot = await loadIotMesin();
                document.getElementById('totalIot').innerText = totalIot;
                cachedTotalActual = totalIot;
                updateSummary(totalIot);
            }

            updateApproveCheckboxState();
        });
    });

    function syncIotToRows() {
        document.querySelectorAll('.iot-mesin').forEach((iotInput, mesinIndex) => {
            const value = iotInput.value;
            const start = mesinIndex * 4;
            for (let i = 0; i < 4; i++) {
                const hidden = document.querySelectorAll('.iot-hidden')[start + i];
                if (hidden) hidden.value = value;
            }
        });
    }

    // ================= PREPARE PRINT =================
    window.addEventListener('beforeprint', function() {
        // Hapus span lama dulu jika ada
        document.querySelectorAll('.print-cell-value').forEach(s => s.remove());

        document.querySelectorAll('.drilling-table input[type="number"], .drilling-table input[type="text"]')
            .forEach(el => {
                const span = document.createElement('span');
                span.className = 'print-cell-value';
                span.textContent = el.value || '';
                el.parentNode.insertBefore(span, el.nextSibling);
            });
    });

    window.addEventListener('afterprint', function() {
        document.querySelectorAll('.print-cell-value').forEach(span => span.remove());
    });

    function fixStickyHeaderTop() {
        const firstRow = document.querySelector('.drilling-table tr:first-child');
        if (!firstRow) return;
        const h = firstRow.offsetHeight;
        document.querySelectorAll('.drilling-table tr:nth-child(2) th').forEach(th => {
            th.style.top = h + 'px';
        });
    }

    fixStickyHeaderTop();
    window.addEventListener('resize', fixStickyHeaderTop);

    function prepareAndPrint() {
        document.querySelector('.page-wrapper').style.height = 'auto';
        document.querySelector('.page-wrapper').style.overflow = 'visible';
        document.querySelector('.table-container').style.overflow = 'visible';
        document.querySelector('.table-container').style.maxHeight = 'none';
        document.querySelector('.bottom-container').style.position = 'static';

        setTimeout(() => {
            window.print();
            setTimeout(() => {
                document.querySelector('.page-wrapper').style.height = '';
                document.querySelector('.page-wrapper').style.overflow = '';
                document.querySelector('.table-container').style.overflow = '';
                document.querySelector('.table-container').style.maxHeight = '';
                document.querySelector('.bottom-container').style.position = '';
            }, 1000);
        }, 300);
    }

    // ================= LOAD SAAT HALAMAN DIBUKA =================
    window.addEventListener('load', () => {
        setTimeout(() => {
            const headerEl = document.querySelector('.table-header-sticky');
            if (headerEl) {
                const h = headerEl.offsetHeight;
                document.documentElement.style.setProperty('--sticky-top-1', (h + 30) + 'px');
                document.documentElement.style.setProperty('--sticky-top-2', (h + 30) + 'px');
            }
            fixStickyHeaderTop()
            initFilterTanggal();
            initShiftTabs();
            initAutocomplete();
            initEnterToNextField();
            relockReadonlyFields();
            loadDrilling();
            syncTargetToRows();
            hitungTotalTarget();
        }, 500);
    });

    window.addEventListener('resize', () => {
        const headerEl = document.querySelector('.table-header-sticky');
        if (headerEl) {
            const h = headerEl.offsetHeight;
            document.documentElement.style.setProperty('--sticky-top-1', (h + 30) + 'px');
            document.documentElement.style.setProperty('--sticky-top-2', (h + 30) + 'px');
        }
    });
</script>