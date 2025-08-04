<?php
$app_settings = model('AppSettings')->find(1);
$logo_web = webFile('app_settings', $app_settings['logo'], $app_settings['updated_at']);
?>

<div class="container-fluid" style="font-size:14px;font-family:sans-serif;">
    <!-- Header -->
    <div class="row">
        <div class="col-md-12">
            <div style="background:#ededed;text-align:center!important;padding:16px 12px">
                <img src="<?= $logo_web ?>" style="height:50px;vertical-align:middle">
            </div>
        </div>
    </div>
    <!-- Body -->
    <div class="row" style="padding:20px 12px">
        <div class="col-md-12">
            <div>
                <p>Hai <?= $for_name ?>,</p>
                <p><?= $message ?></p>
            </div>
            <div style="margin:2rem 0px">
                <a href="<?= $button_link ?>">
                    <button style="cursor:pointer;color:#fff;background:#0d6efd;border:1px solid transparent;padding:0.375rem 0.75rem;border-radius:0.25rem;">
                        <?= $button_name ?>
                    </button>
                </a>
            </div>
            <div style="margin-bottom:2rem">
                <p>Terima kasih, <br>
                    <?= $app_settings['nama_aplikasi'] ?>
                </p>
            </div>
            <hr>
            <p style="font-size:12px">
                Jika Anda mengalami masalah dengan menekan tombol "<?= $button_name ?>", salin dan tempel URL berikut ini di browser Anda:
                <a href="<?= $button_link ?>"><?= $button_link ?></a>
            </p>
        </div>
    </div>
    <!-- Footer -->
    <div class="row">
        <div class="col-md-12">
            <div style="background:#ededed;text-align:center;color:#888888;padding:16px 12px">
                <span>
                    &copy; <?= date('Y') . ' ' . $app_settings['nama_aplikasi'] ?>
                </span>
            </div>
        </div>
    </div>
</div>
