<?php
// petugas/function/supplier-ditolak.php
// Aturan: barang milik supplier yang PO terbarunya DITOLAK manajer tidak boleh dipilih
// di Barang Masuk, Barang Keluar, dan Opname Stok. Kalau supplier itu nanti dibuatkan PO baru
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


// ---------- Syarat tambahan khusus OPNAME ----------
// Barang baru boleh diopname kalau supplier-nya sudah punya PO yang diverifikasi manajer
// (status 'disetujui' atau 'selesai'). Artinya petugas harus sudah mengajukan kebutuhan bahan baku
// ke supplier dan manajer sudah menyetujuinya. PO yang masih 'diajukan' belum cukup.

// Potongan SQL: TRUE kalau supplier pemilik barang sudah punya PO disetujui / selesai.
function syaratPoTerverifikasi(): string
{
    return "EXISTS (
                SELECT 1 FROM purchase_orders AS po_valid
                WHERE po_valid.supplier_id = products.supplier_id
                  AND po_valid.status_po IN ('disetujui', 'selesai')
            )";
}

// Cek apakah barang boleh diopname.
// Hasilnya null kalau boleh; kalau tidak, berisi alasannya: 'ditolak' atau 'belum_po'.
function alasanTidakBisaOpname(mysqli $koneksi, int $product_id): ?string
{
    if (barangDariSupplierDitolak($koneksi, $product_id)) {
        return 'ditolak';
    }

    $cek = $koneksi->prepare(
        "SELECT products.id FROM products WHERE products.id = ? AND " . syaratPoTerverifikasi()
    );
    $cek->bind_param('i', $product_id);
    $cek->execute();
    if ($cek->get_result()->num_rows === 0) {
        return 'belum_po';
    }

    return null;
}