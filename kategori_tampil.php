<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';

$sql = "SELECT * FROM Kategori WHERE status_kategori = 'Tersedia' ORDER BY id_kategori DESC";
$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kategori</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Menggunakan CSS yang sama persis dengan halaman admin lainnya */
        :root {
            --color-blue-primary: #3B82F6;
            --color-blue-secondary: #BFDBFE;
            --color-blue-dark: #1E40AF;
            --color-bg: #F3F4F6;
            --color-surface: #FFFFFF;
            --color-text-dark: #1F2937;
            --color-text-muted: #6B7280;
            --color-success: #10B981;
            --color-warning: #F59E0B;
            --color-danger: #EF4444;
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
        .logout-btn { color: var(--color-danger); text-decoration: none; margin-left: auto; font-size: 1.2em; }

        .main-content {
            margin-left: 250px;
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
        .btn-tambah {
            background-color: var(--color-blue-primary);
            color: white;
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }
        .btn-tambah:hover { background-color: var(--color-blue-dark); }

        .table-container {
            background: var(--color-surface);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            max-width: 800px; /* Lebar tabel disesuaikan untuk kategori */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }
        thead th {
            background-color: #F9FAFB;
            color: var(--color-text-muted);
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
        }
        tbody tr:hover { background-color: #F9FAFB; }
        .action-links a {
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.9em;
            margin-right: 5px;
        }
        .action-links .ubah {
            background-color: #FEF3C7; color: #92400E;
        }
        .action-links .hapus {
            background-color: #FEE2E2; color: #991B1B;
        }
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
            <h1>Manajemen Kategori</h1>
            <a href="kategori_tambah.php" class="btn-tambah"><i class="fas fa-plus"></i> Tambah Kategori Baru</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($data = mysqli_fetch_array($query)) { 
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($data['nama_kategori']); ?></td>
                            <td class="action-links">
                                <a href="kategori_ubah.php?id=<?php echo $data['id_kategori']; ?>" class="ubah">Ubah</a>
                                <a href="kategori_hapus.php?id=<?php echo $data['id_kategori']; ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>