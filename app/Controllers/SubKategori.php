<?php

namespace App\Controllers;

class SubKategori extends BaseController
{
    protected $base_name;
    protected $model_name;
    protected $upload_path;

    public function __construct()
    {
        $this->base_name   = 'sub_kategori';
        $this->model_name  = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->base_name)));
        $this->upload_path = dirUpload() . $this->base_name . '/';
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

        // Datatables
        $columns = array_column($this->request->getVar('columns') ?? [], 'name');
        $search = $this->request->getVar('search')['value'] ?? null;
        dataTablesSearch($columns, $search, $select, $base_query);

        $order = $this->request->getVar('order')[0] ?? null;
        if (isset($order['column'], $order['dir']) && !empty($columns[$order['column']])) {
            $base_query->orderBy($columns[$order['column']], $order['dir'] === 'desc' ? 'desc' : 'asc');
        } else {
            $base_query->orderBy('id_kategori ASC');
            $base_query->orderBy('urutan ASC');
        }
        // End | Datatables

        $total_rows = $base_query->countAllResults(false);
        $data       = $base_query->findAll($limit, $offset);

        foreach ($data as $key => $v) {
            $data[$key]['no_urut'] = $offset + $key + 1;
            $data[$key]['gambar'] = webFile('image', $this->base_name, $v['gambar'], $v['updated_at']);
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
            'kategori' => 'required',
            'nama'     => 'required',
            'gambar'   => 'max_size[gambar,2048]|ext_in[gambar,png,jpg,jpeg]|mime_in[gambar,image/png,image/jpeg]|is_image[gambar]',
            'urutan'   => 'required',
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
        $gambar = $this->request->getFile('gambar');
        if ($gambar->isValid()) {
            $filename_gambar = $gambar->getRandomName();
            if ($gambar->getExtension() != 'jpg') {
                $filename_gambar = str_replace($gambar->getExtension(), 'jpg', $filename_gambar);
            }
            compressConvertImage($gambar, $this->upload_path, $filename_gambar);
        } else {
            $filename_gambar = '';
        }
        
        $kategori = model('Kategori')->find($this->request->getVar('kategori'));

        $nama = $this->request->getVar('nama');
        $slug = url_title($nama, '-', true);
        $cek_nama = model($this->model_name)->select('nama')->where('nama', $nama)->countAllResults();
        if ($cek_nama != 0) {
            $random_string = strtolower(random_string('alpha', 3));
            $slug = $slug . '-' . $random_string;
        }

        $data = [
            'id_kategori'   => $kategori['id'],
            'nama_kategori' => $kategori['nama'],
            'slug_kategori' => $kategori['slug'],
            'nama' => $nama,
            'slug' => $slug,
            'gambar' => $filename_gambar,
            'urutan' => $this->request->getVar('urutan'),
        ];

        model($this->model_name)->insert($data);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil ditambahkan',
            'route'   => $this->base_route,
        ]);
    }

    public function update($id = null)
    {
        $find_data = model($this->model_name)->find($id);

        $rules = [
            'kategori' => 'required',
            'nama'     => 'required',
            'gambar'   => 'max_size[gambar,2048]|ext_in[gambar,png,jpg,jpeg]|mime_in[gambar,image/png,image/jpeg]|is_image[gambar]',
            'urutan'   => 'required',
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
        $gambar = $this->request->getFile('gambar');
        if ($gambar->isValid()) {
            $filename_gambar = $find_data['gambar'] ?: $gambar->getRandomName();
            if ($gambar->getExtension() != 'jpg') {
                $filename_gambar = str_replace($gambar->getExtension(), 'jpg', $filename_gambar);
            }
            compressConvertImage($gambar, $this->upload_path, $filename_gambar);
        } else {
            $filename_gambar = $find_data['gambar'];
        }

        $kategori = model('Kategori')->find($this->request->getVar('kategori'));

        $nama = $this->request->getVar('nama');
        
        $data = [
            'id_kategori'   => $kategori['id'],
            'nama_kategori' => $kategori['nama'],
            'slug_kategori' => $kategori['slug'],
            'nama' => $nama,
            'gambar' => $filename_gambar,
            'urutan' => $this->request->getVar('urutan'),
        ];
        model($this->model_name)->update($id, $data);

        model('SubSubKategori')->set([
            'nama_kategori' => $kategori['nama'],
            'slug_kategori' => $kategori['slug'],
            'nama_sub_kategori' => $nama,
        ])->where('id_sub_kategori', $id)->update();

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Perubahan disimpan',
            'route'   => $this->base_route,
        ]);
    }

    public function updateJsonIdProduk($id = null)
    {
        $data = [
            'json_id_produk' => json_encode($this->request->getVar('json_id_produk')),
        ];
        model($this->model_name)->update($id, $data);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Konfigurasi produk disimpan',
        ]);
    }

    public function delete($id = null)
    {
        $find_data = model($this->model_name)->find($id);

        $gambar = $this->upload_path . $find_data['gambar'];
        if (is_file($gambar)) unlink($gambar);

        model($this->model_name)->delete($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
