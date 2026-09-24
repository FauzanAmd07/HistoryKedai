<?php
include 'cek_login.php';
include 'koneksi.php';

// Set timezone WITA
date_default_timezone_set('Asia/Makassar');

// --- 1. QUERY STATISTIK KARTU (TETAP SAMA) ---
// (Kode bagian ini tidak berubah, tapi disertakan agar file lengkap)

$query_pendapatan = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total FROM Transaksi WHERE status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$pendapatan = mysqli_fetch_assoc($query_pendapatan);
$pendapatan_bulan_ini = $pendapatan['total'] ?? 0;

$query_pesanan_baru = mysqli_query($koneksi, "SELECT COUNT(id_transaksi) AS total FROM Transaksi WHERE status = 'Baru Masuk'");
$pesanan_baru = mysqli_fetch_assoc($query_pesanan_baru);
$jumlah_pesanan_baru = $pesanan_baru['total'] ?? 0;

$query_transaksi_bulan_ini = mysqli_query($koneksi, "SELECT COUNT(id_transaksi) AS total FROM Transaksi WHERE status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())");
$transaksi_bulan_ini = mysqli_fetch_assoc($query_transaksi_bulan_ini);
$jumlah_transaksi_bulan_ini = $transaksi_bulan_ini['total'] ?? 0;

$query_menu_terlaris = mysqli_query($koneksi, "SELECT M.nama_menu, SUM(DT.jumlah) AS total_terjual FROM Detail_Transaksi DT JOIN Menu M ON DT.id_menu = M.id_menu JOIN Transaksi T ON DT.id_transaksi = T.id_transaksi WHERE T.status = 'Selesai' AND MONTH(T.tanggal) = MONTH(CURDATE()) AND YEAR(T.tanggal) = YEAR(CURDATE()) GROUP BY DT.id_menu ORDER BY total_terjual DESC LIMIT 1");
$menu_terlaris = mysqli_fetch_assoc($query_menu_terlaris);
$nama_menu_terlaris = $menu_terlaris['nama_menu'] ?? 'Belum ada';

$query_terjual_bulan = mysqli_query($koneksi, "SELECT SUM(DT.jumlah) AS total FROM Detail_Transaksi DT JOIN Transaksi T ON DT.id_transaksi = T.id_transaksi WHERE T.status = 'Selesai' AND MONTH(T.tanggal) = MONTH(CURDATE()) AND YEAR(T.tanggal) = YEAR(CURDATE())");
$terjual_bulan = mysqli_fetch_assoc($query_terjual_bulan);
$jumlah_terjual_bulan = $terjual_bulan['total'] ?? 0;

$query_terjual_tahun = mysqli_query($koneksi, "SELECT SUM(DT.jumlah) AS total FROM Detail_Transaksi DT JOIN Transaksi T ON DT.id_transaksi = T.id_transaksi WHERE T.status = 'Selesai' AND YEAR(T.tanggal) = YEAR(CURDATE())");
$terjual_tahun = mysqli_fetch_assoc($query_terjual_tahun);
$jumlah_terjual_tahun = $terjual_tahun['total'] ?? 0;


// --- 2. QUERY DATA GRAFIK (YANG DIUBAH) ---

// A. Grafik Mingguan (7 Hari Terakhir)
// Mengambil data penjualan dari 6 hari lalu sampai hari ini
$query_grafik_mingguan = mysqli_query($koneksi, 
    "SELECT DATE(tanggal) as tgl, SUM(total_harga) as total 
     FROM Transaksi 
     WHERE status = 'Selesai' 
     AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
     GROUP BY DATE(tanggal)
     ORDER BY tgl ASC"
);
$data_mingguan = [];
while($row = mysqli_fetch_assoc($query_grafik_mingguan)) {
    $data_mingguan[$row['tgl']] = $row['total'];
}

// B. Grafik Bulanan (Tahun Ini) - Tetap
$query_grafik_bulanan = mysqli_query($koneksi, 
    "SELECT MONTH(tanggal) as bulan, SUM(total_harga) as total 
     FROM Transaksi 
     WHERE status = 'Selesai' AND YEAR(tanggal) = YEAR(CURDATE()) 
     GROUP BY MONTH(tanggal)"
);
$data_bulanan = [];
while($row = mysqli_fetch_assoc($query_grafik_bulanan)) { $data_bulanan[] = $row; }

