<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

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
                    <td colspan="4" class="text-end">Total Belanja</td>
                    <td class="text-end" id="total_belanja"><?= formatRupiah($total_belanja) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <?php if ($keranjang_session) : ?>
    <div class="row mt-4">
        <div class="col-12 col-lg-6">
            <h4 class="mb-3">Dikirim Dari</h4>
            <p>Sawojajar, Kec. Kedungkandang, Kota Malang</p>
        </div>
        <div class="col-12 col-lg-6">
            <h4 class="mb-3">Lengkapi Data Penerima</h4>
            <form id="form">
                <input type="hidden" id="nama_provinsi" name="nama_provinsi">
                <input type="hidden" id="nama_kabupaten" name="nama_kabupaten">
                <input type="hidden" id="nama_kecamatan" name="nama_kecamatan">
                <input type="hidden" id="nama_desa" name="nama_desa">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap">
                    <div class="invalid-feedback" id="invalid_nama"></div>
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No. HP</label>
                    <input type="number" class="form-control" id="no_hp" name="no_hp" placeholder="08xx">
                    <div class="invalid-feedback" id="invalid_no_hp"></div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@gmail.com">
                    <div class="invalid-feedback" id="invalid_email"></div>
                </div>
                <div class="mb-3">
                    <label for="provinsi" class="form-label">Provinsi</label>
                    <div id="component_provinsi">
                        <a class="text-primary">Cari Provinsi</a>
                    </div>
                    <div class="invalid-feedback" id="invalid_provinsi"></div>
                </div>
                <div class="mb-3">
                    <label for="kabupaten" class="form-label">Kabupaten</label>
                    <div id="component_kabupaten">
                        <input type="text" class="form-control" disabled>
                    </div>
                    <div class="invalid-feedback" id="invalid_kabupaten"></div>
                </div>
                <div class="mb-3">
                    <label for="kecamatan" class="form-label">Kecamatan</label>
                    <div id="component_kecamatan">
                        <input type="text" class="form-control" disabled>
                    </div>
                    <div class="invalid-feedback" id="invalid_kecamatan"></div>
                </div>
                <div class="mb-3">
                    <label for="desa" class="form-label">Desa</label>
                    <div id="component_desa">
                        <input type="text" class="form-control" disabled>
                    </div>
                    <div class="invalid-feedback" id="invalid_desa"></div>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat"></textarea>
                    <div class="invalid-feedback" id="invalid_alamat"></div>
                </div>
                <div class="mb-3">
                    <label for="kurir" class="form-label">Kurir</label>
                    <select class="form-select" id="kurir" name="kurir">
                        <option value="">Pilih</option>
                        <?php
                        $kurir = model('Kurir')->where('status', 'ENABLE')->findAll();
                        foreach ($kurir as $v) :
                        ?>
                        <option value="<?= $v['kode'] ?>"><?= $v['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback" id="invalid_kurir"></div>
                </div>
                <div class="mb-3">
                    <label for="layanan_kurir" class="form-label">Layanan Kurir</label>
                    <div id="component_layanan_kurir">
                        <input type="text" class="form-control" disabled>
                    </div>
                    <div class="invalid-feedback" id="invalid_layanan_kurir"></div>
                </div>
                <div class="text-end mt-5">
                    <button type="submit" name="submit" value="admin" class="btn btn-primary me-2">
                        Chat Admin
                    </button>
                    <button type="submit" name="submit" value="va" class="btn btn-primary">
                        Bayar Pakai VA
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
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

<script>
dselect(dom('#kurir'), { search: true });

dom('#component_provinsi a').addEventListener('click', provinsi);

async function provinsi() {
    dom('#component_provinsi').innerHTML = `<div class="spinner-border text-primary"></div>`;

    try {
        const response = await fetch(`<?= base_url() ?>api/ongkir/wilayah?tipe=provinsi`);
        const data = await response.json();
        
        dom('#component_provinsi').innerHTML =
        `<select id="provinsi" name="provinsi">
        <option value="">Pilih</option>` +
            data.data.map(provinsi =>
            `<option value="${provinsi.id}">${provinsi.name}</option>`
            ).join('') +
        `</select>`;

        dselect(dom('#provinsi'), { search: true });
        dom('#provinsi').addEventListener('change', function () {
            dom('#nama_provinsi').value = dom('#provinsi').options[dom('#provinsi').selectedIndex].text;
            kabupaten(this.value);
            dom('#component_kecamatan').innerHTML = `<input type="text" class="form-control" disabled>`;
        });
    } catch (error) {
        console.error(error);
    }
}

async function kabupaten(id_provinsi) {
    dom('#component_kabupaten').innerHTML = `<div class="spinner-border text-primary"></div>`;

    try {
        const response = await fetch(`<?= base_url() ?>api/ongkir/wilayah?tipe=kabupaten&kode=${id_provinsi}`);
        const data = await response.json();

        dom('#component_kabupaten').innerHTML =
        `<select id="kabupaten" name="kabupaten">
            <option value="">Pilih</option>` +
            data.data.map(kabupaten =>
            `<option value="${kabupaten.id}">${kabupaten.name}</option>`
            ).join('') +
        `</select>`;

        dselect(dom('#kabupaten'), { search: true });
        dom('#kabupaten').addEventListener('change', function () {
            dom('#nama_kabupaten').value = dom('#kabupaten').options[dom('#kabupaten').selectedIndex].text;
            kecamatan(this.value);
            dom('#component_desa').innerHTML = `<input type="text" class="form-control" disabled>`;
        });
    } catch (error) {
        console.error(error);
    }
}

async function kecamatan(id_kabupaten) {
    dom('#component_kecamatan').innerHTML = `<div class="spinner-border text-primary"></div>`;

    try {
        const response = await fetch(`<?= base_url() ?>api/ongkir/wilayah?tipe=kecamatan&kode=${id_kabupaten}`);
        const data = await response.json();

        dom('#component_kecamatan').innerHTML =
        `<select id="kecamatan" name="kecamatan">
            <option value="">Pilih</option>` +
            data.data.map(kecamatan =>
            `<option value="${kecamatan.id}">${kecamatan.name}</option>`
            ).join('') +
        `</select>`;

        dselect(dom('#kecamatan'), { search: true });
        dom('#kecamatan').addEventListener('change', function () {
            dom('#nama_kecamatan').value = dom('#kecamatan').options[dom('#kecamatan').selectedIndex].text;
            desa(this.value);
        });
    } catch (error) {
        console.error(error);
    }
}

async function desa(id_kecamatan) {
    dom('#component_desa').innerHTML = `<div class="spinner-border text-primary"></div>`;

    try {
        const response = await fetch(`<?= base_url() ?>api/ongkir/wilayah?tipe=desa&kode=${id_kecamatan}`);
        const data = await response.json();
        // console.log(data);

        dom('#component_desa').innerHTML =
        `<select id="desa" name="desa">
            <option value="">Pilih</option>` +
            data.data.map(desa =>
            `<option value="${desa.id}">${desa.name}</option>`
            ).join('') +
        `</select>`;

        dselect(dom('#desa'), { search: true });
        dom('#desa').addEventListener('change', function () {
            dom('#nama_desa').value = dom('#desa').options[dom('#desa').selectedIndex].text;
        });
    } catch (error) {
        console.error(error);
    }
}

dom('#kurir').addEventListener('change', tarif);

async function tarif() {
    const kode_desa = dom('#desa').value;
    if (! kode_desa) {
        await Swal.fire({
            icon: 'error',
            title: 'Alamat penerima wajib isi!',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
        return;
    }

    dom('#component_layanan_kurir').innerHTML = `<div class="spinner-border text-primary"></div>`;

    try {
        const kurir = dom('#kurir').value;
        const response = await fetch(`<?= base_url() ?>api/ongkir/tarif`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                destination: kode_desa,
                kurir: kurir
            }),
        });
        const data = await response.json();

        if (data.status == 'success') {
            dom('#component_layanan_kurir').innerHTML =
            `<select id="layanan_kurir" name="layanan_kurir">
                <option value="">Pilih</option>` +
                data.data.map((layanan_kurir, index) =>
                `<option value="${index}">${layanan_kurir.service} - ${formatRupiah(layanan_kurir.cost)} - ${layanan_kurir.etd}</option>`
                ).join('') +
            `</select>`;
            dselect(dom('#layanan_kurir'), { search: true });
        } else {
             dom('#component_layanan_kurir').innerHTML = `<input type="text" class="form-control" disabled>`;
              await Swal.fire({
                icon: data.status,
                title: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        }
    } catch (error) {
        console.error(error);
    }
}
</script>

<script>
let tombol = null;
document.querySelectorAll('#form button[type="submit"]').forEach(btn => {
    btn.addEventListener('click', () => tombol = btn);
});

const form = document.getElementById('form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
    if (tombol && tombol.name) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = tombol.name;
        input.value = tombol.value;
        form.appendChild(input);
    }
    const endpoint = '<?= base_url() ?>api/transaksi/create';
    submitDataWithConfirm(form, endpoint, confirm_title = 'Proses Transaksi');
});
</script>
