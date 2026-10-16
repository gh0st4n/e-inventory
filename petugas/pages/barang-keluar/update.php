<?php
include_once __DIR__ . '/../../database/connection.php';
$koneksi = (new Database())->conn;

$id = (int) ($_GET['id'] ?? 0);

// Ambil data barang keluar yang mau diubah (lengkap dengan nama barangnya)
$ambil_data = $koneksi->prepare(
    "SELECT stock_transactions.id, stock_transactions.product_id, stock_transactions.jumlah,
            stock_transactions.tanggal, products.nama_barang
     FROM stock_transactions
     INNER JOIN products ON products.id = stock_transactions.product_id
     WHERE stock_transactions.id = ? AND stock_transactions.jenis_transaksi = 'keluar'"
);
$ambil_data->bind_param('i', $id);
$ambil_data->execute();
$data = $ambil_data->get_result()->fetch_assoc();

// Kalau datanya tidak ada, kembali ke daftar
if (!$data) {
    echo "<script>window.location.href='index.php?page=barang-keluar'</script>";
    return;
}

// Stok yang tersedia untuk transaksi ini = stok barang tanpa transaksi ini sendiri
// (stok = masuk - keluar + opname). Jumlah baru tidak boleh melebihi angka ini.
$ambil_stok = $koneksi->prepare(
    "SELECT COALESCE(SUM(CASE stock_transactions.jenis_transaksi
                WHEN 'masuk'  THEN stock_transactions.jumlah
                WHEN 'keluar' THEN -stock_transactions.jumlah
                WHEN 'opname' THEN stock_transactions.jumlah
                ELSE 0 END), 0) AS stok_tersedia
     FROM stock_transactions
     WHERE stock_transactions.product_id = ?
       AND stock_transactions.id <> ?"
);
$ambil_stok->bind_param('ii', $data['product_id'], $id);
$ambil_stok->execute();
$stok_tersedia = max(0, (int) $ambil_stok->get_result()->fetch_assoc()['stok_tersedia']);

$daftar_pesan = [
    'tidak_valid' => 'Jumlah tidak valid, periksa kembali.',
    'stok_kurang' => 'Stok barang tidak cukup untuk jumlah yang diminta.',
];
$pesan = $daftar_pesan[$_GET['pesan'] ?? ''] ?? '';
?>
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="">
              <h1 class="fs-3 mb-1">Edit Barang Keluar</h1>
              <p class="mb-0">Perbarui jumlah barang yang keluar dari gudang</p>
            </div>
            <div>
              <a href="index.php?page=barang-keluar" class="btn btn-primary">Kembali ke Daftar Barang Keluar</a>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <?php if ($pesan): ?>
            <div class="alert alert-danger"><?= $pesan ?></div>
          <?php endif; ?>
          <div class="card">
            <div class="card-body p-4">
              <form method="post" action="function/barang-keluar.php?aksi=ubah">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" value="<?= htmlspecialchars($data['nama_barang']) ?>" disabled>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" max="<?= max(1, $stok_tersedia) ?>" value="<?= (int) $data['jumlah'] ?>" required>
                    <div class="form-text">Maksimal <?= $stok_tersedia ?> (stok yang tersedia).</div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="tanggal" class="form-label">Tanggal Keluar</label>
                    <input type="date" class="form-control" id="tanggal" value="<?= date('Y-m-d', strtotime($data['tanggal'])) ?>" disabled>
                  </div>
                </div>
                <p class="text-secondary small">Barang dan tanggal tidak dapat diubah. Hanya jumlah yang bisa diperbarui.</p>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                  <a href="index.php?page=barang-keluar" class="btn btn-secondary">Batal</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <footer class="text-center py-2 mt-6 text-secondary ">
            <p class="mb-0">Copyright © 2026 E-Inventory Manajemen Logistik &amp; Stok Gudang. Developed by <a href="https://codescandy.com/" target="_blank" class="text-primary">CodesCandy</a> • Distributed by <a href="https://themewagon.com/" target="_blank" class="text-primary">ThemeWagon</a> </p>
          </footer>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Saat halaman dibuka, kursor langsung di BELAKANG angka pada kolom Jumlah
    (function () {
      var kolom_jumlah = document.getElementById('jumlah');
      kolom_jumlah.focus();
      var nilai = kolom_jumlah.value;
      kolom_jumlah.value = '';
      kolom_jumlah.value = nilai;
    })();
  </script>