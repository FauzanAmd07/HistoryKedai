<?php
include 'cek_login.php';
include 'koneksi.php';

$query_pesanan = mysqli_query($koneksi, 
    "SELECT T.*, P.nama AS nama_pelanggan 
     FROM Transaksi T 
     JOIN Pelanggan P ON T.id_pelanggan = P.id_pelanggan 
     WHERE T.status = 'Baru Masuk' OR T.status = 'Sedang Diproses' 
     ORDER BY T.tanggal ASC"
);

// Cek apakah ada pesanan
if(mysqli_num_rows($query_pesanan) > 0) {
    // Jika ada, loop dan buat HTML untuk setiap kartu pesanan
    while ($pesanan = mysqli_fetch_assoc($query_pesanan)) {
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
                    while ($detail = mysqli_fetch_assoc($query_detail)) {
                        echo "<li><span>{$detail['jumlah']}x " . htmlspecialchars($detail['nama_menu']) . "</span></li>";
                    }
                    ?>
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
        <?php
    }
} else {

    echo '<div style="text-align:center; padding: 4rem; background: var(--color-surface); border-radius: var(--border-radius); box-shadow: var(--shadow); grid-column: 1 / -1;">
              <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--color-success);"></i>
              <h3 style="margin-top: 1rem;">Semua pesanan sudah selesai!</h3>
              <p style="color: var(--color-text-muted);">Tidak ada pesanan aktif saat ini.</p>
          </div>';
}
?>