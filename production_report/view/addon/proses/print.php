<?php
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

require '../../../vendor/autoload.php';
include(__DIR__ . "/../../../database.php");

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Dompdf\Dompdf;
use Dompdf\Options;

// ================= PARAMETER =================
$type           = $_POST['export']       ?? $_GET['export']       ?? 'pdf';
$selected_seksi = $_POST['seksi']        ?? $_GET['seksi']        ?? [];
$date_from      = $_POST['date_from']    ?? $_GET['date_from']    ?? '';
$date_to        = $_POST['date_to']      ?? $_GET['date_to']      ?? '';
$granularitas   = $_POST['granularitas'] ?? $_GET['granularitas'] ?? 'harian';

// Chart images dikirim via POST sebagai JSON base64
$chartImages = [];
if (!empty($_POST['chart_images'])) {
    $decoded = json_decode($_POST['chart_images'], true);
    if (is_array($decoded)) $chartImages = $decoded;
}

if (empty($selected_seksi)) die('Seksi tidak dipilih.');
if (!is_array($selected_seksi)) $selected_seksi = [$selected_seksi];

// ================= CONTROLLER =================
ob_start();
include(__DIR__ . "/../controller/dashboardController.php");
include(__DIR__ . "/../controller/reportController.php");
ob_end_clean();

// ================= DATA SEKSI =================
$seksiId   = $selected_seksi[0];
$data      = $reportBySeksi[$seksiId] ?? [];
$kpi       = $kpiBySeksi[$seksiId]    ?? [];
$seksiNama = 'SEKSI';
foreach ($seksiList as $s) {
    if ($s['id_seksi'] == $seksiId) {
        $seksiNama = strtoupper($s['nama_seksi']);
        break;
    }
}

$totalIot    = $kpi['total_iot']    ?? 0;
$totalActual = $kpi['total_actual'] ?? 0;
$totalNg     = $kpi['total_ng']     ?? 0;
$achievement = $kpi['achievement']  ?? '0.0';
$ngRate      = $totalActual > 0 ? round(($totalNg / $totalActual) * 100, 2) : 0;

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
$fmt          = fn($tgl) => date('d', strtotime($tgl)) . ' ' . $bulan_id[(int)date('n', strtotime($tgl))] . ' ' . date('Y', strtotime($tgl));
$periodeLabel = $fmt($date_from) . ' s/d ' . $fmt($date_to);
$granLabel    = ucfirst($granularitas);
$filename     = 'Laporan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $seksiNama) . '_' . date('Ymd_His');

// Simpan base64 PNG ke file temp (untuk XLSX) ──────
function saveChartTemp($base64)
{
    if (empty($base64)) return null;
    $imgData = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
    $imgData = base64_decode($imgData);
    if (!$imgData) return null;
    $tmpFile = tempnam(sys_get_temp_dir(), 'chart_') . '.png';
    file_put_contents($tmpFile, $imgData);
    return $tmpFile;
}

// Mengembalikan data URI base64 untuk embed di PDF
// Dompdf tidak bisa load dari path filesystem, harus base64 inline
function chartToDataUri($base64)
{
    if (empty($base64)) return null;
    if (strpos($base64, 'data:image') === 0) return $base64;
    return 'data:image/png;base64,' . $base64;
}

// Aggregate data per periode untuk chart native XLSX
$byPeriode = [];
foreach ($data as $row) {
    $p = $row['periode'];
    if (!isset($byPeriode[$p])) $byPeriode[$p] = ['iot' => 0, 'actual' => 0, 'ng' => 0];
    $byPeriode[$p]['iot']    += $row['iot'];
    $byPeriode[$p]['actual'] += $row['actual'];
    $byPeriode[$p]['ng']     += $row['ng'];
}
$periodeKeys = array_keys($byPeriode);
$iotArr      = array_column(array_values($byPeriode), 'iot');
$actualArr   = array_column(array_values($byPeriode), 'actual');
$ngArr       = array_column(array_values($byPeriode), 'ng');
$achArr      = array_map(fn($d) => $d['iot'] > 0 ? round(($d['actual'] / $d['iot']) * 100, 1) : 0, array_values($byPeriode));

