<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Admin') {
    die("Halaman ini hanya bisa diakses oleh Admin. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

if (isset($_POST['bulan']) && isset($_POST['tahun'])) {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    // 1. Set header untuk memberitahu browser bahwa ini adalah file CSV untuk di-download
    $nama_file = "laporan_penjualan_{$bulan}_{$tahun}.csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $nama_file);

    // 2. Buat file pointer yang terhubung ke output PHP
    $output = fopen('php://output', 'w');

    // 3. Tulis baris header (judul kolom) ke file CSV
    fputcsv($output, array('ID Transaksi', 'Tanggal', 'Nama Pelanggan', 'Nama Kasir', 'Metode Bayar', 'Total Harga'));

    // 4. Query data dari database sesuai bulan dan tahun yang dipilih
    $query = mysqli_prepare($koneksi, 
        "SELECT T.id_transaksi, T.tanggal, P.nama AS nama_pelanggan, K.nama AS nama_karyawan, T.metode_bayar, T.total_harga
         FROM Transaksi T 
         JOIN Pelanggan P ON T.id_pelanggan = P.id_pelanggan
         JOIN Karyawan K ON T.id_karyawan = K.id_karyawan
         WHERE T.status = 'Selesai' AND MONTH(T.tanggal) = ? AND YEAR(T.tanggal) = ?
         ORDER BY T.tanggal ASC"
    );
    mysqli_stmt_bind_param($query, "ii", $bulan, $tahun);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);

    // 5. Loop melalui hasil query dan tulis setiap baris ke file CSV
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }

    // 6. Tutup file pointer
    fclose($output);
    exit();

} else {
    // Redirect jika halaman diakses tanpa memilih bulan/tahun
    header("Location: laporan.php");
    exit();
}
?>