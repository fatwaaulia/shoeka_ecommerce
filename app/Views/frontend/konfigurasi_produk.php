<?php
$base_api = base_url('api/varian-produk');
?>

<script src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/modules/datatables/css/dataTables.dataTables.min.css">

<style>
/*--------------------------------------------------------------
  # Datatables
--------------------------------------------------------------*/
#dt-length-0 { margin-right: 8px; }
#dt-search-0 { margin-left: 8px; }

#myTable_wrapper > .dt-layout-row:first-child {
    margin-top: 0px!important;
}

table.dataTable,
table.dataTable th,
table.dataTable td {
    border: 1px solid #ddd!important;
}

.dt-layout-full { overflow: hidden; }

.dt-input {
    border-radius: var(--border-radius)!important;
    transition: .2s;
}

.dt-input:focus {
    border-color: var(--main-color)!important;
    box-shadow: var(--box-shadow-form-input)!important;
}

.dt-container input:focus,
.dt-container select:focus {
    border-color: var(--main-color);
    box-shadow: var(--box-shadow-form-input);
    outline: 0;
}

div.dt-container div.dt-layout-row { margin-bottom: 0!important; }

table.dataTable th.dt-type-numeric,
table.dataTable th.dt-type-date,
table.dataTable td.dt-type-numeric,
table.dataTable td.dt-type-date {
    text-align: center!important;
}

.dt-paging-button { min-width: 40px!important; }

div.dt-processing > div:last-child > div {
    background-color: var(--main-color)!important;
}
</style>

<body style="padding-top: 130px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <h5 class="text-center">
                <?= $kategori['nama'] ?>
                <?= $sub_kategori ? ' - ' . $sub_kategori['nama'] : '' ?>
                <?= $sub_sub_kategori ? ' - ' . $sub_sub_kategori['nama'] : '' ?>
            </h5>
        </div>
    </div>
    <a href="<?= base_url(userSession('slug_role')) ?>/dashboard" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali
    </a>
    <?php if ($sub_kategori || $sub_sub_kategori) : ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="display nowrap" id="myTable">
                        <thead class="bg-primary-subtle">
                            <tr>
                                <th>No.</th>
                                <th class="text-center">#</th>
                                <th>Kategori</th>
                                <th>Produk</th>
                                <th>Nama Varian</th>
                                <th>Gambar</th>
                                <th>Harga Pokok</th>
                                <th>Biaya Produk</th>
                                <th>Harga Ecommerce</th>
                                <th>Berat (gram)</th>
                                <th>SKU</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <button class="btn btn-primary" id="submit_checked_box">
                Simpan Konfigurasi
            </button>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    <?php if (!empty($array_id_varian_produk)) : ?>
    sessionStorage.setItem('checked_id', '<?= $array_id_varian_produk ?>');
    <?php else : ?>
    sessionStorage.removeItem('checked_id');
    <?php endif; ?>
});

function itemChecked(el) {
    let session_checked_id = JSON.parse(sessionStorage.getItem('checked_id')) || [];

    if (el.checked) {
        if (!session_checked_id.includes(el.value)) session_checked_id.push(el.value);
    } else {
        session_checked_id = session_checked_id.filter(id => id !== el.value);
    }

    // console.log(session_checked_id);

    sessionStorage.setItem('checked_id', JSON.stringify(session_checked_id));
}

dom('#submit_checked_box').addEventListener('click', async function(event) {
    const tombol_submit = dom('#submit_checked_box');
    let original_text = tombol_submit.innerHTML;
    tombol_submit.disabled = true;
    tombol_submit.style.width = tombol_submit.getBoundingClientRect().width + 'px';
    tombol_submit.innerHTML = `<div class="spinner-border spinner-border-sm"></div>`;

    const session_checked_id = JSON.parse(sessionStorage.getItem('checked_id')) || [];

    try {
        const response = await fetch('<?= $api_json_id_varian_produk ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ json_id_varian_produk: session_checked_id })
        });
        const data = await response.json();

        tombol_submit.disabled = false;
        tombol_submit.innerHTML = original_text;

        // console.log(data);
        // return;

        if (['success', 'error'].includes(data.status)) {
            await Swal.fire({
                icon: data.status,
                title: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
            if (data.status == 'success') {
                sessionStorage.removeItem('checked_id');
            }
            window.location.reload();
        } else {
            await Swal.fire({
                icon: 'error',
                title: data.message,
                showConfirmButton: false,
            });
        }
    } catch (error) {
        tombol_submit.disabled = false;
        tombol_submit.innerHTML = original_text;

        console.error(error);
        await Swal.fire({
            icon: 'error',
            title: 'Oops! Terjadi kesalahan',
            text: 'Silakan coba lagi nanti.',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#myTable', {
        ajax: '<?= base_url() ?>api/varian-produk',
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
                data: null,
                render: data => {
                    const session_checked_id = JSON.parse(sessionStorage.getItem('checked_id')) || [];
                    const is_checked = session_checked_id.includes(String(data.id)) ? 'checked' : '';
                    return `<input type="checkbox" class="form-check-input fa-lg" value="${data.id}" ${is_checked} onchange="itemChecked(this)" style="cursor:pointer;">`;
                }
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
                data: 'harga_ecommerce',
            }, {
                name: '',
                data: 'berat',
            }, {
                name: 'sku',
                data: 'sku',
            }, {
                name: '',
                data: 'stok',
            },
        ].map(col => ({ ...col, orderable: col.name !== '' })),
    });
});
</script>

<script src="<?= base_url() ?>assets/modules/datatables/js/dataTables.min.js"></script>

<style>
.btn-scroll,
.btn-scroll:focus {
    position: fixed;
    bottom: 2%;
    right: 2%;
    transition: .3s;
}
.btn-scroll-to-bottom:hover,
.btn-scroll-to-top:hover {
    bottom: 3%;
}
</style>

<div class="btn-scroll">
    <button class="btn btn-primary btn-scroll-to-top me-1" style="z-index:999;" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <button class="btn btn-primary btn-scroll-to-bottom" style="z-index:999;" onclick="window.scrollTo({top: document.body.scrollHeight, behavior: 'smooth'})">
        <i class="fa-solid fa-arrow-down"></i>
    </button>
</div>
