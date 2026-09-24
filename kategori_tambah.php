<?php
include 'cek_login.php';
if ($_SESSION['jabatan'] != 'Pemilik') {
    die("Halaman ini hanya bisa diakses oleh Pemilik. <a href='dashboard.php'>Kembali</a>");
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori Baru</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS Lengkap Dasbor Biru Soft */
        :root {
            --color-blue-primary: #3B82F6; --color-blue-secondary: #BFDBFE; --color-blue-dark: #1E40AF;
            --color-bg: #F3F4F6; --color-surface: #FFFFFF; --color-text-dark: #1F2937;
            --color-text-muted: #6B7280; --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --border-radius: 12px; --font-body: 'Poppins', sans-serif;
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
        .sidebar .logo { font-weight: 700; font-size: 1.5rem; color: var(--color-blue-dark); margin-bottom: 2rem; text-align: center; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 0.9rem 1.2rem; text-decoration: none; color: var(--color-text-muted); font-weight: 500; border-radius: 8px; margin-bottom: 0.5rem; transition: all 0.2s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: var(--color-blue-secondary); color: var(--color-blue-dark); }
        .sidebar-nav a .icon { width: 20px; text-align: center; }
        .user-profile { margin-top: auto; display: flex; align-items: center; gap: 10px; padding: 1rem; border-top: 1px solid #E5E7EB; }
        .user-profile .avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--color-blue-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .logout-btn { color: #EF4444; text-decoration: none; margin-left: auto; font-size: 1.2em; }

        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            padding: 2rem;
        }
        .main-header h1 { margin: 0 0 2rem 0; font-size: 1.8rem; }
        
        .form-container {
            background: var(--color-surface);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            max-width: 500px;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--color-text-dark);
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--color-blue-primary);
            box-shadow: 0 0 0 2px var(--color-blue-secondary);
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            border-top: 1px solid #E5E7EB;
            padding-top: 1.5rem;
        }
        .btn { padding: 0.8rem 1.5rem; border: none; border-radius: 8px; text-decoration: none; color: white; text-align: center; cursor: pointer; font-weight: 500; font-size: 1rem; }
        .btn-simpan { background-color: var(--color-blue-primary); }
        .btn-batal { background-color: var(--color-text-muted); color: var(--color-surface); }
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
        <div class="main-header"><h1>Tambah Kategori Baru</h1></div>
        <div class="form-container">
            <form action="kategori_proses_tambah.php" method="post">
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" id="nama_kategori" name="nama_kategori" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-simpan">Simpan Kategori</button>
                    <a href="kategori_tampil.php" class="btn btn-batal">Batal</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>