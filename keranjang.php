<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'function.php';
require 'cek.php';

$user_id = $_SESSION['user_id']; // Gantilah ini
// Ambil data barang dari tabel keranjang sesuai dengan user_id
$keranjang_query = mysqli_query($conn, "SELECT keranjang.*, stock.namabarang, stock.gambar, stock.harga FROM keranjang JOIN stock ON keranjang.kode = stock.kode WHERE keranjang.user_id = '$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Keranjang Belanja</title>
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
        <h4 class="mt-4">Keranjang Belanja</h4>
        <form action="transaksi.php" method="post"> <!-- Form di tambahkan di sini -->
        <div class="card mb-4">
            <div class="card-header">
                
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pilih</th>
                            <th>Nama Barang</th>
                            <th>Gambar</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $totalBelanja = 0;
                        while ($data = mysqli_fetch_assoc($keranjang_query)) {
                            $namaBarang = $data['namabarang'];
                            $gambar = $data['gambar'];
                            $harga = $data['harga'];
                            $jumlah = $data['jumlah'];
                            $totalHarga = $harga * $jumlah;
                            
                            // Memeriksa apakah item dicentang (dipilih)
                            $checked = isset($_POST['pilihan']) && in_array($data['kode'], $_POST['pilihan']);

                            // Jika dicentang, tambahkan total belanja
                            if ($checked) {
                                $totalBelanja += $totalHarga;
                            }
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><input type="checkbox" name="pilihan[]" value="<?= $data['kode']; ?>" <?= $checked ? 'checked' : ''; ?>></td>
                                <td><?= $namaBarang; ?></td>
                                <td><img src="gambar_obat/<?= $gambar; ?>" alt="<?= $namaBarang; ?>" style="max-width: 100px; max-height: 100px;"></td>
                                <td><?= "Rp " . number_format($harga, 2, ',', '.'); ?></td>
                                <td><?= $jumlah; ?></td>
                                <td><?= "Rp " . number_format($totalHarga, 2, ',', '.'); ?></td>
                                
                                
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <p id="totalBelanja"></p>
                <button type="submit" class="btn btn-primary">Check Out</button>
            </div>
        </div>
        </form>
    </div>
    </main>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var checkboxes = document.querySelectorAll(".item-checkbox");
        var totalBelanjaElement = document.getElementById("totalBelanja");

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener("change", function () {
                updateTotalBelanja();
            });
        });

        function updateTotalBelanja() {
            var totalBelanja = 0;

            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    var row = checkbox.closest("tr");
                    var totalHargaElement = row.querySelector(".total-harga");
                    var totalHarga = parseFloat(totalHargaElement.dataset.harga);

                    totalBelanja += totalHarga;
                }
            });

            totalBelanjaElement.textContent = "Total Belanja: Rp " + totalBelanja.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
            console.log("Total Belanja updated:", totalBelanja);
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>