// C. Grafik Harian (Bulan Ini) - Tetap, tapi kita tidak tampilkan di HTML jika tidak diminta
$query_grafik_harian = mysqli_query($koneksi, 
    "SELECT DAY(tanggal) as tanggal, SUM(total_harga) as total 
     FROM Transaksi 
     WHERE status = 'Selesai' AND MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) 
     GROUP BY DAY(tanggal)"
);
$data_harian = [];
while($row = mysqli_fetch_assoc($query_grafik_harian)) { $data_harian[] = $row; }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dasbor - History Kedai</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* CSS Sama seperti sebelumnya */
        :root {
            --color-blue-primary: #3B82F6; --color-blue-secondary: #BFDBFE; --color-blue-dark: #1E40AF;
            --color-bg: #F3F4F6; --color-surface: #FFFFFF; --color-text-dark: #1F2937;
            --color-text-muted: #6B7280; --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --border-radius: 12px; --font-body: 'Poppins', sans-serif;
            --color-success: #10B981; --color-warning: #F59E0B; --color-info: #6366F1;
        }
        * { box-sizing: border-box; }
        body { font-family: var(--font-body); margin: 0; background-color: var(--color-bg); color: var(--color-text-dark); display: flex; }

        .sidebar { width: 250px; background: var(--color-surface); height: 100vh; padding: 2rem 1rem; display: flex; flex-direction: column; border-right: 1px solid #E5E7EB; position: fixed; overflow-y: auto; }
        .sidebar .logo { font-weight: 700; font-size: 1.5rem; color: var(--color-blue-dark); margin-bottom: 2rem; text-align: center; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.2rem; text-decoration: none; color: var(--color-text-muted); font-weight: 500; border-radius: 8px; margin-bottom: 0.5rem; transition: all 0.2s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: var(--color-blue-secondary); color: var(--color-blue-dark); }
        .sidebar-nav a .icon { width: 20px; text-align: center; }
        .user-profile { margin-top: auto; display: flex; align-items: center; gap: 10px; padding: 1rem; border-top: 1px solid #E5E7EB; }
        .user-profile .avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--color-blue-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .logout-btn { color: #EF4444; text-decoration: none; margin-left: auto; font-size: 1.2em; }
        
        .main-content { margin-left: 250px; flex-grow: 1; padding: 2rem; }
        .main-header { margin-bottom: 2rem; }
        .main-header h1 { margin: 0; font-size: 1.8rem; }
        .main-header p { color: var(--color-text-muted); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: var(--color-surface); padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow); display: flex; align-items: center; gap: 1rem; }
        .stat-card .icon-container { padding: 1rem; border-radius: 50%; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; }
        .stat-card.pendapatan .icon-container { background: #D1FAE5; color: #065F46; }
        .stat-card.pesanan .icon-container { background: #FEF3C7; color: #92400E; }
        .stat-card.transaksi .icon-container { background: #DBEAFE; color: #1E40AF; }
        .stat-card.terlaris .icon-container { background: #E0E7FF; color: #3730A3; }
        .stat-card.bulan .icon-container { background: #E0F2FE; color: #0284C7; }
        .stat-card.tahun .icon-container { background: #E0E7FF; color: #4338CA; }
        .stat-card .info h3 { margin: 0; font-size: 1.5rem; }
        .stat-card .info span { font-size: 0.9em; color: var(--color-text-muted); }

        .charts-container { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        .charts-row-full { display: grid; grid-template-columns: 1fr; gap: 25px; }
        .chart-card { background: var(--color-surface); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); }
        .chart-card h3 { margin-top: 0; color: var(--color-text-dark); font-size: 1.2rem; margin-bottom: 15px; }
        
        @media (max-width: 1000px) { .charts-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">HistoryKedai</div>
        <nav class="sidebar-nav">
            <?php 
            $jabatan = $_SESSION['jabatan']; 
            $halaman_ini = basename($_SERVER['PHP_SELF']);
            ?>
            <a href="dashboard.php" class="active"><span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dasbor</a>
            <?php if ($jabatan == 'Kasir'): ?>
                <a href="daftar_pesanan.php"><span class="icon"><i class="fas fa-inbox"></i></span> Pesanan Masuk</a>
                <a href="riwayat_transaksi.php"><span class="icon"><i class="fas fa-history"></i></span> Riwayat Transaksi</a>
            <?php endif; ?>
            <?php if ($jabatan == 'Admin'): ?>
                <a href="riwayat_transaksi.php"><span class="icon"><i class="fas fa-history"></i></span> Riwayat Transaksi</a>
                <a href="laporan.php"><span class="icon"><i class="fas fa-file-excel"></i></span> Laporan Penjualan</a>
            <?php endif; ?>
            <?php if ($jabatan == 'Pemilik'): ?>
                <a href="menu_tampil.php"><span class="icon"><i class="fas fa-utensils"></i></span> Manajemen Menu</a>
                <a href="kategori_tampil.php"><span class="icon"><i class="fas fa-tags"></i></span> Manajemen Kategori</a>
                <a href="karyawan_tampil.php"><span class="icon"><i class="fas fa-users"></i></span> Manajemen Karyawan</a>
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
            <h1>Dasbor</h1>
            <p>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>! Berikut ringkasan bisnis.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card pendapatan">
                <div class="icon-container"><i class="fas fa-dollar-sign"></i></div>
                <div class="info"><h3>Rp <?php echo number_format($pendapatan_bulan_ini); ?></h3><span>Pendapatan Bulan Ini</span></div>
            </div>
            <div class="stat-card pesanan">
                <div class="icon-container"><i class="fas fa-bell"></i></div>
                <div class="info"><h3><?php echo $jumlah_pesanan_baru; ?></h3><span>Pesanan Baru</span></div>
            </div>
            <div class="stat-card transaksi">
                <div class="icon-container"><i class="fas fa-check-circle"></i></div>
                <div class="info"><h3><?php echo $jumlah_transaksi_bulan_ini; ?></h3><span>Transaksi Selesai Bulan Ini</span></div>
            </div>
            <div class="stat-card terlaris">
                <div class="icon-container"><i class="fas fa-star"></i></div>
                <div class="info"><h3 style="font-size: 1.2rem;"><?php echo htmlspecialchars($nama_menu_terlaris); ?></h3><span>Menu Terlaris Bulan Ini</span></div>
            </div>
            <div class="stat-card bulan">
                <div class="icon-container"><i class="fas fa-calendar-alt"></i></div>
                <div class="info"><h3><?php echo $jumlah_terjual_bulan; ?> Pcs</h3><span>Terjual Bulan Ini</span></div>
            </div>
            <div class="stat-card tahun">
                <div class="icon-container"><i class="fas fa-calendar-check"></i></div>
                <div class="info"><h3><?php echo $jumlah_terjual_tahun; ?> Pcs</h3><span>Terjual Tahun Ini</span></div>
            </div>
        </div>

        <div class="charts-row-full" style="margin-bottom: 25px;">
            <div class="chart-card">
                <h3>Grafik Penjualan Minggu Ini (7 Hari Terakhir)</h3>
                <canvas id="weeklySalesChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h3>Grafik Penjualan Bulan Ini (Per Tanggal)</h3>
                <canvas id="dailySalesChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>Grafik Penjualan Tahun Ini (Per Bulan)</h3>
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        // --- PROSES DATA GRAFIK MINGGUAN ---
        const weeklyDataRaw = <?php echo json_encode($data_mingguan); ?>;
        
        // Membuat Label 7 Hari Terakhir (Contoh: 'Senin', 'Selasa', dst)
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const weeklyLabels = [];
        const weeklyData = [];

        for (let i = 6; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const dateStr = d.toISOString().split('T')[0]; // Format YYYY-MM-DD
            
            // Nama Hari
            weeklyLabels.push(days[d.getDay()]);
            
            // Data Penjualan (0 jika tidak ada data)
            weeklyData.push(weeklyDataRaw[dateStr] || 0);
        }

        // 1. Render Grafik Mingguan
        new Chart(document.getElementById('weeklySalesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: weeklyData,
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true, tension: 0.4, pointRadius: 5, pointHoverRadius: 7
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // --- PROSES DATA GRAFIK LAINNYA (TETAP) ---
        const dailyDataRaw = <?php echo json_encode($data_harian); ?>;
        const monthlyDataRaw = <?php echo json_encode($data_bulanan); ?>;

        // 2. Grafik Per Tanggal (Bulan Ini)
        const daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();
        const dailyLabels = Array.from({length: daysInMonth}, (_, i) => i + 1);
        const dailyData = new Array(daysInMonth).fill(0);
        dailyDataRaw.forEach(item => { dailyData[item.tanggal - 1] = item.total; });
        
        new Chart(document.getElementById('dailySalesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: dailyData,
                    backgroundColor: '#3B82F6',
                    borderRadius: 5
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // 3. Grafik Per Bulan (Tahun Ini)
        const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthlyData = new Array(12).fill(0);
        monthlyDataRaw.forEach(item => { monthlyData[item.bulan - 1] = item.total; });

        new Chart(document.getElementById('monthlySalesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Pendapatan Bulanan',
                    data: monthlyData,
                    backgroundColor: '#10B981',
                    borderRadius: 5
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>

</body>
</html>