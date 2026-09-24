<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Menangkap data yang dikirim dari form
$id_kategori = $_POST['id_kategori'];
$nama_kategori = $_POST['nama_kategori'];

// Memperbarui data di database menggunakan prepared statement
$stmt = $koneksi->prepare("UPDATE Kategori SET nama_kategori = ? WHERE id_kategori = ?");
$stmt->bind_param("si", $nama_kategori, $id_kategori);

if ($stmt->execute()) {
    // Jika berhasil, redirect ke halaman tampil kategori
    header("location:kategori_tampil.php");
} else {
    // Jika gagal, tampilkan pesan error
    echo "Error: Gagal memperbarui data. " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>