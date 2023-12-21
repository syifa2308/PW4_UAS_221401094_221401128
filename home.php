<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'function.php';
require 'cek.php';

$user_id = $_SESSION['user_id']; // Gantilah ini dengan cara Anda mendapatkan nilai user_id
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
                        <h4 class="mt-4">Selamat datang di Apotek Sehat, mau beli obat apa?</h4>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                 
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Obat</th>
                                            <th>Gambar</th>
                                            <th>Deskripsi</th>
                                            <th>Harga</th>
                                            <th>Stock</th>
                                            <th>Keranjang</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php
                                        
                                        $ambilsemuadatastock = mysqli_query($conn, "SELECT * from stock");
                                        $i = 1;
                                        while($data=mysqli_fetch_array($ambilsemuadatastock)){    
                                            $namabarang = $data['namabarang'];
                                            $gambar = $data['gambar'];
                                            $deskripsi = $data['deskripsi'];
                                            $harga = $data['harga'];
                                            $stock = $data['stock'];
                                            $idb = $data['kode'];
                                        ?>

                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=$namabarang;?></td>
                                            
                                            <td><img src="gambar_obat/<?=$gambar;?>" alt="<?=$namabarang;?>" style="max-width: 100px; max-height: 100px;"></td>
                                            <td><?=$deskripsi;?></td>
                                            <td><?= "Rp " . number_format($harga, 2, ',', '.');?></td>
                                            <td><?=$stock;?></td>
                                            <td>
                                                <!-- Tambahkan tombol tambah ke keranjang dengan menggunakan atribut onclick -->
                                                <div class="input-group mb-3">
                                                    
                                                <input type="number" name="jumlah" id="jumlah<?=$idb;?>" class="form-control" value="1" min="1" placeholder="Jumlah" required>
                                                <button type="button" name="addtocart" onclick="tambahKeKeranjang ('<?=$idb;?>', '<?=$user_id;?>')" class="btn btn-primary" >+</button>
                                                

                                            </div>
                                            </td>   
                                        </tr>
                                        </div>
                                    </div>
                                    </div>

                                        <?php
                                        };

                                        ?>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                
            </div>
        </div>
        <script>
            function tambahKeKeranjang(idb, user_id) {
                alert('Barang dimasukkan ke keranjang.');
                var jumlah = document.getElementById('jumlah' + idb).value;

                // Tambahkan pemberitahuan untuk memeriksa nilai
                


                // Kirim permintaan AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'tambah_keranjang.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Tanggapi dari server (jika perlu)
                        console.log(xhr.responseText);
                    }
                };
                xhr.send('idb=' + idb + '&user_id=' + '<?=$user_id;?>' + '&jumlah=' + jumlah);
            }
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

