<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

<body style="padding-top: 111.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url() ?>">Home</a>
            <span> > </span>
            <span class="fw-500">Keranjang</span>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td>Gambar</td>
                        <td>Produk</td>
                        <td class="text-end">Harga</td>
                        <td class="text-center">Qty</td>
                        <td class="text-end">Total</td>
                    </tr>
                    <?php
                    $keranjang_session = json_decode(session('keranjang'), true) ?? [];
                    $array_id_varian_produk = array_column($keranjang_session, 'id_varian_produk');
                    $total_belanja = 0;
                    if ($array_id_varian_produk) :
                        $varian_produk = model('VarianProduk')->whereIn('id', $array_id_varian_produk)->findAll();

                        foreach ($varian_produk as $key => $v) :
                            $total_harga_ecommerce = 0;
                            foreach ($keranjang_session as $v2) {
                                if ($v2['id_varian_produk'] === $v['id']) {
                                    $qty = (int)$v2['qty'];
                                    break;
                                }
                            }

                            $total_harga_ecommerce += $v['harga_ecommerce'] * $qty;
                            $total_belanja += $total_harga_ecommerce;
                    ?>
                    <tr>
                        <td style="width: 100px;">
                            <img src="<?= webFile('image', 'varian_produk', $v['gambar'], $v['updated_at']) ?>" class="wh-100 cover-center me-3" alt="<?= $v['nama'] ?>">
                        </td>
                        <td class="text-wrap">
                            <?= $v['nama'] ?>
                        </td>
                        <td class="text-end"><?= formatRupiah($v['harga_ecommerce']) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-light" data-id="<?= $v['id'] ?>" onclick="kurang_qty(this, dom('#qty_<?= $key ?>'), <?= $v['harga_ecommerce'] ?>, dom('#total_belanja_item_<?= $key ?>'))">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" id="qty_<?= $key ?>" name="qty" value="<?= $qty ?>" min="1" style="width: 100px;" placeholder="qty" required autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                <button type="button" class="btn btn-light" data-id="<?= $v['id'] ?>" onclick="tambah_qty(this, dom('#qty_<?= $key ?>'), <?= $v['harga_ecommerce'] ?>, dom('#total_belanja_item_<?= $key ?>'))">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <br>
                            <a href="#" class="text-danger" onclick="deleteItem('<?= $v['id'] ?>')">
                                Hapus
                                <i class="fa-solid fa-trash-can"></i>
                            </div>
                        </td>
                        <td class="text-end" id="total_belanja_item_<?= $key ?>"><?= formatRupiah($total_harga_ecommerce) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end fw-600">Total Belanja</td>
                        <td class="text-end fw-600" id="total_belanja"><?= formatRupiah($total_belanja) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 text-end">
            <p class="text-secondary">Ongkos kirim dihitung saat checkout.</p>
            <a href="<?= base_url() ?>checkout" class="btn btn-primary">
                Checkout
            </a>
        </div>
    </div>
</section>

<script>
function kurang_qty(el_kurang, el_qty, harga_ecommerce, el_total_harga_item) {
    let qty = parseInt(el_qty.value) || 0;
    if (qty > 1) {
        qty = qty - 1;
        el_qty.value = qty;
        const total = harga_ecommerce * qty;
        el_total_harga_item.innerHTML = formatRupiah(total);
    }

    const id = el_kurang.getAttribute('data-id');
    updateItem(id, 'decrement');
}

function tambah_qty(el_tambah, el_qty, harga_ecommerce, el_total_harga_item) {
    let qty = parseInt(el_qty.value) || 0;
    qty = qty + 1;
    el_qty.value = qty;
    const total = harga_ecommerce * qty;
    el_total_harga_item.innerHTML = formatRupiah(total);

    const id = el_tambah.getAttribute('data-id');
    updateItem(id, 'increment');
}

async function updateItem(id, tipe) {
    try {
        const response = await fetch(`<?= base_url() ?>session/keranjang/update/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipe: tipe
            })
        });
        const data = await response.json();

        // console.log(data);
        // return;
        
        dom('#total_belanja').innerHTML = formatRupiah(data.total_belanja);
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
            const response = await fetch(`<?= base_url() ?>session/keranjang/delete/${id}`, {
                method: 'POST'
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
</script>
