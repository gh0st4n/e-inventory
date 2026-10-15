<?php
// supplier/function/hapus-barang.php
// Supplier menghapus bahan baku miliknya sendiri (misalnya karena datanya tidak sesuai).

include_once __DIR__ . '/../login/cek-login.php';
include_once __DIR__ . '/../database/connection.php';

$koneksi = (new Database())->conn;
$kembali = '/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $kembali#harga");
    exit;
}

$supplier_id = (int) ($_SESSION['supplier_id'] ?? 0);
$product_id  = (int) ($_POST['product_id'] ?? 0);

if ($supplier_id <= 0 || $product_id <= 0) {
    header("Location: $kembali?pesan=hapus_gagal#harga");
    exit;
}

try {
    // Hanya barang milik supplier yang sedang login yang bisa dihapus
    $hapus = $koneksi->prepare("DELETE FROM products WHERE id = ? AND supplier_id = ?");
    $hapus->bind_param('ii', $product_id, $supplier_id);
    $hapus->execute();

    if ($hapus->affected_rows === 0) {
        header("Location: $kembali?pesan=hapus_gagal#harga");
        exit;
    }
} catch (mysqli_sql_exception $kesalahan) {
    // Kode 1451: barang masih dipakai di data lain (misalnya sudah ada transaksi stok), jadi ditolak database
    $pesan = ($kesalahan->getCode() === 1451) ? 'hapus_dipakai' : 'hapus_gagal';
    header("Location: $kembali?pesan=$pesan#harga");
    exit;
} catch (Throwable $kesalahan) {
    header("Location: $kembali?pesan=hapus_gagal#harga");
    exit;
}

header("Location: $kembali?pesan=hapus_ok#harga");
exit;