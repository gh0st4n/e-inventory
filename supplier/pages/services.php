<?php
// Variabel di bawah disiapkan di function/data-dashboard.php (lewat partials/head.php).
// Baris ??= hanya memberi nilai awal supaya editor tidak menandai "undefined variable";
// kalau variabelnya sudah ada, nilainya tidak berubah.
$jumlah_po         ??= ['diajukan' => 0, 'disetujui' => 0, 'ditolak' => 0, 'selesai' => 0];
$filter_status     ??= '';

/**
 * @var array<string,int> $jumlah_po
 * @var string $filter_status
 */

// Tombol berwarna oranye hanya muncul di kartu yang sedang dipilih (sesuai ?status= di alamat)

$kartu_status = [
    [
        'status'     => 'diajukan',
        'judul'      => 'PO Diajukan',
        'deskripsi'  => 'PO baru dari gudang yang menunggu keputusan manajer',
        'keterangan' => 'Menunggu keputusan manajer',
    ],
    [
        'status'     => 'disetujui',
        'judul'      => 'PO Disetujui',
        'deskripsi'  => 'PO sudah disetujui manajer. Siapkan dan kirim barang ke gudang',
        'keterangan' => 'Menunggu barang dikirim dan dicatat gudang',
    ],
    [
        'status'     => 'selesai',
        'judul'      => 'PO Selesai',
        'deskripsi'  => 'Barang sudah diterima dan dicatat gudang, PO ditutup manajer',
        'keterangan' => 'PO sudah ditutup',
    ],
    [
        'status'     => 'ditolak',
        'judul'      => 'PO Ditolak',
        'deskripsi'  => 'PO ditolak manajer, tidak perlu dipenuhi',
        'keterangan' => 'Tidak perlu dikirim',
    ],
];
?>
<section id="status-po" class="pricing section light-background">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Status PO</h2>
        <p>Pantau jumlah Purchase Order Anda berdasarkan statusnya</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <?php foreach ($kartu_status as $indeks => $kartu): $jumlah = $jumlah_po[$kartu['status']] ?? 0; ?>
          <div class="col-xl-3 col-lg-6" data-aos="zoom-in" data-aos-delay="<?= ($indeks + 1) * 100 ?>">
            <div class="pricing-item<?= $kartu['status'] === $filter_status ? ' featured' : '' ?>">
              <h3><?= $kartu['judul'] ?></h3>
              <p class="description"><?= $kartu['deskripsi'] ?></p>
              <h4><?= $jumlah ?><span> PO</span></h4>
              <a href="/?status=<?= $kartu['status'] ?>#riwayat-po" class="cta-btn">Lihat Daftar PO</a>
              <p class="text-center small mb-0"><?= $kartu['keterangan'] ?></p>
            </div>
          </div><!-- End Pricing Item -->
          <?php endforeach; ?>

        </div>

      </div>

    </section>