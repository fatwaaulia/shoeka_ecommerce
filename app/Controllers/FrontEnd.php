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
            'title'                     => $this->app_settings['nama_aplikasi'],
        ];

        $view['navbar'] = view('frontend/components/navbar');

        if ($this->request->getVar('config') == 'produk') {
            if (! userSession()) {
                return redirect()->to(base_url('login'));
            }
            $view['content'] = view('frontend/konfigurasi_produk', $data);
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
