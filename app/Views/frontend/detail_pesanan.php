<?php
$app_settings = model('AppSettings')->find(1);
$no_hp_admin = $app_settings['no_hp'];

$href_admin = "https://wa.me/" . preg_replace('/^0/', '62', $no_hp_admin) . "?text=Halo%20Admin%20Shoeka,%20saya%20ingin%20konfirmasi%20pembayaran.%0AKode%20Pesanan:%20" . $data['kode'] . "%0A%0A" . base_url() . "detail-pesanan%3Fkode%3D" . $data['kode'];
?>

<section class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url() ?>">Home</a>
            <span> > </span>
            <span>Detail Pesanan</span>
            <span> > </span>
            <span class="fw-500"><?= $data['kode'] ?></span>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-md-6">
            <table>
                <tr>
                    <td>Kode Pesanan</td>
                    <td>
                        : <span class="fw-600" id="kode_pesanan"><?= $data['kode'] ?></span>
                        <a href="#" id="salinKode">Salin</a>
                    </td>

                    <script>
                    document.getElementById("salinKode").addEventListener("click", function (event) {
                        event.preventDefault();
                        let text = document.getElementById("kode_pesanan").innerText;

                        navigator.clipboard.writeText(text).then(() => {
                            return Swal.fire({
                                icon: 'success',
                                title: 'Kode disalin',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                            });
                        }).catch(err => {
                            console.error("Gagal menyalin kode", err);
                        });
                    });
                    </script>
                </tr>
                <tr>
                    <td>Tanggal Pesanan</td>
                    <td>: <?= dateFormatter($data['created_at'], 'd MMMM yyyy HH:mm') ?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <?php
                    if ($data['status'] == 'Lunas') {
                        $color_status = 'text-success';
                    } elseif ($data['status'] == 'Menunggu Pembayaran') {
                        $color_status = 'text-warning';
                    } else {
                        $color_status = 'text-danger';
                    }
                    ?>
                    <td>:
                        <span class="<?= $color_status ?> fw-500"><?= $data['status'] ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-12 col-md-6 text-end">
            <h4><?= formatRupiah($data['total_tagihan']) ?></h4>
            <?php if ($data['status'] != 'Lunas') : ?>
            <small class="text-danger fw-600">Jatuh Tempo pada <?= dateFormatter($data['expired_at'], 'd MMMM yyyy HH:mm') ?> (GMT+07:00)</small>
            <br>
            <?php endif; ?>
            <?php if ($data['status'] == 'Menunggu Pembayaran') : if ($data['tipe_pembayaran'] == 'VA') : ?>
            <a href="<?= $data['invoice_url'] ?>" target="_blank" class="btn btn-primary mt-3">Bayar Sekarang</a>
            <?php else : ?>
            <a href="<?= $href_admin ?>" target="_blank" class="btn btn-primary mt-3">Hubungi Admin</a>
            <?php endif; endif; ?>
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
                    <?php foreach ($item_pesanan as $key => $v) : ?>
                    <tr>
                        <td style="width: 100px;">
                            <img src="<?= webFile('image', 'varian_produk', $v['gambar_varian_produk'], $v['updated_at']) ?>" class="wh-100 cover-center me-3" alt="<?= $v['nama_varian_produk'] ?>">
                        </td>
                        <td class="text-wrap">
                            <?= $v['nama_varian_produk'] ?>
                        </td>
                        <td class="text-end"><?= formatRupiah($v['harga_ecommerce']) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <input type="number" class="form-control text-center" value="<?= $v['qty'] ?>" disabled style="width: 100px;">
                            </div>
                        </td>
                        <td class="text-end" id="total_belanja_item_<?= $key ?>"><?= formatRupiah($v['total_harga']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end fw-500">Total Belanja</td>
                        <td class="text-end fw-500" id="total_belanja"><?= formatRupiah($data['total_belanja']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-lg-6">
            <h5 class="mb-3">Dikirim Dari</h5>
            <p>Sawojajar, Kec. Kedungkandang, Kota Malang</p>
        </div>
        <div class="col-12 col-lg-6">
            <h5 class="mb-3">Data Penerima</h5>
            <form id="form">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" value="<?= $data['nama_customer'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. HP</label>
                    <input type="number" class="form-control" value="<?= $data['no_hp_customer'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= $data['email_customer'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Provinsi</label>
                    <input type="text" class="form-control" value="<?= $data['nama_provinsi'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kabupaten</label>
                    <input type="text" class="form-control" value="<?= $data['nama_kabupaten'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" class="form-control" value="<?= $data['nama_kecamatan'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Desa</label>
                    <input type="text" class="form-control" value="<?= $data['nama_desa'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat" disabled><?= $data['alamat_customer'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kurir</label>
                    <input type="text" class="form-control" value="<?= $data['tarif_ongkir_name'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Layanan Kurir</label>
                    <input type="text" class="form-control" value="<?= $data['tarif_ongkir_service'] ?> - <?= formatRupiah($data['tarif_ongkir_cost']) ?> - <?= $data['tarif_ongkir_etd'] ?>" disabled>
                </div>

                <div class="mt-4">
                    <h5 class="mb-3">Rincian Pembayaran</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="<?= $data['kode_voucher_belanja'] ?>" placeholder="Punya kode promo? Masukkan disini ✨" disabled>
                            <button type="button" class="btn btn-outline-primary" disabled>Pakai</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Belanja</span>
                        <span><?= formatRupiah($data['total_belanja']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon Belanja</span>
                        <span id="diskon_belanja"><?= formatRupiah($data['potongan_diskon']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkir</span>
                        <span id="ongkir"><?= formatRupiah($data['tarif_ongkir_cost']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Potongan Ongkir</span>
                        <span id="potongan_ongkir"><?= formatRupiah($data['potongan_ongkir']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-500">Total Tagihan</span>
                        <span class="fw-500" id="total_tagihan"><?= formatRupiah($data['total_tagihan']) ?></span>
                    </div>
                    
                    <hr style="border: 1px solid #ddd;">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tipe Pembayaran</span>
                        <span><?= $data['tipe_pembayaran'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Metode Pembayaran</span>
                        <span><?= $data['payment_method'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span>
                        <span><?= $data['status'] ?></span>
                    </div>
                    <?php if (userSession() && $data['tipe_pembayaran'] == 'VA') : ?>
                    <div class="d-flex justify-content-end mb-2">
                        <a href="#" id="sinkronisasi_pembayaran">Sinkronisasi</a>
                    </div>
                    <script>
                    dom('#sinkronisasi_pembayaran').addEventListener('click', async(event) => {
                        event.preventDefault();

                        try {
                            const result = await Swal.fire({
                                icon: 'question',
                                title: 'Sinkronisasi Pembayaran',
                                confirmButtonText: 'Iya, Sinkronkan',
                                cancelButtonText: 'Batal',
                                showCancelButton: true,
                                reverseButtons: true,
                            });

                            if (result.isConfirmed) {
                                dom('#loading').innerHTML = `<div class="full-transparent"> <div class="spinner"> </div> </div>`;

                                const response = await fetch(`<?= base_url() ?>api/pesanan/sinkronisasi/<?= $data['id'] ?>`);
                                const data = await response.json();

                                dom('#loading').innerHTML = ``;

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
                        }
                    });
                    </script>
                    <?php endif; ?>

                    <hr style="border: 1px solid #ddd;">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Kurir</span>
                        <span><?= $data['tarif_ongkir_name'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Berat</span>
                        <span><?= $data['total_berat'] ?> gram</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Nomor Resi</span>
                        <span>
                            <?php if ($data['nomor_resi']) : ?>
                            <a href="#" id="salinResi">Salin</a>
                            <script>
                            document.getElementById("salinResi").addEventListener("click", function (event) {
                                event.preventDefault();
                                let text = document.getElementById("nomor_resi").innerText;

                                navigator.clipboard.writeText(text).then(() => {
                                    return Swal.fire({
                                        icon: 'success',
                                        title: 'Nomor resi disalin',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                }).catch(err => {
                                    console.error("Gagal menyalin kode", err);
                                });
                            });
                            </script>
                            <?php endif; ?>
                            <span id="nomor_resi"><?= $data['nomor_resi'] ?: '-' ?></span>
                        </span>
                    </div>
                    
                    <?php if ($data['nomor_resi']) : ?>
                    <hr style="border: 1px solid #ddd;">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span>
                        <span id="status_pengiriman" class="fw-500">-</span>
                    </div>
                    <div id="manifest_resi" class="mt-3"></div>
                    <script>
                    document.addEventListener('DOMContentLoaded', async() => {
                        dom('#manifest_resi').innerHTML = `<div class="spinner-border text-primary"></div>`;
                        try {
                            const response = await fetch(`<?= base_url() ?>api/ongkir/resi?awb=<?= $data['nomor_resi'] ?>&kurir=<?= $data['tarif_ongkir_code'] ?>&last_phone_number=<?= substr($data['no_hp_customer'], -5) ?>`);
                            const data = await response.json();

                            dom('#status_pengiriman').innerText = data.delivery_status.status;

                            dom('#manifest_resi').innerHTML = 
                            data.manifest.slice().reverse().map(manifest => {
                                let date = new Date(manifest.manifest_date);
                                let formatted = new Intl.DateTimeFormat('id-ID', { 
                                    weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' 
                                }).format(date);
                                let manifest_time = `${manifest.manifest_time.slice(0, 5)} WIB`;

                                return `
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-500">${formatted}</span>
                                        <span>${manifest_time}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small>${manifest.manifest_description}</small>
                                    </div>
                                </div>`;
                            }).join('');
                        } catch (error) {
                            dom('#manifest_resi').innerHTML = ``;
                            console.error(error);
                        }
                    });
                    </script>
                    <?php endif; ?>
                </div>


                <div class="text-end mt-4">
                    <?php if ($data['status'] == 'Menunggu Pembayaran') : if ($data['tipe_pembayaran'] == 'VA') : ?>
                    <a href="<?= $data['invoice_url'] ?>" target="_blank" class="btn btn-primary mt-3">Bayar Sekarang</a>
                    <?php else : ?>
                    <a href="<?= $href_admin ?>" target="_blank" class="btn btn-primary mt-3">Hubungi Admin</a>
                    <?php endif; endif; ?>
                </div>
            </form>
        </div>
    </div>
</section>
