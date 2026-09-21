<h3 class="section-title">Production Data - PREFORMING</h3>

<?php
$id = $_GET['seksi'];

$sql = "
    SELECT 
        produksi.*,
        mesin.nama_mesin
    FROM produksi
    JOIN mesin 
        ON produksi.mesin_id = mesin.id_mesin
    JOIN seksi 
        ON mesin.seksi_id = seksi.id_seksi
    WHERE seksi.id_seksi = '$id'
";

$q = odbc_exec($con, $sql);

if (!$q) {
    die("Query Error: " . odbc_errormsg($con));
}
?>

<div class="table-container">
    <table class="preforming-table">

        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>WG Number</th>
                <th>Lot Number</th>
                <th>Target</th>
                <th>OK</th>
                <th>NG</th>
                <th>Operator</th>
                <th>Tanggal / Shift</th>
                <th>Mesin / Mould</th>
                <th>Start / Finish</th>
                <th>Validasi</th>
                <th>Checked By</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            while ($r = odbc_fetch_array($q)) {
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="model-cell">
                        <?= $r['model'] ?><br>
                        <small><?= $r['part_number'] ?? '-' ?></small>
                    </td>
                    <td><?= $r['wg_number'] ?></td>
                    <td><?= $r['lot_number'] ?></td>
                    <td class="target">
                        <?= $r['target'] ?>
                    </td>
                    <td>
                        <input 
                            type="number"
                            class="input-box ok"
                            value="<?= $r['ok'] ?>"
                        >
                    </td>
                    <td>
                        <input 
                            type="number"
                            class="input-box ng"
                            value="<?= $r['ng'] ?>"
                        >
                    </td>
                    <td class="operator">
                        <?= $r['operator'] ?>
                    </td>
                    <td>
                        <?= date("Y-m-d", strtotime($r['tanggal'])) ?><br>
                        <small>Shift <?= $r['shift'] ?></small>
                    </td>
                    <td>
                        <?= $r['nama_mesin'] ?><br>
                        <small><?= $r['mould'] ?></small>
                    </td>

                    <td class="time-cell">
                        <?php if (!empty($r['start_produksi'])) { ?>
                            <?= date("Y-m-d H:i:s", strtotime($r['start_produksi'])) ?><br>
                        <?php } ?>
                        <?php if (!empty($r['finish_produksi'])) { ?>
                            <?= date("Y-m-d H:i:s", strtotime($r['finish_produksi'])) ?>
                        <?php } ?>
                        <div class="iot">IOT PF</div>
                    </td>

                    <td>
                        <?php if ($r['validasi'] == 1) { ?>
                            <span class="check">✔</span>
                        <?php } else { ?>
                            <input 
                                type="checkbox"
                                class="validasi-check"
                                data-id="<?= $r['id'] ?>"
                            >
                        <?php } ?>
                    </td>

                    <td class="checked">
                        <?php if (!empty($r['checked_by'])) { ?>
                            <?= $r['checked_by'] ?><br>
                            <small>
                                <?= date("d-m-Y H:i", strtotime($r['checked_at'])) ?>
                            </small>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>