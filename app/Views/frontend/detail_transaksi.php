<?php
$no_hp_admin = model('Users')->select('no_hp')->find(1)['no_hp'];
?>

<body style="padding-top: 111.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <a href="<?= base_url() ?>">Home</a>
            <span> > </span>
            <span>Detail Transaksi</span>
            <span> > </span>
            <span class="fw-500"><?= $data['kode'] ?></span>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <table>
                <tr>
                    <td>Kode Pemesanan</td>
                    <td>
                        : <span class="fw-600" id="kode_transaksi"><?= $data['kode'] ?></span>
                        <a href="#" id="copyBtn">Salin</a>
                    </td>

                    <script>
                    document.getElementById("copyBtn").addEventListener("click", function () {
                        let text = document.getElementById("kode_transaksi").innerText;

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
                    <td>Tanggal Transaksi</td>
                    <td>: <?= dateFormatter($data['created_at'], 'd MMMM yyyy HH:mm') ?></td>
                </tr>
            </table>
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
                    <?php foreach ($item_transaksi as $key => $v) : ?>
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
                        <td colspan="4" class="text-end fw-600">Total Belanja</td>
                        <td class="text-end fw-600" id="total_belanja"><?= formatRupiah($data['total_belanja']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 col-lg-6">
            <h4 class="mb-3">Dikirim Dari</h4>
            <p>Sawojajar, Kec. Kedungkandang, Kota Malang</p>
        </div>
        <div class="col-12 col-lg-6">
            <h4 class="mb-3">Data Penerima</h4>
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
                    <h4 class="mb-3">Rincian Pembayaran</h4>
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
                        <span id="diskon_belanja"><?= formatRupiah($data['diskon_voucher_belanja']) ?></span>
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
                        <span class="fw-600">Total Tagihan</span>
                        <span class="fw-600" id="total_tagihan"><?= formatRupiah($data['total_tagihan']) ?></span>
                    </div>
                    
                    <hr style="border: 1px solid #ddd;">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tipe Pembayaran</span>
                        <span><?= $data['tipe_pembayaran'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span>
                        <span><?= $data['status'] ?></span>
                    </div>
                </div>


                <div class="text-end mt-4">
                    <?php if ($data['status'] != 'Lunas') : if ($data['tipe_pembayaran'] == 'VA') : ?>
                    <a href="<?= $data['invoice_url'] ?>" target="_blank" class="btn btn-primary mt-3">Bayar Sekarang</a>
                    <?php else : ?>
                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', $no_hp_admin) ?>" target="_blank" class="btn btn-primary mt-3">Hubungi Admin</a>
                    <?php endif; endif; ?>
                </div>
            </form>
        </div>
    </div>
</section>
