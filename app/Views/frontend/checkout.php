
<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

<body style="padding-top: 114.38px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url() ?>">Home</a>
            <span> > </span>
            <span class="fw-500">Checkout</span>
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
                    $total_berat = 0;
                    $total_belanja = 0;
                    if ($array_id_varian_produk) :
                        $varian_produk = model('VarianProduk')->whereIn('id', $array_id_varian_produk)->findAll();

                        foreach ($varian_produk as $key => $v) :
                            $total_harga_ecommerce = 0;
                            foreach ($keranjang_session as $v2) {
                                if ($v2['id_varian_produk'] === $v['id']) {
                                    $qty = (int)$v2['qty'];
                                    $total_berat += ($v['berat'] * $qty);
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
                                <input type="number" class="form-control text-center" value="<?= $qty ?>" disabled style="width: 100px;">
                            </div>
                        </td>
                        <td class="text-end" id="total_belanja_item_<?= $key ?>"><?= formatRupiah($total_harga_ecommerce) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end fw-500">Total Belanja</td>
                        <td class="text-end fw-500" id="total_belanja"><?= formatRupiah($total_belanja) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    <?php if ($keranjang_session) : ?>
    <div class="row mt-4">
        <div class="col-12 col-lg-6">
            <h5 class="mb-3">Dikirim Dari</h5>
            <p>Sawojajar, Kec. Kedungkandang, Kota Malang</p>
        </div>
        <div class="col-12 col-lg-6">
            <h5 class="mb-3">Lengkapi Data Penerima</h5>
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
                    <select class="form-select" id="kurir" name="kurir" onchange="tarif('<?= $total_berat ?>')">
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

                <div class="mt-4">
                    <h5 class="mb-3">Rincian Pembayaran</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="kode_voucher_belanja" name="kode_voucher_belanja" placeholder="Punya kode promo? Masukkan disini ✨" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" autocomplete="off">
                            <button type="button" class="btn btn-outline-primary" id="submit_kode_voucher_belanja">Pakai</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Belanja</span>
                        <span><?= formatRupiah($total_belanja) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon Belanja</span>
                        <span id="diskon_belanja">-Rp0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkir</span>
                        <span id="ongkir">Silakan pilih kurir</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Potongan Ongkir</span>
                        <span id="potongan_ongkir">Silakan pilih kurir</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-500">Total Tagihan</span>
                        <span class="fw-500" id="total_tagihan">-</span>
                    </div>
                </div>

                <div class="text-end mt-5">
                    <input type="hidden" name="submit" id="submit">
                    <button type="submit" id="btn_chat_admin" onclick="dom('#submit').value='Admin'" class="btn btn-primary me-2">
                        Chat Admin
                    </button>
                    <button type="submit" id="btn_va" onclick="dom('#submit').value='VA'" class="btn btn-primary">
                        Bayar Pakai VA
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</section>

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
            dom('#component_desa').innerHTML = `<input type="text" class="form-control" disabled>`;
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

async function tarif(weight) {
    const kode_desa = dom('#desa').value;
    if (! kode_desa) {
        await Swal.fire({
            icon: 'error',
            title: 'Silakan lengkapi data penerima!',
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
                weight: weight,
                kurir: kurir
            }),
        });
        const data = await response.json();
        // console.log(data);

        if (data.status == 'success') {
            dom('#component_layanan_kurir').innerHTML =
            `<select id="layanan_kurir" name="layanan_kurir" onchange="updateRincianPembayaran(0)">
                <option value="">Pilih</option>` +
                data.data.map((layanan_kurir, index) =>
                `<option value="${index}" data-tarif="${layanan_kurir.cost}">${layanan_kurir.service} - ${formatRupiah(layanan_kurir.cost)} - ${layanan_kurir.etd}</option>`
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

dom('#submit_kode_voucher_belanja').addEventListener('click', async() => {
    const layanan_kurir = dom('#layanan_kurir').value;
    if (! layanan_kurir) {
        await Swal.fire({
            icon: 'error',
            title: 'Silakan lengkapi data penerima!',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
        return;
    }

    try {
        const kode_voucher_belanja = dom('#kode_voucher_belanja').value;
        if (! kode_voucher_belanja) {
            await Swal.fire({
                icon: 'error',
                title: 'Voucher tidak ditemukan',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        };

        const response = await fetch(`<?= base_url() ?>api/voucher-belanja/kode/${kode_voucher_belanja}`);
        const data = await response.json();

        // console.log(data);
        // return;

        if (data.status == 'success') {
            await Swal.fire({
                icon: 'success',
                title: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });

            updateRincianPembayaran(data.diskon_belanja);
        } else {
            await Swal.fire({
                icon: 'error',
                title: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        }
    } catch (error) {
        console.error(error);
    }
});

async function updateRincianPembayaran(diskon_belanja = 0) {
    try {
        const ongkir = parseInt(dom('#layanan_kurir option:checked')?.getAttribute('data-tarif')) || 0;
        const response = await fetch(`<?= base_url() ?>api/potongan-ongkir/aktif?ongkir=${ongkir}`);
        const data = await response.json();

        const total_belanja = <?= $total_belanja ?> - diskon_belanja;

        let potongan_ongkir = data.potongan_ongkir;
        if (potongan_ongkir >= ongkir) {
            potongan_ongkir = ongkir;
        }

        const final_ongkir = ongkir - potongan_ongkir;
        const total_tagihan = total_belanja + final_ongkir;

        dom('#diskon_belanja').innerHTML = formatRupiah(0 - diskon_belanja);
        dom('#ongkir').innerHTML = formatRupiah(ongkir);
        dom('#potongan_ongkir').innerHTML = formatRupiah(0 - potongan_ongkir);
        dom('#total_tagihan').innerHTML = formatRupiah(total_tagihan);
    } catch (error) {
        console.error(error);
    }
}
</script>

<script>
const form = document.getElementById('form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= base_url() ?>api/pesanan/create';
    submitPesanan(form, endpoint, confirm_title = 'Proses Pesanan');
});

async function submitPesanan(form, endpoint, confirm_title = '', confirm_message = '', confirm_button_text = 'Iya') {
    try {
        const result = await Swal.fire({
            icon: 'question',
            title: confirm_title,
            text: confirm_message,
            confirmButtonText: confirm_button_text,
            cancelButtonText: 'Batal',
            showCancelButton: true,
            reverseButtons: true,
        });

        if (result.isConfirmed) {
            dom('#loading').innerHTML = `<div class="full-transparent"> <div class="spinner"> </div> </div>`;
        
            const form_data = new FormData(form);
            for (const [key, value] of form_data.entries()) {
                if (typeof value === 'string') {
                    form_data.set(key, value.trim());
                }
            }
            const response = await fetch(endpoint, {
                method: 'POST',
                body: form_data,
            });
            const data = await response.json();

            dom('#loading').innerHTML = ``;

            // Menangani semua elemen id="invalid_*" dan menampilkan pesan error secara dinamis
            Array.from(form.querySelectorAll('[id^="invalid_"]')).forEach(element => {
                const field = element.id.replace('invalid_', '');
                const element_by_name = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${field}[]"]`);
                element.textContent = data.errors?.[field] || '';
                if (element_by_name && !['radio', 'checkbox'].includes(element_by_name.type)) {
                    element_by_name.classList.toggle('is-invalid', !!data.errors?.[field]);
                    element_by_name.nextElementSibling?.querySelector('.dselect-wrapper > .form-select')?.classList.toggle('is-invalid', !!data.errors?.[field]);
                }
            });

            // console.log(data);
            // return;

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
        dom('#loading').innerHTML = ``;

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
