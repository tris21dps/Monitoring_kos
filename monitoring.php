<?php
include 'koneksi.php';
include 'config.php';

function ambilDataMaksimum($conn, $kamar) {
    $kamar = mysqli_real_escape_string($conn, $kamar);

    $sql = mysqli_query($conn, "
        SELECT energi, daya, waktu
        FROM data_listrik
        WHERE kamar='$kamar'
        ORDER BY energi DESC, waktu DESC, id DESC
        LIMIT 1
    ");

    return mysqli_fetch_assoc($sql);
}

$dataKamar1 = ambilDataMaksimum($conn, 'kamar_1');
$dataKamar2 = ambilDataMaksimum($conn, 'kamar_2');

$energi1 = isset($dataKamar1['energi']) ? (float)$dataKamar1['energi'] : 0;
$energi2 = isset($dataKamar2['energi']) ? (float)$dataKamar2['energi'] : 0;

$daya1 = isset($dataKamar1['daya']) ? (float)$dataKamar1['daya'] : 0;
$daya2 = isset($dataKamar2['daya']) ? (float)$dataKamar2['daya'] : 0;

$waktu1 = $dataKamar1['waktu'] ?? '-';
$waktu2 = $dataKamar2['waktu'] ?? '-';

$biaya1 = $energi1 * $tarifPerKwh;
$biaya2 = $energi2 * $tarifPerKwh;

$sisa1 = $ambangBatas - $biaya1;
$sisa2 = $ambangBatas - $biaya2;

$over1 = $biaya1 > $ambangBatas;
$over2 = $biaya2 > $ambangBatas;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Perbandingan Pemakaian Listrik</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        h2 {
            color: #222;
        }

        .card {
            background: #f9fafb;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
        }

        .card.red {
            background: #fff1f1;
            border: 1px solid #ef4444;
        }

        .judul {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .nilai {
            margin: 6px 0;
        }

        .aman {
            font-weight: bold;
            color: #166534;
        }

        .bahaya {
            font-weight: bold;
            color: #b91c1c;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Monitoring Pemakaian Listrik Kos</h2>

    <!-- Kamar 1 -->
    <div class="card <?= $over1 ? 'red' : '' ?>">

        <div class="judul">Kamar 1</div>

        <div class="nilai">
            Energi tertinggi:
            <?= number_format($energi1, 4, ',', '.') ?> kWh
        </div>

        <div class="nilai">
            Daya saat tertinggi:
            <?= number_format($daya1, 2, ',', '.') ?> W
        </div>

        <div class="nilai">
            Biaya:
            Rp <?= number_format($biaya1, 0, ',', '.') ?>
        </div>

        <div class="nilai">
            Ambang batas:
            Rp <?= number_format($ambangBatas, 0, ',', '.') ?>
        </div>

        <div class="nilai">
            Sisa pemakaian:

            <?php if ($over1): ?>

                <span class="bahaya">
                    kamu sudah memakai listrik melebihi kesepakatan
                </span>

            <?php else: ?>

                <span class="aman">
                    kamu masih bisa menggunakan listrik lagi
                    Rp <?= number_format($sisa1, 0, ',', '.') ?>
                </span>

            <?php endif; ?>
        </div>

        <div class="nilai">
            Tanggal dan jam: <?= $waktu1 ?>
        </div>

    </div>


    <!-- Kamar 2 -->
    <div class="card <?= $over2 ? 'red' : '' ?>">

        <div class="judul">Kamar 2</div>

        <div class="nilai">
            Energi tertinggi:
            <?= number_format($energi2, 4, ',', '.') ?> kWh
        </div>

        <div class="nilai">
            Daya saat tertinggi:
            <?= number_format($daya2, 2, ',', '.') ?> W
        </div>

        <div class="nilai">
            Biaya:
            Rp <?= number_format($biaya2, 0, ',', '.') ?>
        </div>

        <div class="nilai">
            Ambang batas:
            Rp <?= number_format($ambangBatas, 0, ',', '.') ?>
        </div>

        <div class="nilai">
            Sisa pemakaian:

            <?php if ($over2): ?>

                <span class="bahaya">
                    kamu sudah memakai listrik melebihi kesepakatan
                </span>

            <?php else: ?>

                <span class="aman">
                    kamu masih bisa menggunakan listrik lagi
                    Rp <?= number_format($sisa2, 0, ',', '.') ?>
                </span>

            <?php endif; ?>
        </div>

        <div class="nilai">
            Tanggal dan jam: <?= $waktu2 ?>
        </div>

    </div>

</div>

</body>
</html>