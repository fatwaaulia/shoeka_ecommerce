<style>
.banner { height: 40vh; }

@media (min-width: 768px) {
    .banner { height: 50vh; }
}

@media (min-width: 1200px) {
    .banner { height: calc(100vh - 114.38px) }
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">
            <div id="carouselExample" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $banner = model('Banner')->orderBy('urutan ASC')->findAll();
                    foreach ($banner as $key => $v) :
                    ?>
                    <div class="carousel-item <?= $key == 0 ? 'active' : '' ?>" data-bs-interval="5000">
                        <a <?= $v['tautan'] ? 'href="' . $v['tautan'] . '" target="_blank"' : '' ?>>
                            <img src="<?= webFile('image', 'banner', $v['gambar'], $v['updated_at']) ?>" class="d-block w-100 rounded-0 cover-center banner" alt="<?= $v['judul'] ?>">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
</div>

<section class="container">
    <div class="row">
        <div class="col-12 mt-4 mb-3">
            <h4>Kategori Populer</h4>
        </div>
    </div>
    <div class="row gx-2 gx-md-4 gy-3">
        <?php
        $kategori_populer = model('SubKategori')->where('nama_kategori', 'KATEGORI POPULER')->findAll();
        foreach ($kategori_populer as $key => $v) :
        ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= base_url() ?>koleksi?kategori=<?= $v['slug_kategori'] ?>&sub=<?= $v['slug'] ?>">
                <img src="<?= webFile('image', 'sub_kategori', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center" style="aspect-ratio: 1 / 1.2;" alt="<?= $v['nama'] ?>">
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container">
    <div class="row">
        <div class="col-12 mt-4 mb-3">
            <h4>Koleksi Spesial</h4>
        </div>
    </div>
    <div class="row gx-2 gx-md-4 gy-3">
        <?php
        $kategori_populer = model('SubKategori')->where('nama_kategori', 'Koleksi Spesial')->findAll();
        foreach ($kategori_populer as $key => $v) :
        ?>
        <div class="col-6 col-md-4 col-xl-4">
            <a href="<?= base_url() ?>koleksi?kategori=<?= $v['slug_kategori'] ?>&sub=<?= $v['slug'] ?>">
                <img src="<?= webFile('image', 'sub_kategori', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php
$no_hp_admin = model('Users')->select('no_hp')->find(1)['no_hp'];
?>
<section class="container mt-5 cover-center" style="background-image: linear-gradient(rgba(0, 0, 0, .2), rgba(0, 0, 0, .2)), url('<?= base_url('assets/img/bg-login.jpg') ?>')">
    <div class="row">
        <div class="col-12 col-md-6 offset-md-3 col-xl-4 offset-xl-4">
            <div class="card py-4 my-4" style="background-color: rgba(255, 255, 255, 0.6); border-radius: 6px!important;">
                <div class="card-body text-center">
                    <h3 class="fw-600">Pembelian Jumlah Besar</h3>
                    <p>Tersedia layanan pembelian jumlah besar untuk berbagai keperluan!</p>
                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', $no_hp_admin) ?>" target="_blank" class="btn btn-light mt-3" style="border-radius: 6px!important;">PESAN SEKARANG</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mt-5">
    <div class="row">
        <div class="col-12 col-md-6 col-xl-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-percent" viewBox="0 0 16 16">
                <path d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0M4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5m7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
            </svg>
            <h5 class="fw-600 mt-3">Harga Khusus</h5>
            <p class="text-secondary">Dapatkan harga khusus dengan pembelian jumlah besar.</p>
        </div>
        <div class="col-12 col-md-6 col-xl-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-bag-plus" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 7.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V12a.5.5 0 0 1-1 0v-1.5H6a.5.5 0 0 1 0-1h1.5V8a.5.5 0 0 1 .5-.5"/>
                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
            </svg>
            <h5 class="fw-600 mt-3">Kustomisasi Produk</h5>
            <p class="text-secondary">Desain produk yang unik dan sesuai kebutuhan Anda.</p>
        </div>
        <div class="col-12 col-md-6 col-xl-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
            </svg>
            <h5 class="fw-600 mt-3">VA atau Admin</h5>
            <p class="text-secondary">Opsi pembayaran pakai VA otomatis atau chat admin.</p>
        </div>
        <div class="col-12 col-md-6 col-xl-3 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
            </svg>
            <h5 class="fw-600 mt-3">Pelacakan Resi</h5>
            <p class="text-secondary">Tersedia detail informasi pengiriman barang Anda.</p>
        </div>
    </div>
</section>