<?php
include 'koneksi.php';

// Menangkap semua data yang dikirim dari form checkout
$nama_pelanggan = $_POST['nama_pelanggan'];
$total_harga = $_POST['total_harga'];
$metode_bayar = $_POST['metode_bayar'];
$pesanan_json = $_POST['pesanan_json'];
$pesanan = json_decode($pesanan_json, true);

// Mulai Database Transaction
mysqli_begin_transaction($koneksi);

try {
    // Langkah 1: Buat pelanggan baru
    $stmt_pelanggan = mysqli_prepare($koneksi, "INSERT INTO Pelanggan (nama) VALUES (?)");
    mysqli_stmt_bind_param($stmt_pelanggan, "s", $nama_pelanggan);
    mysqli_stmt_execute($stmt_pelanggan);
    $id_pelanggan_baru = mysqli_insert_id($koneksi);

    // Langkah 2: Buat transaksi baru
    $status_pesanan = 'Baru Masuk';
    date_default_timezone_set('Asia/Makassar');
    $tanggal = date('Y-m-d H:i:s');
    $id_karyawan_sistem = 1;

    $stmt_transaksi = mysqli_prepare($koneksi, "INSERT INTO Transaksi (id_pelanggan, id_karyawan, tanggal, total_harga, status, metode_bayar) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_transaksi, "iisdss", $id_pelanggan_baru, $id_karyawan_sistem, $tanggal, $total_harga, $status_pesanan, $metode_bayar);
    mysqli_stmt_execute($stmt_transaksi);
    $id_transaksi_baru = mysqli_insert_id($koneksi);

    // Langkah 3: Simpan detail pesanan
    $stmt_detail = mysqli_prepare($koneksi, "INSERT INTO Detail_Transaksi (id_transaksi, id_menu, jumlah, subtotal) VALUES (?, ?, ?, ?)");
    foreach ($pesanan as $item) {
        $id_menu = $item['id'];
        $jumlah = $item['qty'];
        $subtotal = $item['price'] * $jumlah;
        mysqli_stmt_bind_param($stmt_detail, "iiid", $id_transaksi_baru, $id_menu, $jumlah, $subtotal);
        mysqli_stmt_execute($stmt_detail);
    }
    
    // Jika berhasil, commit
    mysqli_commit($koneksi);

    // --- TAMPILAN HALAMAN SUKSES DENGAN CSS BARU ---
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Pesanan Berhasil - History Kedai</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            :root {
                --color-primary: #6D28D9; --color-secondary: #8B5CF6;
                --color-bg: #F9FAFB; --color-surface: #FFFFFF;
                --color-dark: #1F2937; --color-muted: #6B7280;
                --color-success: #10B981;
                --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                --radius: 16px;
            }
            body {
                font-family: 'Poppins', sans-serif;
                background-color: var(--color-bg);
                display: flex; align-items: center; justify-content: center;
                height: 100vh; margin: 0; color: var(--color-dark);
            }
            .success-card {
                background: var(--color-surface);
                padding: 40px;
                border-radius: var(--radius);
                box-shadow: var(--shadow);
                text-align: center;
                max-width: 450px;
                width: 90%;
            }
            .icon-wrapper {
                width: 80px; height: 80px;
                background-color: #D1FAE5; color: var(--color-success);
                border-radius: 50%; display: flex; align-items: center; justify-content: center;
                font-size: 40px; margin: 0 auto 20px;
            }
            h1 { margin: 0 0 10px; color: var(--color-dark); font-size: 1.8rem; }
            p { color: var(--color-muted); margin: 0 0 20px; line-height: 1.6; }
            .details {
                background: #F3F4F6; padding: 20px; border-radius: 10px;
                margin-bottom: 30px; text-align: left;
            }
            .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
            .detail-row:last-child { margin-bottom: 0; border-top: 1px dashed #ccc; padding-top: 10px; font-weight: 600; color: var(--color-primary); }
            .btn-home {
                display: inline-block; width: 100%; padding: 15px 0;
                background: var(--color-primary); color: white;
                text-decoration: none; border-radius: 50px; font-weight: 600;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .btn-home:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(109, 40, 217, 0.3); }
        </style>
    </head>
    <body>
        <div class="success-card">
            <div class="icon-wrapper"><i class="fas fa-check"></i></div>
            <h1>Pesanan Berhasil!</h1>
            <p>Terima kasih, <strong><?php echo htmlspecialchars($nama_pelanggan); ?></strong>. Pesanan Anda telah kami terima dan sedang diproses.</p>
            
            <div class="details">
                <div class="detail-row">
                    <span>Metode Bayar</span>
                    <strong><?php echo htmlspecialchars($metode_bayar); ?></strong>
                </div>
                <div class="detail-row">
                    <span>Total Tagihan</span>
                    <span>Rp <?php echo number_format($total_harga); ?></span>
                </div>
            </div>

            <p style="font-size: 0.9em;">Silakan menuju kasir untuk pembayaran.</p>
            <a href="index.php" class="btn-home">Buat Pesanan Baru</a>
        </div>
        <script>sessionStorage.removeItem('cart');</script>
    </body>
    </html>
    <?php

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo "<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h1 style='color:red;'>❌ Pesanan Gagal!</h1>
            <p>Terjadi kesalahan sistem. Silakan coba lagi.</p>
            <a href='keranjang.php'>Kembali ke Keranjang</a>
          </div>";
}

mysqli_close($koneksi);
?>