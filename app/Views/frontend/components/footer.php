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

<footer class="container-fluid px-0 text-white pt-5 pb-3 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 pb-4">
                <img src="<?= $logo_web ?>" class="w-50 w-md-25 w-lg-50" alt="<?= $app_settings['nama_aplikasi'] ?>" title="<?= $app_settings['nama_aplikasi'] ?>">
            </div>
            <div class="col-lg-3 pb-3">
                <h5 class="mb-3">Web App</h5>
                <div class="mb-2">
                    <a href="/" class="text-slider-left">> Beranda</a>
                </div>
            </div>
            <div class="col-lg-3 pb-3">
                <h5 class="mb-3">Media & Informasi</h5>
                <div class="mb-2">
                    <a href="<?= base_url('berita') ?>" class="text-slider-left">> Berita</a>
                </div>
            </div>
            <style>
            .sosial-media img {
                filter: grayscale(100%);
                transition: .3s;
            }
            .sosial-media:hover img {
                filter: grayscale(0%);
            }
            </style>
            <div class="col-lg-3 pb-3">
                <h5 class="mb-3">Lokasi</h5>
                <table class="mb-2">
                    <tr>
                        <td style="padding-left: 1.5rem; text-indent: -11px;">
                            <i class="fa-solid fa-location-dot me-2"></i>
                            <span>
                                <?= $app_settings['nama_perusahaan'] ?> <br>
                                <?= $app_settings['alamat'] ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <hr style="opacity: .25;">
        <div class="row">
            <div class="col-12 py-1">
                <span>Copyright © <?= date('Y') ?> | Web App</span>
            </div>
        </div>
    </div>
</footer>
