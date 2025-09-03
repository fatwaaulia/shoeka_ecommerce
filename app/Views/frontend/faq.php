<?php
$app_settings = model('AppSettings')->find(1);
?>

<body style="padding-top: 114.38px;">

<section class="container">
    <div class="row py-2">
        <div class="col-12 text-center">
            <h4>FAQ</h4>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12 col-xl-8 offset-xl-2">
            <?= $app_settings['faq'] ?>
        </div>
    </div>
</section>