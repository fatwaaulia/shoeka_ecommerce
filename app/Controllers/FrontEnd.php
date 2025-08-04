<?php

namespace App\Controllers;

class FrontEnd extends BaseController
{
    protected $app_settings;

    public function __construct()
    {
        if (strpos($_SERVER['HTTP_HOST'], 'www.') === 0) {
            $url = 'http://' . substr($_SERVER['HTTP_HOST'], 4) . $_SERVER['REQUEST_URI'];
            header('Location: ' . $url);
            exit();
        }

        // if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        //     $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //     header('Location: ' . $url);
        //     exit();
        // }

        $this->app_settings = model('AppSettings')->find(1);
    }

    public function beranda()
    {
        $kategori = model('Kategori')->where('slug', service('uri')->getSegment(2))->first();
        if (! $kategori) {
            $kategori = model('Kategori')->first();
        }

        $sub_kategori = model('SubKategori')->where('slug', $this->request->getVar('sub'))->first();
        $sub_sub_kategori = model('SubSubKategori')->where('slug', $this->request->getVar('sub_sub'))->first();

        $api_json_id_varian_produk = '';
        $array_id_varian_produk = [];
        if ($sub_sub_kategori) {
            $api_json_id_varian_produk = base_url() . 'api/sub-sub-kategori/update/' . $sub_sub_kategori['id'] . '/json-id-varian-produk';
            $array_id_varian_produk = $sub_sub_kategori['json_id_varian_produk'];
        } elseif ($sub_kategori) {
            $api_json_id_varian_produk = base_url() . 'api/sub-kategori/update/' . $sub_kategori['id'] . '/json-id-varian-produk';
            $array_id_varian_produk = $sub_kategori['json_id_varian_produk'];
        } else {
            $sub_kategori = model('SubKategori')->first();
            return redirect()->to(base_url() . 'koleksi?kategori=' . $sub_kategori['slug_kategori'] . '&sub=' . $sub_kategori['slug']);
        }

        $data = [
            'kategori'                  => $kategori,
            'sub_kategori'              => $sub_kategori,
            'sub_sub_kategori'          => $sub_sub_kategori,
            'api_json_id_varian_produk' => $api_json_id_varian_produk,
            'array_id_varian_produk'    => $array_id_varian_produk,
            'title'                     => $this->app_settings['nama_aplikasi'],
        ];

        $view['navbar'] = view('frontend/components/navbar');

        if ($this->request->getVar('config') == 'kelola_produk') {
            if (! userSession()) {
                return redirect()->to(base_url('login'));
            }
            $view['content'] = view('frontend/kelola_produk', $data);
        } else {
            $view['content'] = view('frontend/beranda', $data);
        }
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }

    public function koleksi()
    {
        return $this->beranda();
    }

    public function detailProduk($id)
    {
        $varian_produk = model('VarianProduk')->find($id);

        $data = [
            'data'  => $varian_produk,
            'title' => $varian_produk['nama'],
        ];

        $view['navbar'] = view('frontend/components/navbar');
        $view['content'] = view('frontend/detail_produk', $data);
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }

    public function keranjang()
    {
        $data['title'] = 'Keranjang';

        $view['navbar'] = view('frontend/components/navbar');
        $view['content'] = view('frontend/keranjang', $data);
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }

    public function keranjangSession()
    {
        $tipe = $this->request->getVar('tipe');

        if ($tipe == 'create') {
            $keranjang_session = json_decode(session('keranjang'), true) ?? [];

            $id_varian_produk = $this->request->getVar('id_varian_produk');
            $qty = $this->request->getVar('qty');
            $found = false;
            $keranjang = [];
            foreach ($keranjang_session as &$v) {
                if ($v['id_varian_produk'] == $id_varian_produk) {
                    $v['qty'] += (int)$qty;
                    $found = true;
                    break;
                }
            }
            unset($v);
    
            if (! $found) {
                $keranjang_session[] = [
                    'id_varian_produk' => $id_varian_produk,
                    'qty' => $qty
                ];
            }

            session()->set('keranjang', json_encode($keranjang_session));

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Berhasil masuk keranjang',
                'route'   => base_url() . 'detail-produk/' . $id_varian_produk,
            ]);
        }

        if ($tipe == 'delete') {
            $keranjang_session = json_decode(session('keranjang'), true) ?? [];
            $id_varian_produk = $this->request->getVar('id_varian_produk');

            foreach ($keranjang_session as $key => $item) {
                if ($item['id_varian_produk'] == $id_varian_produk) {
                    unset($keranjang_session[$key]);
                    $keranjang_session = array_values($keranjang_session);
                    break;
                }
            }

            session()->set('keranjang', json_encode($keranjang_session));

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Item berhasil dihapus',
                'route'   => base_url('keranjang'),
            ]);
        }
    }

    public function createInvoice()
    {
        for (;;) {
            $random_string = 'INV' . strtoupper(random_string('alnum', 6));
            $cek_kode = model('Transaksi')->where('kode', $random_string)->countAllResults();
            if ($cek_kode == 0) {
                $kode = $random_string;
                break;
            }
        }

        $kode = $kode;
        $total_tagihan = '150000';
        $nama_customer = 'fatwa aulia';
        $email_customer = 'fatwaaulia.fy@gmail.com';
        $no_hp_customer = '082345566500';
        $detail_transaksi = base_url() . 'detail-transaksi?kode=' . $kode;

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

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        $data = [
            'kode'             => $kode,
            'nama_customer'    => $nama_customer,
            'alamat_customer'  => 'Jl. Diponegoro',
            'no_hp_customer'   => $no_hp_customer,
            'email_customer'   => $email_customer,
            'invoice_sent'     => json_encode($invoice_sent, true),
            'invoice_received' => json_encode($response, true),
            'status'           => 'Menunggu Pembayaran',
            'invoice_url'      => $response['invoice_url'],
            'invoice_id'       => $response['id'],
            'invoice_status'   => $response['status'],
            'expired_at'       => $response['expiry_date'],
            'paid_at'          => null,
        ];
        model('Transaksi')->insert($data);
        session()->remove('keranjang');

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Transaksi berhasil. Segera lakukan pembayaran.',
            'route'   => $detail_transaksi,
        ]);
    }

    public function detailTransaksi()
    {
        $kode = $this->request->getVar('kode', FILTER_SANITIZE_SPECIAL_CHARS);
        $transaksi = model('Transaksi')->where('kode', $kode)->first();

        if (! $transaksi) {
            return redirect()->back()->with('message',
            '<script>
            Swal.fire({
                icon: "error",
                title: "Invoice tidak ditemukan!",
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
            </script>');
        }

        $data = [
            'data'  => $transaksi,
            'title' => 'Detail Transaksi #' . $transaksi['kode'],
        ];

        $view['navbar'] = view('frontend/components/navbar');
        $view['content'] = view('frontend/detail_transaksi', $data);
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }
}
