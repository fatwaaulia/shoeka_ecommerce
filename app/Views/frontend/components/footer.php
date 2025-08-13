<?php
$app_settings = model('AppSettings')->find(1);
$logo_web = webFile('image', 'app_settings', $app_settings['logo'], $app_settings['updated_at']);
?>

<style>
footer a { color: white; }
footer a:hover { color: white; }
.text-slider-left { transition: .3s; }
.text-slider-left:hover { padding-left: 5px; }
</style>

<section class="container-fluid py-5 mt-5" style="border-top: 1px solid #ddd;">
    <div class="container">
        <div class="row gy-3">
            <div class="col-12 col-md-6 col-xl-3">
                <p class="mb-3 fw-600">TEMUKAN KAMI</p>
                <div class="mb-2">
                    <a href="https://www.instagram.com/shoekashoes" class="me-2 text-dark" target="_blank">
                        <i class="fa-brands fa-instagram fa-lg"></i>
                    </a>
                    <a href="https://www.tiktok.com/@shoekashoes" class="me-2 text-dark" target="_blank">
                        <i class="fa-brands fa-tiktok fa-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/ShoekaShoesStore" class="me-2 text-dark" target="_blank">
                        <i class="fa-brands fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UC9MOdVbKxYdacZzhVF2c_zA" class="me-2 text-dark" target="_blank">
                        <i class="fa-brands fa-youtube fa-lg"></i>
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <p class="mb-2 fw-600">INFORMASI KAMI</p>
                <div class="mb-2">
                    <a href="" class="text-dark">
                        Kontak
                    </a> <br>
                    <a href="" class="text-dark">
                        Tentang Shoeka
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <p class="mb-2 fw-600">PUSAT BANTUAN</p>
                <div class="mb-2">
                    <a href="" class="text-dark">
                        FAQ
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <p class="mb-2 fw-600">LACAK PESANAN</p>
                <div class="mb-2">
                    <form action="<?= base_url() ?>detail-pesanan" method="get">
                        <div class="mb-2">
                            <label for="kode" class="form-label">Kode Pesanan</label>
                            <input type="text" class="form-control" id="kode" name="kode" placeholder="Masukkan kode pesanan" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="mb-2">
                              <small><i>* Kode pesanan bisa dicek di email atau whatsapp yang digunakan untuk melakukan transaksi.</i></small>
                        </div>
                        <button type="submit" class="btn btn-primary float-start">Cari Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="container-fluid px-0 text-white py-3 bg-dark mt-4">
    <div class="container">
        <div class="row">
            <div class="col-12 py-1 d-flex justify-content-between">
                <img src="<?= $logo_web ?>" style="height: 20px;" alt="<?= $app_settings['nama_aplikasi'] ?>" title="<?= $app_settings['nama_aplikasi'] ?>">
                <span> <?= date('Y') ?> © CV SEPATU SHOEKA</span>
            </div>
        </div>
    </div>
</footer>
