<section class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="my-4">Profil</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="form">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="mb-3">
                                    <div class="d-flex">
                                        <div class="position-relative">
                                            <img src="<?= webFile('image_user', 'users', $data['foto'], $data['updated_at']) ?>" class="wh-150 cover-center rounded-circle" id="frame_foto">
                                            <div class="position-absolute" style="bottom: 0px; right: 0px;">
                                                <button class="btn btn-secondary rounded-circle" style="padding: 8px;" type="button" data-bs-toggle="modal" data-bs-target="#option">
                                                    <i class="fa-solid fa-camera fa-lg"></i>
                                                </button>
                                                <div class="modal fade" id="option" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <div data-bs-dismiss="modal">
                                                                    <input type="file" class="form-control" name="foto" accept=".png,.jpg,.jpeg" onchange="dom('#frame_foto').src = window.URL.createObjectURL(this.files[0]);">
                                                                </div>
                                                                <?php if ($data['foto']) : ?>
                                                                <div class="mt-3">
                                                                    <a style="cursor: pointer;" onclick="deleteData('<?= $base_api ?>delete/photo')" class="text-danger">
                                                                        <i class="fa-solid fa-trash-can fa-lg"></i>
                                                                        Hapus Foto
                                                                    </a>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="invalid_foto"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $data['nama'] ?>" placeholder="Masukkan nama lengkap">
                                    <div class="invalid-feedback" id="invalid_nama"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                        <option value="">Pilih</option>
                                        <?php
                                        $jenis_kelamin = ['Laki-laki', 'Perempuan'];
                                        foreach ($jenis_kelamin as $v) :
                                            $selected = ($v == $data['jenis_kelamin']) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $v ?>" <?= $selected ?>><?= $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" id="invalid_jenis_kelamin"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat"><?= $data['alamat'] ?></textarea>
                                    <div class="invalid-feedback" id="invalid_alamat"></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="<?= userSession('nama_role') ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="number" class="form-control" id="no_hp" name="no_hp" value="<?= $data['no_hp'] ?>" placeholder="08xx">
                                    <div class="invalid-feedback" id="invalid_no_hp"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $data['email'] ?>" placeholder="name@gmail.com">
                                    <div class="invalid-feedback" id="invalid_email"></div>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ubah_password">
                                        <i class="fa-solid fa-lock me-2"></i>
                                        <span class="align-middle">Ubah Password</span>
                                    </a>
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

<!-- Modal Ubah Password -->
<div class="modal fade" id="ubah_password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Ubah Password</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form_ubah_password">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="oldpass" class="form-label">Password Saat Ini</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" name="oldpass" id="oldpass" placeholder="Password saat ini">
                            <div class="invalid-feedback" id="invalid_oldpass"></div>
                            <img src="<?= base_url('assets/icons/show.png') ?>" class="position-absolute" id="eye_oldpass">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <div class="mb-2 position-relative">
                            <input type="password" class="form-control" name="password" id="password" placeholder="password" autocomplete="off">
                            <div class="invalid-feedback" id="invalid_password"></div>
                            <img src="<?= base_url('assets/icons/show.png') ?>" class="position-absolute" id="eye_password">
                        </div>
                        <div class="position-relative">
                            <input type="password" class="form-control" name="passconf" id="passconf" placeholder="confirm password" autocomplete="off">
                            <div class="invalid-feedback" id="invalid_passconf"></div>
                            <img src="<?= base_url('assets/icons/show.png') ?>" class="position-absolute" id="eye_passconf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary float-end">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleVisibility(inputElement, eyeElement) {
    const showIcon = "<?= base_url('assets/icons/show.png') ?>";
    const hideIcon = "<?= base_url('assets/icons/hide.png') ?>";
    inputElement.type = inputElement.type === 'password' ? 'text' : 'password';
    eyeElement.src = inputElement.type === 'password' ? showIcon : hideIcon;
}

dom('#eye_oldpass').addEventListener('click', () => {
    toggleVisibility(dom('#oldpass'), dom('#eye_oldpass'));
});

dom('#eye_password').addEventListener('click', () => {
    toggleVisibility(dom('#password'), dom('#eye_password'));
});

dom('#eye_passconf').addEventListener('click', () => {
    toggleVisibility(dom('#passconf'), dom('#eye_passconf'));
});
</script>

<script>
dom('#form').addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>update';
    submitData(dom('#form'), endpoint);
});

dom('#form_ubah_password').addEventListener('submit', function(event) {
    event.preventDefault();
    const endpoint = '<?= $base_api ?>update/password';
    submitData(dom('#form_ubah_password'), endpoint);
});
</script>
