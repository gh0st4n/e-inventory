<?php
// petugas/function/supplier-ditolak.php
// Aturan: barang milik supplier yang PO terbarunya DITOLAK manajer tidak boleh dipilih
// di Barang Masuk dan Barang Keluar. Kalau supplier itu nanti dibuatkan PO baru
// (PO terbarunya bukan 'ditolak' lagi), barangnya muncul kembali.
// Barang yang belum punya supplier atau supplier tanpa PO tetap boleh dipilih.

// Potongan SQL: TRUE kalau supplier pemilik barang (products.supplier_id) PO terbarunya ditolak.
// Dipakai di query yang memakai tabel products.
function syaratSupplierDitolak(): string
{
    return "EXISTS (
                SELECT 1 FROM purchase_orders AS po_terakhir
                WHERE po_terakhir.supplier_id = products.supplier_id
                  AND po_terakhir.status_po = 'ditolak'
                  AND po_terakhir.id = (
                        SELECT MAX(po_semua.id) FROM purchase_orders AS po_semua
                        WHERE po_semua.supplier_id = products.supplier_id)
            )";
}

// Cek satu barang: true kalau barang itu milik supplier yang ditolak.
function barangDariSupplierDitolak(mysqli $koneksi, int $product_id): bool
{
    $cek = $koneksi->prepare(
        "SELECT products.id FROM products WHERE products.id = ? AND " . syaratSupplierDitolak()
    );
    $cek->bind_param('i', $product_id);
    $cek->execute();
    return $cek->get_result()->num_rows > 0;
}