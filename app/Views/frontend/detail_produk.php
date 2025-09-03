<?php
$varian_produk = model('VarianProduk')
->baseQuery()
->where([
    'id_produk' => $data['id'],
    'berat !=' => 0,
    'stok !=' => 0,
])
->get()->getResultArray();
sort($varian_produk);
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/modules/swiperjs/swiper.css">
<style>
.swiper-button-prev, .swiper-button-next {
    background-color: rgba(0, 0, 0, 0.5)!important;
    width: 50px!important;
    height: 50px!important;
    border-radius: 50%!important;
    pointer-events: auto!important;
}
.swiper-button-prev::after, .swiper-button-next::after {
    font-size: 20px!important;
    color: white!important;
}
.swiper-pagination-bullet-active {
    background: var(--main-color);
}
</style>

<section class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url() ?>">Home</a>
            <span> > </span>
            <span>Detail Produk</span>
            <span> > </span>
            <span class="fw-500"><?= $data['nama'] ?></span>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-md-1 col-lg-1 order-2 order-md-1">
            <style>
            .swipper-thumbs { width: 100%; }
            @media (max-width: 768px) {
                .swipper-thumbs {
                    width: 50px;
                    height: 50px;
                }
            }
            </style>
            <div class="overflow-auto scrollbar-hidden d-flex flex-nowrap gap-2 d-md-block" style="max-height: 75vh;">
                <img src="<?= webFile('image', 'produk', $data['gambar'], $data['updated_at']) ?>" class="mb-2 swipper-thumbs" style="cursor: pointer;">
                <?php
                $json_gambar_ecommerce = json_decode($data['json_gambar_ecommerce'], true);
                if ($json_gambar_ecommerce) :
                foreach ($json_gambar_ecommerce as $v) :
                ?>
                <img data-src="<?= webFile('image', 'produk', $v, $data['updated_at']) ?>" class="lazy-shimmer mb-2 swipper-thumbs" style="cursor: pointer;">
                <?php endforeach; endif; ?>
                <?php foreach ($varian_produk as $v) : ?>
                <img data-src="<?= webFile('image', 'varian_produk', $v['gambar'], $v['updated_at']) ?>" class="lazy-shimmer mb-2 swipper-thumbs" style="cursor: pointer;">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5 order-1 order-md-2">
            <div class="swiper swiper-produk">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="<?= webFile('image', 'produk', $data['gambar'], $data['updated_at']) ?>" class="mb-2 w-100" id="sub_gambar" style="cursor: pointer;">
                    </div>
                    <?php
                    $json_gambar_ecommerce = json_decode($data['json_gambar_ecommerce'], true);
                    if ($json_gambar_ecommerce) :
                    foreach ($json_gambar_ecommerce as $v) :
                    ?>
                    <div class="swiper-slide">
                        <img src="<?= webFile('image', 'produk', $v, $data['updated_at']) ?>" class="mb-3 w-100" alt="<?= $data['nama'] ?>">
                    </div>
                    <?php endforeach; endif; ?>
                    <?php foreach ($varian_produk as $v) : ?>
                    <div class="swiper-slide">
                        <img src="<?= webFile('image', 'varian_produk', $v['gambar'], $v['updated_at']) ?>" class="mb-3 w-100" alt="<?= $v['nama'] ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div> -->
                <div class="swiper-pagination d-flex" style="left: 16px; bottom: 24px;"></div>
            </div>
        </div>
        <div class="col-12 col-md-5 col-lg-6 order-3">
            <h4 class="mt-4 mt-md-0 fw-600"><?= $data['nama'] ?></h4>
            <div class="d-md-flex align-items-center">
                <h4 class="me-3 fw-600 mb-0 text-primary" id="harga_varian">
                    <?= formatRupiah($varian_produk['0']['harga_ecommerce']) ?>
                </h4>
                <div id="harga_coret" class="mt-2 mt-md-0">
                    <?php
                    if ($varian_produk['0']['harga_ecommerce'] < $varian_produk['0']['harga_ecommerce_coret']) :
                        $persentase = (($varian_produk['0']['harga_ecommerce_coret'] - $varian_produk['0']['harga_ecommerce']) / $varian_produk['0']['harga_ecommerce_coret']) * 100;
                    ?>
                    <small class="me-2">
                        <s class="text-secondary">
                            <?= formatRupiah($varian_produk['0']['harga_ecommerce_coret']) ?>
                        </s>
                    </small>
                    <div class="badge text-success bg-success-subtle" style="border-radius: 6px!important;">
                        <?= 0 - round($persentase, 2) ?>%
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr style="border: 1px solid #ddd;">

            <form id="form">
                <input type="hidden" name="route" value="<?= current_url(true) ?>">
                <div class="mb-3">
                    <label class="mb-2">Varian</label> <br>
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        $json_gambar_ecommerce = json_decode($data['json_gambar_ecommerce'], true);
                        $total_gambar_ecommerce = is_array($json_gambar_ecommerce) ? count($json_gambar_ecommerce) : 1;
                        foreach ($varian_produk as $key => $v) :
                            $index_thumbs = ($total_gambar_ecommerce) + ($key+1);
                        ?>
                        <span>
                            <input type="radio" class="btn-check" id="checked_<?= $key ?>" name="varian_produk" value="<?= encode($v['id']) ?>" onclick="pilihVarian(this, <?= $index_thumbs ?>)" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="checked_<?= $key ?>"><?= $v['nama'] ?></label>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="invalid-feedback" id="invalid_varian_produk"></div>
                </div>
                <div class="mb-3">
                    <label for="qty" class="form-label">Kuantitas</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" id="kurang_qty">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <input type="number" class="form-control text-center" id="qty" name="qty" value="1" min="1" style="width: 100px;" placeholder="qty" required autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                        <button type="button" class="btn btn-light" id="tambah_qty">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="invalid_qty"></div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Masukkan Keranjang</button>
            </form>

            <hr style="border: 1px solid #ddd;">
            <p>Deskripsi :</p>
            <p class="text-secondary"><?= $data['deskripsi'] ?></p>
        </div>
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
            foreach ($rekomendasi_produk as $key => $v) :
                $varian_produk = model('VarianProduk')
                ->baseQuery()
                ->where([
                    'id_produk' => $v['id'],
                    'berat !=' => 0,
                    'stok !=' => 0,
                ])
                ->get()->getResultArray();

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

                if ($harga_varian_termurah == 0) continue;

        ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= base_url() ?>detail-produk/<?= $v['slug'] ?>?kategori=<?= $_GET['kategori'] ?? '' ?>">
                <img data-src="<?= webFile('image', 'produk', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center lazy-shimmer" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
                <p class="mt-3 mb-1 text-dark fw-600"><?= $v['nama'] ?></p>
                  <div class="mb-0 fw-500 d-md-flex justify-content-between">
                    <div>
                        <?= formatRupiah($harga_varian_termurah) ?>
                    </div>
                    <?php
                    if ($harga_varian_termurah < $harga_coret_varian_termurah) :
                        $persentase = (($harga_coret_varian_termurah - $harga_varian_termurah) / $harga_coret_varian_termurah) * 100;
                    ?>
                    <div>
                        <small class="me-2">
                            <s class="text-secondary">
                                <?= formatRupiah($harga_coret_varian_termurah) ?>
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