// Regresi linear untuk chart achievement
$n = count($periodeKeys);
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

// ================ REPORT XLSX =================
if ($type === 'xlsx') {

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan Produksi');

    $colWidths = ['A' => 30, 'B' => 30, 'C' => 30, 'D' => 30, 'E' => 30, 'F' => 30, 'G' => 30, 'H' => 30];
    foreach ($colWidths as $col => $w) $sheet->getColumnDimension($col)->setWidth($w);
    $r = 1;

    // ROW 1: JUDUL
    $sheet->mergeCells("A{$r}:H{$r}");
    $sheet->setCellValue("A{$r}", 'LAPORAN PRODUKSI ' . $seksiNama);
    $sheet->getStyle("A{$r}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F2A5C']],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(36);
    $r++;

    // ROW 2: PERIODE
    $sheet->mergeCells("A{$r}:H{$r}");
    $sheet->setCellValue("A{$r}", 'Periode: ' . $periodeLabel . '   |   Granularitas: ' . $granLabel . '   |   Dicetak: ' . date('d/m/Y H:i') . ' WIB');
    $sheet->getStyle("A{$r}")->applyFromArray([
        'font'      => ['italic' => true, 'size' => 12, 'color' => ['rgb' => '374151']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(20);
    $r++;

    // SPASI 
    $sheet->getRowDimension($r)->setRowHeight(8);
    $r++;

    // ROW 4-5: KPI
    $kpiLabels = ['Total IOT', 'Total Actual', 'Total NG', 'Achievement', 'NG Rate'];
    $kpiValues = [
        number_format($totalIot, 0, ',', '.'),
        number_format($totalActual, 0, ',', '.'),
        number_format($totalNg, 0, ',', '.'),
        $achievement . '%',
        $ngRate . '%',
    ];
    $kpiColors = ['2563EB', '16A34A', 'DC2626', 'EA580C', 'D97706'];
    $kpiCols   = ['B', 'C', 'D', 'E', 'F'];

    foreach ($kpiCols as $i => $col) {
        $sheet->setCellValue($col . $r, $kpiLabels[$i]);
        $sheet->getStyle($col . $r)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $kpiColors[$i]]],
        ]);
    }
    $sheet->getRowDimension($r)->setRowHeight(18);
    $r++;

    foreach ($kpiCols as $i => $col) {
        $sheet->setCellValue($col . $r, $kpiValues[$i]);
        $sheet->getStyle($col . $r)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $kpiColors[$i]]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            'borders'   => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $kpiColors[$i]]]],
        ]);
    }
    $sheet->getRowDimension($r)->setRowHeight(30);
    $r++;

    // SPASI
    $sheet->getRowDimension($r)->setRowHeight(8);
    $r++;

    // HEADER GRAFIK
    $sheet->mergeCells("A{$r}:H{$r}");
    $sheet->setCellValue("A{$r}", 'GRAFIK PRODUKSI');
    $sheet->getStyle("A{$r}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E4FBF']],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(22);
    $r++;

    // CHART 1 & 2 BERDAMPINGAN
    // Setiap baris Excel tinggi 15pt default.
    $ptPerRow  = 20;   // tinggi tiap baris di area chart (pt)
    $pxPerPt   = 0.75; // 1pt ≈ 0.75px (Excel)

    $chartHeight1 = 165; // px untuk chart 1 & 2
    $chartHeight3 = 218; // px untuk chart 3

    $rows1 = (int)ceil($chartHeight1 / ($ptPerRow / $pxPerPt)); // ~7 baris
    $rows3 = (int)ceil($chartHeight3 / ($ptPerRow / $pxPerPt)); // ~8 baris

    $chartTitlesXlsx = [
        'Trend Produksi - IOT vs Actual',
        'Trend NG - Defect Rate',
        'Visualisasi Tren Performa & Regresi Linear',
        'Akar Masalah',
        'Mesin Bermasalah',
    ];

    if (!empty($chartImages)) {

        // Judul baris chart 1 & 2
        $sheet->mergeCells("A{$r}:C{$r}");
        $sheet->setCellValue("A{$r}", $chartTitlesXlsx[0]);
        $sheet->getStyle("A{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F2A5C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->mergeCells("E{$r}:G{$r}");
        $sheet->setCellValue("E{$r}", $chartTitlesXlsx[1]);
        $sheet->getStyle("E{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F2A5C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
        ]);
        $sheet->getRowDimension($r)->setRowHeight(16);
        $r++;

        // Set tinggi baris area chart 1 & 2
        $chartStartRow = $r;
        for ($i = 0; $i < $rows1; $i++) {
            $sheet->getRowDimension($r + $i)->setRowHeight($ptPerRow);
        }

        // Embed chart 1
        if (!empty($chartImages[0])) {
            $tmp1 = saveChartTemp($chartImages[0]);
            if ($tmp1) {
                $d1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $d1->setName('Chart1');
                $d1->setPath($tmp1);
                $d1->setHeight($chartHeight1);
                $d1->setOffsetX(2);
                $d1->setOffsetY(2);
                $d1->setCoordinates("A{$chartStartRow}");
                $d1->setWorksheet($sheet);
            }
        }

        // Embed chart 2
        if (!empty($chartImages[1])) {
            $tmp2 = saveChartTemp($chartImages[1]);
            if ($tmp2) {
                $d2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $d2->setName('Chart2');
                $d2->setPath($tmp2);
                $d2->setHeight($chartHeight1);
                $d2->setOffsetX(2);
                $d2->setOffsetY(2);
                $d2->setCoordinates("E{$chartStartRow}");
                $d2->setWorksheet($sheet);
            }
        }

        // Maju melewati baris chart 1&2
        $r += $rows1;

        // Spasi
        $sheet->getRowDimension($r)->setRowHeight(6);
        $r++;

        //Judul chart 3
        $sheet->mergeCells("A{$r}:H{$r}");
        $sheet->setCellValue("A{$r}", $chartTitlesXlsx[2]);
        $sheet->getStyle("A{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F2A5C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F3FF']],
        ]);
        $sheet->getRowDimension($r)->setRowHeight(16);
        $r++;

        // Set tinggi baris area chart 3
        $chart3StartRow = $r;
        for ($i = 0; $i < $rows3; $i++) {
            $sheet->getRowDimension($r + $i)->setRowHeight($ptPerRow);
        }

        // Embed chart 3
        if (!empty($chartImages[2])) {
            $tmp3 = saveChartTemp($chartImages[2]);
            if ($tmp3) {
                $d3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $d3->setName('Chart3');
                $d3->setPath($tmp3);
                $d3->setHeight($chartHeight3);
                $d3->setOffsetX(2);
                $d3->setOffsetY(2);
                $d3->setCoordinates("A{$chart3StartRow}");
                $d3->setWorksheet($sheet);
            }
        }

        // Maju melewati baris chart 3
        $r += $rows3;

        // SPASI
        $sheet->getRowDimension($r)->setRowHeight(6);
        $r++;

        // Judul chart 4 & 5 (berdampingan)
        $sheet->mergeCells("A{$r}:C{$r}");
        $sheet->setCellValue("A{$r}", $chartTitlesXlsx[3]);
        $sheet->getStyle("A{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F2A5C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']],
        ]);
        $sheet->mergeCells("E{$r}:G{$r}");
        $sheet->setCellValue("E{$r}", $chartTitlesXlsx[4]);
        $sheet->getStyle("E{$r}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F2A5C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
        ]);
        $sheet->getRowDimension($r)->setRowHeight(16);
        $r++;

        // Set tinggi baris area chart 4 & 5
        $chart45StartRow = $r;
        $chartHeight45   = 246;
        $rows45 = (int)ceil($chartHeight45 / ($ptPerRow / $pxPerPt));
        for ($i = 0; $i < $rows45; $i++) {
            $sheet->getRowDimension($r + $i)->setRowHeight($ptPerRow);
        }

        // Embed chart 4 (Akar Masalah)
        if (!empty($chartImages[3])) {
            $tmp4 = saveChartTemp($chartImages[3]);
            if ($tmp4) {
                $d4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $d4->setName('Chart4');
                $d4->setPath($tmp4);
                $d4->setHeight($chartHeight45);
                $d4->setOffsetX(2);
                $d4->setOffsetY(2);
                $d4->setCoordinates("A{$chart45StartRow}");
                $d4->setWorksheet($sheet);
            }
        }

        // Embed chart 5 (Mesin Bermasalah)
        if (!empty($chartImages[4])) {
            $tmp5 = saveChartTemp($chartImages[4]);
            if ($tmp5) {
                $d5 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $d5->setName('Chart5');
                $d5->setPath($tmp5);
                $d5->setHeight($chartHeight45);
                $d5->setOffsetX(2);
                $d5->setOffsetY(2);
                $d5->setCoordinates("E{$chart45StartRow}");
                $d5->setWorksheet($sheet);
            }
        }

        // Maju melewati baris chart 4 & 5
        $r += $rows45;
    } else {
        $sheet->mergeCells("A{$r}:H{$r}");
        $sheet->setCellValue("A{$r}", 'Chart tidak tersedia');
        $sheet->getStyle("A{$r}")->getFont()->setItalic(true);
        $sheet->getRowDimension($r)->setRowHeight(20);
        $r++;
    }

    // SPASI
    $sheet->getRowDimension($r)->setRowHeight(10);
    $r++;

    // HEADER TABEL
    $sheet->mergeCells("A{$r}:H{$r}");
    $sheet->setCellValue("A{$r}", 'DETAIL LAPORAN PRODUKSI');
    $sheet->getStyle("A{$r}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E4FBF']],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(22);
    $r++;

    // KOLOM HEADER
    $headers    = ['Periode', 'Mesin', 'Shift', 'Target', 'IOT', 'Actual', 'NG', 'Achievement'];
    $headerCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    foreach ($headerCols as $i => $col) {
        $sheet->setCellValue($col . $r, $headers[$i]);
    }
    $sheet->getStyle("A{$r}:H{$r}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F2A5C']],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E4FBF']]],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(20);
    $r++;

    // DATA ROWS
    foreach ($data as $i => $row) {
        $target = $row['target'] ?? 0;
        $ach    = $target > 0 ? round(($row['actual'] / $target) * 100, 1) : 0;
        $bg     = ($i % 2 === 0) ? 'FFFFFF' : 'EFF6FF';
        $achBg  = $ach >= 100 ? 'D1FAE5' : ($ach >= 90 ? 'FEF3C7' : 'FEE2E2');
        $achClr = $ach >= 100 ? '065F46' : ($ach >= 90 ? '92400E' : '991B1B');

        $vals = [
            'A' => $row['periode'],
            'B' => $row['mesin'],
            'C' => 'Shift ' . $row['shift'],
            'D' => $target > 0 ? number_format($target, 0, ',', '.') : '–',
            'E' => number_format($row['iot'], 0, ',', '.'),
            'F' => number_format($row['actual'], 0, ',', '.'),
            'G' => number_format($row['ng'], 0, ',', '.'),
        ];
        foreach ($vals as $col => $val) {
            $sheet->setCellValue($col . $r, $val);
            $sheet->getStyle($col . $r)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'font'      => ['size' => 12],
            ]);
        }
        $sheet->setCellValue("H{$r}", $ach . '%');
        $sheet->getStyle("H{$r}")->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $achBg]],
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $achClr]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
        ]);
        $sheet->getRowDimension($r)->setRowHeight(18);
        $r++;
    }

    // TOTAL ROW
    $sheet->mergeCells("A{$r}:C{$r}");
    $sheet->setCellValue("A{$r}", 'TOTAL');
    $sheet->setCellValue("D{$r}", '');  
    $sheet->setCellValue("E{$r}", number_format($totalIot, 0, ',', '.'));
    $sheet->setCellValue("F{$r}", number_format($totalActual, 0, ',', '.'));
    $sheet->setCellValue("G{$r}", number_format($totalNg, 0, ',', '.'));
    $sheet->setCellValue("H{$r}", $achievement . '%');
    $sheet->getStyle("A{$r}:H{$r}")->applyFromArray([
        'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F2A5C']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E4FBF']]],
    ]);
    $sheet->getRowDimension($r)->setRowHeight(24);

    // DOWNLOAD
    while (ob_get_level()) ob_end_clean();

    $tmpXlsx = tempnam(sys_get_temp_dir(), 'xlsx_') . '.xlsx';
    $writer  = new Xlsx($spreadsheet);
    $writer->setIncludeCharts(false);
    $writer->save($tmpXlsx);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Content-Length: ' . filesize($tmpXlsx));
    header('Cache-Control: max-age=0');

    readfile($tmpXlsx);
    unlink($tmpXlsx);
    exit;
}

