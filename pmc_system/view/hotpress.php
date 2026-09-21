<?php
$hotpressRowCount = 18; // jumlah baris
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

    /* ================= HOTPRESS ================= */
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

    /* ================= HOTPRESS TABLE ================= */
    .hotpress-table th {
        border: 1px solid #000;
        padding: 15px;
        text-align: center;
        font-size: 15px;
    }

    .hotpress-table td {
        border: 1px solid #000;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        height: auto;
        vertical-align: middle;
    }

    .hotpress-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .hotpress-table tr:first-child th {
        position: sticky;
        top: 0;
        z-index: 20;
        font-size: 17px;
        background: #0a2a66;
    }

    .hotpress-table tr:nth-child(2) th {
        position: sticky;
        top: 0;
        z-index: 20;
        font-size: 15px;
        background: #0a2a66;
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

    /* ================= KETERANGAN ================= */
    .keterangan-input {
        width: 100%;
        min-height: 40px;
        height: auto;
        border: none;
        outline: none;
        text-align: center;
        font-weight: 600;
        font-size: 13px;
        background: transparent;
        overflow: hidden;
        resize: none;
        box-sizing: border-box;
        color: #374151;
    }

    /* ================= LOCKED ROW ================= */
    .locked-row {
        background-color: transparent;
        opacity: 1;
    }

    .locked-row input,
    .locked-row textarea {
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

    /* ================= VERTICAL INPUT ================= */
    .hotpress-table td .cell-input.vertical {
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

    .hotpress-table textarea,
    .hotpress-table .cell-input.vertical,
    .hotpress-table .keterangan-input {
        overflow: hidden !important;
        resize: none;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .hotpress-table textarea::-webkit-scrollbar,
    .hotpress-table .cell-input.vertical::-webkit-scrollbar,
    .hotpress-table .keterangan-input::-webkit-scrollbar {
        display: none;
    }

    .hotpress-table textarea[readonly],
    .hotpress-table input[readonly]:not(.jam) {
        background: #f8fafc;
        color: #6b7280;
        cursor: default;
    }

    .hotpress-table input[type="number"] {
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
        font-size: 14px;
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

        /* MOBILE/TABLET FIX */
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

        .hotpress-table {
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
        .ac-dropdown {
            display: none !important;
        }

        @page {
            size: A4 landscape;
            margin: 3mm 3mm;
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
            margin-top: -5px !important;
            margin-bottom: -2px !important;
            padding-right: 15px !important;
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
            line-height: 1.4 !important;
            padding-left: 14px !important;
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

        /* ================= HOTPRESS TABLE ================= */
        .hotpress-table {
            width: 98% !important;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            table-layout: fixed !important;
            overflow: visible !important;
            max-height: none !important;
            margin: 0 auto !important;
        }

        .hotpress-table th,
        .hotpress-table td {
            border: 0.5px solid #555 !important;
            padding: 1px 2px !important;
            line-height: 1.2 !important;
            height: 25px !important;
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

        .hotpress-table th {
            background: #c8d6f0 !important;
            font-weight: bold !important;
            color: #000 !important;
            font-size: 8px !important;
            padding: 1px !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* ================= LEBAR KOLOM ================= */
        .hotpress-table th:nth-child(1),
        .hotpress-table td:nth-child(1) {
            width: 2% !important;
        }

        .hotpress-table th:nth-child(2),
        .hotpress-table td:nth-child(2) {
            width: 2% !important;
        }

        .hotpress-table th:nth-child(3),
        .hotpress-table td:nth-child(3) {
            width: 4% !important;
        }

        .hotpress-table th:nth-child(4),
        .hotpress-table td:nth-child(4) {
            width: 6% !important;
        }

        .hotpress-table th:nth-child(5),
        .hotpress-table td:nth-child(5) {
            width: 4% !important;
        }

        .hotpress-table th:nth-child(6),
        .hotpress-table td:nth-child(6) {
            width: 2% !important;
        }

        .hotpress-table th:nth-child(7),
        .hotpress-table td:nth-child(7) {
            width: 2% !important;
        }

        .hotpress-table th:nth-child(8),
        .hotpress-table td:nth-child(8) {
            width: 3% !important;
        }

        .hotpress-table th:nth-child(9),
        .hotpress-table td:nth-child(9) {
            width: 2% !important;
        }

        .hotpress-table th:nth-child(10),
        .hotpress-table td:nth-child(10) {
            width: 2% !important;
        }

        .hotpress-table th:nth-child(11),
        .hotpress-table td:nth-child(11) {
            width: 4% !important;
        }

        .hotpress-table th:nth-child(12),
        .hotpress-table td:nth-child(12) {
            width: 4% !important;
        }

        .hotpress-table th:nth-child(13),
        .hotpress-table td:nth-child(13) {
            width: 6% !important;
        }

        .hotpress-table th:nth-child(14),
        .hotpress-table td:nth-child(14) {
            width: 5% !important;
        }

        .hotpress-table th:nth-child(15),
        .hotpress-table td:nth-child(15) {
            width: 12% !important;
        }

        .col-validasi {
            display: none !important;
        }

        /* ================= INPUT & TEXTAREA ================= */
        .hotpress-table input,
        .hotpress-table textarea {
            border: none !important;
            background: transparent !important;
            font-size: 8px !important;
            font-family: Arial, sans-serif !important;
            line-height: 1.2 !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
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

        .hotpress-table textarea {
            display: block !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: hidden !important;
        }

        .hotpress-table input[type="number"],
        .hotpress-table input[type="text"] {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
        }

        .hotpress-table input::placeholder,
        .hotpress-table textarea::placeholder {
            color: transparent !important;
        }

        /* ================= KETERANGAN TEXTAREA ================= */
        .keterangan-input {
            font-size: 8px !important;
            line-height: 1.2 !important;
            display: block !important;
            width: 100% !important;
            height: auto !important;
            text-align: center !important;
            color: #000 !important;
            background: transparent !important;
            border: none !important;
            resize: none !important;
            overflow: hidden !important;
        }

        /* ================= PRINT CELL VALUE ================= */
        .print-cell-value {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            font-size: 8px !important;
            font-weight: bold !important;
            color: #000 !important;
            line-height: 1.2 !important;
            overflow: hidden !important;
        }

        /* ================= PAGE BREAK ================= */
        tr {
            page-break-inside: avoid !important;
        }

        thead {
            display: table-header-group !important;
        }

        /* ================= BOTTOM SECTION ================= */
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

        /* ================= TTD BOX ================= */
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

        /* ================= SEMBUNYIKAN ELEMEN NON-PRINT ================= */
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

        .hotpress-table {
            width: max-content !important;
            table-layout: auto !important;
        }
    }

    /* ================= RESPONSIVE 1024px ================= */
    @media only screen and (max-width: 1024px) {
        .hotpress-table {
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
        .hotpress-table {
            min-width: 1300px !important;
            width: max-content !important;
            table-layout: auto !important;
        }

        .hotpress-table th,
        .hotpress-table td,
        .cell-input {
            font-size: 15px !important;
            padding: 6px;
        }

        .jam,
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
            PT. EXEDY PRIMA INDONESIA<br>
            LAPORAN HARIAN HOT PRESS
        </h3>
        <div id="shift-mode-label"></div>
    </div>

    <!-- ================= CONTAINER 2: TABEL ================= -->
    <div class="table-container">
        <form id="hotpressForm">
            <div id="shift-mode-label"></div>

            <table class="hotpress-table">
                <thead>
                    <tr>
                        <th rowspan="2">NO. WOS</th>
                        <th rowspan="2">MC NO.</th>
                        <th rowspan="2">TYPE</th>
                        <th rowspan="2">C/F NO.</th>
                        <th rowspan="2">CUST.</th>
                        <th colspan="1">BERAT</th>
                        <th rowspan="2">HASIL KG</th>
                        <th colspan="2">HASIL PCS</th>
                        <th colspan="1">TOTAL</th>
                        <th colspan="1">REFF NO.</th>
                        <th colspan="2">PROSES</th>
                        <th rowspan="2">OPERATOR</th>
                        <th rowspan="2">KETERANGAN</th>
                        <th rowspan="2" class="col-validasi">VALIDASI</th>
                    </tr>
                    <tr>
                        <th>PCS</th>
                        <th>BAIK</th>
                        <th>RUSAK</th>
                        <th>HASIL</th>
                        <th>(SS)</th>
                        <th>START</th>
                        <th>FINISH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($rIndex = 0; $rIndex < $hotpressRowCount; $rIndex++) { ?>
                        <tr>
                            <!-- hidden: row index -->
                            <input type="hidden" name="row_id[]" value="<?= $rIndex ?>">

                            <!-- NO. WOS -->
                            <td>
                                <textarea name="no_wos[]" class="cell-input vertical"
                                    placeholder="isi no. wos" rows="1" style="resize:none;"></textarea>
                            </td>

                            <!-- MC NO. (diisi user) -->
                            <td>
                                <input type="text" name="mc_no[]" class="cell-input"
                                    placeholder="isi mc no.">
                            </td>

                            <!-- TYPE (auto-fill dari CF) -->
                            <td>
                                <textarea name="type[]" class="cell-input vertical readonly-field"
                                    rows="1" style="resize:none;" readonly></textarea>
                            </td>

                            <!-- C/F NO (autocomplete) -->
                            <td style="position:relative;">
                                <textarea name="cf_no[]" class="cell-input cf-input vertical"
                                    placeholder="isi c/f no." autocomplete="off"
                                    rows="1" style="resize:none;"></textarea>
                            </td>

                            <!-- CUSTOMER (auto-fill dari CF) -->
                            <td>
                                <textarea name="customer[]" class="cell-input vertical readonly-field"
                                    rows="1" style="resize:none;" readonly></textarea>
                            </td>

                            <!-- BERAT PCS -->
                            <td>
                                <input type="number" name="berat_pcs[]" class="cell-input"
                                    placeholder="isi berat" min="0">
                            </td>

                            <!-- HASIL KG -->
                            <td>
                                <input type="number" name="hasil_kg[]" class="cell-input"
                                    placeholder="isi hasil (kg)" min="0">
                            </td>

                            <!-- BAIK -->
                            <td>
                                <input type="number" name="baik[]" class="cell-input actual"
                                    placeholder="isi hasil (pcs) baik" min="0">
                            </td>

                            <!-- RUSAK -->
                            <td>
                                <input type="number" name="rusak[]" class="cell-input ng"
                                    placeholder="isi hasil (pcs) rusak" min="0">
                            </td>

                            <!-- TOTAL HASIL -->
                            <td>
                                <input type="number" name="total_hasil[]" class="cell-input total-hasil"
                                    placeholder="isi total hasil" min="0">
                            </td>

                            <!-- REFF NO (SS) -->
                            <td>
                                <input type="text" name="reff_no[]" class="cell-input"
                                    placeholder="isi reff no.">
                            </td>

                            <!-- START -->
                            <td>
                                <input type="text" name="start[]" class="jam"
                                    placeholder="isi start">
                            </td>

                            <!-- FINISH -->
                            <td>
                                <input type="text" name="finish[]" class="jam"
                                    placeholder="isi finish">
                            </td>

                            <!-- OPERATOR -->
                            <td>
                                <input type="text" name="operator[]" class="cell-input"
                                    placeholder="isi nama operator">
                            </td>

                            <!-- KETERANGAN -->
                            <td>
                                <textarea name="keterangan[]" class="keterangan-input"
                                    placeholder="isi keterangan..." rows="1"
                                    style="resize:none;"></textarea>
                            </td>

                            <!-- VALIDASI -->
                            <td class="col-validasi">
                                <input type="checkbox"
                                    name="validasi[<?= $rIndex ?>]"
                                    class="validasi">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </form>
    </div>

    <!-- ================= CONTAINER 3: BOTTOM SECTION ================= -->
    <div class="bottom-container">
        <div class="bottom-section">
            <div class="left-section">
                <div class="summary-box">
                    <h4>SUMMARY PRODUKSI</h4>
                    <div class="summary-row">
                        <span>Total Target</span>
                        <input type="number" id="sumTarget" class="target-input"
                            placeholder="isi target" min="1"
                            <?php if (!in_array($_SESSION['role'], ['supervisor', 'ass_manager'])) echo 'disabled'; ?>>
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
    const HP_READONLY = ['type[]', 'customer[]'];

    function isHpReadonly(el) {
        return HP_READONLY.includes(el.getAttribute('name'));
    }

    function relockReadonlyFields() {
        document.querySelectorAll('[name="type[]"], [name="customer[]"]')
            .forEach(el => {
                el.readOnly = true;
            });
    }

    // ================= HOTPRESS CORE =================
    const userRole = "<?= $_SESSION['role'] ?>";
    let isLoaded = false;
    let isSaving = false;
    let pendingSave = false;
    let saveTimeout;
    let loadRequestId = 0;
    let cachedTotalBaik = 0;

    // ================= TOLERANSI =================
    const _restoredChangeTime = localStorage.getItem('hp_shiftChangeTime') ?
        parseInt(localStorage.getItem('hp_shiftChangeTime')) : null;
    const _restoredChangedFrom = localStorage.getItem('hp_shiftChangedFrom') ?? null;
    const _stillInTolerance = _restoredChangeTime !== null &&
        (Date.now() - _restoredChangeTime) / 1000 / 60 < 30;

    function getShiftStartTime() {
        const now = new Date();
        const time = now.toTimeString().split(' ')[0];
        const today = now.toISOString().split('T')[0];
        let shiftStart = null;
        if (time >= '07:30:00' && time < '15:30:00') shiftStart = new Date(today + 'T07:30:00');
        else if (time >= '15:30:00' && time < '23:30:00') shiftStart = new Date(today + 'T15:30:00');
        else if (time >= '23:30:00') shiftStart = new Date(today + 'T23:30:00');
        else {
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            shiftStart = new Date(yesterday.toISOString().split('T')[0] + 'T23:30:00');
        }
        return shiftStart;
    }

    function getPrevShift(s) {
        if (s === 'Shift-1') return 'Shift-3';
        if (s === 'Shift-2') return 'Shift-1';
        return 'Shift-2';
    }

    let _autoTolerance = false;
    let _autoToleranceFrom = null;

    if (!_stillInTolerance) {
        const shiftStart = getShiftStartTime();
        if (shiftStart) {
            const minSinceStart = (Date.now() - shiftStart.getTime()) / 1000 / 60;
            if (minSinceStart >= 0 && minSinceStart < 30) {
                _autoTolerance = true;
                _autoToleranceFrom = getPrevShift(getShift() ?? 'Shift-1');
                if (!localStorage.getItem('hp_shiftChangeTime')) {
                    localStorage.setItem('hp_shiftChangeTime', shiftStart.getTime().toString());
                    localStorage.setItem('hp_shiftChangedFrom', _autoToleranceFrom);
                    shiftChangeTime = shiftStart.getTime();
                    shiftChangedFrom = _autoToleranceFrom;
                }
            }
        }
    }

    const inToleranceOnLoad = _stillInTolerance || _autoTolerance;
    const toleranceFromOnLoad = _restoredChangedFrom || _autoToleranceFrom;

    let lastShift = (inToleranceOnLoad && toleranceFromOnLoad) ?
        toleranceFromOnLoad : getShift();

    let shiftChangeTime = localStorage.getItem('hp_shiftChangeTime') ?
        parseInt(localStorage.getItem('hp_shiftChangeTime')) : null;
    let shiftChangedFrom = localStorage.getItem('hp_shiftChangedFrom') ?? null;

    // ================= SHIFT =================
    function getShift() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(' ')[0];
        if (day === 0) return null;
        if (day === 6) {
            if (time >= '07:30:00' && time < '12:30:00') return 'Shift-1';
            if (time >= '12:30:00' && time < '17:30:00') return 'Shift-2';
            if (time >= '17:30:00' && time < '23:30:00') return 'Shift-3';
            return null;
        }
        if (time >= '07:30:00' && time < '15:30:00') return 'Shift-1';
        if (time >= '15:30:00' && time < '23:30:00') return 'Shift-2';
        if (time >= '23:30:00' || time < '07:30:00') return 'Shift-3';
        return null;
    }

    function getShiftDate() {
        const now = new Date();
        const time = now.toTimeString().split(' ')[0];
        if (time >= '00:00:00' && time < '07:30:00') {
            const y = new Date(now);
            y.setDate(y.getDate() - 1);
            return y.toISOString().split('T')[0];
        }
        return now.toISOString().split('T')[0];
    }

    function getShiftDateFor(shiftName, referenceDate) {
        const now = new Date();
        const time = now.toTimeString().split(' ')[0];
        if (shiftName === 'Shift-3' && time >= '00:00:00' && time < '08:00:00') {
            const yesterday = new Date(now);
            yesterday.setDate(yesterday.getDate() - 1);
            return yesterday.toISOString().split('T')[0];
        }
        return referenceDate ?? now.toISOString().split('T')[0];
    }

    // ================= ACTIVE VIEW =================
    const todayDate = getShiftDate();
    let activeViewShift = (inToleranceOnLoad && toleranceFromOnLoad) ?
        toleranceFromOnLoad : (getShift() ?? 'Shift-1');
    let activeViewDate = (inToleranceOnLoad && toleranceFromOnLoad) ?
        getShiftDateFor(toleranceFromOnLoad, todayDate) : todayDate;

    function getCurrentShiftRealtime() {
        return getShift() ?? 'Shift-1';
    }

    // ================= FILTER TANGGAL =================
    function initFilterTanggal() {
        const input = document.getElementById('filterTanggal');
        if (!input) return;
        input.value = todayDate;
        input.max = todayDate;
        input.addEventListener('change', function() {
            activeViewDate = this.value;
            loadHotpressByShift(activeViewShift);
        });
    }

    // ================= SHIFT TABS =================
    function initShiftTabs() {
        const dropdown = document.getElementById('shiftDropdown');
        if (!dropdown) return;
        dropdown.value = activeViewShift;
        dropdown.addEventListener('change', function() {
            activeViewShift = this.value;
            activeViewDate = (this.value === toleranceFromOnLoad || this.value === shiftChangedFrom) ?
                getShiftDateFor(this.value, todayDate) :
                getShiftDate();
            loadHotpressByShift(activeViewShift);
        });
    }

    // ================= AUTO RESIZE TEXTAREA =================
    function autoResize(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    document.querySelectorAll('.cell-input, .keterangan-input').forEach(el => {
        el.addEventListener('input', () => autoResize(el));
        autoResize(el);
    });

    // ================= SUMMARY =================
    function updateSummary() {
        const totalTarget = parseInt(document.getElementById('sumTarget').value) || 0;
        const totalActual = cachedTotalBaik;

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

    document.getElementById('sumTarget').addEventListener('input', function() {
        updateSummary();
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => saveHotpress(), 1500);
    });

    // ================= SHIFT LABEL =================
    function updateShiftModeLabel() {
        const latestShift = getShift();
        const label = document.getElementById('shift-mode-label');
        const isToday = activeViewDate === todayDate;
        const isCurrent = activeViewShift === latestShift;

        const inTolerance = shiftChangeTime !== null &&
            (Date.now() - shiftChangeTime) / 1000 / 60 < 30;

        if (inTolerance && shiftChangedFrom) {
            const sisaMenit = Math.ceil(30 - (Date.now() - shiftChangeTime) / 1000 / 60);
            if (activeViewShift === shiftChangedFrom) {
                label.innerHTML = `
                <span class="view-only-label" style="color:#ea580c;">
                    ⚠️ Shift berganti ke ${latestShift} — Toleransi pengisian: ${sisaMenit} menit lagi
                    &nbsp;|&nbsp; <strong>Sedang lihat: ${activeViewShift}</strong>
                </span>`;
                setFormReadOnly(false);
            } else {
                label.innerHTML = `<span class="active-shift-label">${activeViewShift} - Sedang Aktif</span>`;
                setFormReadOnly(false);
            }
            return;
        }

        if (!isToday || !isCurrent) {
            label.innerHTML = `<span class="view-only-label">${activeViewShift} - ${formatTanggal(activeViewDate)} (read-only)</span>`;
            setFormReadOnly(true);
        } else {
            label.innerHTML = `<span class="active-shift-label">${latestShift} - Sedang Aktif</span>`;
            setFormReadOnly(false);
        }
    }

    function formatTanggal(dateStr) {
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    // ================= SET FORM READ ONLY =================
    function setFormReadOnly(isReadOnly) {
        document.querySelectorAll('#hotpressForm input, #hotpressForm textarea')
            .forEach(el => {
                if (el.type === 'hidden') return;
                if (isHpReadonly(el)) {
                    el.readOnly = true;
                    return;
                }

                const isValidasiCb = el.classList.contains('validasi');
                const isLockedRow = el.closest('tr')?.classList.contains('locked-row');

                if (isReadOnly) {
                    el.disabled = true;
                } else {
                    if (!isLockedRow && !isValidasiCb) {
                        el.disabled = false;
                        if (el.tagName !== 'SELECT') el.readOnly = false;
                    }
                    if (isValidasiCb) el.disabled = el.checked;
                }
            });

        if (userRole === 'supervisor' || userRole === 'ass_manager') {
            document.getElementById('sumTarget').disabled = false;
        }

        relockReadonlyFields();
    }

    // ================= AUTOCOMPLETE C/F NO =================
    function initAutocomplete() {
        document.querySelectorAll('[name="cf_no[]"]').forEach(input => {
            const td = input.closest('td');
            td.style.position = 'relative';

            const dropdown = document.createElement('div');
            dropdown.className = 'ac-dropdown';
            dropdown.style.cssText = `
            display:none; position:absolute; top:100%; left:0;
            min-width:300px; background:#fff; border:1px solid #d1d5db;
            border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,.15);
            z-index:9999; max-height:220px; overflow-y:auto;
        `;
            td.appendChild(dropdown);

            let debounceTimer;
            let activeIdx = -1;

            function getRowIndex() {
                return [...document.querySelectorAll('[name="cf_no[]"]')].indexOf(input);
            }

            function fillRow(item) {
                const i = getRowIndex();

                input.value = item.dataset.partno ?? '';
                autoResize(input);

                const typeEl = document.querySelectorAll('[name="type[]"]')[i];
                if (typeEl) {
                    typeEl.value = item.dataset.type ?? '';
                    autoResize(typeEl);
                }

                const custEl = document.querySelectorAll('[name="customer[]"]')[i];
                if (custEl) {
                    custEl.value = item.dataset.customer ?? '';
                    autoResize(custEl);
                }

                dropdown.style.display = 'none';
                activeIdx = -1;
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => saveHotpress(), 1000);
            }

            function setActive(idx) {
                const items = dropdown.querySelectorAll('.ac-item');
                if (!items.length) return;
                items.forEach(it => {
                    it.style.background = '#fff';
                    it.removeAttribute('data-active');
                });
                idx = Math.max(0, Math.min(idx, items.length - 1));
                activeIdx = idx;
                items[idx].style.background = '#eff6ff';
                items[idx].setAttribute('data-active', 'true');
                items[idx].scrollIntoView({
                    block: 'nearest'
                });
            }

            function selectActive() {
                const items = dropdown.querySelectorAll('.ac-item');
                if (activeIdx < 0 || activeIdx >= items.length) return;
                fillRow(items[activeIdx]);
            }

            input.addEventListener('input', function() {
                const q = this.value.trim();
                clearTimeout(debounceTimer);
                activeIdx = -1;

                if (q.length === 0) {
                    dropdown.style.display = 'none';
                    const i = getRowIndex();
                    ['[name="type[]"]', '[name="customer[]"]'].forEach(sel => {
                        const el = document.querySelectorAll(sel)[i];
                        if (el) {
                            el.value = '';
                            autoResize(el);
                        }
                    });
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => saveHotpress(), 800);
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

                        dropdown.innerHTML = items.map(it => `
                        <div class="ac-item"
                            data-partno="${(it.partno   || '').replace(/"/g,'&quot;')}"
                            data-type="${(it.type       || it.size || '').replace(/"/g,'&quot;')}"
                            data-customer="${(it.customer || '').replace(/"/g,'&quot;')}"
                            style="padding:8px 12px; cursor:pointer; font-size:13px;
                                   border-bottom:1px solid #f1f5f9; line-height:1.4;">
                            <div><strong>${highlight(it.partno, q)}</strong></div>
                            <div style="color:#94a3b8; font-size:11px;">
                                ${it.customer ? it.customer + ' &nbsp;|&nbsp; ' : ''}
                                Type: ${it.type || it.size || '-'}
                            </div>
                        </div>
                    `).join('');

                        dropdown.querySelectorAll('.ac-item').forEach((it, i) => {
                            it.addEventListener('mouseenter', () => setActive(i));
                            it.addEventListener('mouseleave', () => {
                                it.style.background = '#fff';
                                activeIdx = -1;
                            });
                            it.addEventListener('mousedown', e => {
                                e.preventDefault();
                                activeIdx = i;
                                selectActive();
                            });
                        });

                        dropdown.style.display = 'block';
                        activeIdx = -1;
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
                        setActive(activeIdx + 1);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        setActive(activeIdx - 1);
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (activeIdx >= 0) selectActive();
                        break;
                    case 'Escape':
                        dropdown.style.display = 'none';
                        activeIdx = -1;
                        break;
                    case 'Tab':
                        if (activeIdx >= 0) {
                            e.preventDefault();
                            selectActive();
                        } else dropdown.style.display = 'none';
                        break;
                }
            });

            input.addEventListener('blur', () => {
                setTimeout(() => {
                    dropdown.style.display = 'none';
                    activeIdx = -1;
                }, 200);
            });
        });
    }

    function highlight(text, query) {
        const idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx === -1) return text;
        return text.substring(0, idx) +
            `<strong style="color:#1d4ed8;">${text.substring(idx, idx + query.length)}</strong>` +
            text.substring(idx + query.length);
    }

    // ================= VALIDASI (CHECKBOX PER ROW) =================
    document.querySelectorAll('.validasi').forEach(cb => {
        cb.addEventListener('change', async function() {
            const role = userRole;
            const row = this.closest('tr');

            if (!this.checked) {
                if (!['supervisor', 'ass_manager'].includes(role)) {
                    alert('Hanya Supervisor / Ast. Manager yang bisa membatalkan validasi.');
                    this.checked = true;
                    return;
                }
                row.classList.remove('locked-row');
                row.querySelectorAll('input, textarea').forEach(el => {
                    if (el.type === 'hidden') return;
                    if (isHpReadonly(el)) {
                        el.readOnly = true;
                        return;
                    }
                    el.readOnly = false;
                    el.disabled = false;
                });
                this.disabled = false;
                await saveHotpress();
                return;
            }

            const cf = row.querySelector('[name="cf_no[]"]')?.value.trim();
            const operator = row.querySelector('[name="operator[]"]')?.value.trim();
            const baik = row.querySelector('[name="baik[]"]')?.value.trim();

            if (!cf || !operator || !baik) {
                alert('C/F No., Operator, dan Baik wajib diisi sebelum validasi!');
                this.checked = false;
                return;
            }

            const finishEl = row.querySelector('[name="finish[]"]');
            if (finishEl && !finishEl.value) {
                const d = new Date();
                finishEl.value = d.getHours().toString().padStart(2, '0') + ':' +
                    d.getMinutes().toString().padStart(2, '0');
            }

            await saveHotpress();
            lockRow(row);
            this.disabled = !['supervisor', 'ass_manager'].includes(role);
        });
    });

    function lockRow(row) {
        row.querySelectorAll('input, textarea').forEach(el => {
            if (el.type === 'hidden') return;
            el.readOnly = true;
        });
        row.classList.add('locked-row');
    }

    function lockRowByIndex(index) {
        const rows = [...document.querySelectorAll('#hotpressForm tbody tr')]
            .filter(tr => tr.querySelector('.validasi'));
        if (rows[index]) lockRow(rows[index]);

        const cb = document.querySelectorAll('.validasi')[index];
        if (cb) cb.disabled = !['supervisor', 'ass_manager'].includes(userRole);
    }

    // ================= CEK APAKAH BOLEH APPROVE =================
    function isApproveAllowed() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(' ')[0];

        // Minggu tidak ada shift
        if (day === 0) return false;

        // Sabtu — shift pendek
        if (day === 6) {
            // Shift-1 sabtu: 07:30–12:30 → approve mulai 12:00
            if (time >= '12:00:00' && time <= '12:30:00') return true;
            // Grace period setelah Shift-1 selesai
            if (time > '12:30:00' && time < '13:00:00') return true;
            // Shift-2 sabtu: 12:30–17:30 → approve mulai 17:00
            if (time >= '17:00:00' && time <= '17:30:00') return true;
            // Grace period setelah Shift-2 selesai
            if (time > '17:30:00' && time < '18:00:00') return true;
            // Shift-3 sabtu: 17:30–23:30 → approve mulai 23:00
            if (time >= '23:00:00') return true;
            return false;
        }

        // Senin–Jumat
        // Shift-1: 07:30–15:30 → approve mulai 15:00
        if (time >= '15:00:00' && time <= '15:30:00') return true;
        // Grace period setelah Shift-1 selesai (15:30–16:00)
        if (time > '15:30:00' && time < '16:00:00') return true;

        // Shift-2: 15:30–23:30 → approve mulai 23:00
        if (time >= '23:00:00') return true;

        // Shift-3: 23:30–07:30 → approve mulai 07:00
        if (time >= '00:00:00' && time <= '07:30:00') return true;
        // Grace period setelah Shift-3 selesai (07:30–08:00)
        if (time > '07:30:00' && time < '08:00:00') return true;

        return false;
    }

    function getApproveWindowInfo() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(' ')[0];

        if (day === 0) return {
            openAt: null,
            shiftName: null
        };

        let openAt = null,
            shiftName = null;

        if (day === 6) {
            if (time >= '07:30:00' && time < '12:00:00') {
                openAt = '12:00';
                shiftName = 'Shift-1';
            } else if (time >= '12:30:00' && time < '17:00:00') {
                openAt = '17:00';
                shiftName = 'Shift-2';
            } else if (time >= '17:30:00' && time < '23:00:00') {
                openAt = '23:00';
                shiftName = 'Shift-3';
            }
        } else {
            if (time >= '07:30:00' && time < '15:00:00') {
                openAt = '15:00';
                shiftName = 'Shift-1';
            } else if (time >= '15:30:00' && time < '23:00:00') {
                openAt = '23:00';
                shiftName = 'Shift-2';
            } else if (time >= '00:00:00' && time < '07:00:00') {
                openAt = '07:00';
                shiftName = 'Shift-3';
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

    // ================= CEK APAKAH BOLEH APPROVE =================
    function isApproveAllowed() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(' ')[0];

        // Minggu tidak ada shift
        if (day === 0) return false;

        // Sabtu — shift pendek
        if (day === 6) {
            // Shift-1 sabtu: 07:30–12:30 → approve mulai 12:00
            if (time >= '12:00:00' && time <= '12:30:00') return true;
            if (time > '12:30:00' && time < '13:00:00') return true;

            // Shift-2 sabtu: 12:30–17:30 → approve mulai 17:00
            if (time >= '17:00:00' && time <= '17:30:00') return true;
            if (time > '17:30:00' && time < '18:00:00') return true;

            // Shift-3 sabtu: 17:30–23:30 → approve mulai 23:00
            if (time >= '23:00:00') return true;
            return false;
        }

        // Senin–Jumat
        // Shift-1: 07:30–15:30 → approve mulai 15:00
        if (time >= '15:00:00' && time <= '15:30:00') return true;
        if (time > '15:30:00' && time < '16:00:00') return true;

        // Shift-2: 15:30–23:30 → approve mulai 23:00
        if (time >= '23:00:00') return true;

        // Shift-3: 23:30–07:30 → approve mulai 07:00
        if (time >= '00:00:00' && time <= '07:30:00') return true;
        if (time > '07:30:00' && time < '08:00:00') return true;

        return false;
    }

    function getApproveWindowInfo() {
        const now = new Date();
        const day = now.getDay();
        const time = now.toTimeString().split(' ')[0];

        if (day === 0) return {
            openAt: null,
            shiftName: null
        };

        let openAt = null,
            shiftName = null;

        if (day === 6) {
            if (time >= '07:30:00' && time < '12:00:00') {
                openAt = '12:00';
                shiftName = 'Shift-1';
            } else if (time >= '12:30:00' && time < '17:00:00') {
                openAt = '17:00';
                shiftName = 'Shift-2';
            } else if (time >= '17:30:00' && time < '23:00:00') {
                openAt = '23:00';
                shiftName = 'Shift-3';
            }
        } else {
            if (time >= '07:30:00' && time < '15:00:00') {
                openAt = '15:00';
                shiftName = 'Shift-1';
            } else if (time >= '15:30:00' && time < '23:00:00') {
                openAt = '23:00';
                shiftName = 'Shift-2';
            } else if (time >= '00:00:00' && time < '07:00:00') {
                openAt = '07:00';
                shiftName = 'Shift-3';
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

    // ================= VALIDASI BOX (APPROVE) =================
    document.querySelectorAll('.approve').forEach(cb => {
        cb.addEventListener('change', async function(e) {
            const label = this.closest('.validasi-item').querySelector('label').innerText.toLowerCase();
            const roleMatch =
                (label.includes('foreman') && userRole === 'foreman') ||
                (label.includes('supervisor') && userRole === 'supervisor') ||
                (label.includes('ast') && userRole === 'ass_manager');

            if (!roleMatch) {
                e.preventDefault();
                this.checked = false;
                alert('Role anda tidak memiliki akses validasi ini.');
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

            // ======= Save approve =======
            clearTimeout(saveTimeout);
            await saveHotpress();

            updateApproveCheckboxState();
        });
    });

    // ================= SAVE =================
    async function saveHotpress() {
        const latestShift = getShift();
        const canEditPast = userRole === 'supervisor' || userRole === 'ass_manager';

        if (!canEditPast) {
            const latestShift = getShift();
            if (!latestShift) return;

            // Cek toleransi
            const inTolerance = shiftChangeTime !== null &&
                (Date.now() - shiftChangeTime) / 1000 / 60 < 30;

            if (!inTolerance) {
                if (activeViewShift !== latestShift) return;
                if (activeViewDate !== todayDate) return;
            }
        }

        if (isSaving) {
            pendingSave = true;
            return;
        }

        const formData = new FormData();
        formData.append('shift', activeViewShift);
        formData.append('tanggal', activeViewDate);
        formData.append('target', document.getElementById('sumTarget').value || 0);
        formData.append('seksi', 'hotpress');

        document.querySelectorAll('.approve').forEach((cb, i) => {
            formData.append(`approve[${i}]`, cb.checked ? '1' : '0');
        });

        const cfInputs = document.querySelectorAll('[name="cf_no[]"]');
        cfInputs.forEach((cf, index) => {
            const baik = document.querySelectorAll('[name="baik[]"]')[index]?.value ?? '';
            const operator = document.querySelectorAll('[name="operator[]"]')[index]?.value ?? '';
            const validasi = document.querySelectorAll('.validasi')[index];
            const isChecked = validasi?.checked ?? false;

            const hasData = cf.value.trim() !== '' || baik.trim() !== '' || operator.trim() !== '';
            const wasLoaded = validasi?.closest('tr')?.dataset.wasLoaded === '1';

            if (!hasData && !isChecked && !wasLoaded) return;

            formData.append('row_id[]', index);
            formData.append('no_wos[]', document.querySelectorAll('[name="no_wos[]"]')[index]?.value ?? '');
            formData.append('mc_no[]', document.querySelectorAll('[name="mc_no[]"]')[index]?.value ?? '');
            formData.append('type[]', document.querySelectorAll('[name="type[]"]')[index]?.value ?? '');
            formData.append('cf_no[]', cf.value);
            formData.append('customer[]', document.querySelectorAll('[name="customer[]"]')[index]?.value ?? '');
            formData.append('berat_pcs[]', document.querySelectorAll('[name="berat_pcs[]"]')[index]?.value ?? '');
            formData.append('hasil_kg[]', document.querySelectorAll('[name="hasil_kg[]"]')[index]?.value ?? '');
            formData.append('baik[]', baik);
            formData.append('rusak[]', document.querySelectorAll('[name="rusak[]"]')[index]?.value ?? '');
            formData.append('total_hasil[]', document.querySelectorAll('[name="total_hasil[]"]')[index]?.value ?? '');
            formData.append('reff_no[]', document.querySelectorAll('[name="reff_no[]"]')[index]?.value ?? '');
            formData.append('start[]', document.querySelectorAll('[name="start[]"]')[index]?.value ?? '');
            formData.append('finish[]', document.querySelectorAll('[name="finish[]"]')[index]?.value ?? '');
            formData.append('operator[]', operator);
            formData.append('keterangan[]', document.querySelectorAll('[name="keterangan[]"]')[index]?.value ?? '');
            formData.append(`validasi[${index}]`, isChecked ? '1' : '0');
        });

        try {
            isSaving = true;
            pendingSave = false;
            const res = await fetch('view/addon/proses/save_hotpress.php', {
                method: 'POST',
                body: formData
            });
            const text = await res.text();
            if (!res.ok) {
                console.error('❌ SAVE HOTPRESS ERROR:', text);
            } else {
                console.log('SAVE HOTPRESS:', text);
            }
        } catch (err) {
            console.error('Fetch error:', err);
        } finally {
            isSaving = false;
            if (pendingSave) {
                pendingSave = false;
                setTimeout(() => saveHotpress(), 100);
            }
        }
    }

    // ================= AUTO SAVE =================
    ['input', 'change'].forEach(evt => {
        document.addEventListener(evt, function(e) {
            if (e.target.closest('#hotpressForm') && isLoaded) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => saveHotpress(), 1000);
            }
        });
    });

    setInterval(() => {
        if (isLoaded) saveHotpress();
    }, 30000);

    // ================= CLEAR FORM =================
    function clearFormDisplay() {
        isLoaded = false;
        cachedTotalBaik = 0;

        const fields = ['no_wos[]', 'mc_no[]', 'type[]', 'cf_no[]', 'customer[]', 'berat_pcs[]',
            'hasil_kg[]', 'baik[]', 'rusak[]', 'total_hasil[]', 'reff_no[]',
            'start[]', 'finish[]', 'operator[]', 'keterangan[]'
        ];

        fields.forEach(name => {
            document.querySelectorAll(`[name="${name}"]`).forEach(el => {
                el.value = '';
                if (!isHpReadonly(el)) {
                    el.readOnly = false;
                    el.disabled = false;
                }
                if (el.tagName === 'TEXTAREA') el.style.height = 'auto';
            });
        });

        document.querySelectorAll('.validasi').forEach(cb => {
            cb.checked = false;
            cb.disabled = false;
        });
        document.querySelectorAll('#hotpressForm tr').forEach(tr => {
            tr.classList.remove('locked-row', 'full-locked');
            delete tr.dataset.wasLoaded;
        });

        const sumTarget = document.getElementById('sumTarget');
        if (sumTarget) sumTarget.value = '';

        const sumActual = document.getElementById('sumActual');
        if (sumActual) sumActual.innerText = '0';

        const sumPercent = document.getElementById('sumPercent');
        if (sumPercent) {
            sumPercent.innerText = '–';
            sumPercent.style.color = '#9ca3af';
        }

        const totalBaik = document.getElementById('totalBaik');
        if (totalBaik) totalBaik.innerText = '0';

        const totalRusak = document.getElementById('totalRusak');
        if (totalRusak) totalRusak.innerText = '0';

        const totalHasil = document.getElementById('totalHasil');
        if (totalHasil) totalHasil.innerText = '0';

        relockReadonlyFields();
    }

    // ================= LOAD =================
    function loadHotpress() {
        if (!getShift()) {
            const label = document.getElementById('shift-mode-label');
            if (label) label.innerHTML = `<span class="view-only-label">Tidak ada shift aktif hari ini</span>`;
            return;
        }
        loadHotpressByShift(activeViewShift);
    }

    async function loadHotpressByShift(shift) {
        if (!shift) return;
        const myRequestId = ++loadRequestId;
        clearFormDisplay();

        try {
            const res = await fetch(`view/addon/proses/load_hotpress.php?tanggal=${activeViewDate}&shift=${shift}`);
            if (!res.ok) throw new Error('load_hotpress failed');
            const json = await res.json();

            if (myRequestId !== loadRequestId) return;

            const data = json.data || [];

            // Target
            const savedTarget = json.target ?? 0;
            const targetInput = document.getElementById('sumTarget');
            if (savedTarget > 0) {
                targetInput.value = savedTarget;
            } else {
                targetInput.value = '';
                targetInput.placeholder = 'isi target';
            }

            // Approve
            if (json.approve) {
                document.querySelectorAll('.approve').forEach((cb, i) => {
                    cb.checked = parseInt(json.approve[i]) === 1;
                });
            }

            // Isi baris
            data.forEach(row => {
                const i = parseInt(row.row_id);

                const fill = (name, val) => {
                    const el = document.querySelectorAll(`[name="${name}"]`)[i];
                    if (el) {
                        el.value = val || '';
                        if (el.tagName === 'TEXTAREA') autoResize(el);
                    }
                };

                fill('no_wos[]', row.no_wos);
                fill('mc_no[]', row.mc_no);
                fill('type[]', row.type);
                fill('cf_no[]', row.cf_no);
                fill('customer[]', row.customer);
                fill('berat_pcs[]', row.berat_pcs);
                fill('hasil_kg[]', row.hasil_kg);
                fill('baik[]', row.baik);
                fill('rusak[]', row.rusak);
                fill('total_hasil[]', row.total_hasil);
                fill('reff_no[]', row.reff_no);
                fill('start[]', row.start_proses);
                fill('finish[]', row.finish_proses);
                fill('operator[]', row.operator);
                fill('keterangan[]', row.keterangan);

                const cb = document.querySelectorAll('.validasi')[i];
                if (cb) {
                    cb.checked = parseInt(row.validasi) === 1;
                    if (cb.checked) lockRowByIndex(i);
                    const tr = cb.closest('tr');
                    if (tr) tr.dataset.wasLoaded = '1';
                }
            });

            updateSummary();
            updateShiftModeLabel();

            // Print info
            const codeEl = document.getElementById('printCodeInfo');
            if (codeEl) codeEl.innerHTML = `EPI-FORM/PROD/01/06/02<br>REV &nbsp;&nbsp;: 0<br>DATE : 09.09.2021`;

            const infoEl = document.getElementById('printHeaderInfo');
            if (infoEl) {
                const [y, m, d] = activeViewDate.split('-');
                infoEl.innerHTML = `Tanggal: ${d}/${m}/${y}<br>${activeViewShift}`;
            }

            isLoaded = true;
            relockReadonlyFields();

        } catch (err) {
            console.error('Gagal load hotpress:', err);
            isLoaded = true;
        }
    }

    // ================= SHIFT CHANGE CHECK =================
    function checkShiftChange() {
        const newShift = getShift();
        if (!newShift) {
            const label = document.getElementById('shift-mode-label');
            if (label) label.innerHTML = `<span class="view-only-label">Tidak ada shift aktif hari ini</span>`;
            setFormReadOnly(true);
            return;
        }

        if (newShift !== lastShift) {
            if (!shiftChangeTime) {
                shiftChangeTime = Date.now();
                shiftChangedFrom = lastShift;
                localStorage.setItem('hp_shiftChangeTime', shiftChangeTime);
                localStorage.setItem('hp_shiftChangedFrom', shiftChangedFrom);
            }

            const elapsedMinutes = (Date.now() - shiftChangeTime) / 1000 / 60;

            if (elapsedMinutes < 30) {
                const label = document.getElementById('shift-mode-label');
                const sisaMenit = Math.ceil(30 - elapsedMinutes);
                if (label) {
                    label.innerHTML = `
                    <span class="view-only-label" style="color:#ea580c;">
                        ⚠️ Shift berganti ke ${newShift} — Toleransi pengisian: ${sisaMenit} menit lagi
                    </span>`;
                }
                setFormReadOnly(false);
                return;
            }

            // Toleransi habis, reset ke shift baru
            lastShift = newShift;
            activeViewShift = newShift;
            activeViewDate = getShiftDate();
            shiftChangeTime = null;
            shiftChangedFrom = null;
            localStorage.removeItem('hp_shiftChangeTime');
            localStorage.removeItem('hp_shiftChangedFrom');

            clearFormDisplay();
            const dd = document.getElementById('shiftDropdown');
            if (dd) dd.value = newShift;
            loadHotpressByShift(newShift);

        } else {
            // Shift sama, reset timer toleransi
            if (shiftChangeTime !== null) {
                shiftChangeTime = null;
                shiftChangedFrom = null;
                localStorage.removeItem('hp_shiftChangeTime');
                localStorage.removeItem('hp_shiftChangedFrom');
            }
        }
    }

    setInterval(checkShiftChange, 1000);

    // ================= PREPARE PRINT =================
    window.addEventListener('beforeprint', function() {
        document.querySelectorAll('.print-cell-value').forEach(s => s.remove());
        document.querySelectorAll('.hotpress-table input[type="number"], .hotpress-table input[type="text"]')
            .forEach(el => {
                const span = document.createElement('span');
                span.className = 'print-cell-value';
                span.textContent = el.value || '';
                el.parentNode.insertBefore(span, el.nextSibling);
            });
    });

    window.addEventListener('afterprint', function() {
        document.querySelectorAll('.print-cell-value').forEach(s => s.remove());
    });

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

    // ================= ENTER TO NEXT ROW =================
    function initEnterNavigation() {
        const fieldOrder = [
            'no_wos[]',
            'mc_no[]',
            'cf_no[]',
            'berat_pcs[]',
            'hasil_kg[]',
            'baik[]',
            'rusak[]',
            'total_hasil[]',
            'reff_no[]',
            'start[]',
            'finish[]',
            'operator[]',
            'keterangan[]'
        ];


        function getAllFieldsByOrder() {
            const rows = [...document.querySelectorAll('#hotpressForm tbody tr')]
                .filter(tr => tr.querySelector('.validasi'));

            const cells = [];
            rows.forEach(tr => {
                fieldOrder.forEach(name => {
                    const el = tr.querySelector(`[name="${name}"]`);
                    if (el && !el.disabled && !el.readOnly) {
                        cells.push(el);
                    }
                });
            });
            return cells;
        }

        document.querySelector('#hotpressForm').addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;

            const target = e.target;
            const isInput = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA';
            if (!isInput) return;

            e.preventDefault();

            const cells = getAllFieldsByOrder();
            const idx = cells.indexOf(target);
            if (idx === -1) return;

            const next = cells[idx + 1];
            if (next) {
                next.focus();
                if (next.tagName === 'TEXTAREA') {
                    next.select();
                }
            }
        });
    }

    function fixStickyHeaderTop() {
        const firstRow = document.querySelector('.hotpress-table tr:first-child');
        if (!firstRow) return;
        const h = firstRow.offsetHeight;
        document.querySelectorAll('.hotpress-table tr:nth-child(2) th').forEach(th => {
            th.style.top = h + 'px';
        });
    }

    fixStickyHeaderTop();
    window.addEventListener('resize', fixStickyHeaderTop);

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
            initEnterNavigation();
            relockReadonlyFields();
            loadHotpress();
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