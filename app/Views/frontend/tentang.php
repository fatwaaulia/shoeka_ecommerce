<?php
$app_settings = model('AppSettings')->find(1);
?>

<section class="container">
    <div class="row">
        <div class="col-12 text-center mt-3">
            <h4>Tentang Shoeka</h4>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 col-xl-8 offset-xl-2">
            <?= $app_settings['tentang'] ?>
        </div>
    </div>
</section>