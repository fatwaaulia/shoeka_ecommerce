<script src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/modules/datatables/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="<?= base_url() ?>assets/modules/dselect/dselect.min.css">
<script src="<?= base_url() ?>assets/modules/dselect/dselect.min.js"></script>

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
                    <div class="col-12 col-md-5 col-lg-4 col-xxl-3">
                        <select class="form-select" id="kategori" onchange="location = this.value;">
                            <option value="<?= current_url() ?>">Semua Produk</option>
                            <?php
                            $kategori = model('Kategori')->findAll();
                            foreach ($kategori as $v) :
                                $selected = (($_GET['kategori'] ?? '') == $v['nama']) ? 'selected' : '';
                            ?>
                            <option value="<?= current_url() . '?kategori=' . $v['nama'] ?>" <?= $selected ?>><?= $v['nama'] ?> - <?= $v['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <table class="display nowrap" id="myTable">
                    <thead class="bg-primary-subtle">
                        <tr>
                            <th>No.</th>
                            <th>Kategori</th>
                            <th>Produk</th>
                            <th>Nama Varian</th>
                            <th>Gambar</th>
                            <th>Harga Pokok</th>
                            <th>Biaya Produk</th>
                            <th>Harga Jual</th>
                            <th>SKU</th>
                            <th>Status</th>
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
        columns: [
            {
                name: '',
                data: 'no_urut',
            }, {
                name: '',
                data: 'nama_kategori',
            }, {
                name: '',
                data: 'nama_produk',
            }, {
                name: 'nama',
                data: 'nama',
            }, {
                name: '',
                data: null,
                render: data => `<img src="${data.gambar}" class="wh-40 cover-center" loading="lazy">`,
            }, {
                name: '',
                data: 'harga_pokok',
            }, {
                name: '',
                data: 'biaya_produk',
            }, {
                name: '',
                data: 'harga_jual',
            }, {
                name: 'sku',
                data: 'sku',
            }, {
                name: 'status',
                data: 'status',
            }, {
                name: '',
                data: null,
                render: renderOpsi,
            },
        ].map(col => ({ ...col, orderable: col.name !== '' })),
    });
});

function renderOpsi(data) {
    let endpoint_edit_data = `<?= $base_route ?>edit/${data.id}`;
    let endpoint_hapus_data = `<?= $base_api ?>delete/${data.id}`;
    return `
    <a href="#" class="me-2" title="Cetak Barcode" data-bs-toggle="modal" data-bs-target="#cetakBarcode${data.id}">
        <i class="fa-solid fa-barcode fa-lg text-success"></i>
    </a>
    <?php if (in_array(userSession('id_role'), [1, 2, 5])) : ?>
    <div class="modal fade" id="cetakBarcode${data.id}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Cetak Barcode</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= $base_route ?>cetak-barcode" method="get" target="_blank">
                    <div class="modal-body">
                        <input type="hidden" name="sku" value="${data.sku}">
                        <div class="mb-3">
                            <label class="form-label">Nama Varian</label>
                            <input type="text" class="form-control" value="${data.nama}" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" value="1" placeholder="Masukkan jumlah" required>
                            <div class="invalid-feedback" id="invalid_jumlah"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="${endpoint_edit_data}" class="me-2" title="Edit">
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>
    <a onclick="deleteData('${endpoint_hapus_data}')" title="Delete">
        <i class="fa-regular fa-trash-can fa-lg text-danger"></i>
    </a>
    <?php endif; ?>
    `;
}
</script>

<script src="<?= base_url() ?>assets/modules/datatables/js/dataTables.min.js"></script>

<!-------------------------------------------------------------
# Import Excel
-------------------------------------------------------------->
<script>
const form = document.getElementById('form');
const excel = document.getElementById('excel');

form.addEventListener('submit', event => event.preventDefault());

excel.addEventListener('change', () => {
    const endpoint    = '<?= $base_api ?>import-excel';
    const title       = 'Import Excel';
    const text        = 'Pastikan kolom excel sesuai template!';
    const button_text = 'Import Sekarang';
    submitDataWithConfirm(form, endpoint, title, text, button_text);
});
</script>