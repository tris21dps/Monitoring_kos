<?php include 'koneksi.php'; ?>
<h2>Data Kamar 2</h2>
<table border="1" cellpadding="10">
<tr><th>Tegangan</th><th>Arus</th><th>Daya</th><th>Energi</th><th>Biaya</th><th>Waktu</th></tr>
<?php
$q = mysqli_query($conn, "SELECT * FROM data_listrik WHERE kamar='kamar_2' ORDER BY id DESC LIMIT 20");
while($row = mysqli_fetch_assoc($q)) {
    $biaya = $row['energi'] * 1444.7;
    echo "<tr>
        <td>{$row['tegangan']} V</td>
        <td>{$row['arus']} A</td>
        <td>{$row['daya']} W</td>
        <td>{$row['energi']} kWh</td>
        <td>Rp " . number_format($biaya,0,',','.') . "</td>
        <td>{$row['waktu']}</td>
    </tr>";
}
?>
</table>