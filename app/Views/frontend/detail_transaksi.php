<body style="padding-top: 121.88px;">

<section class="container">
    <div class="row">
        <div class="col-12">
            <h1>Invoice #<?= $data['kode'] ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table>
                <tr>
                    <td>Status</td>
                    <td>: <?= $data['status'] ?></td>
                </tr>
            </table>
            <?php if ($data['tipe_pembayaran'] == 'va') : ?>
            <a href="<?= $data['invoice_url'] ?>" target="_blank" class="btn btn-primary mt-3">Bayar Sekarang</a>
            <?php else : ?>
            <a href="https://wa.me/62082345566500" target="_blank" class="btn btn-primary mt-3">Hubungi Admin</a>
            <?php endif; ?>
        </div>
    </div>
</section>
