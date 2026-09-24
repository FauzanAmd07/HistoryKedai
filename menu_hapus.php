<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Pastikan ID ada dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_menu = $_GET['id'];

    // Kueri UPDATE, bukan DELETE. Mengubah status menjadi 'Diarsipkan'
    $stmt = $koneksi->prepare("UPDATE Menu SET status_menu = 'Diarsipkan' WHERE id_menu = ?");
    $stmt->bind_param("i", $id_menu);

    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman manajemen menu
        header("location:menu_tampil.php");
        exit();
    } else {
        echo "Error: Gagal mengarsipkan data. " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();

} else {
    echo "ID menu tidak valid.";
    exit();
}
?>