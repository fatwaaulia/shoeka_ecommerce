<div class="container-fluid">
    <div class="row">
        <div class="col-12 mt-4">
            <h4 class="fw-600">Selamat datang <?= implode(' ', array_slice(explode(' ', userSession('nama')), 0, 3)); ?>!</h4>
            <p>Akses cepat dan pengelolaan informasi secara efisien.</p>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 d-flex">
            <div class="card flex-fill mb-0">
                <div class="card-body text-center" style="border-bottom:4px solid var(--main-color); border-radius:var(--border-radius)">
                    <p class="fw-500 d-block mb-2">
                        <i class="fa-solid fa-user-group me-1"></i>
                        Total Transaksi
                    </p>
                    <?php $total_transaksi = model('Transaksi')->countAll() ?>
                    <h4 class="mb-0"><?= $total_transaksi ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>
