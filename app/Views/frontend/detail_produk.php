<body style="padding-top: 111.88px;">

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
            <?php
            $json_gambar_ecommerce = json_decode($data['json_gambar_ecommerce'], true);
            if ($json_gambar_ecommerce) :
            foreach ($json_gambar_ecommerce as $v) :
            ?>
            <img src="<?= webFile('image', 'varian_produk', $v, $data['updated_at']) ?>" class="mb-2" id="sub_gambar" style="cursor: pointer;" onclick="previewGambar('<?= webFile('image', 'varian_produk', $v, $data['updated_at']) ?>')">
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
            <img src="<?= webFile('image', 'varian_produk', $data['gambar'], $data['updated_at']) ?>" class="w-100 mb-3" id="sampul" alt="<?= $data['nama'] ?>">
        </div>
        <div class="col-12 col-md-5 col-lg-6 order-3">
            <h4><?= $data['nama'] ?></h4>
            <h5><?= formatRupiah($data['harga_ecommerce']) ?></h5>

            <hr style="border: 1px solid #ddd;">

            <form id="form">
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
                </div>
                <button type="submit" class="btn btn-primary mt-3">Masukkan Keranjang</button>
            </form>

            <hr style="border: 1px solid #ddd;">
            <p>Deskripsi :</p>
            <p class="text-secondary"><?= $data['deskripsi'] ?></p>
        </div>
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
        const slug_varian_produk = '<?= $data['slug'] ?>';
        const qty = dom('#qty').value;

        const response = await fetch('<?= base_url() ?>session/keranjang/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_varian_produk: id_varian_produk,
                slug_varian_produk: slug_varian_produk,
                qty: qty
            })
        });
        const data = await response.json();

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
