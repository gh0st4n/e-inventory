<?php
include_once __DIR__ . '/../../database/connection.php';
$koneksi = (new Database())->conn;

// Barang yang diopname dipilih dari tombol "Opname" di daftar stok, jadi namanya tidak bisa diganti di sini
$barang_dipilih = (int) ($_GET['product_id'] ?? 0);

// Ambil barang itu beserta stok sistem saat ini (stok = masuk - keluar + opname)
$ambil_barang = $koneksi->prepare(
    "SELECT products.id, products.nama_barang,
            COALESCE(SUM(CASE stock_transactions.jenis_transaksi
                WHEN 'masuk'  THEN stock_transactions.jumlah
                WHEN 'keluar' THEN -stock_transactions.jumlah
                WHEN 'opname' THEN stock_transactions.jumlah
                ELSE 0 END), 0) AS stok_saat_ini
     FROM products
     LEFT JOIN stock_transactions ON stock_transactions.product_id = products.id
     WHERE products.id = ?
     GROUP BY products.id, products.nama_barang"
);
$ambil_barang->bind_param('i', $barang_dipilih);
$ambil_barang->execute();
$barang = $ambil_barang->get_result()->fetch_assoc();

$daftar_pesan = [
    'tidak_valid'      => 'Isian tidak valid, periksa kembali.',
    'barang_tidak_ada' => 'Barang yang dipilih tidak ditemukan.',
];
$pesan = $daftar_pesan[$_GET['pesan'] ?? ''] ?? '';
?>
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="">
              <h1 class="fs-3 mb-1">Catat Opname Stok</h1>
              <p class="mb-0">Masukkan jumlah barang hasil hitung fisik di gudang yang terindikasi rusak</p>
            </div>
            <div>
              <a href="index.php?page=stok" class="btn btn-primary">Kembali ke Stok &amp; Opname</a>
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
              <?php if ($barang): ?>
              <form id="addProductForm" method="post" action="function/stok.php?aksi=simpan">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']) ?>" readonly>
                    <input type="hidden" name="product_id" value="<?= (int) $barang['id'] ?>">
                    <div class="form-text">Stok sistem saat ini: <?= (int) $barang['stok_saat_ini'] ?></div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="stok_fisik" class="form-label">Stok Rusak</label>
                    <input type="number" class="form-control" name="stok_fisik" id="stok_fisik" placeholder="0" min="0" required>
                    <div class="form-text">Selisih dengan stok sistem dicatat otomatis.</div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="tanggal" class="form-label">Tanggal Opname</label>
                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                  </div>
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>

              </form>
              <?php else: ?>
                <div class="text-center py-4">
                  <p class="mb-3">Pilih barang lewat tombol Opname di daftar Stok &amp; Opname.</p>
                  <a href="index.php?page=stok" class="btn btn-primary">Ke Stok &amp; Opname</a>
                </div>
              <?php endif; ?>
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