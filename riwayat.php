<?php
// riwayat.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'function.php';
require 'cek.php';

$user_id = $_SESSION['user_id'];

// Ambil data transaksi dari tabel transaksi dan tabel stock
$query = "SELECT transaksi.kode_barang, stock.namabarang, transaksi.jumlah_barang, transaksi.total_harga, transaksi.alamat, transaksi.waktu_transaksi
          FROM transaksi
          JOIN stock ON transaksi.kode_barang = stock.kode
          WHERE transaksi.user_id = '$user_id'
          ORDER BY transaksi.waktu_transaksi DESC";

$result = mysqli_query($conn, $query);
// Periksa apakah query berhasil dijalankan
if (!$result) {
    die("Query error: " . mysqli_error($conn));
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
        <title>Apotek Sehat</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    </head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="home.php">Apotek Sehat</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <!-- Isi halaman seperti sebelumnya -->

    <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                           
                            <a class="nav-link" href="home.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Produk
                            </a>

                            <a class="nav-link" href="keranjang.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Keranjang
                            </a>

                            <a class="nav-link" href="riwayat.php">
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
                    <h4 class="mt-4">Riwayat Transaksi</h4>
                    <div class="card mb-4">
                        
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah Barang</th>
                                        <th>Total Pembayaran</th>
                                        <th>Alamat</th>
                                        <th>Tanggal Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query error: " . mysqli_error($conn));
                                    }
                                    $nomor = 1; // Inisialisasi nomor awal
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>{$nomor}</td>";
                                        echo "<td>{$row['namabarang']}</td>"; // Perbaiki bagian ini
                                        echo "<td>{$row['jumlah_barang']}</td>";
                                        echo "<td>Rp " . number_format($row['total_harga'], 2, ',', '.') . "</td>";
                                        echo "<td>{$row['alamat']}</td>";
                                        echo "<td>{$row['waktu_transaksi']}</td>";
                                        echo "</tr>";

                                        

                                        $kode_barang = $row['kode_barang'];
                                        $jumlah_barang = $row['jumlah_barang'];
                                        
                                        
                                        
                                        $nomor++;

                                    }
                                    
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <!-- Script seperti sebelumnya -->
        </div>
    </div>
    </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>

</body>
</html>
