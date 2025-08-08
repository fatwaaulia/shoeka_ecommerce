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
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama">
                            <div class="invalid-feedback" id="invalid_nama"></div>
                        </div>
                        <div class="mb-3">
                            <label for="potongan" class="form-label">Potongan</label>
                            <input type="text" inputmode="numeric" class="form-control" id="potongan" name="potongan" placeholder="Masukkan potongan" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_potongan"></div>
                        </div>
                        <div class="mb-3">
                            <label for="minimal_ongkir" class="form-label">Minimal Ongkir</label>
                            <input type="text" inputmode="numeric" class="form-control" id="minimal_ongkir" name="minimal_ongkir" placeholder="Masukkan minimal ongkir" oninput="this.value = dotsNumber(this.value)">
                            <div class="invalid-feedback" id="invalid_minimal_ongkir"></div>
                        </div>
                        <div class="row gx-2">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="periode_awal" class="form-label">Periode Awal</label>
                                    <input type="date" class="form-control" id="periode_awal" name="periode_awal">
                                    <div class="invalid-feedback" id="invalid_periode_awal"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="periode_akhir" class="form-label">Periode Akhir</label>
                                    <input type="date" class="form-control" id="periode_akhir" name="periode_akhir">
                                    <div class="invalid-feedback" id="invalid_periode_akhir"></div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end">Tambahkan</button>
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
    const endpoint = '<?= $base_api ?>create';
    submitData(dom('#form'), endpoint);
});
</script>
