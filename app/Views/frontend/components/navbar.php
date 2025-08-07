<?php
$app_settings = model('AppSettings')->find(1);
$logo_web = webFile('image', 'app_settings', $app_settings['logo'], $app_settings['updated_at']);
$uri = service('uri');
$uri->setSilent(true);

$get_kategori = $_GET['kategori'] ?? '';
$get_sub = $_GET['sub'] ?? '';
$get_sub_sub = $_GET['sub_sub'] ?? '';
$get_config = !empty($_GET['config']) ? ('&config=' . $_GET['config']) : '';
?>

<style>
.navbar { box-shadow: 0px 2px 20px rgba(1, 41, 112, 0.1)!important; }
@media (min-width: 1200px) {
    .navbar { padding-right: 10px!important; }
}
.navbar-toggler { border: none; }
.nav-link { color: #000000!important }
.nav-link:hover { color:var(--main-color)!important; }
.nav-active {
    color:var(--main-color)!important;
    font-weight: 500;
}
.dropdown-menu .nav-active:active {
    color:white!important;
    background-color:var(--main-color)!important;
}
</style>
<nav class="navbar navbar-expand-lg bg-light fixed-top flex-wrap" style="z-index: 100;">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <img src="<?= $logo_web ?>" style="height: 45px; filter: brightness(0) saturate(100%) sepia(1) hue-rotate(170deg) saturate(600%) brightness(1.2);" alt="<?= $app_settings['nama_aplikasi'] ?>">
        </a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav w-100 gap-3">
                <?php
                $kategori = model('Kategori')->findAll();
                foreach ($kategori as $key => $v) :
                    $active = '';
                    if ($get_kategori == '' && $key === array_key_first($kategori)) {
                        $active = 'nav-active';
                    } elseif ($get_kategori == $v['slug']) {
                        $active = 'nav-active';
                    }
                ?>
                <a class="nav-link <?= $active ?> fw-500 d-flex align-items-center" href="<?= base_url() ?>koleksi?kategori=<?= $v['slug'] ?><?= $get_config ?>">
                    <h5 class="mb-0"><?= $v['nama'] ?></h5>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ms-lg-auto mt-3 mt-lg-0">
            <a class="fw-600 me-3 text-dark" data-bs-toggle="modal" data-bs-target="#lacakPesanan">
                LACAK PESANAN
            </a>
            <a href="<?= base_url() ?>keranjang" class="position-relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                    <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                </svg>
                <span class="position-absolute top-0 start-100 translate-middle badge bg-dark">
                    <?= session('keranjang') ? count(json_decode(session('keranjang'))) : 0 ?>
                </span>
            </a>
        </div>
    </div>
    <div class="container py-3">
        <div class="d-flex flex-wrap gap-4">
            <?php
            $sub_kategori = model('SubKategori')->where('slug_kategori', $get_kategori)->findAll();
            if (! $sub_kategori) {
                $kategori = model('Kategori')->first();
                $sub_kategori = model('SubKategori')->where('id_kategori', $kategori['id'])->findAll();
            }

            foreach ($sub_kategori as $key => $v) :
                $active_sub = '';
                if ($get_sub == $v['slug']) {
                    $active_sub = 'nav-active';
                }

                $sub_sub_kategori = model('SubSubKategori')->where('id_sub_kategori', $v['id'])->findAll();
                if ($sub_sub_kategori) :
            ?>
            <div class="dropdown">
                <a href="#" class="nav-link <?= $active_sub ?>" data-bs-toggle="dropdown">
                    <?= $v['nama'] ?>
                    <i class="fa-solid fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($sub_sub_kategori as $v2) :
                        $active_sub_sub = '';
                        if ($get_sub_sub == $v2['slug']) {
                            $active_sub_sub = 'nav-active';
                        }
                    ?>
                    <li>
                        <a class="dropdown-item text-nowrap <?= $active_sub_sub ?>" href="<?= base_url() ?>koleksi?kategori=<?= $v['slug_kategori'] ?>&sub=<?= $v['slug'] ?>&sub_sub=<?= $v2['slug'] ?><?= $get_config ?>"><?= $v2['nama'] ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php else : ?>
            <a class="nav-link <?= $active_sub ?>" href="<?= base_url() ?>koleksi?kategori=<?= $v['slug_kategori'] ?>&sub=<?= $v['slug'] ?><?= $get_config ?>"><?= $v['nama'] ?></a>
            <?php endif ?>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<div class="modal fade" id="lacakPesanan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Lacak Pesanan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url() ?>detail-transaksi" method="get">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Invoice</label>
                        <input type="text" class="form-control" id="kode" name="kode" placeholder="Masukkan kode invoice" required oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Cari Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>