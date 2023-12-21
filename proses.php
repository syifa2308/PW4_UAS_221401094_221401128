<?php
// proses_transaksi.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'function.php';
require 'cek.php';

$user_id = $_SESSION['user_id'];

// Ambil data dari form pengiriman
$provinsi = isset($_POST['provinsi']) ? $_POST['provinsi'] : '';
$kota = isset($_POST['kota']) ? $_POST['kota'] : '';
$kecamatan = isset($_POST['kecamatan']) ? $_POST['kecamatan'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
$totalBelanja = isset($_POST['total_belanja']) ? floatval($_POST['total_belanja']) : 0; // Konversi ke float
$pilihan_barang = isset($_POST['pilihan']) ? $_POST['pilihan'] : '';

// Ambil nilai variabel jumlah_barang dari session
$jumlah_barang = isset($_SESSION['jumlah_barang']) ? $_SESSION['jumlah_barang'] : 0;

// Gabungkan informasi pengiriman menjadi satu string

$informasiPengiriman = "$alamat, $kecamatan, $kota, $provinsi";

// Hitung ongkos kirim (contoh: 10,000 per barang)
$ongkosKirimPerBarang = 10000;
$jumlahBarang = count(explode(',', $pilihan_barang));
$ongkosKirimTotal = $ongkosKirimPerBarang * $jumlahBarang;

// Hitung total pembayaran
$totalPembayaran = $totalBelanja + $ongkosKirimTotal;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (isset($_POST['cancel'])) {
        // Tombol "Cancel" ditekan
        echo "<script>alert('Pesanan dibatalkan. Barang masih ada di keranjang.');</script>";
        header("refresh:1;url=keranjang.php");
        exit();
    } elseif (isset($_POST['checkout'])) {
        // Tombol "Checkout" ditekan
        $cek_keranjang_query = mysqli_query($conn, "SELECT kode FROM keranjang WHERE user_id = '$user_id' AND kode IN ($pilihan_barang)");

        // Lakukan proses penyimpanan data transaksi ke database sesuai kebutuhan
        $sql = "INSERT INTO transaksi (user_id, total_harga, alamat, kode_barang,  jumlah_barang) VALUES ('$user_id', '$totalPembayaran', '$informasiPengiriman', '$pilihan_barang', '$jumlah_barang')";
        
    // Eksekusi query
    if (mysqli_query($conn, $sql)) {
        // Dapatkan ID transaksi terakhir yang baru saja dimasukkan
        $lastInsertedID = mysqli_insert_id($conn);

        // Pisahkan kode_barang menjadi array
        $arrKodeBarang = explode(',', $pilihan_barang);

        // Loop untuk memasukkan setiap barang ke dalam tabel keluar
        foreach ($arrKodeBarang as $kodeBarang) {
            $kodeBarang = mysqli_real_escape_string($conn, $kodeBarang);
            // Dapatkan email user berdasarkan ID user
            $sqlGetUserEmail = "SELECT email FROM user WHERE user_id = ?";
            $stmtGetUserEmail = mysqli_prepare($conn, $sqlGetUserEmail);
            mysqli_stmt_bind_param($stmtGetUserEmail, "i", $user_id);
            mysqli_stmt_execute($stmtGetUserEmail);
            mysqli_stmt_bind_result($stmtGetUserEmail, $user_email);
            mysqli_stmt_fetch($stmtGetUserEmail);
            mysqli_stmt_close($stmtGetUserEmail);

            $sqlInsertKeluar = "INSERT INTO keluar (kode, penerima, qty) VALUES (?, ?, ?)";
            $stmtInsertKeluar = mysqli_prepare($conn, $sqlInsertKeluar);

            // Bind parameter ke statement untuk keluar
            mysqli_stmt_bind_param($stmtInsertKeluar, "ssi", $kodeBarang, $user_email, $jumlah_barang);

            // Eksekusi query untuk keluar
            mysqli_stmt_execute($stmtInsertKeluar);

            // Kurangi stok di tabel stock setiap kali barang di-checkout
            kurangiStok($conn, $kodeBarang, $jumlah_barang);

            // Tutup statement keluar
            mysqli_stmt_close($stmtInsertKeluar);
        }

        // Hapus data keranjang setelah transaksi selesai
        $pilihan_barang_clean = mysqli_real_escape_string($conn, $pilihan_barang);
        $sqlDeleteKeranjang = "DELETE FROM keranjang WHERE user_id = '$user_id' AND kode IN ('$pilihan_barang_clean')";
        
        // Eksekusi query penghapusan
        if (mysqli_query($conn, $sqlDeleteKeranjang)) {
            // Tampilkan pemberitahuan pesanan berhasil
            echo "<script>alert('Pesanan berhasil! Terima kasih telah berbelanja.');</script>";

            // Redirect ke home.php setelah 3 detik
            header("refresh:1;url=riwayat.php");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Error inserting record: " . mysqli_error($conn);
    }
}

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Proses Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="home.php">Apotek Sehat</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                           
                            <a class="nav-link" href="home.php" onclick="return confirm('Apakah Anda yakin ingin meninggalkan halaman ini?');">>
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Produk
                            </a>

                            <a class="nav-link" href="keranjang.php" onclick="return confirm('Apakah Anda yakin ingin meninggalkan halaman ini?');">>
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Keranjang
                            </a>

                            <a class="nav-link" href="riwayat.php" onclick="return confirm('Apakah Anda yakin ingin meninggalkan halaman ini?');">>
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Riwayat Transaksi
                            </a>

                            <a class="nav-link" href="logout.php">
                                Logout
                            </a>
                    </div>
                    
                </nav>
            </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h4 class="mt-4"> Konfirmasi Transaksi</h4>
                    <div class="card mb-4">
                        
                        <div class="card-body">
                        <!-- Tampilkan informasi transaksi-->
                        <h5>Informasi Transaksi:</h5>
                        <p>Total Belanja: Rp <?= number_format($totalBelanja, 2, ',', '.'); ?></p>
                        <p>Ongkos Kirim: Rp <?= number_format($ongkosKirimTotal, 2, ',', '.'); ?></p>
                        <p>Total Pembayaran: Rp <?= number_format($totalPembayaran, 2, ',', '.'); ?></p>
                        <p>
</p>
                        <!-- Tampilkan informasi pengiriman -->
                        <h5>Informasi Pengiriman:</h5>
                        <p>Alamat: <?= $informasiPengiriman; ?></p>


    <form method="post" action="">
        <input type="hidden" name="pilihan" value="<?php echo $pilihan_barang; ?>">
        <input type="hidden" name="total_belanja" value="<?php echo $totalBelanja; ?>">
        <input type="hidden" name="provinsi" value="<?php echo $provinsi; ?>">
        <input type="hidden" name="kota" value="<?php echo $kota; ?>">
        <input type="hidden" name="kecamatan" value="<?php echo $kecamatan; ?>">
        <input type="hidden" name="alamat" value="<?php echo $alamat; ?>">
        <input type="submit" name="cancel" value="Cancel" class="btn btn-secondary">
        <input type="submit" name="checkout" value="Checkout" class="btn btn-primary">
    </form>

    
</body>
</html>
