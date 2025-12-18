<?php

namespace App\Controllers;

class Pesanan extends BaseController
{
    protected $base_name;
    protected $model_name;

    public function __construct()
    {
        $this->base_name   = 'pesanan';
        $this->model_name  = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->base_name)));
    }

    /*--------------------------------------------------------------
    # Front-End
    --------------------------------------------------------------*/
    public function main()
    {
        $query = $_SERVER['QUERY_STRING'] ? ('?' . $_SERVER['QUERY_STRING']) : '';
        $data = [
            'get_data'   => $this->base_api . $query,
            'base_route' => $this->base_route,
            'base_api'   => $this->base_api,
            'title'      => ucwords(str_replace('_', ' ', $this->base_name)),
        ];

        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view($this->base_name . '/main', $data);
        return view('dashboard/header', $view);
    }

    public function strukAlamat()
    {
        $id_pesanan = ($this->request->getVar('id_pesanan'));

        $pesanan = model('Pesanan')->find($id_pesanan);
        $item_pesanan = model('ItemPesanan')->where('id_pesanan', $id_pesanan)->findAll();

        model('Pesanan')->update($id_pesanan, ['is_cetak_struk_alamat' => 'ENABLE']);

        $data = [
            'base_route' => $this->base_route,
            'title'      => ucwords(str_replace('_', ' ', $this->base_name)),
            'pesanan'      => $pesanan,
            'item_pesanan' => $item_pesanan,
        ];

        return view($this->base_name . '/struk_alamat', $data);
    }

    /*--------------------------------------------------------------
    # API
    --------------------------------------------------------------*/
    public function index()
    {
        $select     = ['*'];
        $base_query = model($this->model_name)->select($select);
        $limit      = (int)$this->request->getVar('length');
        $offset     = (int)$this->request->getVar('start');
        $records_total = $base_query->countAllResults(false);

        $get_status = $this->request->getVar('status');
        if ($get_status) {
            $base_query->where('status', $get_status);
        }

        $get_tanggal_awal = $this->request->getVar('tanggal_awal');
        if ($get_tanggal_awal) {
            $base_query->where('DATE(created_at) >=', $get_tanggal_awal);
        }
        
        $get_tanggal_akhir = $this->request->getVar('tanggal_akhir');
        if ($get_tanggal_akhir) {
            $base_query->where('DATE(created_at) <=', $get_tanggal_akhir);
        }

        // Datatables
        $columns = array_column($this->request->getVar('columns') ?? [], 'name');
        $search = $this->request->getVar('search')['value'] ?? null;
        dataTablesSearch($columns, $search, $select, $base_query);

        $order = $this->request->getVar('order')[0] ?? null;
        if (isset($order['column'], $order['dir']) && !empty($columns[$order['column']])) {
            $base_query->orderBy($columns[$order['column']], $order['dir'] === 'desc' ? 'desc' : 'asc');
        } else {
            $base_query->orderBy('id DESC');
        }
        // End | Datatables

        $total_rows = $base_query->countAllResults(false);
        $data       = $base_query->findAll($limit, $offset);

        foreach ($data as $key => $v) {
            $data[$key]['no_urut'] = $offset + $key + 1;
            $data[$key]['created_at'] = date('d-m-Y H:i:s', strtotime($v['created_at']));
        }

        return $this->response->setStatusCode(200)->setJSON([
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $total_rows,
            'data'            => $data,
        ]);
    }

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

        $keranjang_session = json_decode(session('keranjang'), true) ?? [];
        $array_id_varian_produk = array_column($keranjang_session, 'id_varian_produk');
        $total_berat = 0;
        $total_belanja = 0;

        $varian_produk = model('VarianProduk')->whereIn('id', $array_id_varian_produk)->findAll();
        foreach ($varian_produk as $v) {
            foreach ($keranjang_session as $v2) {
                if ($v2['id_varian_produk'] === $v['id']) {
                    $qty = (int)$v2['qty'];
                    break;
                }
            }

            $total_berat += ($v['berat'] * $qty);
            $total_belanja += ($v['harga_ecommerce'] * $qty);
        }

        // Get API Tarif Ongkir
        $origin = 46740; // Sawojajar, Kec. Kedungkandang, Malang.
        $destination = $this->request->getVar('desa');
        $kurir = $this->request->getVar('kurir');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost?origin=$origin&destination=$destination&weight=$total_berat&courier=$kurir",
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

        $index_layanan_kurir = $this->request->getVar('layanan_kurir');
        $tarif_ongkir = $response['data'][$index_layanan_kurir];

        // Voucher Belanja
        $kode_voucher_belanja = trim($this->request->getVar('kode_voucher_belanja'));
        $voucher_belanja = model('VoucherBelanja')->where([
            'kode' => trim($kode_voucher_belanja),
            'periode_awal <='  => date('Y-m-d'),
            'periode_akhir >=' => date('Y-m-d'),
        ])
        ->first();

        if ($voucher_belanja) {
            if ($total_belanja < $voucher_belanja['minimal_belanja']) return;

            if ($voucher_belanja['jenis_diskon'] == 'Rp') {
                $diskon_belanja = $voucher_belanja['diskon'];

            } elseif ($voucher_belanja['jenis_diskon'] == '%') {
                $diskon_belanja = ($total_belanja * $voucher_belanja['diskon'] / 100);
            } else {
                $diskon_belanja = 0;
            }

            $id_voucher_belanja = $voucher_belanja['id'];
            $kode_voucher_belanja = $voucher_belanja['kode'];
            $diskon_voucher_belanja = $voucher_belanja['diskon'];
            $jenis_diskon_voucher_belanja = $voucher_belanja['jenis_diskon'];
            $minimal_belanja_voucher_belanja = $voucher_belanja['minimal_belanja'];
            $potongan_diskon = (0 - $diskon_belanja);
            $total_belanja = $total_belanja;
            $final_total_belanja = $total_belanja + $potongan_diskon;

        } else {
            $id_voucher_belanja = 0;
            $kode_voucher_belanja = '';
            $diskon_voucher_belanja = 0;
            $jenis_diskon_voucher_belanja = '';
            $minimal_belanja_voucher_belanja = 0;
            $potongan_diskon = 0;
            $total_belanja = $total_belanja;
            $final_total_belanja = $total_belanja;
        }
        // END | Voucher Belanja

        // Potongan Ongkir
        $potongan_ongkir = model('PotonganOngkir')->where([
            'periode_awal <='  => date('Y-m-d'),
            'periode_akhir >=' => date('Y-m-d'),
            'minimal_ongkir <=' => $tarif_ongkir['cost'],
        ])
        ->orderBy('periode_awal DESC')
        ->first();

        if ($potongan_ongkir) {
            $id_potongan_ongkir = $potongan_ongkir['id'];
            $potongan_ongkir = (0 - $potongan_ongkir['potongan']);
            if (abs($potongan_ongkir) >= $tarif_ongkir['cost']) {
                $potongan_ongkir = (0 - $tarif_ongkir['cost']);
            }
            $final_ongkir = $tarif_ongkir['cost'] + $potongan_ongkir;
        } else {
            $id_potongan_ongkir = 0;
            $potongan_ongkir = 0;
            $final_ongkir = $tarif_ongkir['cost'];
        }
        // END | Potongan Ongkir

        $total_tagihan = $final_total_belanja + $final_ongkir;

        for (;;) {
            $random_string = 'INV' . date('ymd') . strtoupper(random_string('alnum', 5));
            $cek_kode = model('Pesanan')->where('kode', $random_string)->countAllResults();
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
        $detail_pesanan = base_url() . 'detail-pesanan?kode=' . $kode;

        $invoice_duration = 3600;
        if ($submit == 'VA') {
            // Doku Payment Gateway
            $request_id  = uniqid();

            $invoice_data = [
                "order" => [
                    "amount"         => (int)$total_tagihan,
                    "invoice_number" => $request_id,
                    "currency"       => "IDR",
                    "callback_url"   => $detail_pesanan,
                ],
                "customer" => [
                    "name" => $nama_customer,
                    "email" => $email_customer,
                    "phone" => $no_hp_customer,
                ],
                "payment" => [
                    "payment_due_date" => 60,
                ],
            ];
            $invoice_sent = json_encode($invoice_data);

            // $api_key = 'SK-8wQdzxaAmPzxAfAQ1Ihw'; // Demo
            $api_key = 'SK-aFuKG4c6vkBuO1KNudHZ'; // Production

            // $client_id   = 'BRN-0210-1756035403723'; // Demo
            $client_id   = 'BRN-0217-1755940419110'; // Production
            $timestamp   = gmdate("Y-m-d\TH:i:s\Z");
            $target_path = '/checkout/v1/payment';

            $digest = base64_encode(hash('sha256', $invoice_sent, true));
            $signature_component =
                "Client-Id:$client_id\n" .
                "Request-Id:$request_id\n" .
                "Request-Timestamp:$timestamp\n" .
                "Request-Target:$target_path\n" .
                "Digest:$digest";
            $signature = base64_encode(hash_hmac('sha256', $signature_component, $api_key, true));

            $curl = curl_init();

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://api-sandbox.doku.com' . $target_path, // Demo
                CURLOPT_URL => 'https://api.doku.com' . $target_path, // Production
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $invoice_sent,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Client-Id: $client_id",
                    "Request-Id: $request_id",
                    "Request-Timestamp: $timestamp",
                    "Signature: HMACSHA256=$signature",
                ],
            ));

            $response_doku = curl_exec($curl);
            curl_close($curl);
            $response_doku = json_decode($response_doku, true)['response'];
            // END | Doku Payment Gateway

            $tipe_pembayaran = 'VA';
            $expired_at = date('Y-m-d H:i:s', strtotime($response_doku['payment']['expired_date']));
        } else {
            $tipe_pembayaran = 'Admin';
            $invoice_sent = '';
            $response_doku = '';
            $expired_at = date('Y-m-d H:i:s', time() + $invoice_duration);
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
            
            'id_potongan_ongkir' => $id_potongan_ongkir,
            'potongan_ongkir' => $potongan_ongkir,
            'final_ongkir' => $final_ongkir,

            'id_voucher_belanja' => $id_voucher_belanja,
            'kode_voucher_belanja' => $kode_voucher_belanja,
            'diskon_voucher_belanja' => $diskon_voucher_belanja,
            'jenis_diskon_voucher_belanja' => $jenis_diskon_voucher_belanja,
            'minimal_belanja_voucher_belanja' => $minimal_belanja_voucher_belanja,
            'potongan_diskon' => $potongan_diskon,
            'total_belanja' => $total_belanja,
            'final_total_belanja' => $final_total_belanja,

            'total_berat'   => $total_berat,

            'total_tagihan' => $total_tagihan,

            'tipe_pembayaran'  => $tipe_pembayaran,
            'status'           => 'Menunggu Pembayaran',
            'invoice_sent'     => $invoice_sent,
            'invoice_received' => json_encode($response_doku),
            'invoice_url'      => $response_doku['payment']['url'] ?? '',
            'invoice_id'       => $request_id ?? '',
            'invoice_status'   => '',
            'expired_at'       => $expired_at,
            'paid_at'          => null,
        ];

        model('Pesanan')->insert($data);
        $id_pesanan = model($this->model_name)->getInsertID();

        $data_item_pesanan = [];
        foreach ($varian_produk as $v) {
            $total_harga = 0;
            $total_berat = 0;
            foreach ($keranjang_session as $v2) {
                if ($v2['id_varian_produk'] === $v['id']) {
                    $qty = (int)$v2['qty'];
                    $total_harga = $v['harga_ecommerce'] * $qty;
                    $total_berat = $v['berat'] * $qty;
                    break;
                }
            }

            $data_item_pesanan[] = [
                'id_pesanan' => $id_pesanan,
                'kode_pesanan' => $kode,
                'id_kategori'   => $v['id_kategori'],
                'nama_kategori' => $v['nama_kategori'],
                'id_produk'     => $v['id_produk'],
                'nama_produk'   => $v['nama_produk'],
                'id_varian_produk' => $v['id'],
                'sku_varian_produk' => $v['sku'],
                'nama_varian_produk' => $v['nama'],
                'gambar_varian_produk' => $v['gambar'],
                'harga_pokok_varian_produk' => $v['harga_pokok'],
                'biaya_produk_varian_produk' => $v['biaya_produk'],
                'harga_ecommerce' => $v['harga_ecommerce'],
                'berat' => $v['berat'],
                'qty' => $qty,
                'total_harga' => $total_harga,
                'total_berat' => $total_berat,
                'nama_customer'    => $nama_customer,
                'no_hp_customer'   => $no_hp_customer,
                'email_customer'   => $email_customer,
                'alamat_customer'  => $alamat_customer,
            ];
        }

        model('ItemPesanan')->insertBatch($data_item_pesanan);
        session()->remove('keranjang');

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Pesanan berhasil. Segera lakukan pembayaran.',
            'route'   => $detail_pesanan,
        ]);
    }

    public function sinkronisasi($id = null)
    {
        $pesanan = model($this->model_name)->find($id);

        $invoice_data = [
            "order" => [
                "invoice_number" => $pesanan['invoice_id'],
            ],
        ];
        $invoice_sent = json_encode($invoice_data);

        // $api_key = 'SK-8wQdzxaAmPzxAfAQ1Ihw'; // Demo
        $api_key = 'SK-aFuKG4c6vkBuO1KNudHZ'; // Production

        // $client_id   = 'BRN-0210-1756035403723'; // Demo
        $client_id   = 'BRN-0217-1755940419110'; // Production
        $request_id  = uniqid();
        $timestamp   = gmdate("Y-m-d\TH:i:s\Z");
        $target_path = '/orders/v1/status/' . $pesanan['invoice_id'];

        $digest = base64_encode(hash('sha256', $invoice_sent, true));
        $signature_component =
            "Client-Id:$client_id\n" .
            "Request-Id:$request_id\n" .
            "Request-Timestamp:$timestamp\n" .
            "Request-Target:$target_path\n" .
            "Digest:$digest";
        $signature = base64_encode(hash_hmac('sha256', $signature_component, $api_key, true));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => base_url() . 'webhook/doku',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $invoice_sent,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Client-Id: $client_id",
                "Request-Id: $request_id",
                "Request-Timestamp: $timestamp",
                "Signature: HMACSHA256=$signature",
            ],
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // $response = json_decode($response,true);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Berhasil sinkronisasi pembayaran',
            'route'   => base_url() . 'detail-pesanan?kode=' . $pesanan['kode'],
        ]);
    }

    public function updateNomorResi($id = null)
    {
        $rules = [
            'nomor_resi' => 'required',
        ];
        if (! $this->validate($rules)) {
            $errors = array_map(fn($error) => str_replace('_', ' ', $error), $this->validator->getErrors());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors'  => $errors,
            ]);
        }

        $data = [
            'nomor_resi' => $this->request->getVar('nomor_resi'),
        ];
        model($this->model_name)->update($id, $data);
        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Nomor resi berhasil disimpan',
            'route'   => $this->base_route . 'struk-alamat?id_pesanan=' . $id,
        ]);
    }

    public function updateStatus($id = null)
    {
        $pesanan = model($this->model_name)->find($id);

        $rules = [
            'status'            => 'required',
            'metode_pembayaran' => 'required',
        ];
        if (! $this->validate($rules)) {
            $errors = array_map(fn($error) => str_replace('_', ' ', $error), $this->validator->getErrors());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors'  => $errors,
            ]);
        }

        $status = $this->request->getVar('status');
        $data = [
            'status'  => $status,
            'payment_channel' => $this->request->getVar('metode_pembayaran'),
            'paid_at' => $status == 'Lunas' ? date('Y-m-d H:i:s') : null,
        ];
        model($this->model_name)->update($id, $data);

        // Proses Transaksi Kasir
        $transaksi = model('KasirTransaksi')->where('id_pesanan', $pesanan['id'])->first();
        if ($status == 'Lunas' && !$transaksi) {
            $kode_transaksi_terakhir = model('KasirTransaksi')->select('kode')->orderBy('id DESC')->first()['kode'] ?? '';
            $tanggal_transaksi = substr($kode_transaksi_terakhir, 6, 6);
            $nomor_urut_transaksi = substr($kode_transaksi_terakhir, 12, 4);

            if ($tanggal_transaksi == date('ymd')) {
                $kode_transaksi = 'SHOEKA' . date('ymd') . str_pad($nomor_urut_transaksi + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $kode_transaksi = 'SHOEKA' . date('ymd') . str_pad(1, 4, '0', STR_PAD_LEFT);
            }

            $biaya_marketplace = 0;
            $total_penghasilan = $pesanan['total_tagihan'] - $biaya_marketplace;

            $warehouse = model('KasirWarehouse')->find(1); // Outlet Offline

            $data_customer = [
                'nama'          => $pesanan['nama_customer'],
                'jenis_kelamin' => '',
                'alamat'        => $pesanan['alamat_customer'],
                'no_hp'         => $pesanan['no_hp_customer'],
                'email'         => $pesanan['email_customer'],
            ];

            model('Customer')->insert($data_customer);
            $id_customer = model('Customer')->getInsertID();

            $data_transaksi = [
                'id_pesanan' => $pesanan['id'],
                'kode'          => $kode_transaksi,
                'id_customer'   => $id_customer,
                'nama_customer' => $pesanan['nama_customer'],
                'jenis_kelamin_customer' => '',
                'alamat_customer' => $pesanan['alamat_customer'] . ' - ' . ucwords(strtolower($pesanan['nama_kecamatan'])) . ', ' . ucwords(strtolower($pesanan['nama_kabupaten'])) . ', ' . ucwords(strtolower($pesanan['nama_provinsi'])),
                'no_hp_customer'  => $pesanan['no_hp_customer'],
                'email_customer'  => $pesanan['email_customer'],
                'id_warehouse'    => $warehouse['id'],
                'nama_warehouse'  => $warehouse['nama'],
                'id_kasir'   => userSession('id'),
                'nama_kasir' => userSession('nama'),
                'order_id'          => '',
                'biaya_marketplace' => $biaya_marketplace,
                'total_belanja'   => $pesanan['total_belanja'],
                'ongkir'          => $pesanan['final_ongkir'],
                'diskon'          => $pesanan['diskon_voucher_belanja'],
                'jenis_diskon'    => $pesanan['jenis_diskon_voucher_belanja'],
                'potongan_diskon' => $pesanan['potongan_diskon'],
                'total_tagihan'   => $pesanan['total_tagihan'],
                'jumlah_bayar'    => $pesanan['paid_amount'],
                'kembalian'       => 0,
                'metode_pembayaran' => $this->request->getVar('metode_pembayaran'),
                'marketplace'       => 'WEB',
                'total_penghasilan' => $total_penghasilan,
            ];

            model('KasirTransaksi')->insert($data_transaksi);
            $id_transaksi_kasir = model('KasirTransaksi')->getInsertID();

            $keranjang = model('ItemPesanan')->where('id_pesanan', $pesanan['id'])->findAll();

            $data_item_transaksi = [];
            $data_stok = [];
            $data_stok_konfig = [];
            $id_stok = model('KasirStok')->select('id')->orderBy('id DESC')->first()['id'];
            foreach ($keranjang as $v) {
                if ($v['qty'] == 0) continue;
                $id_stok += 1;
                $data_item_transaksi[] = [
                    'id_pesanan' => $pesanan['id'],
                    'id_transaksi'   => $id_transaksi_kasir,
                    'kode_transaksi' => $kode_transaksi,
                    'metode_pembayaran' => $this->request->getVar('metode_pembayaran'),
                    'marketplace'       => 'WEB',
                    'id_stok'        => $id_stok,
                    'id_warehouse'   => $warehouse['id'],
                    'nama_warehouse' => $warehouse['nama'],
                    'id_kasir'   => userSession('id'),
                    'nama_kasir' => userSession('nama'),
                    'order_id'   => '',
                    'id_kategori'   => $v['id_kategori'],
                    'nama_kategori' => $v['nama_kategori'],
                    'id_produk'     => $v['id_produk'],
                    'nama_produk'   => $v['nama_produk'],
                    'id_varian_produk'   => $v['id_varian_produk'],
                    'sku_varian_produk'  => $v['sku_varian_produk'],
                    'nama_varian_produk' => $v['nama_varian_produk'],
                    'harga_pokok_varian_produk' => $v['harga_pokok_varian_produk'],
                    'biaya_produk_varian_produk' => $v['biaya_produk_varian_produk'],
                    'harga_satuan' => $v['harga_ecommerce'],
                    'qty'          => $v['qty'],
                    'total_harga'  => $v['total_harga'],
                    'id_customer'   => $id_customer,
                    'nama_customer' => $pesanan['nama_customer'],
                    'jenis_kelamin_customer' => '',
                    'alamat_customer' => $pesanan['alamat_customer'] . ' - ' . ucwords(strtolower($pesanan['nama_kecamatan'])) . ', ' . ucwords(strtolower($pesanan['nama_kabupaten'])) . ', ' . ucwords(strtolower($pesanan['nama_provinsi'])),
                    'no_hp_customer'  => $pesanan['no_hp_customer'],
                    'email_customer'  => $pesanan['email_customer'],
                ];

                $data_stok[] = [
                    'id_warehouse'   => $warehouse['id'],
                    'nama_warehouse' => $warehouse['nama'],
                    'tipe'         => 'KELUAR',
                    'sub_tipe'     => 'WEB',
                    'id_varian_produk'   => $v['id_varian_produk'],
                    'sku_varian_produk'  => $v['sku_varian_produk'],
                    'nama_varian_produk' => $v['nama_varian_produk'],
                    'qty'          => -abs($v['qty']),
                    'catatan'      => '',
                    'tanggal'      => date('Y-m-d H:i:s'),
                    'created_by'   => 0,
                ];

                $stok_konfig = model('KasirStokKonfig')->where([
                    'id_warehouse' => $warehouse['id'],
                    'id_varian_produk' => $v['id_varian_produk']
                ])->first();
                $stok_in = $stok_konfig['stok_in'];
                $stok_out = $stok_konfig['stok_out'];
                $stok_out_pos = $stok_konfig['stok_out_pos'];
                $stok_out_web = $stok_konfig['stok_out_web'] + -abs($v['qty']);
                $stok = $stok_in + $stok_out + $stok_out_pos + $stok_out_web;
                $data_stok_konfig[] = [
                    'id'           => $stok_konfig['id'],
                    'stok_in'      => $stok_in,
                    'stok_out'     => $stok_out,
                    'stok_out_pos' => $stok_out_pos,
                    'stok_out_web' => $stok_out_web,
                    'stok'         => $stok,
                ];
            }

            model('KasirItemTransaksi')->insertBatch($data_item_transaksi);
            model('KasirStok')->insertBatch($data_stok);
            model('KasirStokKonfig')->updateBatch($data_stok_konfig, 'id');

            // return $this->response->setStatusCode(200)->setJSON([
            //     'status'  => 'success',
            //     'pesanan' => $data,
            //     'data_transaksi' => $data_transaksi,
            //     'data_item_transaksi' => $data_item_transaksi,
            //     'data_stok'           => $data_stok,
            //     'data_stok_konfig'    => $data_stok_konfig,
            // ]);
        }
        // END - Proses Transaksi Kasir

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Ubah status berhasil',
            'route'   => $this->base_route,
        ]);
    }

    public function delete($id = null)
    {
        model($this->model_name)->delete($id);
        model('ItemPesanan')->where('id_pesanan', $id)->delete();

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
