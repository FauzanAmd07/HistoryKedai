<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Kasir' && $_SESSION['jabatan'] != 'Admin') {
    die("Halaman ini hanya bisa diakses oleh Kasir dan Admin. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Ambil semua transaksi yang statusnya 'Selesai', diurutkan dari yang terbaru
$query = mysqli_query($koneksi, 
    "SELECT T.*, P.nama AS nama_pelanggan, K.nama AS nama_karyawan 
     FROM Transaksi T 
     JOIN Pelanggan P ON T.id_pelanggan = P.id_pelanggan
     JOIN Karyawan K ON T.id_karyawan = K.id_karyawan
     WHERE T.status = 'Selesai' 
     ORDER BY T.tanggal DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Menggunakan CSS Dasbor Biru Soft yang konsisten */
        :root {
            --color-blue-primary: #3B82F6;
            --color-blue-secondary: #BFDBFE;
            --color-blue-dark: #1E40AF;
            --color-bg: #F3F4F6;
            --color-surface: #FFFFFF;
            --color-text-dark: #1F2937;
            --color-text-muted: #6B7280;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --border-radius: 12px;
            --font-body: 'Poppins', sans-serif;
        }
        
        * { box-sizing: border-box; }
        body { font-family: var(--font-body); margin: 0; background-color: var(--color-bg); color: var(--color-text-dark); display: flex; }

        .sidebar { width: 250px; background: var(--color-surface); height: 100vh; padding: 2rem 1rem; display: flex; flex-direction: column; border-right: 1px solid #E5E7EB; position: fixed; }
        .sidebar .logo { font-weight: 700; font-size: 1.5rem; color: var(--color-blue-dark); margin-bottom: 2rem; text-align: center; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.2rem; text-decoration: none; color: var(--color-text-muted); font-weight: 500; border-radius: 8px; margin-bottom: 0.5rem; transition: all 0.2s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: var(--color-blue-secondary); color: var(--color-blue-dark); }
        .sidebar-nav a .icon { width: 20px; text-align: center; }
        .user-profile { margin-top: auto; display: flex; align-items: center; gap: 10px; padding: 1rem; border-top: 1px solid #E5E7EB; }
        .user-profile .avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--color-blue-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .user-profile .name { font-weight: 600; }
        .user-profile .role { font-size: 0.8em; color: var(--color-text-muted); }
        .logout-btn { color: #EF4444; text-decoration: none; margin-left: auto; font-size: 1.2em; }
        
        .main-content { margin-left: 250px; flex-grow: 1; padding: 2rem; }
        .main-header h1 { margin: 0 0 2rem 0; font-size: 1.8rem; }
        
        .table-container { background: var(--color-surface); border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #E5E7EB; }
        thead th { background-color: #F9FAFB; color: var(--color-text-muted); font-weight: 600; font-size: 0.9em; text-transform: uppercase; }
        tbody tr:hover { background-color: #F9FAFB; }
        .btn-detail { background-color: var(--color-blue-secondary); color: var(--color-blue-dark); padding: 5px 10px; text-decoration: none; border-radius: 5px; font-weight: 500; font-size: 0.9em; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="logo">HistoryKedai</div>
    <nav class="sidebar-nav">
        <?php 
        // Mengambil jabatan dari session
        $jabatan = $_SESSION['jabatan']; 
        // Mengambil nama file yang sedang dibuka
        $halaman_ini = basename($_SERVER['PHP_SELF']);
        ?>

        <a href="dashboard.php" 
           class="<?php echo ($halaman_ini == 'dashboard.php') ? 'active' : ''; ?>">
            <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dasbor
        </a>

        <?php if ($jabatan == 'Kasir'): ?>
            <a href="daftar_pesanan.php" 
               class="<?php echo ($halaman_ini == 'daftar_pesanan.php') ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-inbox"></i></span> Pesanan Masuk
            </a>
            <a href="riwayat_transaksi.php" 
               class="<?php echo ($halaman_ini == 'riwayat_transaksi.php' || $halaman_ini == 'detail_transaksi.php') ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-history"></i></span> Riwayat Transaksi
            </a>
        <?php endif; ?>

        <?php if ($jabatan == 'Admin'): ?>
            <a href="riwayat_transaksi.php" 
               class="<?php echo ($halaman_ini == 'riwayat_transaksi.php' || $halaman_ini == 'detail_transaksi.php') ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-history"></i></span> Riwayat Transaksi
            </a>
            <a href="laporan.php" 
               class="<?php echo ($halaman_ini == 'laporan.php' || $halaman_ini == 'export_csv.php') ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-file-excel"></i></span> Laporan Penjualan
            </a>
        <?php endif; ?>

        <?php if ($jabatan == 'Pemilik'): ?>
            <a href="menu_tampil.php" 
               class="<?php echo (strpos($halaman_ini, 'menu_') === 0) ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-utensils"></i></span> Manajemen Menu
            </a>
            <a href="kategori_tampil.php" 
               class="<?php echo (strpos($halaman_ini, 'kategori_') === 0) ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-tags"></i></span> Manajemen Kategori
            </a>
            <a href="karyawan_tampil.php" 
               class="<?php echo (strpos($halaman_ini, 'karyawan_') === 0) ? 'active' : ''; ?>">
                <span class="icon"><i class="fas fa-users"></i></span> Manajemen Karyawan
            </a>
        <?php endif; ?>
    </nav>
    
    <div class="user-profile">
        <div class="avatar"><?php echo substr($_SESSION['nama'], 0, 1); ?></div>
        <div>
            <div class="name"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
            <div class="role"><?php echo htmlspecialchars($_SESSION['jabatan']); ?></div>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</aside>

    <main class="main-content">
        <div class="main-header">
            <h1>Riwayat Transaksi Selesai</h1>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td>#<?php echo $row['id_transaksi']; ?></td>
                        <td><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_karyawan']); ?></td>
                        <td>Rp <?php echo number_format($row['total_harga']); ?></td>
                        <td>
                            <a href="detail_transaksi.php?id=<?php echo $row['id_transaksi']; ?>" class="btn-detail">Lihat Detail</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr><td colspan="6" style="text-align:center; padding: 2rem;">Belum ada riwayat transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>