<?php

include 'koneksi.php';
include 'config.php';
include 'telegram.php';

function ambilDataMaksimum($conn, $kamar)
{
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

function cekKamar($conn, $kamar, $namaKamar, $tarifPerKwh, $ambangBatas, $botToken, $chatId)
{
    $data = ambilDataMaksimum($conn, $kamar);

    if (!$data) {
        return;
    }

    $energi = (float)$data['energi'];
    $daya   = (float)$data['daya'];
    $waktu  = $data['waktu'];


    $biaya = $energi * $tarifPerKwh;


    $cekStatus = mysqli_query($conn, "
        SELECT sudah_dikirim
        FROM status_notifikasi
        WHERE kamar='$kamar'
        LIMIT 1
    ");

    $status = mysqli_fetch_assoc($cekStatus);

    $sudahDikirim = isset($status['sudah_dikirim'])
        ? (int)$status['sudah_dikirim']
        : 0;


    if ($biaya > $ambangBatas && $sudahDikirim == 0) {

        $pesan =
            "⚠️ PERINGATAN PEMAKAIAN LISTRIK\n\n" .
            $namaKamar . " telah melewati ambang batas.\n\n" .
            "Energi : " . number_format($energi, 4, ',', '.') . " kWh\n" .
            "Daya : " . number_format($daya, 2, ',', '.') . " W\n" .
            "Biaya : Rp " . number_format($biaya, 0, ',', '.') . "\n" .
            "Batas : Rp " . number_format($ambangBatas, 0, ',', '.') . "\n\n" .
            "Waktu : " . $waktu . "\n\n" .
            "kamu sudah memakai listrik melebihi kesepakatan";


        $berhasil = kirimTelegram($botToken, $chatId, $pesan);


        if ($berhasil) {
            mysqli_query($conn, "
                UPDATE status_notifikasi
                SET sudah_dikirim=1
                WHERE kamar='$kamar'
            ");
        }
    }
}


cekKamar(
    $conn,
    'kamar_1',
    'Kamar 1',
    $tarifPerKwh,
    $ambangBatas,
    $botToken,
    $chatId
);

cekKamar(
    $conn,
    'kamar_2',
    'Kamar 2',
    $tarifPerKwh,
    $ambangBatas,
    $botToken,
    $chatId
);

echo "Pengecekan notifikasi selesai.";
?>