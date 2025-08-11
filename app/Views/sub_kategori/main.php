<script src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/modules/datatables/css/dataTables.dataTables.min.css">

<section class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="my-4"><?= isset($title) ? $title : '' ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card p-3">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                        <!--  -->
                    </div>
                    <div class="col-12 col-md-6 col-lg-7 col-xl-8 d-flex justify-content-end align-items-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new">
                            <i class="fa-solid fa-plus fa-sm"></i> New
                        </button>
                        <div class="modal fade" id="new" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5">Add <?= isset($title) ? $title : '' ?></h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form id="form">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="kategori" class="form-label">Kategori</label>
                                                <select class="form-select" id="kategori" name="kategori">
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    $kategori = model('Kategori')->findAll();
                                                    foreach ($kategori as $v) :
                                                    ?>
                                                    <option value="<?= $v['id'] ?>"><?= $v['nama'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback" id="invalid_kategori"></div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nama" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama">
                                                <div class="invalid-feedback" id="invalid_nama"></div>
                                            </div>
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
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary float-end">Tambahkan</button>
                                        </div>
                                    </form>
                                    <script>
                                    dom('#form').addEventListener('submit', function(event) {
                                        event.preventDefault();
                                        const endpoint = '<?= $base_api ?>create';
                                        submitData(dom('#form'), endpoint);
                                    });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="display nowrap" id="myTable">
                    <thead class="bg-primary-subtle">
                        <tr>
                            <th>No.</th>
                            <th>Kategori</th>
                            <th>Nama</th>
                            <th>Gambar</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#myTable', {
        ajax: '<?= $get_data?>',
        processing: true,
        serverSide: true,
        order: [],
        initComplete: function (settings, json) {
            $('#myTable').wrap('<div style="overflow: auto; width: 100%; position: relative;"></div>');
        },
        drawCallback: function () {
            new LazyLoad({
                elements_selector: '.lazy-shimmer',
                callback_loaded: (el) => {
                    el.classList.remove('lazy-shimmer');
                }
            });
        },
        columns: [
            {
                name: '',
                data: 'no_urut',
            }, {
                name: 'nama_kategori',
                data: 'nama_kategori',
            }, {
                name: 'nama',
                data: 'nama',
            }, {
                name: '',
                data: null,
                render: data => `<img data-src="${data.gambar}" class="wh-40 cover-center lazy-shimmer">`,
            }, {
                name: '',
                data: null,
                render: renderOpsi,
            },
        ].map(col => ({ ...col, orderable: col.name !== '' })),
    });
});

function renderOpsi(data) {
    const kategori = <?= json_encode(model('Kategori')->findAll()) ?>;
    let endpoint_hapus_data = `<?= $base_api ?>delete/${data.id}`;
    let html = `
    <a class="me-2" title="Edit" data-bs-toggle="modal" data-bs-target="#edit${data.id}">
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>
    <div class="modal fade" id="edit${data.id}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit <?= isset($title) ? $title : '' ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form_${data.id}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Pilih</option>
                                ${kategori.map(item => `<option value="${item.id}" ${item.id == data.id_kategori ? 'selected' : ''}>${item.nama}</option>`).join('')}
                            </select>
                            <div class="invalid-feedback" id="invalid_kategori"></div>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="${data.nama}" placeholder="Masukkan nama">
                            <div class="invalid-feedback" id="invalid_nama"></div>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <div class="col-12 col-md-6 col-lg-5 col-xl-4 position-relative">
                                <img src="${data.gambar}" class="w-100 cover-center" id="frame_edit_gambar_${data.id}">
                                <div class="position-absolute" style="bottom: 0px; right: 0px;">
                                    <label for="gambar_${data.id}" class="btn btn-secondary rounded-circle" style="padding: 8px;">
                                        <i class="fa-solid fa-camera fa-lg"></i>
                                    </label>
                                    <input type="file" class="form-control d-none" id="gambar_${data.id}" name="gambar" accept=".png,.jpg,.jpeg" onchange="dom('#frame_edit_gambar_${data.id}').src = window.URL.createObjectURL(this.files[0]);">
                                </div>
                            </div>
                            <div class="invalid-feedback" id="invalid_gambar"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary float-end">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a onclick="deleteData('${endpoint_hapus_data}')" title="Delete">
        <i class="fa-regular fa-trash-can fa-lg text-danger"></i>
    </a>`;

    setTimeout(() => actionEdit(data.id), 0);

    return html;
}

function actionEdit(id) {
    const form = dom(`#form_${id}`);

    if (! form.dataset.isInitialized) {
        form.dataset.isInitialized  = 'true';
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const endpoint = `<?= $base_api ?>update/${id}`;
            submitData(form, endpoint);
        });
    }
}
</script>

<script src="<?= base_url() ?>assets/modules/datatables/js/dataTables.min.js"></script>
