<?php
include_once __DIR__ . '/../../database/connection.php';
$koneksi = (new Database())->conn;

// Ambil data akun petugas yang sedang login
$user_id = (int) $_SESSION['user_id'];
$ambil_akun = $koneksi->prepare("SELECT nama, email, role FROM users WHERE id = ?");
$ambil_akun->bind_param('i', $user_id);
$ambil_akun->execute();
$akun = $ambil_akun->get_result()->fetch_assoc();

// ---------- PESAN ----------
$daftar_pesan = [
    'password_ok'         => ['success', 'Password berhasil diubah.'],
    'kosong'              => ['danger',  'Semua kolom password wajib diisi.'],
    'terlalu_pendek'      => ['danger',  'Password baru minimal 6 karakter.'],
    'terlalu_panjang'     => ['danger',  'Password baru maksimal 72 karakter.'],
    'konfirmasi_beda'     => ['danger',  'Konfirmasi password tidak sama dengan password baru.'],
    'password_lama_salah' => ['danger',  'Password lama yang Anda masukkan salah.'],
    'password_sama'       => ['warning', 'Password baru tidak boleh sama dengan password lama.'],
    'gagal'               => ['danger',  'Password gagal diubah. Coba lagi.'],
];
$pesan = $daftar_pesan[$_GET['pesan'] ?? ''] ?? null;
?>
  <main id="content" class="content py-10">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="mb-4">
            <h1 class="fs-3 mb-1">Akun Saya</h1>
            <p class="mb-0">Lihat data akun dan ubah password Anda</p>
          </div>
        </div>
      </div>

      <?php if ($pesan): ?>
        <div class="alert alert-<?= $pesan[0] ?>"><?= $pesan[1] ?></div>
      <?php endif; ?>

      <div class="row g-3">
        <!-- Informasi akun (hanya dibaca) -->
        <div class="col-lg-5 col-12">
          <div class="card h-100">
            <div class="card-header bg-white px-4 py-3">
              <h3 class="h5 mb-0">Informasi Akun</h3>
            </div>
            <div class="card-body p-4">
              <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" value="<?= htmlspecialchars($akun['nama'] ?? '') ?>" disabled>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" value="<?= htmlspecialchars($akun['email'] ?? '') ?>" disabled>
              </div>
              <div class="mb-3">
                <label class="form-label d-block">Peran</label>
                <span class="badge bg-primary-subtle text-primary">Petugas Gudang</span>
              </div>
              <p class="text-secondary small mb-0">Nama dan email tidak dapat diubah. Anda hanya dapat mengubah password.</p>
            </div>
          </div>
        </div>

        <!-- Form ubah password -->
        <div class="col-lg-7 col-12">
          <div class="card h-100">
            <div class="card-header bg-white px-4 py-3">
              <h3 class="h5 mb-0">Ubah Password</h3>
            </div>
            <div class="card-body p-4">
              <form method="post" action="function/akun.php?aksi=password">
                <div class="mb-3">
                  <label for="password_lama" class="form-label">Password Lama</label>
                  <input type="password" class="form-control" name="password_lama" id="password_lama" autocomplete="current-password" required>
                </div>
                <div class="mb-3">
                  <label for="password_baru" class="form-label">Password Baru</label>
                  <input type="password" class="form-control" name="password_baru" id="password_baru" minlength="6" maxlength="72" autocomplete="new-password" required>
                  <div class="form-text">Minimal 6 karakter.</div>
                </div>
                <div class="mb-3">
                  <label for="konfirmasi_password" class="form-label">Ulangi Password Baru</label>
                  <input type="password" class="form-control" name="konfirmasi_password" id="konfirmasi_password" minlength="6" maxlength="72" autocomplete="new-password" required>
                </div>
                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" id="lihat_password">
                  <label class="form-check-label" for="lihat_password">Tampilkan password</label>
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Simpan Password</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
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
    // Centang "Tampilkan password": tampilkan / sembunyikan ketiga kolom password
    document.getElementById('lihat_password').addEventListener('change', function () {
      var jenis = this.checked ? 'text' : 'password';
      ['password_lama', 'password_baru', 'konfirmasi_password'].forEach(function (id) {
        document.getElementById(id).type = jenis;
      });
    });
  </script>