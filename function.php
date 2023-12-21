<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

//membuat koneksi ke database
$conn = mysqli_connect("localhost", "root","","stockobat");


//Menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $user_id = $_POST['user_id'];
    $namabarang = $_POST['namabarang'];
    $kode = $_POST['kode'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stock = $_POST['stock'];
    $gambar = $_POST['gambar'];
    
    $direktori = "gambar_obat/";
    $gambar_name = $_FILES['gambar']['name'];

    move_uploaded_file($_FILES['gambar']['tmp_name'], $direktori.$gambar_name);
    
    $addtotabel = mysqli_query($conn, "INSERT INTO stock (namabarang, kode, gambar, deskripsi, stock, harga) VALUES ('$namabarang', '$kode', '$gambar_name', '$deskripsi', '$stock', '$harga')");
    var_dump($addtotabel); // Melihat nilai yang dikembalikan

    if ($addtotabel) {
        header('location:index.php');
    } else {
        echo 'Gagal menjalankan query INSERT: ' . mysqli_error($conn);
        header('location:index.php');   
    }
}

//menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $hargabeli = $_POST['hargabeli'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "SELECT * from stock where kode='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya ['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = mysqli_query($conn, "INSERT into masuk (kode, hargabeli, keterangan, qty) values ('$barangnya', '$hargabeli', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock = '$tambahkanstocksekarangdenganquantity' where kode='$barangnya'" );
    if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
    }else {
        echo 'Gagal';
        header('location:masuk.php');
    }
}


//menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn, "SELECT * from stock where kode='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya ['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

    $addtokeluar = mysqli_query($conn, "INSERT into keluar (kode, penerima, qty) values ('$barangnya', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "update stock set stock = '$tambahkanstocksekarangdenganquantity' where kode='$barangnya'" );
    if($addtokeluar&&$updatestockmasuk){
        header('location:keluar.php');
    }else {
        echo 'Gagal';
        header('location:keluar.php');
    }
}

//update info barang
if(isset($_POST['updatebarang'])){
    $kode = $_POST['kode'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar'];
    
    $direktori = "gambar_obat/";
    $gambar_name = $_FILES['gambar']['name'];

    move_uploaded_file($_FILES['gambar']['tmp_name'], $direktori.$gambar_name);

    $update = mysqli_query($conn, "update stock set namabarang='$namabarang', gambar='$gambar_name', deskripsi='$deskripsi', harga='$harga'  where kode='$kode'");
    if($update){
        header('location:index.php');
    }else {
        echo 'Gagal';
        header('location:index.php'); 
    }
}

//Menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $kode = $_POST['kode'];

    $hapus = mysqli_query($conn, "delete from stock where kode='$kode'");
    if($hapus){
        header('location:index.php');
    }else {
        echo 'Gagal';
        header('location:index.php'); 
    }
}

//mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];
    $hargabeli = $_POST['hargabeli'];

    $lihatstock = mysqli_query($conn, "select * from stock where kode='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from masuk where idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock= '$kurangin' where kode='$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else {
                echo 'Gagal';
                header('location:masuk.php'); 
            }
    }else {
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock= '$kurangin' where kode='$idb'");
        $updatenya = mysqli_query($conn, "update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else {
                echo 'Gagal';
                header('location:masuk.php'); 
            }
    }
}

//menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($conn, "select * from stock where kode='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock-$qty;

    $update = mysqli_query($conn, "update stock set stock='$selisih' where kode='$idb'");
    $hapusdata = mysqli_query($conn, "delete from masuk where idmasuk='$idm'");

    if($update&&$hapusdata){
        header('location:masuk.php');
    } else{
        header('location:masuk.php');
    }
}

//Mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];
    $hargabeli = $_POST['hargabeli'];

    $lihatstock = mysqli_query($conn, "select * from stock where kode='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from keluar where idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock= '$kurangin' where kode='$idb'");
        $updatenya = mysqli_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else {
                echo 'Gagal';
                header('location:keluar.php'); 
            }
    }else {
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock= '$kurangin' where kode='$idb'");
        $updatenya = mysqli_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else {
                echo 'Gagal';
                header('location:keluar.php'); 
            }
    }
}

//menghapus barang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($conn, "select * from stock where kode='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock+$qty;

    $update = mysqli_query($conn, "update stock set stock='$selisih' where kode='$idb'");
    $hapusdata = mysqli_query($conn, "delete from keluar where idkeluar='$idk'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    } else{
        header('location:keluar.php');
    }
}


// function.php

// Fungsi untuk mendapatkan informasi stok barang berdasarkan kode_barang
function getStockInfo($conn, $kode_barang) {
    $query = "SELECT stock FROM stock WHERE kode = '$kode_barang'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row; // Mengembalikan array asosiatif dengan informasi stok
    } else {
        return false; // Mengembalikan false jika query tidak berhasil
    }
}

// Fungsi untuk mengurangi stok barang setelah transaksi
function kurangiStok($conn, $kode_barang, $jumlah_barang) {
    // Ambil stok saat ini dari tabel stock
    $stokInfo = getStockInfo($conn, $kode_barang);

    if ($stokInfo) {
        $stokSaatIni = $stokInfo['stock'];

        // Kurangi stok dengan jumlah barang yang dibeli
        $stokBaru = $stokSaatIni - $jumlah_barang;

        // Update stok di tabel stock
        $queryUpdateStok = "UPDATE stock SET stock = '$stokBaru' WHERE kode = '$kode_barang'";
        $resultUpdateStok = mysqli_query($conn, $queryUpdateStok);

        if (!$resultUpdateStok) {
            die("Query error: " . mysqli_error($conn));
        }

        
    } else {
        die("Query error: " . mysqli_error($conn));
    }
}





?>