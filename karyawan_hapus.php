<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Pastikan ID ada dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_karyawan = $_GET['id'];

    // Kueri UPDATE untuk mengubah status menjadi 'Tidak Aktif'
    $stmt = $koneksi->prepare("UPDATE Karyawan SET status_karyawan = 'Tidak Aktif' WHERE id_karyawan = ?");
    $stmt->bind_param("i", $id_karyawan);

    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman daftar karyawan
        header("location:karyawan_tampil.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error saat mengarsipkan data karyawan: " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo "ID karyawan tidak valid.";
    exit();
}
?>