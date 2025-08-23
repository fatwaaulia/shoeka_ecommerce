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
                        <div class="row">
                            <div class="col-xxl-6">
                                <div class="mb-3">
                                    <label for="nama_aplikasi" class="form-label">Nama Aplikasi</label>
                                    <input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi" value="<?= $data['nama_aplikasi'] ?>" placeholder="Masukkan nama aplikasi">
                                    <div class="invalid-feedback" id="invalid_nama_aplikasi"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?= $data['nama_perusahaan'] ?>" placeholder="PT / Organisasi dsb.">
                                    <div class="invalid-feedback" id="invalid_nama_perusahaan"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi"><?= $data['deskripsi'] ?></textarea>
                                    <div class="invalid-feedback" id="invalid_deskripsi"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="number" class="form-control" id="no_hp" name="no_hp" value="<?= $data['no_hp'] ?>" placeholder="08xx">
                                    <div class="form-text">
                                        Wajib terdaftar di whatsapp!
                                    </div>
                                    <div class="invalid-feedback" id="invalid_no_hp"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat"><?= $data['alamat'] ?></textarea>
                                    <div class="invalid-feedback" id="invalid_alamat"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="maps" class="form-label">Maps</label>
                                    <input type="text" class="form-control" id="maps" name="maps" value="<?= $data['maps'] ?>" placeholder="https://www.google.com/maps/embed..">
                                    <div class="invalid-feedback" id="invalid_maps"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="tentang" class="form-label">Tentang</label>
                                    <textarea type="text" class="form-control summernote" name="tentang" id="tentang"><?= $data['tentang'] ?></textarea>
                                    <div class="invalid-feedback" id="invalid_tentang"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="faq" class="form-label">FAQ</label>
                                    <textarea type="text" class="form-control summernote" name="faq" id="faq"><?= $data['faq'] ?></textarea>
                                    <div class="invalid-feedback" id="invalid_faq"></div>
                                </div>
                            </div>
                            <div class="col-xxl-6">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="logo" class="form-label">Logo</label>
                                        <div class="position-relative">
                                            <img src="<?= webFile('image', $base_name, $data['logo'], $data['updated_at']) ?>" class="w-100 h-100 cover-center" id="frame_logo">
                                            <div class="position-absolute" style="bottom:0px; right:0px;">
                                                <label for="logo" class="btn btn-secondary rounded-circle" style="padding: 8px;">
                                                    <i class="fa-solid fa-camera fa-lg"></i>
                                                </label>
                                                <input type="file" class="form-control d-none" id="logo" name="logo" accept=".png,.jpg,.jpeg" onchange="dom('#frame_logo').src = window.URL.createObjectURL(this.files[0]);">
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="invalid_logo"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="favicon" class="form-label">Favicon</label>
                                        <div class="position-relative">
                                            <img src="<?= webFile('image', $base_name, $data['favicon'], $data['updated_at']) ?>" class="w-100 cover-center" id="frame_favicon">
                                            <div class="position-absolute" style="bottom:0px; right:0px;">
                                                <label for="favicon" class="btn btn-secondary rounded-circle" style="padding: 8px;">
                                                    <i class="fa-solid fa-camera fa-lg"></i>
                                                </label>
                                                <input type="file" class="form-control d-none" id="favicon" name="favicon" accept=".png,.jpg,.jpeg" onchange="dom('#frame_favicon').src = window.URL.createObjectURL(this.files[0]);">
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="invalid_favicon"></div>
                                    </div>
                                </div>
                                 <div class="mb-3">
                                    <label class="form-label">Open VA</label>
                                    <?php
                                    $open_va = ['Tutup', 'Buka'];
                                    foreach ($open_va as $key => $v) :
                                    ?>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="open_va_<?= $key ?>" name="open_va" value="<?= $v ?>" <?= $data['open_va'] == $v ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="open_va_<?= $key ?>"><?= $v ?></label>
                                    </div>
                                    <?php endforeach ?>
                                    <div class="invalid-feedback" id="invalid_status"></div>
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
    $('.summernote').summernote({
        placeholder: '',
        lang: 'id-ID', // default: 'en-US'
        tabsize: 2,
        height: 200,
        toolbar: [
            ['font', ['bold', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
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