<script src="<?= base_url() ?>assets/modules/swiperjs/swiper.js"></script>
<script>
const swiper_preview = new Swiper('.swiper-produk', {
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    spaceBetween: 24,
    slidesPerView: 1,
    loop: true,
    breakpoints: {
        576: { slidesPerView: 1 },
        768: { slidesPerView: 1 },
        992: { slidesPerView: 1 },
    },
});

document.querySelectorAll(".swipper-thumbs").forEach((el, index) => {
    el.addEventListener("click", () => {
        swiper_preview.slideTo(index);
    });
});
</script>

<script>
async function pilihVarian(el, index_thumbs) {
    const response = await fetch(`<?= base_url() ?>api/varian-produk/${el.value}`);
    const data = await response.json();

    dom('#harga_varian').innerText = formatRupiah(data.data.harga_ecommerce);
    dom('#harga_coret').innerHTML = (data.data.persentase != 0) ? `
    <small class="me-2">
        <s class="text-secondary">
            ${formatRupiah(data.data.harga_ecommerce_coret)}
        </s>
    </small>
    <div class="badge text-success bg-success-subtle" style="border-radius: 6px!important;">
        ${data.data.persentase}
    </div>` : '';
    swiper_preview.slideTo(index_thumbs);
}

dom('#kurang_qty').addEventListener('click', () => {
    const qty = parseInt(dom('#qty').value) || 0;
    if (qty > 1) {
        dom('#qty').value = qty - 1;
    }
});

dom('#tambah_qty').addEventListener('click', () => {
    const qty = parseInt(dom('#qty').value) || 0;
    dom('#qty').value = qty + 1;
});

dom('#form').addEventListener('submit', async function(event) {
    event.preventDefault();

    try {
        const form_data = new FormData(form);
        const response = await fetch('<?= base_url() ?>session/keranjang/create', {
            method: 'POST',
            body: form_data,
        });
        const data = await response.json();

        Array.from(form.querySelectorAll('[id^="invalid_"]')).forEach(element => {
            const field = element.id.replace('invalid_', '');
            const element_by_name = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${field}[]"]`);
            element.textContent = data.errors?.[field] || '';
            if (element_by_name && !['radio', 'checkbox'].includes(element_by_name.type)) {
                element_by_name.classList.toggle('is-invalid', !!data.errors?.[field]);
                element_by_name.nextElementSibling?.querySelector('.dselect-wrapper > .form-select')?.classList.toggle('is-invalid', !!data.errors?.[field]);
            }
        });

        if (['success', 'error'].includes(data.status)) {
            await Swal.fire({
                icon: data.status,
                title: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
            data.route && (window.location.href = data.route);
        } else {
            await Swal.fire({
                icon: 'error',
                title: data.message,
                showConfirmButton: false,
            });
        }
    } catch (error) {
        console.error(error);
        await Swal.fire({
            icon: 'error',
            title: 'Oops! Terjadi kesalahan',
            text: 'Silakan coba lagi nanti.',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    }
});
</script>
