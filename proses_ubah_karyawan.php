<?php
include 'cek_login.php';
// Hanya 'Pemilik' yang bisa menjalankan skrip ini
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Anda tidak memiliki akses untuk melakukan tindakan ini.");
}
include 'koneksi.php';

// Menangkap data dari formulir
$id_karyawan = $_POST['id_karyawan'];
$nama = $_POST['nama'];
$username = $_POST['username'];
$jabatan = $_POST['jabatan'];
$password_baru = $_POST['password'];

// Logika untuk update password:
// Cek apakah kolom password baru diisi atau tidak
if (!empty($password_baru)) {
    // Jika diisi, siapkan kueri UPDATE untuk semua kolom termasuk password
    $stmt = $koneksi->prepare("UPDATE Karyawan SET nama = ?, username = ?, password = ?, jabatan = ? WHERE id_karyawan = ?");
    $stmt->bind_param("ssssi", $nama, $username, $password_baru, $jabatan, $id_karyawan);
} else {
    // Jika password dikosongkan, siapkan kueri UPDATE untuk semua kolom KECUALI password
    $stmt = $koneksi->prepare("UPDATE Karyawan SET nama = ?, username = ?, jabatan = ? WHERE id_karyawan = ?");
    $stmt->bind_param("sssi", $nama, $username, $jabatan, $id_karyawan);
}

// Eksekusi kueri
if ($stmt->execute()) {
    // Jika berhasil, arahkan kembali ke halaman daftar karyawan
    header("location:karyawan_tampil.php");
} else {
    // Jika gagal, tampilkan pesan error
    echo "Error saat mengupdate data: " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>