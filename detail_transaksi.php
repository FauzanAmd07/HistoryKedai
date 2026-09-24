<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Kasir' && $_SESSION['jabatan'] != 'Admin') {
    die("Halaman ini hanya bisa diakses oleh Kasir dan Admin. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Ambil ID dari URL
$id_transaksi = $_GET['id'];
if (!is_numeric($id_transaksi)) {
    die("ID Transaksi tidak valid.");
}

// Query untuk info utama transaksi
$query_transaksi = mysqli_query($koneksi, "SELECT T.*, P.nama AS nama_pelanggan, K.nama AS nama_karyawan FROM Transaksi T JOIN Pelanggan P ON T.id_pelanggan = P.id_pelanggan JOIN Karyawan K ON T.id_karyawan = K.id_karyawan WHERE T.id_transaksi = $id_transaksi");
$transaksi = mysqli_fetch_assoc($query_transaksi);

// Query untuk detail item yang dibeli
$query_detail = mysqli_query($koneksi, "SELECT M.nama_menu, M.harga, DT.jumlah, DT.subtotal FROM Detail_Transaksi DT JOIN Menu M ON DT.id_menu = M.id_menu WHERE DT.id_transaksi = $id_transaksi");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi #<?php echo $id_transaksi; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --color-blue-primary: #3B82F6;
            --color-blue-dark: #1E40AF;
            --color-bg: #F3F4F6;
            --color-surface: #FFFFFF;
            --color-text-dark: #1F2937;
            --color-text-muted: #6B7280;
            --border-radius: 12px;
            --font-body: 'Poppins', sans-serif;
        }
        body { font-family: var(--font-body); margin: 0; background-color: var(--color-bg); }
        .container { max-width: 800px; margin: 50px auto; background: var(--color-surface); padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border-radius: var(--border-radius); }
        .header-detail { text-align: center; border-bottom: 2px dashed #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .header-detail h2 { margin: 0; font-size: 2rem; color: var(--color-blue-dark); }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 30px; }
        .info-grid div { font-size: 0.9em; }
        .info-grid strong { display: block; color: var(--color-text-muted); font-weight: 500; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 0; border-bottom: 1px solid #eee; text-align: left; }
        thead th { color: var(--color-text-muted); font-weight: 500; }
        tbody td { font-weight: 500; }
        .total-row { font-weight: 600; font-size: 1.3em; }
        
        /* --- Style Baru untuk Tombol Aksi --- */
        .action-buttons {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .action-buttons a, .action-buttons button {
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            font-family: var(--font-body);
            font-size: 1em;
        }
        .back-link { 
            color: var(--color-blue-primary);
        }
        .print-btn {
            background-color: var(--color-blue-primary);
            color: var(--color-surface);
            transition: background-color 0.2s;
        }
        .print-btn:hover {
            background-color: var(--color-blue-dark);
        }

        /* --- Style untuk mode cetak --- */
        @media print {
            body { background-color: white; }
            .container { margin: 0; padding: 0; box-shadow: none; border-radius: 0; }
            .action-buttons { display: none; } /* Sembunyikan tombol saat mencetak */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-detail">
            <h2>Struk Digital</h2>
            <span>ID Transaksi: #<?php echo $transaksi['id_transaksi']; ?></span>
        </div>

        <div class="info-grid">
            <div><strong>Pelanggan:</strong> <?php echo htmlspecialchars($transaksi['nama_pelanggan']); ?></div>
            <div><strong>Tanggal:</strong> <?php echo date('d M Y, H:i', strtotime($transaksi['tanggal'])); ?></div>
            <div><strong>Kasir:</strong> <?php echo htmlspecialchars($transaksi['nama_karyawan']); ?></div>
            <div><strong>Metode Bayar:</strong> <?php echo htmlspecialchars($transaksi['metode_bayar']); ?></div>
        </div>
        
        <h3>Item yang Dipesan</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($detail = mysqli_fetch_assoc($query_detail)): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($detail['nama_menu']); ?><br>
                        <small style="color: var(--color-text-muted);">@ Rp <?php echo number_format($detail['harga']); ?></small>
                    </td>
                    <td style="text-align:center;"><?php echo $detail['jumlah']; ?></td>
                    <td style="text-align:right;">Rp <?php echo number_format($detail['subtotal']); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="total-row">
                    <td colspan="2" style="text-align:right;">Total Keseluruhan</td>
                    <td style="text-align:right;">Rp <?php echo number_format($transaksi['total_harga']); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="action-buttons">
            <a href="riwayat_transaksi.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Riwayat</a>
            <button onclick="window.print()" class="print-btn"><i class="fas fa-print"></i> Cetak Struk</button>
        </div>
    </div>
</body>
</html>