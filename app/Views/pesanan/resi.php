<?php
$app_settings = model('AppSettings')->find(1);
$logo = webFile('image', 'app_settings', $app_settings['logo'], $app_settings['updated_at']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi - <?= $pesanan['kode'] ?></title>

    <link rel="stylesheet" href="<?= base_url() ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <style>
    @page { margin: 0; }
    body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, label {
        font-family: 'Calibry', monospace!important;
    }
    #struk {
        width: 80mm;
        font-size: 12px;
        padding: 6px;
        margin: 0;
    }

    @media print {
        .outside-invoice {
            display: none !important;
        }
    }

    .offset-double-dots {
        padding-left: 0.5rem;
        text-indent: -7px;
    }
    .offset-double-dots::first-line { text-indent: 0; }
    </style>
</head>

<script>
window.onload = function () {
    window.print();
    // window.onafterprint = function () {
    //     window.close();
    // };
};
</script>

<body class="d-flex justify-content-center">
    <div id="struk" class="my-3">
        <div class="text-center">
            <img src="<?= $logo ?>" class="w-25" alt="<?= $app_settings['nama_aplikasi'] ?>" title="<?= $app_settings['nama_aplikasi'] ?>" style="filter: brightness(0) saturate(100%) sepia(1) hue-rotate(170deg) saturate(600%) brightness(1.2);"> <br>
        </div>

        <p style="font-weight: 600;" class="mt-4">NO. RESI : <?= $pesanan['nomor_resi'] ?></p>

        <h6 style="font-weight: 600;">PENERIMA</h6>

        <p>
            <span style="font-weight: 600;">
            <?= $pesanan['nama_customer'] ?> (<?= $pesanan['no_hp_customer'] ?>)
            </span> <br>
            <?= $pesanan['alamat_customer'] ?> <br>
            <?= ucwords(strtolower($pesanan['nama_kecamatan'])) ?>, <?= ucwords(strtolower($pesanan['nama_kabupaten'])) ?>, <?= ucwords(strtolower($pesanan['nama_provinsi'])) ?>
        </p>

        <h6 style="font-weight: 600;" class="mt-4">PENGIRIM</h6>

        <p>
            <span style="font-weight: 600;">
            Shoeka Shoes (081390692727)
            </span> <br>
            Jl. Danau Maninjau Barat B2 / A 41 <br> Sawojajar - Malang
        </p>

        <table class="w-100">
            <tr>
                <td class="align-top">Berat</td>
                <td class="offset-double-dots">: <?= $pesanan['total_berat'] ?> gr</td>
            </tr>
            <tr>
                <td class="align-top">Layanan</td>
                <td class="offset-double-dots">: <?= $pesanan['tarif_ongkir_service'] ?> - <?= $pesanan['tarif_ongkir_name'] ?></td>
            </tr>
            <tr>
                <td class="align-top">Kode Pesanan</td>
                <td class="offset-double-dots">: <?= $pesanan['kode'] ?></td>
            </tr>
        </table>

        <div class="outside-invoice">
            <hr>
            <button class="btn btn-success w-100" onclick="window.print();">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" fill="#ddd">
                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/>
                </svg>
                <span>Cetak Resi</span>
            </button>
            <button class="btn btn-secondary w-100 mt-2" onclick="window.location.href='<?= $base_route ?>'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" fill="#ddd">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                </svg>
                <span>Kembali</span>
            </button>
        </div>
    </div>
</body>
</html>