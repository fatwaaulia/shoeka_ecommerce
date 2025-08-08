<link href="<?= base_url() ?>assets/modules/summernote/summernote-lite.min.css" rel="stylesheet">

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
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $data['nama'] ?>" placeholder="Masukkan nama">
                            <div class="invalid-feedback" id="invalid_nama"></div>
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input type="text" class="form-control" id="kode" name="kode" value="<?= $data['kode'] ?>" placeholder="Masukkan kode" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                            <div class="invalid-feedback" id="invalid_kode"></div>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_diskon" class="form-label">Jenis Diskon</label>
                            <select class="form-select" id="jenis_diskon" name="jenis_diskon">
                                <option value="">Pilih</option>
                                <?php
                                $jenis_diskon = ['Rp', '%'];
                                foreach ($jenis_diskon as $v) :
                                    $selected = ($v == $data['jenis_diskon']) ? 'selected' : '';
                                ?>
                                <option value="<?= $v ?>" <?= $selected ?>><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="invalid_jenis_diskon"></div>
                        </div>
                        <div class="mb-3">
                            <label for="diskon" class="form-label">Diskon</label>
                            <input type="text" inputmode="numeric" class="form-control" id="diskon" name="diskon" value="<?= dotsNumber(abs($data['diskon'])) ?>" placeholder="Masukkan diskon" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_diskon"></div>
                        </div>
                        <div class="mb-3">
                            <label for="minimal_belanja" class="form-label">Minimal Belanja</label>
                            <input type="text" inputmode="numeric" class="form-control" id="minimal_belanja" name="minimal_belanja" value="<?= dotsNumber(abs($data['minimal_belanja'])) ?>" placeholder="Masukkan minimal belanja" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_minimal_belanja"></div>
                        </div>
                        <div class="row gx-2">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="periode_awal" class="form-label">Periode Awal</label>
                                    <input type="date" class="form-control" id="periode_awal" name="periode_awal" value="<?= $data['periode_awal'] ?>">
                                    <div class="invalid-feedback" id="invalid_periode_awal"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="periode_akhir" class="form-label">Periode Akhir</label>
                                    <input type="date" class="form-control" id="periode_akhir" name="periode_akhir" value="<?= $data['periode_akhir'] ?>">
                                    <div class="invalid-feedback" id="invalid_periode_akhir"></div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<script src="<?= base_url() ?>assets/modules/summernote/summernote-lite.min.js"></script>
<script src="<?= base_url() ?>assets/modules/summernote/lang/summernote-id-ID.js"></script>
<script>
$(document).ready(function() {
    $('#konten').summernote({
        placeholder: '',
        lang: 'id-ID', // default: 'en-US'
        tabsize: 2,
        height: 350,
        toolbar: [
            ['font', ['bold', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['picture', 'link']],
        ]
    });
});
</script>

<script>
dom('#form').addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>update/<?= $data['id'] ?>';
    submitData(dom('#form'), endpoint);
});
</script>
