<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Menangkap data yang dikirim dari form
$nama_kategori = $_POST['nama_kategori'];

// Menyimpan data ke database menggunakan prepared statement
$stmt = $koneksi->prepare("INSERT INTO Kategori (nama_kategori) VALUES (?)");
$stmt->bind_param("s", $nama_kategori);

if ($stmt->execute()) {
    // Jika berhasil, redirect ke halaman tampil kategori
    header("location:kategori_tampil.php");
} else {
    // Jika gagal, tampilkan pesan error
    echo "Error: Gagal menyimpan data. " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>