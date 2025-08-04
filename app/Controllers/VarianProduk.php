<?php

namespace App\Controllers;

class VarianProduk extends BaseController
{
    protected $base_name;
    protected $model_name;
    protected $upload_path;

    public function __construct()
    {
        $this->base_name   = 'varian_produk';
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
        $array_query_key = ['produk'];

        if (array_intersect(array_keys($_GET), $array_query_key)) {
            $get_produk = $this->request->getVar('produk');
            if ($get_produk) {
                $produk = model('Produk')->select(['id', 'nama'])->where('nama', $get_produk)->first();
                $base_query->where('a.id_produk', $produk['id']);
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
        $data       = $base_query->limit($limit, $offset)->get()->getResultArray();

        foreach ($data as $key => $v) {
            $data[$key]['no_urut'] = $offset + $key + 1;
            $data[$key]['gambar'] = webFile('image', $this->base_name, $v['gambar'], $v['updated_at']);
            $data[$key]['harga_pokok'] = formatRupiah($v['harga_pokok']);
            $data[$key]['biaya_produk'] = formatRupiah($v['biaya_produk']);
            $data[$key]['harga_jual'] = formatRupiah($v['harga_jual']);
            $data[$key]['created_at'] = date('d-m-Y H:i:s', strtotime($v['created_at']));
        }

        return $this->response->setStatusCode(200)->setJSON([
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $total_rows,
            'data'            => $data,
        ]);
    }
}
