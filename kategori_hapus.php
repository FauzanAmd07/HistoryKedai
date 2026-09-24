<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_kategori = $_GET['id'];

    // Kueri UPDATE, mengubah status menjadi 'Diarsipkan'
    $stmt = $koneksi->prepare("UPDATE Kategori SET status_kategori = 'Diarsipkan' WHERE id_kategori = ?");
    $stmt->bind_param("i", $id_kategori);

    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman manajemen kategori
        header("location:kategori_tampil.php");
        exit();
    } else {
        echo "Error: Gagal mengarsipkan data. " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();

} else {
    echo "ID kategori tidak valid.";
    exit();
}
?>