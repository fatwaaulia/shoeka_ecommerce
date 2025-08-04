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
                            <div class="col-xl-6">
                                <div class="mb-3">
                                    <div class="d-flex">
                                        <div class="position-relative">
                                            <img src="<?= webFile('image_user', $base_name, $data['foto'], $data['updated_at']) ?>" class="wh-150 cover-center rounded-circle" id="frame_foto">
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
                                                                    <?php if ($data['foto']) : ?>
                                                                    <div class="mt-3">
                                                                        <a style="cursor: pointer;" onclick="deleteData('<?= $base_api ?>foto/delete/<?= $data['id'] ?>')" class="text-danger">
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
                                    <label for="id_role" class="form-label">Role</label>
                                    <select class="form-select" id="id_role" name="id_role">
                                        <?php
                                        $role = model('Role')->where('id !=', 1)->findAll();
                                        foreach ($role as $v) :
                                            $selected = ($v['id'] == $data['id_role']) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $v['id'] ?>" <?= $selected ?>><?= $v['nama'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" id="invalid_id_role"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="number" class="form-control" id="no_hp" name="no_hp" value="<?= $data['no_hp'] ?>" placeholder="08xx">
                                    <div class="invalid-feedback" id="invalid_no_hp"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= $data['username'] ?>" placeholder="username">
                                    <div class="invalid-feedback" id="invalid_username"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $data['email'] ?>" placeholder="name@gmail.com">
                                    <div class="invalid-feedback" id="invalid_email"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Ubah Password</label><span class="text-secondary"> (Opsional)</span>
                                    <div class="mb-2 position-relative">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password">
                                        <div class="invalid-feedback" id="invalid_password"></div>
                                        <img src="<?= base_url('assets/icons/show.png') ?>" class="position-absolute" id="eye_password">
                                    </div>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" id="passconf" name="passconf" placeholder="Confirm password">
                                        <div class="invalid-feedback" id="invalid_passconf"></div>
                                       <img src="<?= base_url('assets/icons/show.png') ?>" class="position-absolute" id="eye_passconf">
                                    </div>
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

<script>
function toggleVisibility(inputElement, eyeElement) {
    const showIcon = "<?= base_url('assets/icons/show.png') ?>";
    const hideIcon = "<?= base_url('assets/icons/hide.png') ?>";
    inputElement.type = inputElement.type === 'password' ? 'text' : 'password';
    eyeElement.src = inputElement.type === 'password' ? showIcon : hideIcon;
}

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
    const endpoint = '<?= $base_api ?>update/<?= $data['id'] ?>';
    submitData(dom('#form'), endpoint);
});
</script>
