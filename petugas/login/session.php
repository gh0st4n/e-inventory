<?php
// petugas/login/session.php
// Satu-satunya tempat session dimulai. Semua file (login, cek-login, logout, function/login)
// memanggil file ini, jadi pengaturan keamanan session cukup diatur di sini.
//
// Kenapa perlu: sebelumnya tampilan petugas dan manajer memakai session yang SAMA
// (cookie PHPSESSID di localhost). Login di manajer menimpa role petugas, dan logout di
// salah satunya menghapus session yang lain, sehingga harus login ulang terus.
// Sekarang tiap tampilan punya nama session, cookie, dan folder penyimpanan sendiri.

const SESSION_NAMA        = 'INV_PETUGAS';
const SESSION_PATH_COOKIE = '/e-inventory/petugas/';
const SESSION_BATAS_IDLE  = 7200;   // 2 jam tanpa aktivitas -> harus login lagi
const SESSION_BATAS_TOTAL = 28800;  // 8 jam sejak login -> harus login lagi
const SESSION_GANTI_ID    = 1800;   // ID session diganti tiap 30 menit

if (session_status() === PHP_SESSION_NONE) {
    // Folder penyimpanan session khusus (di luar folder web) supaya tidak ikut
    // terhapus oleh aplikasi PHP lain di localhost, misalnya phpMyAdmin
    $folder_session = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'e-inventory-session-petugas';
    if (!is_dir($folder_session)) {
        @mkdir($folder_session, 0700, true);
    }
    if (is_dir($folder_session) && is_writable($folder_session)) {
        session_save_path($folder_session);
    }

    $pakai_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);

    ini_set('session.use_strict_mode', '1');     // tolak ID session buatan penyerang
    ini_set('session.use_only_cookies', '1');    // ID session tidak boleh lewat URL
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');     // tidak bisa dibaca JavaScript
    ini_set('session.gc_maxlifetime', (string) SESSION_BATAS_TOTAL);

    session_name(SESSION_NAMA);
    session_set_cookie_params([
        'lifetime' => 0,                    // hilang saat browser ditutup
        'path'     => SESSION_PATH_COOKIE,  // hanya dikirim ke tampilan petugas
        'secure'   => $pakai_https,         // otomatis aktif kalau memakai HTTPS
        'httponly' => true,
        'samesite' => 'Lax',                // menahan serangan CSRF dari situs lain
    ]);
    session_start();

    $sekarang = time();

    // Session kedaluwarsa (terlalu lama diam, terlalu lama sejak login) atau browser berbeda:
    // kosongkan isinya dan beri ID baru
    $kedaluwarsa = false;
    if (isset($_SESSION['aktivitas_terakhir']) && $sekarang - $_SESSION['aktivitas_terakhir'] > SESSION_BATAS_IDLE) {
        $kedaluwarsa = true;
    }
    if (isset($_SESSION['login_pada']) && $sekarang - $_SESSION['login_pada'] > SESSION_BATAS_TOTAL) {
        $kedaluwarsa = true;
    }
    if (isset($_SESSION['ua_hash']) && !hash_equals($_SESSION['ua_hash'], hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? ''))) {
        $kedaluwarsa = true;
    }
    if ($kedaluwarsa) {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    // Ganti ID session secara berkala (mengurangi risiko ID yang bocor dipakai lama)
    if (isset($_SESSION['user_id'])) {
        if (!isset($_SESSION['id_dibuat_pada'])) {
            $_SESSION['id_dibuat_pada'] = $sekarang;
        } elseif ($sekarang - $_SESSION['id_dibuat_pada'] > SESSION_GANTI_ID) {
            session_regenerate_id(true);
            $_SESSION['id_dibuat_pada'] = $sekarang;
        }
        $_SESSION['aktivitas_terakhir'] = $sekarang;
    }
}