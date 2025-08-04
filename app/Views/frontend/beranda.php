<?php
$get_order_by = $_GET['order_by'] ?? '';
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

<body style="padding-top: 121.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <h5 class="text-center">
                <?= $kategori['nama'] ?>
                <?= $sub_kategori ? ' - ' . $sub_kategori['nama'] : '' ?>
            </h5>
        </div>
    </div>
    <form action="" method="get">
        <div class="row gx-2 gy-3">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <label for="order_by" class="form-label">Urutkan</label>
                <select id="order_by" name="order_by">
                    <option value="">Pilih</option>
                    <?php
                    $order_by = ['Harga Termurah', 'Harga Tertinggi'];
                    foreach ($order_by as $v) :
                        $selected = ($v == $get_order_by) ? 'selected' : '';
                    ?>
                    <option value="<?= $v ?>" <?= $selected ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 col-lg-2 col-xl-1 d-flex justify-content-start align-items-end">
                <button type="submit" class="btn btn-primary me-2 w-100" title="Cari">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="<?= current_url() ?>" class="btn btn-secondary w-100" title="Reset">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </div>
    </form>
    <div class="row mt-5">
        <div class="col-12">
            <?php
            $ids = $array_id_varian_produk ? json_decode($array_id_varian_produk) : [];

            if ($ids) :
            $varian_produk = model('VarianProduk')->whereIn('id', $ids)->findAll();
            foreach ($varian_produk as $v) :
            ?>
            <a href="<?= base_url() ?>detail-produk/<?= $v['id'] ?>"><?= $v['nama']; ?></a> <br>
            <?php endforeach; endif; ?>

        </div>
    </div>
</section>

<script>
dselect(dom('#order_by'), { search: true, clearable: true });
</script>
