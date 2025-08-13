<?php
$get_order_by = $_GET['order_by'] ?? '';


$array_id_produk = $array_id_produk ? json_decode($array_id_produk, true) : [];

if ($array_id_produk) {
    $data = model('Produk')->whereIn('id', $array_id_produk)->findAll();
    $total_produk  = model('Produk')->whereIn('id', $array_id_produk)->countAllResults();
} else {
    $total_produk = 0;
}
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

<body style="padding-top: 114.38px;">

<section class="container">
    <div class="row">
        <div class="col-12 text-center">
            <h5>
                <?= $title ?>
            </h5>
            <small><?= $total_produk ?> produk</small>
        </div>
    </div>
    <div class="row mt-0 gx-2 gx-md-4 gy-5">
        <?php
        if ($array_id_produk) :
            foreach ($data as $v) :
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
            <a href="<?= base_url() ?>detail-produk/<?= $v['slug'] ?>?kategori=<?= $kategori['slug'] ?>">
                <img data-src="<?= webFile('image', 'produk', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center lazy-shimmer" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
                <p class="mt-3 mb-1 text-dark"><?= $v['nama'] ?></p>
                <p class="mb-0 fw-500"><?= formatRupiah($harga_varian_termurah) ?></p>
            </a>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<script>
dselect(dom('#order_by'), { search: true, clearable: true });
</script>
