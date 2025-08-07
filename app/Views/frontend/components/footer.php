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

<footer class="container-fluid px-0 text-white pt-5 pb-3 bg-dark mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 pb-4">
                <img src="<?= $logo_web ?>" class="w-50 w-md-25 w-lg-50" alt="<?= $app_settings['nama_aplikasi'] ?>" title="<?= $app_settings['nama_aplikasi'] ?>">
            </div>
            <div class="col-lg-3 pb-3">
                <h5 class="mb-3">Temukan Kami</h5>
                <div class="mb-2">
                    <a href="https://www.instagram.com/shoekashoes" class="me-2" target="_blank">
                        <i class="fa-brands fa-instagram fa-lg"></i>
                    </a>
                    <a href="https://www.tiktok.com/@shoekashoes" class="me-2" target="_blank">
                        <i class="fa-brands fa-tiktok fa-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/ShoekaShoesStore" class="me-2" target="_blank">
                        <i class="fa-brands fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UC9MOdVbKxYdacZzhVF2c_zA" class="me-2" target="_blank">
                        <i class="fa-brands fa-youtube fa-lg"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 pb-3">
                <h5 class="mb-3">Alamat</h5>
                <table class="mb-2">
                    <tr>
                        <td>
                            <?= $app_settings['alamat'] ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <hr style="opacity: .25;">
        <div class="row">
            <div class="col-12 py-1">
                <span>Copyright © <?= date('Y') ?> | CV </span>
            </div>
        </div>
    </div>
</footer>
