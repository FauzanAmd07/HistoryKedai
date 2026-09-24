<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

$id_menu = $_POST['id_menu'];
$nama_menu = $_POST['nama_menu'];
$id_kategori = $_POST['id_kategori'];
$harga = $_POST['harga'];

// Kueri UPDATE tanpa 'deskripsi'
$query = "UPDATE Menu SET nama_menu=?, id_kategori=?, harga=? ";
$params = array($nama_menu, $id_kategori, $harga);
$types = "sid"; // Tipe data disesuaikan (string, integer, double)

// Cek jika ada gambar baru yang di-upload
if (!empty($_FILES['gambar']['name'])) {
    // Ambil nama gambar lama untuk dihapus
    $q_gambar_lama = mysqli_query($koneksi, "SELECT gambar FROM Menu WHERE id_menu=$id_menu");
    $data_gambar = mysqli_fetch_array($q_gambar_lama);
    if ($data_gambar && !empty($data_gambar['gambar']) && file_exists("gambar/".$data_gambar['gambar'])) {
        unlink("gambar/".$data_gambar['gambar']);
    }
    
    // Upload gambar baru
    $gambar_baru = time() . '_' . basename($_FILES['gambar']['name']);
    move_uploaded_file($_FILES['gambar']['tmp_name'], "gambar/" . $gambar_baru);
    
    // Tambahkan 'gambar' ke kueri
    $query .= ", gambar=? ";
    $params[] = $gambar_baru;
    $types .= "s";
}

// Tambahkan kondisi WHERE di akhir
$query .= "WHERE id_menu=?";
$params[] = $id_menu;
$types .= "i";

// Eksekusi kueri
$stmt = $koneksi->prepare($query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    header("location:menu_tampil.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>