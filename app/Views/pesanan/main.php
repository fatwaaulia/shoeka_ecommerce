<?php
$get_status = $_GET['status'] ?? '';
$get_tanggal_awal = $_GET['tanggal_awal'] ?? '';
$get_tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
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
                    <div class="col-12">
                        <form action="" method="get">
                            <div class="row g-3">
                                <div class="col-6 col-md-5 col-lg-3 col-xl-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Semua</option>
                                        <?php
                                        $status = ['Menunggu Pembayaran', 'Lunas', 'Kedaluwarsa'];
                                        foreach ($status as $v) :
                                            $selected = ($get_status == $v) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $v ?>" <?= $selected ?>><?= $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-5 col-lg-2 col-xl-2">
                                    <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="<?= $_GET['tanggal_awal'] ?? '' ?>">
                                </div>
                                <div class="col-6 col-md-5 col-lg-2 col-xl-2">
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?= $_GET['tanggal_akhir'] ?? '' ?>">
                                </div>
                                <div class="col-12 col-md-2 col-lg-1 col-xl-2 d-flex justify-content-end align-items-end">
                                    <button type="submit" class="btn btn-primary me-2 w-100" title="Filter">
                                        <i class="fa-solid fa-filter"></i>
                                        <span class="ms-1 d-md-none">Filter</span>
                                    </button>
                                    <a href="<?= $base_route ?>" class="btn btn-outline-danger w-100" title="Reset">
                                        <i class="fa-solid fa-filter-circle-xmark"></i>
                                        <span class="ms-1 d-md-none">Reset</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="display nowrap" id="myTable">
                    <thead class="bg-primary-subtle">
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Customer</th>
                            <th>Tipe Pembayaran</th>
                            <th>Status</th>
                            <th>Nomor Resi</th>
                            <th>Tanggal Pesanan</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
let metode_pembayaran = [];
async function get_metode_pembayaran() {
    try {
        const response = await fetch(`<?= base_url() ?>api/metode-pembayaran`);
        const data = await response.json();
        metode_pembayaran = data.data;
    } catch (error) {
        console.error(error);
    }
}

document.addEventListener('DOMContentLoaded', async function() {
    await get_metode_pembayaran();

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
                name: 'kode',
                data: null,
                render: data => `<a href="<?= base_url() ?>detail-pesanan?kode=${data.kode}" target="_blank">${data.kode}</a>`,
            }, {
                name: 'nama_customer',
                data: 'nama_customer',
            }, {
                name: 'tipe_pembayaran',
                data: 'tipe_pembayaran',
            }, {
                name: '',
                data: null,
                render: renderStatusPembayaran,
            }, {
                name: 'nomor_resi',
                data: null,
                render: renderNomorResi,
            }, {
                name: '',
                data: 'created_at',
            }, {
                name: '',
                data: null,
                render: renderOpsi,
            },
        ].map(col => ({ ...col, orderable: col.name !== '' })),
    });
});

function renderStatusPembayaran(data) {
    if (data.tipe_pembayaran == 'VA' || data.status == 'Lunas') {
        let badge_color = 'bg-primary';
        if (data.status == 'Lunas') {
            badge_color = 'bg-success';
        } else if (data.status == 'Kedaluwarsa') {
            badge_color = 'bg-danger';
        }
        return `<div class="badge ${ badge_color }" style="border-radius: 6px!important;">${data.status}</div>`;
    } else {
        const status = ['Menunggu Pembayaran', 'Lunas'];

        let html = `
        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_status_${data.id}">${data.status}</a>
        <div class="modal fade" id="modal_status_${data.id}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Status Pembayaran</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form_update_status_${data.id}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode</label>
                            <input type="text" class="form-control" value="${data.kode}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Customer</label>
                            <input type="text" class="form-control" value="${data.nama_customer}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Tagihan</label>
                            <input type="text" class="form-control" value="${formatRupiah(data.total_tagihan)}" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran">
                                <option value="">Pilih</option>
                                ${metode_pembayaran.map(item =>
                                `<option value="${item.nama}" ${item.nama == data.payment_channel ? 'selected' : ''}>${item.nama}</option>`
                                ).join('')}
                            </select>
                            <div class="invalid-feedback" id="invalid_metode_pembayaran"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            ${status.map(item =>
                            `<div class="form-check">
                                <input type="radio" class="form-check-input" id="${item}_${data.id}" name="status" value="${item}" ${item == data.status ? 'checked' : ''}>
                                <label class="form-check-label" for="${item}_${data.id}">${item}</label>
                            </div>`
                            ).join('')}
                            <div class="invalid-feedback" id="invalid_status"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>`;

        setTimeout(() => actionStatus(data.id), 0);
        return html;
    }
}

function actionStatus(id) {
    const form = dom(`#form_update_status_${id}`);

    if (! form.dataset.isInitialized) {
        form.dataset.isInitialized  = 'true';
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const endpoint = `<?= $base_api ?>update/${id}/status`;
            submitData(form, endpoint);
        });
    }
}

function renderNomorResi(data) {
    let klik_resi = 'Input Nomor Resi';
    if (data.nomor_resi) {
        klik_resi = `${data.nomor_resi}`;
    }

    let html = `
    <a href="#" data-bs-toggle="modal" data-bs-target="#modal_nomor_resi_${data.id}">${klik_resi}</a>
    ${(data.is_cetak_struk_alamat == 'ENABLE') ? '<br><i>struk alamat tercetak</i>' : ''}
    <div class="modal fade" id="modal_nomor_resi_${data.id}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Nomor Resi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form_update_nomor_resi_${data.id}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kurir</label>
                        <input type="text" class="form-control" value="${data.tarif_ongkir_name}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_resi" class="form-label">Nomor Resi</label>
                        <input type="text" class="form-control" id="nomor_resi" name="nomor_resi" value="${data.nomor_resi}" placeholder="Masukkan nomor resi">
                        <div class="invalid-feedback" id="invalid_nomor_resi"></div>
                    </div>
                    <div class="${data.status != 'Lunas' ? 'd-none' : ''}">
                        <a href="<?= $base_route ?>struk-alamat?id_pesanan=${data.id}" target="_blank" class="btn btn-primary">
                            Cetak Struk Alamat
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>`;

    setTimeout(() => actionNomorResi(data.id), 0);

    return html;
}

function actionNomorResi(id) {
    const form = dom(`#form_update_nomor_resi_${id}`);

    if (! form.dataset.isInitialized) {
        form.dataset.isInitialized = 'true';
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const endpoint = `<?= $base_api ?>update/${id}/nomor-resi`;
            submitData(form, endpoint);
        });
    }
}

function renderOpsi(data) {
    if (data.status != 'Lunas') {
        let endpoint_hapus_data = `<?= $base_api ?>delete/${data.id}`;
        return `
        <a onclick="deleteData('${endpoint_hapus_data}')" title="Delete">
            <i class="fa-regular fa-trash-can fa-lg text-danger"></i>
        </a>`;
    } else {
        return '';
    }
}
</script>

<script src="<?= base_url() ?>assets/modules/datatables/js/dataTables.min.js"></script>
