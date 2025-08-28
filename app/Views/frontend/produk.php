<?php
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

        $id_varian = array_column($varian_produk, 'id');
        if (!empty($id_varian)) {
            $varian_termurah = model('VarianProduk')
                ->whereIn('id', $id_varian)
                ->orderBy('harga_ecommerce', 'ASC')
                ->first();
        } else {
            $varian_termurah = null;
        }

        $harga_varian_termurah = $varian_termurah['harga_ecommerce'] ?? 0;
        $harga_coret_varian_termurah = $varian_termurah['harga_ecommerce_coret'] ?? 0;

        $produk[$key]['harga_varian_termurah'] = $harga_varian_termurah;
        $produk[$key]['harga_coret_varian_termurah'] = $harga_coret_varian_termurah;
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
            <small><?= $total_produk ?> produk</small>
        </div>
    </div>
    <div class="row mt-0 gx-2 gx-md-4 gy-4">
        <?php
        if ($array_id_produk) :
            foreach ($produk as $v) :
        ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= base_url() ?>detail-produk/<?= $v['slug'] ?>?kategori=<?= $kategori['slug'] ?>">
                <img data-src="<?= webFile('image', 'produk', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center lazy-shimmer" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
                <p class="mt-3 mb-1 text-dark fw-600"><?= $v['nama'] ?></p>
                <div class="mb-0 fw-500 d-md-flex justify-content-between">
                    <div>
                        <?= formatRupiah($v['harga_varian_termurah']) ?>
                    </div>
                    <?php
                    if ($v['harga_varian_termurah'] < $v['harga_coret_varian_termurah']) :
                        $persentase = (($v['harga_coret_varian_termurah'] - $v['harga_varian_termurah']) / $v['harga_coret_varian_termurah']) * 100;
                    ?>
                    <div>
                        <small class="me-2">
                            <s class="text-secondary">
                                <?= formatRupiah($v['harga_coret_varian_termurah']) ?>
                            </s>
                        </small>
                        <div class="badge text-success bg-success-subtle" style="border-radius: 6px!important;">
                            <?= 0 - round($persentase, 2) ?>%
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>
