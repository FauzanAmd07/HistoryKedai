<?php
// Aktifkan session PHP
session_start();

include 'koneksi.php';

// Tangkap data yang dikirim dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Seleksi data karyawan dengan username dan password yang sesuai
// PERHATIAN: Kode ini rentan SQL Injection. Gunakan prepared statements untuk produksi.
$login = mysqli_query($koneksi, "SELECT * FROM Karyawan WHERE username='$username' AND password='$password'");
// Hitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

// Cek apakah username dan password ditemukan pada database
if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);

    // Buat session
    $_SESSION['id_karyawan'] = $data['id_karyawan'];
    $_SESSION['username'] = $username;
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['jabatan'] = $data['jabatan'];
    $_SESSION['status'] = "login";
    header("location:dashboard.php"); // Arahkan ke halaman utama setelah login
} else {
    header("location:login.php?pesan=gagal");
}
?>