<body style="padding-top: 121.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url() ?>">Home</a>
            <span> > </span>
            <span class="fw-500">Detail Produk</span>
            <span> > </span>
            <span class="fw-500"><?= $data['nama'] ?></span>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-md-6 col-lg-4">
            <img src="<?= webFile('image', 'varian_produk', $data['gambar'], $data['updated_at']) ?>" class="w-100" alt="<?= $data['nama'] ?>">
        </div>
        <div class="col-12 col-6 col-lg-8">
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
                        <input type="number" class="form-control text-center" id="qty" name="qty" value="1" min="1" style="width: 100px;" placeholder="qty" required autocomplete="off">
                        <button type="button" class="btn btn-light" id="tambah_qty">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">MASUKKAN KERANJANG</button>
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
    dom('#qty').value = qty - 1;
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

        const response = await fetch('<?= base_url() ?>keranjang', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipe: 'create',
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
