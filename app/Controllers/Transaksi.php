<?php

namespace App\Controllers;

class Transaksi extends BaseController
{
    protected $base_name;
    protected $model_name;

    public function __construct()
    {
        $this->base_name   = 'transaksi';
        $this->model_name  = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->base_name)));
    }

    /*--------------------------------------------------------------
    # API
    --------------------------------------------------------------*/
    public function create()
    {
        $rules = [
            'nama'      => 'required',
            'no_hp'     => 'required|permit_empty|numeric|min_length[10]|max_length[20]',
            'email'     => 'required|valid_email',
            'alamat'    => 'required|max_length[500]',
            'provinsi'  => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa'      => 'required',
            'kurir'     => 'required',
            'layanan_kurir' => 'required',
        ];
        if (! $this->validate($rules)) {
            $errors = array_map(fn($error) => str_replace('_', ' ', $error), $this->validator->getErrors());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors'  => $errors,
            ]);
        }

        // Lolos Validasi
        $destination = $this->request->getVar('desa');
        $kurir = $this->request->getVar('kurir');

        // Get API Tarif Ongkir
        $origin = 46740; // Sawojajar, Kec. Kedungkandang, Malang.
        // $destination = 31000; // Genteng Kulon, Kec. Genteng, Banyuwangi.
        $destination = $this->request->getVar('desa');
        $kurir = $this->request->getVar('kurir');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost?origin=$origin&destination=$destination&weight=500&courier=$kurir",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'key: 0kcuwsB2588e12617161919foW7jNt4W'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        // END | Get API Tarif Ongkir

        $keranjang_session = json_decode(session('keranjang'), true) ?? [];
        $array_id_varian_produk = array_column($keranjang_session, 'id_varian_produk');
        $total_berat = 0;
        $total_belanja = 0;

        $varian_produk = model('VarianProduk')->whereIn('id', $array_id_varian_produk)->findAll();
        foreach ($varian_produk as $v) {
            $total_harga_ecommerce = 0;
            foreach ($keranjang_session as $v2) {
                if ($v2['id_varian_produk'] === $v['id']) {
                    $qty = (int)$v2['qty'];
                    break;
                }
            }

            $total_berat += $v['berat'];
            $total_harga_ecommerce += ($v['harga_ecommerce'] * $qty);
            $total_belanja += $total_harga_ecommerce;
        }

        $index_layanan_kurir = $this->request->getVar('layanan_kurir');
        $tarif_ongkir = $response['data'][$index_layanan_kurir];

        $total_tagihan = (int)$total_belanja + (int)$tarif_ongkir['cost'];

        for (;;) {
            $random_string = 'INV' . strtoupper(random_string('alnum', 6));
            $cek_kode = model('Transaksi')->where('kode', $random_string)->countAllResults();
            if ($cek_kode == 0) {
                $kode = $random_string;
                break;
            }
        }

        $nama_customer    = $this->request->getVar('nama', FILTER_SANITIZE_SPECIAL_CHARS);
        $no_hp_customer   = $this->request->getVar('no_hp', FILTER_SANITIZE_SPECIAL_CHARS);
        $email_customer   = $this->request->getVar('email', FILTER_SANITIZE_SPECIAL_CHARS);
        $alamat_customer  = $this->request->getVar('alamat', FILTER_SANITIZE_SPECIAL_CHARS);
        $submit           = $this->request->getVar('submit', FILTER_SANITIZE_SPECIAL_CHARS);
        $detail_transaksi = base_url() . 'detail-transaksi?kode=' . $kode;

        if ($submit == 'va') {
            // Payment Gateway
            $invoice_data = [
                "external_id" => $kode,
                "amount"      => (int)$total_tagihan,
                "description" => "Invoice Demo #$kode",
                "invoice_duration" => 86400,
                "customer" => [
                    "given_names"   => $nama_customer,
                    "email"         => $email_customer,
                    "mobile_number" => $no_hp_customer,
                ],
                "success_redirect_url" => $detail_transaksi,
                "failure_redirect_url" => $detail_transaksi,
                "currency" => "IDR"
            ];
            $invoice_sent = json_encode($invoice_data);

            $api_key = 'xnd_development_Z745AIUbLnrvgz9JtyGSV8mF1UNarORVsj62mirDsKFHCDtsxrzgA9rcueAR9nd';
            $api_key_base64 = base64_encode($api_key);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.xendit.co/v2/invoices',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $invoice_sent,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Basic ' . $api_key_base64,
                ),
            ));

            $response_xendit = curl_exec($curl);
            curl_close($curl);
            $response_xendit = json_decode($response_xendit, true);
            // END | Payment Gateway

            $tipe_pembayaran = 'va';
        } else {
            $tipe_pembayaran = 'admin';
            $invoice_sent = '';
            $response_xendit = '';
        }

        $data = [
            'kode'             => $kode,
            'nama_customer'    => $nama_customer,
            'no_hp_customer'   => $no_hp_customer,
            'email_customer'   => $email_customer,
            'alamat_customer'  => $alamat_customer,

            'kode_provinsi'  => $this->request->getVar('provinsi'),
            'kode_kabupaten' => $this->request->getVar('kabupaten'),
            'kode_kecamatan' => $this->request->getVar('kecamatan'),
            'kode_desa'      => $this->request->getVar('desa'),

            'nama_provinsi'  => $this->request->getVar('nama_provinsi'),
            'nama_kabupaten' => $this->request->getVar('nama_kabupaten'),
            'nama_kecamatan' => $this->request->getVar('nama_kecamatan'),
            'nama_desa'      => $this->request->getVar('nama_desa'),

            'json_tarif_ongkir' => json_encode($tarif_ongkir),
            'tarif_ongkir_name' => $tarif_ongkir['name'],
            'tarif_ongkir_code' => $tarif_ongkir['code'],
            'tarif_ongkir_service' => $tarif_ongkir['service'],
            'tarif_ongkir_description' => $tarif_ongkir['description'],
            'tarif_ongkir_cost' => $tarif_ongkir['cost'],
            'tarif_ongkir_etd' => $tarif_ongkir['etd'],

            'total_berat'   => $total_berat,
            'total_belanja' => $total_belanja,
            'total_tagihan' => $total_tagihan,

            'tipe_pembayaran'  => $tipe_pembayaran,
            'status'           => 'Menunggu Pembayaran',
            'invoice_sent'     => $invoice_sent,
            'invoice_received' => json_encode($response_xendit),
            'invoice_url'      => $response_xendit['invoice_url'] ?? '',
            'invoice_id'       => $response_xendit['id'] ?? '',
            'invoice_status'   => $response_xendit['status'] ?? '',
            'expired_at'       => $response_xendit['expiry_date'] ?? null,
            'paid_at'          => null,
        ];
        model('Transaksi')->insert($data);
        $id_transaksi = model($this->model_name)->getInsertID();

        $data_item_transaksi = [];
        foreach ($varian_produk as $v) {
            $total_harga_ecommerce = 0;
            foreach ($keranjang_session as $v2) {
                if ($v2['id_varian_produk'] === $v['id']) {
                    $qty = (int)$v2['qty'];
                    break;
                }
            }

            $total_harga_ecommerce += $v['harga_ecommerce'] * $qty;
            $total_belanja += $total_harga_ecommerce;

            $data_item_transaksi[] = [
                'id_transaksi' => $id_transaksi,
                'kode_transaksi' => $kode,
                'id_varian_produk' => $v['id'],
                'sku_varian_produk' => $v['sku'],
                'nama_varian_produk' => $v['nama'],
                'harga_pokok_varian_produk' => $v['harga_pokok'],
                'harga_ecommerce' => $v['harga_ecommerce'],
                'berat' => $v['berat'],
                'qty' => $qty,
                'total_harga' => '',
                'nama_customer'    => $nama_customer,
                'no_hp_customer'   => $no_hp_customer,
                'email_customer'   => $email_customer,
                'alamat_customer'  => $alamat_customer,
            ];
        }
        model('ItemTransaksi')->insertBatch($data_item_transaksi);
        session()->remove('keranjang');

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Transaksi berhasil. Segera lakukan pembayaran.',
            'route'   => $detail_transaksi,
        ]);
    }
}
