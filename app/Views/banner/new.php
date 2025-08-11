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
                            <div class="col-12 col-md-6 col-lg-5 col-xl-4 position-relative">
                                <img src="<?= webFile('image') ?>" class="w-100 cover-center" id="frame_gambar">
                                <div class="position-absolute" style="bottom: 0px; right: 0px;">
                                    <label for="gambar" class="btn btn-secondary rounded-circle" style="padding: 8px;">
                                        <i class="fa-solid fa-camera fa-lg"></i>
                                    </label>
                                    <input type="file" class="form-control d-none" id="gambar" name="gambar" accept=".png,.jpg,.jpeg" onchange="dom('#frame_gambar').src = window.URL.createObjectURL(this.files[0]);">
                                </div>
                            </div>
                            <div class="invalid-feedback" id="invalid_gambar"></div>
                        </div>
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul">
                            <div class="invalid-feedback" id="invalid_judul"></div>
                        </div>
                        <div class="mb-3">
                            <label for="tautan" class="form-label">Tautan</label>
                            <input type="text" class="form-control" id="tautan" name="tautan" placeholder="https://masukkan-tautan">
                            <div class="invalid-feedback" id="invalid_tautan"></div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end">Tambahkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
dom('#form').addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>create';
    submitData(dom('#form'), endpoint);
});
</script>
