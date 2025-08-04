<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

<section class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="my-4"><?= isset($title) ? $title : '' ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="form">
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <div class="col-12 col-md-6 col-lg-5 col-xxl-4 position-relative">
                                <img src="<?= webFiles('', '', '', true) ?>" class="w-100 cover-center" id="frame_gambar">
                                <div class="position-absolute" style="bottom: 0px; right: 0px;">
                                    <button class="btn btn-secondary rounded-circle" style="padding:8px 10px" type="button" onclick="document.getElementById('gambar').click()">
                                        <i class="fa-solid fa-camera fa-lg"></i>
                                    </button>
                                    <input type="file" class="form-control d-none" id="gambar" name="gambar" accept=".png,.jpg,.jpeg" onchange="document.getElementById('frame_gambar').src = window.URL.createObjectURL(this.files[0]);">
                                </div>
                            </div>
                            <div class="invalid-feedback" id="invalid_gambar"></div>
                        </div>
                        <div class="mb-3">
                            <label for="produk" class="form-label">Produk</label>
                            <select id="produk" name="produk">
                                <option value="">Pilih</option>
                                <?php
                                $produk = model('Produk')->findAll();
                                foreach ($produk as $v) :
                                ?>
                                <option value="<?= $v['id'] ?>"><?= $v['nama_kategori'] ?> - <?= $v['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="invalid_produk"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Varian</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama varian">
                            <div class="invalid-feedback" id="invalid_nama"></div>
                        </div>
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" placeholder="Masukkan sku" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                            <div class="invalid-feedback" id="invalid_sku"></div>
                        </div>
                        <div class="mb-3">
                            <label for="harga_pokok" class="form-label">Harga Pokok</label>
                            <input type="text" inputmode="numeric" class="form-control" id="harga_pokok" name="harga_pokok" placeholder="Masukkan harga pokok" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_harga_pokok"></div>
                        </div>
                        <div class="mb-3">
                            <label for="biaya_produk" class="form-label">Biaya Produk</label>
                            <input type="text" inputmode="numeric" class="form-control" id="biaya_produk" name="biaya_produk" placeholder="Masukkan biaya produk" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_biaya_produk"></div>
                        </div>
                        <div class="mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="text" inputmode="numeric" class="form-control" id="harga_jual" name="harga_jual" placeholder="Masukkan harga jual" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_harga_jual"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <?php
                            $status = ['ENABLE', 'DISABLE'];
                            foreach($status as $v) :
                            ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="<?= $v ?>" name="status" value="<?= $v ?>">
                                <label class="form-check-label" for="<?= $v ?>"><?= $v ?></label>
                            </div>
                            <?php endforeach; ?>
                            <div class="invalid-feedback" id="invalid_status"></div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end">Tambahkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
dselect(document.querySelector('#produk'), { search: true, });
</script>

<script>
const form = document.getElementById('form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>create';
    submitData(form, endpoint);
});
</script>
