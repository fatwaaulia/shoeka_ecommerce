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
            #sub_gambar { width: 100%; }
            @media (max-width: 768px) {
                #sub_gambar {
                    width: 50px;
                    height: 50px;
                }
            }
            </style>
            <div class="overflow-auto scrollbar-hidden d-flex flex-nowrap gap-2 d-md-block">
                
            <img src="<?= webFile('image', 'produk', $data['gambar'], $data['updated_at']) ?>" class="mb-2" id="sub_gambar" style="cursor: pointer;" onclick="previewGambar('<?= webFile('image', 'produk', $data['gambar'], $data['updated_at']) ?>')">
            <?php
            $json_gambar_ecommerce = json_decode($data['json_gambar_ecommerce'], true);
            if ($json_gambar_ecommerce) :
            foreach ($json_gambar_ecommerce as $v) :
            ?>
            <img src="<?= webFile('image', 'produk', $v, $data['updated_at']) ?>" class="mb-2" id="sub_gambar" style="cursor: pointer;" onclick="previewGambar('<?= webFile('image', 'produk', $v, $data['updated_at']) ?>')">
            <?php endforeach; endif; ?>
            </div>

            <script>
            function previewGambar(src) {
                if (src == dom('#sampul').src) return;
                const img = dom('#sampul');
                img.style.transition = 'opacity 0.3s';
                img.style.opacity = 0.5;
                setTimeout(() => {
                    dom('#sampul').src = src;
                    dom('#sampul').onload = () => {
                        dom('#sampul').style.opacity = 1;
                    };
                }, 200);
            }
            </script>
        </div>
        <div class="col-12 col-md-6 col-lg-5 order-1 order-md-2">
            <img src="<?= webFile('image', 'produk', $data['gambar'], $data['updated_at']) ?>" class="w-100 mb-3" id="sampul" alt="<?= $data['nama'] ?>">
        </div>
        <div class="col-12 col-md-5 col-lg-6 order-3">
            <h4 class="mt-4 mt-md-0"><?= $data['nama'] ?></h4>
            <h5><?= formatRupiah($varian_produk['0']['harga_ecommerce']) ?></h5>

            <hr style="border: 1px solid #ddd;">

            <form id="form">
                <div class="mb-3">
                    <label class="mb-2">Varian</label> <br>
                    <?php foreach ($varian_produk as $key => $v) : ?>
                    <span class="me-2">
                        <input type="radio" class="btn-check" id="checked_<?= $key ?>" name="varian_produk" value="<?= encode($v['id']) ?>" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="checked_<?= $key ?>"><?= $v['nama'] ?></label>
                    </span>
                    <?php endforeach; ?>
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

        // Hapus duplikat jika perlu
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
            <a href="<?= base_url() ?>detail-produk/<?= $v['slug'] ?>">
                <img data-src="<?= webFile('image', 'produk', $v['gambar'], $v['updated_at']) ?>" class="w-100 cover-center lazy-shimmer" style="aspect-ratio: 1 / 1;" alt="<?= $v['nama'] ?>">
                <p class="mt-3 mb-1 text-dark"><?= $v['nama'] ?></p>
                <p class="mb-0 fw-500"><?= formatRupiah($harga_varian_termurah) ?></p>
            </a>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<script>
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
        const id_varian_produk = '<?= encode($data['id']) ?>';
        const qty = dom('#qty').value;

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
