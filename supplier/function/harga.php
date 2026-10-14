<?php
// supplier/function/harga.php
// Supplier memperbarui harga satuan bahan baku (penawaran harga).

include_once __DIR__ . '/../login/cek-login.php';
include_once __DIR__ . '/../database/connection.php';

$koneksi = (new Database())->conn;
$kembali = '/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $kembali#harga");
    exit;
}

$supplier_id  = (int) ($_SESSION['supplier_id'] ?? 0);
$product_id   = (int) ($_POST['product_id'] ?? 0);
$harga_satuan = html_entity_decode(htmlspecialchars($_POST['harga_satuan'] ?? ''));

// Harga harus angka dan lebih dari 0
if ($supplier_id <= 0 || $product_id <= 0 || !is_numeric($harga_satuan) || (float) $harga_satuan <= 0 || (float) $harga_satuan > 9999999999) {
    header("Location: $kembali?pesan=harga_tidak_valid#harga");
    exit;
}
$harga_satuan = (float) $harga_satuan;

try {
    // Harga hanya boleh diubah kalau barang itu milik supplier yang sedang login
    $cek_pemilik = $koneksi->prepare("SELECT id FROM products WHERE id = ? AND supplier_id = ?");
    $cek_pemilik->bind_param('ii', $product_id, $supplier_id);
    $cek_pemilik->execute();
    if ($cek_pemilik->get_result()->num_rows === 0) {
        header("Location: $kembali?pesan=harga_gagal#harga");
        exit;
    }

    $ubah = $koneksi->prepare("UPDATE products SET harga_satuan = ? WHERE id = ? AND supplier_id = ?");
    $ubah->bind_param('dii', $harga_satuan, $product_id, $supplier_id);
    $ubah->execute();
} catch (Throwable $kesalahan) {
    header("Location: $kembali?pesan=harga_gagal#harga");
    exit;
}

header("Location: $kembali?pesan=harga_ok#harga");
exit;