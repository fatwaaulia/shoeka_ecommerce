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
        $data['title'] = $this->app_settings['nama_aplikasi'];

        $view['navbar'] = view('frontend/components/navbar');
        $view['content'] = view('frontend/beranda', $data);
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }

    public function koleksi()
    {
        $kategori = model('Kategori')->where('slug', $_GET['kategori'] ?? '')->first();
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
        } elseif ($kategori) {
            $sub_json_id_varian_produk = model('SubKategori')->select('json_id_varian_produk')->where('slug_kategori', $kategori['slug'])->findAll();
            $array_sub_id_varian_produk = [];
            foreach ($sub_json_id_varian_produk as $v) {
                $ids = json_decode($v['json_id_varian_produk'], true);
                if (is_array($ids)) {
                    $array_sub_id_varian_produk = array_merge($array_sub_id_varian_produk, $ids);
                }
            }
            //    dd($array_sub_id_varian_produk);

            $sub_sub_json_id_varian_produk = model('SubSubKategori')->select('json_id_varian_produk')->where('slug_kategori', $kategori['slug'])->findAll();
            $array_sub_sub_id_varian_produk = [];
            foreach ($sub_sub_json_id_varian_produk as $v) {
                $ids = json_decode($v['json_id_varian_produk'], true);
                if (is_array($ids)) {
                    $array_sub_sub_id_varian_produk = array_merge($array_sub_sub_id_varian_produk, $ids);
                }
            }

            $array_id_varian_produk = array_unique(array_merge($array_sub_id_varian_produk, $array_sub_sub_id_varian_produk));
            $array_id_varian_produk = json_encode($array_id_varian_produk);
        }

        $data = [
            'kategori'                  => $kategori,
            'sub_kategori'              => $sub_kategori,
            'sub_sub_kategori'          => $sub_sub_kategori,
            'api_json_id_varian_produk' => $api_json_id_varian_produk,
            'array_id_varian_produk'    => $array_id_varian_produk,
            'title'                     => $kategori['nama'] . ($sub_kategori ? ' - ' . $sub_kategori['nama'] : ''),
        ];

        $view['navbar'] = view('frontend/components/navbar');

        if ($this->request->getVar('config') == 'produk') {
            if (! userSession()) {
                return redirect()->to(base_url('login'));
            }
            $view['content'] = view('frontend/konfigurasi_produk', $data);
        } else {
            $view['content'] = view('frontend/produk', $data);
        }
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }

    public function detailProduk($slug)
    {
        $varian_produk = model('VarianProduk')->baseQuery()->where('slug', $slug)->get()->getRowArray();

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

    public function checkout()
    {
        session()->set(['datetime' => date('Y-m-d H:i:s')]);

        $data['title'] = 'Checkout';

        $view['navbar'] = view('frontend/components/navbar');
        $view['content'] = view('frontend/checkout', $data);
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }

    public function detailPesanan()
    {
        session()->set(['datetime' => date('Y-m-d H:i:s')]);

        $kode = $this->request->getVar('kode', FILTER_SANITIZE_SPECIAL_CHARS);
        $pesanan = model('Pesanan')->where('kode', $kode)->first();

        if (! $pesanan) {
            return redirect()->back()->with('message',
            '<script>
            Swal.fire({
                icon: "error",
                title: "Pesanan tidak ditemukan!",
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
            </script>');
        }

        $item_pesanan = model('ItemPesanan')->where('id_pesanan', $pesanan['id'])->findAll();
        $data = [
            'data'  => $pesanan,
            'item_pesanan'  => $item_pesanan,
            'title' => 'Detail Pesanan #' . $pesanan['kode'],
        ];

        $view['navbar'] = view('frontend/components/navbar');
        $view['content'] = view('frontend/detail_pesanan', $data);
        $view['footer'] = view('frontend/components/footer');
        return view('frontend/header', $view);
    }
}
