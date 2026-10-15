<!-- Vendor JS Files -->
  <script src="/supplier/template/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/supplier/template/assets/vendor/php-email-form/validate.js"></script>
  <script src="/supplier/template/assets/vendor/aos/aos.js"></script>
  <script src="/supplier/template/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/supplier/template/assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="/supplier/template/assets/js/main.js"></script>

  <!-- Pindah filter status PO tanpa memuat ulang halaman -->
  <script>
    (function () {
      var searchSaatIni = location.search;
      var bagianDiganti = ['riwayat-po'];

      document.addEventListener('click', function (e) {
        var tautan = e.target.closest ? e.target.closest('a[href^="/"][href*="#riwayat-po"]') : null;
        if (!tautan || e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        if (tautan.origin !== location.origin || tautan.pathname !== location.pathname) return;

        e.preventDefault();

        fetch(tautan.pathname + tautan.search, { credentials: 'same-origin' })
          .then(function (respons) {
            if (!respons.ok) throw new Error('gagal');
            return respons.text();
          })
          .then(function (html) {
            var dokumen = new DOMParser().parseFromString(html, 'text/html');

            // Kalau bagian yang dicari tidak ada (misalnya sesi habis), muat halaman seperti biasa
            var semuaAda = bagianDiganti.every(function (id) {
              return dokumen.getElementById(id) && document.getElementById(id);
            });
            if (!semuaAda) throw new Error('bagian tidak ada');

            bagianDiganti.forEach(function (id) {
              var lama = document.getElementById(id);
              lama.innerHTML = dokumen.getElementById(id).innerHTML;
              // Hilangkan animasi AOS di isi baru supaya langsung tampil tanpa berkedip
              lama.querySelectorAll('[data-aos]').forEach(function (el) {
                el.removeAttribute('data-aos');
                el.removeAttribute('data-aos-delay');
              });
            });

            searchSaatIni = tautan.search;
            history.pushState({}, '', tautan.pathname + tautan.search + '#riwayat-po');
            document.getElementById('riwayat-po').scrollIntoView({ behavior: 'smooth', block: 'start' });
          })
          .catch(function () {
            window.location.href = tautan.href;
          });
      });

      // Tombol Back/Forward browser: muat ulang kalau filter di alamat berbeda dengan yang tampil
      window.addEventListener('popstate', function () {
        if (location.search !== searchSaatIni) location.reload();
      });
    })();
  </script>