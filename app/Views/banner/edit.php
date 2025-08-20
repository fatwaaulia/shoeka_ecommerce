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
                            <label for="gambar_desktop" class="form-label">Gambar Desktop (16 : 9)</label>
                            <div class="col-12 col-md-6 col-lg-5 col-xl-4 position-relative">
                                <img src="<?= webFile('image', $base_name, $data['gambar_desktop'], $data['updated_at']) ?>" class="w-100 cover-center" style="aspect-ratio: 16 / 9;" id="frame_gambar_desktop">
                                <div class="position-absolute" style="bottom: 0px; right: 0px;">
                                    <label for="gambar_desktop" class="btn btn-secondary rounded-circle" style="padding: 8px;">
                                        <i class="fa-solid fa-camera fa-lg"></i>
                                    </label>
                                    <input type="file" class="form-control d-none" id="gambar_desktop" name="gambar_desktop" accept=".png,.jpg,.jpeg" onchange="dom('#frame_gambar_desktop').src = window.URL.createObjectURL(this.files[0]);">
                                </div>
                            </div>
                            <div class="invalid-feedback" id="invalid_gambar_desktop"></div>
                        </div>
                        <div class="mb-3">
                            <label for="gambar_ponsel" class="form-label">Gambar Ponsel (1 : 1)</label>
                            <div class="col-12 col-md-6 col-lg-5 col-xl-4 position-relative">
                                <img src="<?= webFile('image', $base_name, $data['gambar_ponsel'], $data['updated_at']) ?>" class="w-100 cover-center" id="frame_gambar_ponsel">
                                <div class="position-absolute" style="bottom: 0px; right: 0px;">
                                    <label for="gambar_ponsel" class="btn btn-secondary rounded-circle" style="padding: 8px;">
                                        <i class="fa-solid fa-camera fa-lg"></i>
                                    </label>
                                    <input type="file" class="form-control d-none" id="gambar_ponsel" name="gambar_ponsel" accept=".png,.jpg,.jpeg" onchange="dom('#frame_gambar_ponsel').src = window.URL.createObjectURL(this.files[0]);">
                                </div>
                            </div>
                            <div class="invalid-feedback" id="invalid_gambar_ponsel"></div>
                        </div>

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="judul" name="judul" value="<?= $data['judul'] ?>" placeholder="Masukkan judul">
                            <div class="invalid-feedback" id="invalid_judul"></div>
                        </div>
                        <div class="mb-3">
                            <label for="tautan" class="form-label">Tautan</label>
                            <input type="text" class="form-control" id="tautan" name="tautan" value="<?= $data['tautan'] ?>" placeholder="https://masukkan-tautan">
                                <div class="invalid-feedback" id="invalid_tautan"></div>
                            </div>
                            <div class="mb-3">
                                <label for="urutan" class="form-label">Urutan</label>
                                <input type="text" class="form-control" id="urutan" name="urutan" value="<?= $data['urutan'] ?>" placeholder="Masukkan urutan">
                                <div class="invalid-feedback" id="invalid_urutan"></div>
                            </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
dom('#form').addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>update/<?= $data['id'] ?>';
    submitData(dom('#form'), endpoint);
});
</script>
