<?php
include 'koneksi.php';

$kamar = $_GET['kamar'];
$tegangan = $_GET['tegangan'];
$arus = $_GET['arus'];
$daya = $_GET['daya'];
$energi = $_GET['energi'];

mysqli_query($conn, "INSERT INTO data_listrik(kamar, tegangan, arus, daya, energi)
VALUES('$kamar','$tegangan','$arus','$daya','$energi')");

echo "Data tersimpan";
?>