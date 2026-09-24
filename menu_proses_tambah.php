<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

$nama_menu = $_POST['nama_menu'];
$id_kategori = $_POST['id_kategori'];
$harga = $_POST['harga'];
$gambar_name = "";

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
    $gambar_name = time() . '_' . basename($_FILES['gambar']['name']);
    move_uploaded_file($_FILES['gambar']['tmp_name'], "gambar/" . $gambar_name);
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO Menu (nama_menu, id_kategori, harga, gambar) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sids", $nama_menu, $id_kategori, $harga, $gambar_name);

if (mysqli_stmt_execute($stmt)) {
    header("location:menu_tampil.php");
} else {
    echo "Error: Gagal menyimpan data.";
}
?>