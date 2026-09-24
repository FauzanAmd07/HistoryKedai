<?php
include 'cek_login.php';
// Hanya 'Pemilik' yang bisa menjalankan skrip ini
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Anda tidak memiliki akses untuk melakukan tindakan ini.");
}
include 'koneksi.php';

// Menangkap data dari formulir tambah karyawan
$nama = $_POST['nama'];
$username = $_POST['username'];
$password = $_POST['password']; // Ingat, password ini belum di-hash (tidak aman untuk produksi)
$jabatan = $_POST['jabatan'];

// Menyiapkan kueri INSERT yang aman menggunakan prepared statements
$stmt = $koneksi->prepare("INSERT INTO Karyawan (nama, username, password, jabatan) VALUES (?, ?, ?, ?)");
// Mengikat variabel ke parameter dalam kueri
$stmt->bind_param("ssss", $nama, $username, $password, $jabatan);

// Eksekusi kueri
if ($stmt->execute()) {
    // Jika berhasil, arahkan kembali ke halaman daftar karyawan
    header("location:karyawan_tampil.php");
    exit();
} else {
    // Jika gagal, tampilkan pesan error
    echo "Error saat menambahkan karyawan baru: " . $stmt->error;
}

// Tutup statement dan koneksi
$stmt->close();
$koneksi->close();
?>