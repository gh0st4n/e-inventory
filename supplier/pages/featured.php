<?php
// Variabel di bawah disiapkan di function/data-dashboard.php (lewat partials/head.php).
// Baris ??= hanya memberi nilai awal supaya editor tidak menandai "undefined variable";
// kalau variabelnya sudah ada, nilainya tidak berubah.
$total_po            ??= 0;
$jumlah_po         ??= ['diajukan' => 0, 'disetujui' => 0, 'ditolak' => 0, 'selesai' => 0];

/**
 * @var int $total_po
 * @var array<string,int> $jumlah_po
 */
?>
<section id="ringkasan" class="featured-services featured-5 section light-background">

      <div class="container">

        <div class="row gy-4">

          <div class="col-xl col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-receipt"></i></div>
              <div>
                <h4 class="title"><a href="/#riwayat-po" class="stretched-link"><?= $total_po ?> Total PO</a></h4>
                <p class="description">Semua PO Anda</p>
              </div>
            </div>
          </div>

          <div class="col-xl col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-hourglass-split"></i></div>
              <div>
                <h4 class="title"><a href="/?status=diajukan#riwayat-po" class="stretched-link"><?= $jumlah_po['diajukan'] ?> Diajukan</a></h4>
                <p class="description">Menunggu verifikasi manajer</p>
              </div>
            </div>
          </div>

          <div class="col-xl col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-check2-circle"></i></div>
              <div>
                <h4 class="title"><a href="/?status=disetujui#riwayat-po" class="stretched-link"><?= $jumlah_po['disetujui'] ?> Disetujui</a></h4>
                <p class="description">Siap dipenuhi dan dikirim</p>
              </div>
            </div>
          </div>

          <div class="col-xl col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-box-seam"></i></div>
              <div>
                <h4 class="title"><a href="/?status=selesai#riwayat-po" class="stretched-link"><?= $jumlah_po['selesai'] ?> Selesai</a></h4>
                <p class="description">Sudah diterima gudang</p>
              </div>
            </div>
          </div>

          <div class="col-xl col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-x-circle"></i></div>
              <div>
                <h4 class="title"><a href="/?status=ditolak#riwayat-po" class="stretched-link"><?= $jumlah_po['ditolak'] ?> Ditolak</a></h4>
                <p class="description">Ditolak manajer, tidak perlu dikirim</p>
              </div>
            </div>
          </div>

        </div>

      </div>

    </section>