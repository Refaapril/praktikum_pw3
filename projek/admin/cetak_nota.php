 <?php
// cetak_nota.php - VERSION WITH DEBUG
session_start();
require_once "../config/koneksi.php";





// Cek apakah parameter id ada
if (isset($_GET['id'])) {
    $pesanan_id = $_GET['id'];
    echo "<strong style='color: green;'>✓ Parameter 'id' ditemukan: " . $pesanan_id . "</strong><br>";
    
    // Cek di database
    $check_query = "SELECT pesanan_id, no_pesanan FROM pesanan WHERE pesanan_id = '$pesanan_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if ($check_result) {
        $count = mysqli_num_rows($check_result);
        echo "<strong>Data ditemukan di database: " . $count . " baris</strong><br>";
        
        if ($count > 0) {
            $row = mysqli_fetch_assoc($check_result);
            echo "<strong>No Pesanan: " . $row['no_pesanan'] . "</strong><br>";
            
            // Jika data ditemukan, lanjutkan tanpa tampilkan debug
            echo "</div>";
            
            // SIMPAN ID untuk diproses
            $pesanan_id = mysqli_real_escape_string($conn, $_GET['id']);
            
            // LANJUTKAN KE KODE ASLI...
            // HAPUS SEMUA ECHO DEBUG DI ATAS DAN LANJUTKAN DENGAN KODE CETAK NOTA
            
        } else {
            echo "</div>";
            die("Data pesanan tidak ditemukan di database.");
        }
    } else {
        echo "<strong style='color: red;'>✗ Query error: " . mysqli_error($conn) . "</strong><br>";
        echo "</div>";
        die("Database error.");
    }
} else {
    echo "</div>";
    
    // Tampilkan form untuk input manual
    echo "<h3>Manual Input ID Pesanan</h3>";
    echo "<form method='GET' action=''>";
    echo "<input type='text' name='id' placeholder='Masukkan ID Pesanan'>";
    echo "<button type='submit'>Lihat Nota</button>";
    echo "</form>";
    
    // Tampilkan daftar pesanan yang ada
    echo "<h3>Daftar Pesanan Tersedia:</h3>";
    $list_query = "SELECT pesanan_id, no_pesanan FROM pesanan ORDER BY tanggal_pesan DESC LIMIT 10";
    $list_result = mysqli_query($conn, $list_query);
    
    if (mysqli_num_rows($list_result) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>No Pesanan</th><th>Aksi</th></tr>";
        while($row = mysqli_fetch_assoc($list_result)) {
            echo "<tr>";
            echo "<td>" . $row['pesanan_id'] . "</td>";
            echo "<td>" . $row['no_pesanan'] . "</td>";
            echo "<td><a href='cetak_nota.php?id=" . $row['pesanan_id'] . "'>Cetak Nota</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    die();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTA PESANAN</title>
    <!-- ... CSS dan kode HTML lainnya ... -->
</head>
<body>
    <!-- ... Isi nota ... -->
</body>
</html>
