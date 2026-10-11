<?php
// petugas/function/akun.php
// Petugas hanya boleh mengubah PASSWORD miliknya sendiri.
// Nama dan email tidak bisa diubah dari sini.

include_once __DIR__ . '/../login/cek-login.php';
include_once __DIR__ . '/../database/connection.php';

$koneksi = (new Database())->conn;

$aksi    = $_GET['aksi'] ?? '';
$kembali = '/e-inventory/petugas/index.php?page=akun';

// ---------- UBAH PASSWORD ----------
if ($aksi === 'password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id       = (int) $_SESSION['user_id'];   // selalu akun yang sedang login, bukan dari form
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi    = $_POST['konfirmasi_password'] ?? '';

    // 1. Cek isian masuk akal
    if ($password_lama === '' || $password_baru === '' || $konfirmasi === '') {
        header("Location: $kembali&pesan=kosong");
        exit;
    }
    if (strlen($password_baru) < 6) {
        header("Location: $kembali&pesan=terlalu_pendek");
        exit;
    }
    if (strlen($password_baru) > 72) {   // batas bcrypt
        header("Location: $kembali&pesan=terlalu_panjang");
        exit;
    }
    if ($password_baru !== $konfirmasi) {
        header("Location: $kembali&pesan=konfirmasi_beda");
        exit;
    }

    // 2. Ambil password lama (yang sudah di-hash) dari database
    $ambil_password = $koneksi->prepare("SELECT password FROM users WHERE id = ? AND role = 'gudang'");
    $ambil_password->bind_param('i', $user_id);
    $ambil_password->execute();
    $pengguna = $ambil_password->get_result()->fetch_assoc();
    if (!$pengguna) {
        header("Location: $kembali&pesan=gagal");
        exit;
    }

    // 3. Password lama harus benar
    if (!password_verify($password_lama, $pengguna['password'])) {
        header("Location: $kembali&pesan=password_lama_salah");
        exit;
    }

    // 4. Password baru tidak boleh sama dengan yang lama
    if (password_verify($password_baru, $pengguna['password'])) {
        header("Location: $kembali&pesan=password_sama");
        exit;
    }

    // 5. Simpan password baru (di-hash)
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $ubah = $koneksi->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'gudang'");
    $ubah->bind_param('si', $password_hash, $user_id);
    $ubah->execute();

    // Ganti ID session setelah ganti password (lebih aman)
    session_regenerate_id(true);

    header("Location: $kembali&pesan=password_ok");
    exit;
}

// Aksi tidak dikenal
header("Location: $kembali");
exit;