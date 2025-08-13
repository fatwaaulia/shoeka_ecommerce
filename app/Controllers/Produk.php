<?php

namespace App\Controllers;

use Picqer\Barcode\BarcodeGeneratorPNG;

class Produk extends BaseController
{
    protected $base_name;
    protected $model_name;
    protected $upload_path;

    public function __construct()
    {
        $this->base_name   = 'produk';
        $this->model_name  = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->base_name)));
        $this->upload_path  = dirUpload() . $this->base_name . '/';
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

        $data            = $base_query;
        $records_total   = $base_query->countAllResults(false);
        $array_query_key = ['kategori'];

        if (array_intersect(array_keys($_GET), $array_query_key)) {
            $get_kategori = $this->request->getVar('kategori');
            if ($get_kategori) {
                $kategori = model('KategoriProduk')->select(['id', 'nama'])->where('nama', $get_kategori)->first();
                $base_query->where('id_kategori', $kategori['id']);
            }
        }

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
            $data[$key]['gambar'] = webFile('image', $this->base_name, $v['gambar'], $v['updated_at']);
            $data[$key]['created_at'] = date('d-m-Y H:i:s', strtotime($v['created_at']));
        }

        return $this->response->setStatusCode(200)->setJSON([
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $total_rows,
            'data'            => $data,
        ]);
    }
}
