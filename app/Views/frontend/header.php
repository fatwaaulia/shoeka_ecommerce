<?php
$app_settings = model('AppSettings')->find(1);
$favicon      = isset($favicon) ? $favicon : webFile('image', 'app_settings', $app_settings['favicon'], $app_settings['updated_at']);
$title        = isset($title) ? $title : $app_settings['nama_aplikasi'];
$description  = isset($description) ? $description : $app_settings['deskripsi'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:image" content="<?= $favicon ?>">
    <meta property="og:image:width" content="200">
    <meta property="og:image:height" content="200">
    <meta property="og:title" content="<?= $title ?>">
    <meta property="og:description" content="<?= $description ?>">
    <meta property="og:site_name" content="<?= $app_settings['nama_aplikasi'] ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta name="description" content="<?= $description ?>">
    <link rel="shortcut icon" href="<?= $favicon ?>" type="image/x-icon">
    <title><?= $title ?></title>

    <!-- Source Support -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/poppins/all.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/fontawesome/css/all.min.css">
    <script src="<?= base_url() ?>assets/js/sweetalert2.js"></script>
    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/wowjs/animate.min.css">
    <script src="<?= base_url() ?>assets/modules/wowjs/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>

    <!-- My Style -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/main.css?v=1.0">
    <style>
    section { padding: 25px; }

    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        display: flex;
        flex-direction: column;
    }

    main { flex: 1; }
    </style>

    <!-- My Script -->
    <script>
    const dom = (selector) => {
        const nodes = document.querySelectorAll(selector);
        if (nodes.length === 1) {
            nodes[0].forEach = callback => callback(nodes[0]);
            return nodes[0];
        }
        return nodes;
    };
    </script>
    <script src="<?= base_url() ?>assets/js/fetch/submit_data_with_confirm.js"></script>
</head>

<body>
    <div id="loading"></div>
    <div id="preloader"></div>
    <script>
    const preloader = dom('#preloader');
    preloader.style.display = 'block';
    preloader.style.opacity = '1';

    setTimeout(() => {
        preloader.style.transition = 'opacity 0.4s';
        preloader.style.opacity = preloader.style.opacity === '1' ? '0' : '1';
        if (preloader.style.opacity === '0') {
            setTimeout(() => preloader.style.display = 'none', 400);
        }
    }, 0);
    </script>

    <?= session()->getFlashdata('message') ?>

    <?= $navbar ?? '' ?>
    <main>
        <?= $content ?? '' ?>
    </main>
    <?= $footer ?? '' ?>

    <!-- Source Support -->
    <script src="<?= base_url() ?>assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/js/lazyload.js"></script>
    <script>
    window.addEventListener('load', () => {
        if (document.referrer === location.href) {
            const scrollY = localStorage.getItem('scrollY');
            window.scrollTo(0, +scrollY || 0);
        } else {
            localStorage.removeItem('scrollY');
        }
    });

    window.addEventListener('beforeunload', () => {
        localStorage.setItem('scrollY', window.scrollY);
    });

    new LazyLoad({
        elements_selector: '.lazy-shimmer',
        callback_loaded: (el) => {
            el.classList.remove('lazy-shimmer');
        }
    });
    </script>

    <!-- My Script -->
    <script src="<?= base_url() ?>assets/js/main.js?v=1.0"></script>
</body>
</html>
