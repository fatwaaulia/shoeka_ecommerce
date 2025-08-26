<?php
$get_nama_produk = $_GET['nama_produk'] ?? '';
$array_id_produk = $array_id_produk ? json_decode($array_id_produk, true) : [];

if ($array_id_produk) {
    $produk = model('Produk')->whereIn('id', $array_id_produk)->findAll();

    foreach ($produk as $key => $v) {
        $varian_produk = model('VarianProduk')
        ->baseQuery()
        ->where([
            'id_produk' => $v['id'],
            'berat !=' => 0,
            'stok !=' => 0,
        ])
        ->get()->getResultArray();

        if (empty($varian_produk)) {
            unset($produk[$key]);
            continue;
        }

        $harga_varian = array_column($varian_produk, 'harga_ecommerce');
        sort($harga_varian);
        $harga_varian_termurah = $harga_varian[0] ?? 0;

        $produk[$key]['harga_varian_termurah'] = $harga_varian_termurah;
    }
    $produk = array_values($produk);

    $total_produk  = count($produk);
} else {
    $total_produk = 0;
}
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

<body style="padding-top: 114.38px;">

<section class="container">
    <div class="row py-2">
        <div class="col-12 text-center">
            <h5>
                <?= $title ?>
            </h5>
            <small><?= $total_produk ?> produk ditemukan</small>
        </div>
    </div>
    <div class="row py-3">
        <div class="col-12 col-md-10 offset-md-2 col-xl-6 offset-xl-3 text-center">
            <form action="<?= base_url() ?>pencarian" method="get">
                <input type="search" class="form-control" name="nama_produk" value="<?= $get_nama_produk ?>" placeholder="Masukkan nama produk" required>
            </form>
        </div>
    </div>
    <div class="row mt-0 gx-2 gx-md-4 gy-4">
        <?php
        if ($array_id_produk) :
            foreach ($produk as $v) :
        ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= base_url() ?>detail-produk/<?= $v['slug'] ?>">
                <img data-src="<?= webFile('image', 'produk', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center lazy-shimmer" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
                <p class="mt-3 mb-1 text-dark fw-600"><?= $v['nama'] ?></p>
                <p class="mb-0 fw-500"><?= formatRupiah($v['harga_varian_termurah']) ?></p>
            </a>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<section class="container">
    <div class="row">
        <div class="col-12 mt-4 mb-3">
            <h4>Produk Rekomendasi Lainnya</h4>
        </div>
    </div>
    <div class="row gx-2 gx-md-4 gy-5">
        <?php
        $sub_json_id_produk = model('SubKategori')->where('json_id_produk !=', '')->findColumn('json_id_produk');
        $sub_sub_json_id_produk = model('SubSubKategori')->where('json_id_produk !=', '')->findColumn('json_id_produk');
        $json_id_produk = array_merge($sub_json_id_produk, $sub_sub_json_id_produk);

       $produk = [];
        foreach ($json_id_produk as $v) {
            if (!empty($v)) {
                $ids = json_decode($v, true);
                if (is_array($ids)) {
                    $produk = array_merge($produk, $ids);
                }
            }
        }

        // Hapus duplikat
        $produk = array_unique($produk);
        shuffle($produk);
        $produk = array_slice($produk, 0, 8);
        $rekomendasi_produk = model('Produk')->whereIn('id', $produk)->findAll();
        if ($rekomendasi_produk) :
            foreach ($rekomendasi_produk as $v) :
                
                $varian_produk = model('VarianProduk')
                ->baseQuery()
                ->where([
                    'id_produk' => $v['id'],
                    'berat !=' => 0,
                    'stok !=' => 0,
                ])
                ->get()->getResultArray();
                $harga_varian = array_column($varian_produk, 'harga_ecommerce');
                sort($harga_varian);
                $harga_varian_termurah = $harga_varian[0] ?? 0;
                if ($harga_varian_termurah == 0) continue;

        ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= base_url() ?>detail-produk/<?= $v['slug'] ?>?kategori=<?= $_GET['kategori'] ?? '' ?>">
                <img data-src="<?= webFile('image', 'produk', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center lazy-shimmer" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
                <p class="mt-3 mb-1 text-dark"><?= $v['nama'] ?></p>
                <p class="mb-0 fw-500"><?= formatRupiah($harga_varian_termurah) ?></p>
            </a>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>