<?php
$get_role = $_GET['role'] ?? '';
?>

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
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                        <select class="form-select" id="role" onchange="location = this.value;">
                            <option value="<?= current_url() ?>">Semua Role</option>
                            <?php
                            $role = model('Role')->findAll();
                            foreach ($role as $v) :
                                $selected = ($get_role == $v['id']) ? 'selected' : '';
                            ?>
                            <option value="<?= current_url() ?>?role=<?= $v['id'] ?>" <?= $selected ?>><?= $v['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8 col-xl-9 d-flex justify-content-end align-items-end">
                        <a href="<?= $base_route ?>new" class="btn btn-primary">
                            <i class="fa-solid fa-plus fa-sm"></i> New
                        </a>
                    </div>
                </div>
                <table class="display nowrap" id="myTable">
                    <thead class="bg-primary-subtle">
                        <tr>
                            <th>No.</th>
                            <th>Role</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
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
        ajax: '<?= $get_data ?>',
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
                name: '',
                data: 'nama_role',
            }, {
                name: '',
                data: null,
                render: data => `<img data-src="${data.foto}" class="wh-40 cover-center rounded-circle lazy-shimmer">`,
            }, {
                name: 'nama',
                data: 'nama',
            }, {
                name: 'jenis_kelamin',
                data: 'jenis_kelamin',
            }, {
                name: 'alamat',
                data: 'alamat',
                className: 'text-wrap',
            }, {
                name: 'no_hp',
                data: 'no_hp',
            }, { 
                name: 'username',
                data: 'username',
            }, {
                name: 'email',
                data: 'email',
            }, {
                name: '',
                data: 'created_at',
            }, {
                name: '',
                data: null,
                render: renderOpsi,
            },
        ].map(col => ({ ...col, orderable: col.name !== '' })),
    })
});

function renderOpsi(data) {
    if (data.id_role == 1) return null;
    let endpoint_edit_data = `<?= $base_route ?>edit/${data.id}`;
    let endpoint_hapus_data = `<?= $base_api ?>delete/${data.id}`;
    return `
    <a href="${endpoint_edit_data}" class="me-2" title="Edit">
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>
    <a onclick="deleteData('${endpoint_hapus_data}')" title="Delete">
        <i class="fa-regular fa-trash-can fa-lg text-danger"></i>
    </a>`;
}
</script>

<script src="<?= base_url() ?>assets/modules/datatables/js/dataTables.min.js"></script>
