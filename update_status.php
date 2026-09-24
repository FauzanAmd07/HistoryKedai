<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Kasir') {
    die("Halaman ini hanya bisa diakses oleh Kasir. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_transaksi = $_GET['id'];
    $status_baru = $_GET['status'];

    // Validasi status untuk keamanan
    $status_valid = ['Sedang Diproses', 'Selesai'];
    if (in_array($status_baru, $status_valid)) {
        
        $stmt = mysqli_prepare($koneksi, "UPDATE Transaksi SET status = ? WHERE id_transaksi = ?");
        mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_transaksi);
        
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, kembali ke halaman daftar pesanan
            header("Location: daftar_pesanan.php");
            exit();
        } else {
            echo "Gagal mengupdate status.";
        }
    } else {
        echo "Status tidak valid.";
    }
} else {
    echo "Parameter tidak lengkap.";
}
?>