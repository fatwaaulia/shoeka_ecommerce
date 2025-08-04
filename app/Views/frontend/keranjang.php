<body style="padding-top: 121.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <h1>Keranjang</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>Gambar</td>
                    <td>Produk</td>
                    <td class="text-end">Harga</td>
                    <td class="text-end">Qty</td>
                    <td class="text-end">Total</td>
                </tr>
                <?php
                $keranjang_session = json_decode(session('keranjang'), true) ?? [];
                $array_id_varian_produk = array_column($keranjang_session, 'id_varian_produk');

                if ($array_id_varian_produk) :

                $varian_produk = model('VarianProduk')->whereIn('id', $array_id_varian_produk)->findAll();

                $total_belanja = 0;
                foreach ($varian_produk as $v) :
                    $total_harga_jual = 0;
                    foreach ($keranjang_session as $v2) {
                        if ($v2['id_varian_produk'] === $v['id']) {
                            $qty = (int)$v2['qty'];
                            break;
                        }
                    }

                    $total_harga_jual += $v['harga_jual'] * $qty;
                    $total_belanja += $total_harga_jual;
                ?>
                <tr>
                    <td style="width: 100px;">
                        <img src="<?= webFile('image', 'varian_produk', $v['gambar'], $v['updated_at']) ?>" class="wh-100 cover-center me-3" alt="<?= $v['nama'] ?>">
                    </td>
                    <td class="text-wrap">
                        <?= $v['nama'] ?>
                    </td>
                    <td class="text-end"><?= formatRupiah($v['harga_jual']) ?></td>
                    <td class="text-end">
                        <?= $qty ?>
                        <br>
                        <div class="text-danger" style="cursor: pointer;" onclick="deleteItem('<?= $v['id'] ?>')">
                            Hapus
                            <i class="fa-solid fa-trash-can mt-4"></i>
                        </div>
                    </td>
                    <td class="text-end"><?= formatRupiah($total_harga_jual) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="text-end">Total Belanja</td>
                    <td class="text-end"><?= formatRupiah($total_belanja) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <?php if ($keranjang_session) : ?>
    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary me-2">
                Chat Admin
            </button>
            <form id="form">
                <input type="hidden" name="tanggal" value="123">
                <button type="submit" class="btn btn-primary">
                    Bayar Pakai VA
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
async function deleteItem(id) {
    try {
        const result = await Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Hapus',
            confirmButtonText: 'Iya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
        });
        
        if (result.isConfirmed) {
            const response = await fetch('<?= base_url() ?>keranjang', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    tipe: 'delete',
                    id_varian_produk: id,
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
}

const form = document.getElementById('form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= base_url() ?>api/invoice/create';
    submitDataWithConfirm(form, endpoint, confirm_title = 'Proses Transaksi');
});
</script>