// ================ EXPORT PDF =================
ob_start();

$chartTempFiles = [];
$chartTitles = [
    'Trend Produksi (IOT vs Actual)',
    'Trend NG & Defect Rate',
    'Visualisasi Tren Performa & Regresi Linear',
    'Akar Masalah',
    'Mesin Bermasalah',
];
foreach ($chartImages as $i => $b64) {
    $dataUri = chartToDataUri($b64);
    if ($dataUri) $chartTempFiles[] = ['file' => $dataUri, 'title' => $chartTitles[$i] ?? 'Chart ' . ($i + 1)];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #fff;
            padding: 16px;
        }

        /* HEADER */
        .doc-header {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            border-bottom: 3px solid #0f2a5c;
            padding-bottom: 10px;
        }

        .doc-header-left {
            display: table-cell;
            vertical-align: middle;
            width: 65%;
        }

        .doc-header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .doc-title {
            font-size: 17px;
            font-weight: bold;
            color: #0f2a5c;
            letter-spacing: 0.5px;
        }

        .doc-subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }

        .doc-meta {
            font-size: 9px;
            color: #9ca3af;
            line-height: 1.7;
            text-align: right;
        }

        /* KPI */
        .kpi-row {
            display: table;
            width: 100%;
            margin-bottom: 14px;
        }

        .kpi-box {
            display: table-cell;
            width: 20%;
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            border-right: none;
        }

        .kpi-box:first-child {
            border-radius: 6px 0 0 6px;
        }

        .kpi-box:last-child {
            border-radius: 0 6px 6px 0;
            border-right: 1px solid #e5e7eb;
        }

        .kpi-box-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9ca3af;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .kpi-box-val {
            font-size: 15px;
            font-weight: bold;
        }

        .kpi-box.blue {
            background: #eff6ff;
        }

        .val-blue {
            color: #2563eb;
        }

        .kpi-box.green {
            background: #f0fdf4;
        }

        .val-green {
            color: #16a34a;
        }

        .kpi-box.red {
            background: #fef2f2;
        }

        .val-red {
            color: #dc2626;
        }

        .kpi-box.orange {
            background: #fff7ed;
        }

        .val-orange {
            color: #ea580c;
        }

        .kpi-box.yellow {
            background: #fffbeb;
        }

        .val-yellow {
            color: #d97706;
        }

        /* CHART SECTION */
        .chart-section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .chart-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 12px;
        }

        .chart-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .chart-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .chart-box-header {
            background: #f9fafb;
            padding: 7px 12px;
            font-size: 10px;
            font-weight: bold;
            color: #0f2a5c;
            border-bottom: 1px solid #e5e7eb;
        }

        .chart-box-body {
            padding: 8px;
            text-align: center;
        }

        .chart-box-body img {
            width: 100%;
            height: auto;
            display: block;
        }

        .chart-full {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .chart-full-header {
            background: #f9fafb;
            padding: 7px 12px;
            font-size: 10px;
            font-weight: bold;
            color: #0f2a5c;
            border-bottom: 1px solid #e5e7eb;
        }

        .chart-full-body {
            padding: 8px;
            text-align: center;
        }

        .chart-full-body img {
            width: 100%;
            height: auto;
            display: block;
        }

        .no-chart {
            padding: 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 9px;
            background: #f9fafb;
        }

        /* TABEL */
        .section-label {
            font-size: 11px;
            font-weight: bold;
            color: #0f2a5c;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #1e4fbf;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        thead tr {
            background: #0f2a5c;
            color: #fff;
        }

        thead th {
            padding: 6px 7px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        tbody td {
            padding: 5px 7px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
            font-size: 9px;
        }

        .tfoot-row {
            background: #0f2a5c !important;
            color: #fff;
            font-weight: bold;
        }

        .tfoot-row td {
            border: none;
            font-size: 9px;
            padding: 6px 7px;
        }

        .ach-achieve {
            background: #d1fae5;
            color: #065f46;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: bold;
        }

        .ach-eval {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: bold;
        }

        .ach-below {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: bold;
        }

        /* FOOTER */
        .doc-footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="doc-header">
        <div class="doc-header-left">
            <div class="doc-title">LAPORAN EVALUASI PRODUKSI</div>
            <div class="doc-subtitle">
                <?= htmlspecialchars($seksiNama) ?> &nbsp;|&nbsp; Granularitas: <?= htmlspecialchars($granLabel) ?>
            </div>
        </div>
        <div class="doc-header-right">
            <div class="doc-meta">
                Periode: <?= htmlspecialchars($periodeLabel) ?><br>
                Dicetak: <?= date('d/m/Y H:i') ?> WIB
            </div>
        </div>
    </div>

    <!-- KPI -->
    <div class="kpi-row">
        <div class="kpi-box blue">
            <div class="kpi-box-label">Total IOT</div>
            <div class="kpi-box-val val-blue"><?= number_format($totalIot, 0, ',', '.') ?></div>
        </div>
        <div class="kpi-box green">
            <div class="kpi-box-label">Total Actual</div>
            <div class="kpi-box-val val-green"><?= number_format($totalActual, 0, ',', '.') ?></div>
        </div>
        <div class="kpi-box red">
            <div class="kpi-box-label">Total NG</div>
            <div class="kpi-box-val val-red"><?= number_format($totalNg, 0, ',', '.') ?></div>
        </div>
        <div class="kpi-box orange">
            <div class="kpi-box-label">Achievement</div>
            <div class="kpi-box-val val-orange"><?= $achievement ?>%</div>
        </div>
        <div class="kpi-box yellow">
            <div class="kpi-box-label">NG Rate</div>
            <div class="kpi-box-val val-yellow"><?= $ngRate ?>%</div>
        </div>
    </div>

    <!-- CHART: Produksi + NG (2 kolom) -->
    <div class="chart-section">
        <div class="chart-grid">
            <div class="chart-cell">
                <div class="chart-box">
                    <div class="chart-box-header">
                        <span style="display:inline-block;width:10px;height:10px;background:#2563eb;border-radius:2px;margin-right:6px;"></span>
                        Trend Produksi &mdash; IOT vs Actual
                    </div>
                    <div class="chart-box-body">
                        <?php if (!empty($chartTempFiles[0])): ?>
                            <img src="<?= htmlspecialchars($chartTempFiles[0]['file']) ?>" style="max-width:100%;height:auto;">
                        <?php else: ?>
                            <div class="no-chart">Chart tidak tersedia</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="chart-cell">
                <div class="chart-box">
                    <div class="chart-box-header">
                        <span style="display:inline-block;width:10px;height:10px;background:#dc2626;border-radius:2px;margin-right:6px;"></span>
                        Trend NG &mdash; Defect Rate
                    </div>
                    <div class="chart-box-body">
                        <?php if (!empty($chartTempFiles[1])): ?>
                            <img src="<?= htmlspecialchars($chartTempFiles[1]['file']) ?>" style="max-width:100%;height:auto;">
                        <?php else: ?>
                            <div class="no-chart">Chart tidak tersedia</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART: Achievement full width -->
        <div class="chart-full">
            <div class="chart-full-header">
                <span style="display:inline-block;width:10px;height:10px;background:#7c3aed;border-radius:2px;margin-right:6px;"></span>
                Visualisasi Tren Performa &amp; Regresi Linear
            </div>
            <div class="chart-full-body">
                <?php if (!empty($chartTempFiles[2])): ?>
                    <img src="<?= htmlspecialchars($chartTempFiles[2]['file']) ?>" style="max-width:100%;height:auto;">
                <?php else: ?>
                    <div class="no-chart">Chart tidak tersedia</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CHART BREAKDOWN 2 kolom -->
    <?php if (!empty($chartTempFiles[3]) || !empty($chartTempFiles[4])): ?>
        <div class="chart-grid" style="margin-top:8px;">
            <div class="chart-cell">
                <div class="chart-box">
                    <div class="chart-box-header">
                        Akar Masalah
                    </div>
                    <div class="chart-box-body">
                        <?php if (!empty($chartTempFiles[3])): ?>
                            <img src="<?= htmlspecialchars($chartTempFiles[3]['file']) ?>" style="max-width:100%;height:auto;">
                        <?php else: ?>
                            <div class="no-chart">Tidak ada data</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="chart-cell">
                <div class="chart-box">
                    <div class="chart-box-header">
                        Mesin Bermasalah
                    </div>
                    <div class="chart-box-body">
                        <?php if (!empty($chartTempFiles[4])): ?>
                            <img src="<?= htmlspecialchars($chartTempFiles[4]['file']) ?>" style="max-width:100%;height:auto;">
                        <?php else: ?>
                            <div class="no-chart">Tidak ada data</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- TABEL -->
    <div class="section-label">
        <span style="display:inline-block;width:10px;height:10px;background:#0f2a5c;border-radius:2px;margin-right:6px;"></span>
        Detail Laporan
    </div>
    <table>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row):
                $target = $row['target'] ?? 0;
                $ach    = $target > 0 ? round(($row['actual'] / $target) * 100, 1) : 0;
                $achCls = $ach >= 100 ? 'ach-achieve' : ($ach >= 90 ? 'ach-eval' : 'ach-below');
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['periode']) ?></td>
                    <td><?= htmlspecialchars($row['mesin'])   ?></td>
                    <td>Shift <?= htmlspecialchars($row['shift']) ?></td>
                    <td><?= $target > 0 ? number_format($target, 0, ',', '.') : '–' ?></td>
                    <td><?= number_format($row['iot'],    0, ',', '.') ?></td>
                    <td><?= number_format($row['actual'], 0, ',', '.') ?></td>
                    <td><?= number_format($row['ng'],     0, ',', '.') ?></td>
                    <td><span class="<?= $achCls ?>"><?= $ach ?>%</span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="tfoot-row">
                <td>TOTAL</td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= number_format($totalIot,    0, ',', '.') ?></td>
                <td><?= number_format($totalActual, 0, ',', '.') ?></td>
                <td><?= number_format($totalNg,     0, ',', '.') ?></td>
                <td><?= $achievement ?>%</td>
            </tr>
        </tfoot>
    </table>

    <div class="doc-footer">
        Dokumen digenerate otomatis oleh sistem Exedy Production Report &bull; <?= date('d F Y') ?>
    </div>

</body>

</html>
<?php
$html = ob_get_clean();

while (ob_get_level()) {
    ob_end_clean();
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
header('Cache-Control: max-age=0');

$dompdf->stream($filename . '.pdf', ['Attachment' => true]);
exit;
