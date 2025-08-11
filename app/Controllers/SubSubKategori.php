<?php

namespace App\Controllers;

class SubSubKategori extends BaseController
{
    protected $base_name;
    protected $model_name;

    public function __construct()
    {
        $this->base_name   = 'sub_sub_kategori';
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
            'sub_kategori' => 'required',
            'nama' => 'required',
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
        $sub_kategori = model('SubKategori')->find($this->request->getVar('sub_kategori'));

        $nama = $this->request->getVar('nama');
        $slug = url_title($nama, '-', true);
        $cek_nama = model($this->model_name)->select('nama')->where('nama', $nama)->countAllResults();
        if ($cek_nama != 0) {
            $random_string = strtolower(random_string('alpha', 3));
            $slug = $slug . '-' . $random_string;
        }

        $data = [
            'id_kategori'   => $sub_kategori['id_kategori'],
            'nama_kategori' => $sub_kategori['nama_kategori'],
            'slug_kategori' => $sub_kategori['slug_kategori'],
            'id_sub_kategori'   => $sub_kategori['id'],
            'nama_sub_kategori' => $sub_kategori['nama'],
            'slug_sub_kategori' => $sub_kategori['slug'],
            'nama' => $nama,
            'slug' => $slug,
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
        $rules = [
            'sub_kategori' => 'required',
            'nama' => 'required',
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
        $sub_kategori = model('SubKategori')->find($this->request->getVar('sub_kategori'));

        $nama = $this->request->getVar('nama');
        $slug = url_title($nama, '-', true);
        $cek_nama = model($this->model_name)->select('nama')->where('nama', $nama)->countAllResults();
        if ($cek_nama != 0) {
            $random_string = strtolower(random_string('alpha', 3));
            $slug = $slug . '-' . $random_string;
        }

        $data = [
            'id_kategori'   => $sub_kategori['id_kategori'],
            'nama_kategori' => $sub_kategori['nama_kategori'],
            'slug_kategori' => $sub_kategori['slug_kategori'],
            'id_sub_kategori'   => $sub_kategori['id'],
            'nama_sub_kategori' => $sub_kategori['nama'],
            'slug_sub_kategori' => $sub_kategori['slug'],
            'nama' => $nama,
            'slug' => $slug,
        ];

        model($this->model_name)->update($id, $data);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Perubahan disimpan',
            'route'   => $this->base_route,
        ]);
    }

    public function updateJsonIdVarianProduk($id = null)
    {
        $data = [
            'json_id_varian_produk' => json_encode($this->request->getVar('json_id_varian_produk')),
        ];
        model($this->model_name)->update($id, $data);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Konfigurasi produk disimpan',
        ]);
    }

    public function delete($id = null)
    {
        model($this->model_name)->delete($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
