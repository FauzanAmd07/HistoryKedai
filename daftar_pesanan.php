<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Kasir') {
    die("Halaman ini hanya bisa diakses oleh Kasir. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

// Ambil semua pesanan yang statusnya 'Baru Masuk' atau 'Sedang Diproses'
$query_pesanan = mysqli_query($koneksi, 
    "SELECT T.*, P.nama AS nama_pelanggan 
     FROM Transaksi T 
     JOIN Pelanggan P ON T.id_pelanggan = P.id_pelanggan 
     WHERE T.status = 'Baru Masuk' OR T.status = 'Sedang Diproses' 
     ORDER BY T.tanggal ASC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan Masuk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --color-blue-primary: #3B82F6; /* Biru Utama */
            --color-blue-secondary: #BFDBFE; /* Biru Muda */
            --color-blue-dark: #1E40AF;
            --color-bg: #F3F4F6; /* Abu-abu muda */
            --color-surface: #FFFFFF;
            --color-text-dark: #1F2937;
            --color-text-muted: #6B7280;
            --color-success: #10B981;
            --color-warning: #F59E0B;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --border-radius: 12px;
            --font-body: 'Poppins', sans-serif;
        }
        
        * { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            margin: 0;
            background-color: var(--color-bg);
            color: var(--color-text-dark);
            display: flex;
        }

        /* Sidebar Navigasi */
        .sidebar {
            width: 250px;
            background: var(--color-surface);
            height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #E5E7EB;
            position: fixed;
        }
        .sidebar .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--color-blue-dark);
            margin-bottom: 2rem;
            text-align: center;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.9rem 1.2rem;
            text-decoration: none;
            color: var(--color-text-muted);
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: var(--color-blue-secondary);
            color: var(--color-blue-dark);
        }
        .sidebar-nav a .icon { width: 20px; text-align: center; }
        .user-profile {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 1rem;
            border-top: 1px solid #E5E7EB;
        }
        .user-profile .avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--color-blue-primary); color: white;
            display: flex; align-items: center; justify-content: center; font-weight: 600;
        }
        .user-profile .name { font-weight: 600; }
        .user-profile .role { font-size: 0.8em; color: var(--color-text-muted); }
        .logout-btn { color: #EF4444; text-decoration: none; margin-left: auto; font-size: 1.2em; }

        /* Konten Utama */
        .main-content {
            margin-left: 250px; /* Lebar sidebar */
            flex-grow: 1;
            padding: 2rem;
        }
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .main-header h1 { margin: 0; font-size: 1.8rem; }

        /* Kartu Pesanan */
        .pesanan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        .pesanan-card {
            background: var(--color-surface);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .pesanan-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .card-body { padding: 20px; flex-grow: 1; }
        .card-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 10px; margin-bottom: 15px;
        }
        .card-header h3 { margin: 0; font-size: 1.1em; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8em; font-weight: 600; }
        .status-badge.status-baru { background-color: #DBEAFE; color: #1D4ED8; }
        .status-badge.status-diproses { background-color: #FEF3C7; color: #92400E; }
        .item-list { list-style: none; padding: 0; margin: 0 0 15px 0; color: var(--color-text-muted); }
        .item-list li { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #E5E7EB; }
        .total { text-align: right; font-weight: 600; font-size: 1.2em; margin-top: 15px; color: var(--color-blue-dark); }
        .card-footer {
            background-color: #F9FAFB;
            padding: 15px 20px;
            display: flex;
            gap: 10px;
            border-bottom-left-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
        }
        .btn { padding: 10px 15px; border: none; border-radius: 8px; text-decoration: none; color: white; text-align: center; cursor: pointer; font-weight: 500; flex-grow: 1; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.8; }
        .btn-proses { background-color: var(--color-warning); }
        .btn-selesai { background-color: var(--color-success); }
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
            <h1>Pesanan Aktif</h1>
            <span id="realtime-clock"><?php echo date('d M Y, H:i:s'); ?></span>
        </div>

        <div class="pesanan-grid">
            <?php while ($pesanan = mysqli_fetch_assoc($query_pesanan)): ?>
                <?php
                    $is_baru = ($pesanan['status'] == 'Baru Masuk');
                    $status_class = $is_baru ? 'status-baru' : 'status-diproses';
                ?>
                <div class="pesanan-card">
                    <div class="card-body">
                        <div class="card-header">
                            <h3>#<?php echo $pesanan['id_transaksi']; ?> - <?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></h3>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $pesanan['status']; ?></span>
                        </div>
                        <p style="font-size: 0.8em; color: var(--color-text-muted); margin-top: -10px; margin-bottom: 15px;">
                            <i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($pesanan['tanggal'])); ?> WITA
                        </p>
                        <ul class="item-list">
                            <?php
                            $id_transaksi = $pesanan['id_transaksi'];
                            $query_detail = mysqli_query($koneksi, 
                                "SELECT M.nama_menu, DT.jumlah 
                                 FROM Detail_Transaksi DT JOIN Menu M ON DT.id_menu = M.id_menu 
                                 WHERE DT.id_transaksi = $id_transaksi"
                            );
                            while ($detail = mysqli_fetch_assoc($query_detail)):
                            ?>
                            <li>
                                <span><?php echo $detail['jumlah']; ?>x <?php echo htmlspecialchars($detail['nama_menu']); ?></span>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                        <div class="total">
                            Total: Rp <?php echo number_format($pesanan['total_harga']); ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if ($is_baru): ?>
                            <a href="update_status.php?id=<?php echo $id_transaksi; ?>&status=Sedang Diproses" class="btn btn-proses">
                                <i class="fas fa-sync-alt"></i> Proses Pesanan
                            </a>
                        <?php endif; ?>
                        <a href="update_status.php?id=<?php echo $id_transaksi; ?>&status=Selesai" class="btn btn-selesai">
                            <i class="fas fa-check-circle"></i> Selesaikan Pesanan
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if(mysqli_num_rows($query_pesanan) == 0): ?>
                <div style="text-align:center; padding: 4rem; background: var(--color-surface); border-radius: var(--border-radius); box-shadow: var(--shadow); grid-column: 1 / -1;">
                    <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--color-success);"></i>
                    <h3 style="margin-top: 1rem;">Semua pesanan sudah selesai!</h3>
                    <p style="color: var(--color-text-muted);">Tidak ada pesanan aktif saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Jam Real-time Sederhana
        const clockElement = document.getElementById('realtime-clock');
        setInterval(() => {
            const now = new Date();
            const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            clockElement.innerText = now.toLocaleDateString('id-ID', options).replace(/\./g, ':');
        }, 1000);

        // --- JAVASCRIPT BARU UNTUK AUTO-REFRESH ---

        // Fungsi ini akan me-refresh seluruh halaman setiap 15 detik
        setTimeout(function(){
           window.location.reload(1);
        }, 15000); // 15000 milidetik = 15 detik
    </script>
</body>
</html>