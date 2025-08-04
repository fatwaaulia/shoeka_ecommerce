<body style="padding-top: 121.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <h1><?= $data['nama'] ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <form id="form">
                <div class="mb-3">
                    <label for="qty" class="form-label">Qty</label>
                    <input type="text" class="form-control" id="qty" name="qty" value="1" placeholder="Masukkan qty" required>
                </div>
                <button type="submit" class="btn btn-primary">Masukkan Keranjang</button>
            </form>
        </div>
    </div>
</section>

<script>
dom('#form').addEventListener('submit', async function(event) {
    event.preventDefault();

    try {
        const id_varian_produk = '<?= $data['id'] ?>';
        const qty = dom('#qty').value;

        const response = await fetch('<?= base_url() ?>keranjang', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipe: 'create',
                id_varian_produk: id_varian_produk,
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
