<?php

namespace App\Controllers;

class Webhook extends BaseController
{
    public function doku()
    {
        $json     = file_get_contents('php://input');
        $response = json_decode($json, true);

        $data = [
            'input'      => json_encode($response, true),
            'invoice_id' => $response['order']['invoice_number'] ?? '',
            'kode'       => $response['order']['invoice_number'] ?? '',
        ];
        model('Webhook')->insert($data);

        if (isset($response['order']['invoice_number'])) {
            $pesanan = model('Pesanan')->where('invoice_id', $response['order']['invoice_number'])->first();

            if ($pesanan) {
                $api_key = 'SK-8wQdzxaAmPzxAfAQ1Ihw';

                $client_id   = 'BRN-0210-1756035403723';
                $request_id  = uniqid();
                $timestamp   = gmdate("Y-m-d\TH:i:s\Z");
                $target_path = '/orders/v1/status/' . $pesanan['invoice_id'];

                $signature_component =
                    "Client-Id:$client_id\n" .
                    "Request-Id:$request_id\n" .
                    "Request-Timestamp:$timestamp\n" .
                    "Request-Target:$target_path";
                $signature = base64_encode(hash_hmac('sha256', $signature_component, $api_key, true));

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api-sandbox.doku.com/orders/v1/status/' . $response['order']['invoice_number'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
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

                $response = json_decode($response, true);

                $status = 'Menunggu Pembayaran';
                if ($response['transaction']['status'] == 'PENDING') {
                    $status = 'Menunggu Pembayaran';
                } elseif ($response['transaction']['status'] == 'SUCCESS') {
                    $status = 'Lunas';
                } elseif ($response['transaction']['status'] == 'EXPIRED') {
                    $status = 'Kedaluwarsa';
                }

                $data_pesanan = [
                    'currency'        => '',
                    'bank_code'       => $response['acquirer']['id'] ?? '',
                    'payment_id'      => '',
                    'paid_amount'     => $response['order']['amount'] ?? '',
                    'merchant_name'   => '',
                    'payment_method'  => $response['acquirer']['name'] ?? '',
                    'payment_channel' => '',
                    'payment_destination' => '',

                    'status'         => $status,
                    'invoice_status' => $response['transaction']['status'],
                    'paid_at'        => ($response['transaction']['date'] ?? '') ? date('Y-m-d H:i:s', strtotime($response['transaction']['date'])) : null,
                ];

                // return $this->response->setStatusCode(200)->setJSON([
                //     'data' => $response,
                // ]);

                model('Pesanan')->update($pesanan['id'], $data_pesanan);

                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => 'success',
                    'message' => 'Webhook doku berhasil',
                ]);
                die;

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
                        'id_kasir'        => 0,
                        'nama_kasir'      => 'WEB',
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
                        'metode_pembayaran' => $response['acquirer']['name'] ?? '',
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
                            'metode_pembayaran' => $response['acquirer']['name'] ?? '',
                            'marketplace'       => 'WEB',
                            'id_stok'        => $id_stok,
                            'id_warehouse'   => $warehouse['id'],
                            'nama_warehouse' => $warehouse['nama'],
                            'id_kasir'   => 0,
                            'nama_kasir' => 'WEB',
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
                        $stok_out_pos = $stok_konfig['stok_out_pos'] + -abs($v['qty']);
                        $stok = $stok_in + $stok_out + $stok_out_pos;
                        $data_stok_konfig[] = [
                            'id'       => $stok_konfig['id'],
                            'stok_in'  => $stok_in,
                            'stok_out' => $stok_out,
                            'stok_out_pos' => $stok_out_pos,
                            'stok'     => $stok,
                        ];
                    }

                    model('KasirItemTransaksi')->insertBatch($data_item_transaksi);
                    model('KasirStok')->insertBatch($data_stok);
                    model('KasirStokKonfig')->updateBatch($data_stok_konfig, 'id');

                    // return $this->response->setStatusCode(200)->setJSON([
                    //     'status'  => 'success',
                    //     'pesanan' => $data_pesanan,
                    // 'data_transaksi' => $data_transaksi,
                    //     'data_item_transaksi' => $data_item_transaksi,
                    //     'data_stok'           => $data_stok,
                    //     'data_stok_konfig'    => $data_stok_konfig,
                    // ]);
                }
                // END - Proses Transaksi Kasir

                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => 'success',
                    'message' => 'Webhook doku berhasil',
                    'data'    => $response,
                ]);
            } else {
                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => 'error',
                    'message' => 'Transaksi tidak ditemukan!',
                    'data'    => $response,
                ]);
            }
        } else {
            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'error',
                'message' => 'Webhook doku gagal',
                'data'    => $response,
            ]);
        }
    }
}
