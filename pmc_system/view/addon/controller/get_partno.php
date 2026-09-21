<?php
session_start();
include(__DIR__ . "/../../../database.php");

while (ob_get_level() > 0) ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode(['data' => []]);
    exit;
}

$q_safe = str_replace("'", "''", $q);

$sql = "
    SELECT TOP 20 partno, customer, size, mataborstandar
    FROM (
        SELECT DISTINCT
            LTRIM(RTRIM(PARTNO))          AS partno,
            LTRIM(RTRIM(ISNULL(CUSTOMER, ''))) AS customer,
            LTRIM(RTRIM(ISNULL(SIZE, ''))) AS size,
            LTRIM(RTRIM(ISNULL(MATABORSTANDAR, ''))) AS mataborstandar,
            1 AS urut
        FROM cf
        WHERE PARTNO IS NOT NULL
        AND LTRIM(RTRIM(PARTNO)) != ''
        AND UPPER(LTRIM(RTRIM(PARTNO))) LIKE UPPER('$q_safe%')
        AND LTRIM(RTRIM(ISNULL(BORNONBOR, ''))) = 'B'   

        UNION ALL

        SELECT DISTINCT
            LTRIM(RTRIM(PARTNO))          AS partno,
            LTRIM(RTRIM(ISNULL(CUSTOMER, ''))) AS customer,
            LTRIM(RTRIM(ISNULL(SIZE, ''))) AS size,
            LTRIM(RTRIM(ISNULL(MATABORSTANDAR, ''))) AS mataborstandar,
            2 AS urut
        FROM cf
        WHERE PARTNO IS NOT NULL
        AND LTRIM(RTRIM(PARTNO)) != ''
        AND UPPER(LTRIM(RTRIM(PARTNO))) LIKE UPPER('%$q_safe%')
        AND UPPER(LTRIM(RTRIM(PARTNO))) NOT LIKE UPPER('$q_safe%')
        AND LTRIM(RTRIM(ISNULL(BORNONBOR, ''))) = 'B'  
    ) AS combined
    ORDER BY urut ASC, partno ASC
";

$result = odbc_exec($con, $sql);

if (!$result) {
    echo json_encode(['error' => odbc_errormsg($con)]);
    exit;
}

$data = [];
$seen = [];

while ($row = odbc_fetch_array($result)) {
    $partno = trim($row['partno'] ?? '');
    if ($partno === '') continue;

    $key = strtolower($partno);
    if (isset($seen[$key])) continue;
    $seen[$key] = true;

    $customer       = trim($row['customer'] ?? '');
    $size           = trim($row['size'] ?? '');
    $mataborstandar = trim($row['mataborstandar'] ?? '');

    $partno         = mb_convert_encoding($partno, 'UTF-8', 'Windows-1252');
    $customer       = mb_convert_encoding($customer, 'UTF-8', 'Windows-1252');
    $size           = mb_convert_encoding($size, 'UTF-8', 'Windows-1252');
    $mataborstandar = mb_convert_encoding($mataborstandar, 'UTF-8', 'Windows-1252');

    $data[] = [
        'partno'         => $partno,
        'customer'       => $customer,
        'size'           => $size,
        'mataborstandar' => $mataborstandar
    ];
}

$output = json_encode(
    ['data' => $data],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
);

echo $output ?: json_encode(['data' => [], 'error' => json_last_error_msg()]);
exit;