<?php

namespace App\Controllers;

class Banner extends BaseController
{
    protected $base_name;
    protected $model_name;
    protected $upload_path;

    public function __construct()
    {
        $this->base_name   = 'banner';
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

    public function new()
    {
        $data = [
            'base_api' => $this->base_api,
            'title'    => 'Add ' . ucwords(str_replace('_', ' ', $this->base_name)),
        ];

        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view($this->base_name . '/new', $data);
        return view('dashboard/header', $view);
    }

    public function edit($id = null)
    {
        $find_data = model($this->model_name)->find($id);

        $data = [
            'base_api'  => $this->base_api,
            'base_name' => $this->base_name,
            'data'      => $find_data,
            'title'     => 'Edit ' . ucwords(str_replace('_', ' ', $this->base_name)),
        ];

        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view($this->base_name . '/edit', $data);
        return view('dashboard/header', $view);
    }

    /*--------------------------------------------------------------
    # API
    --------------------------------------------------------------*/
    public function index()
    {
        $select          = ['*'];
        $base_query      = model($this->model_name)->select($select);
        $limit           = (int)$this->request->getVar('length');
        $offset          = (int)$this->request->getVar('start');
        $records_total   = $base_query->countAllResults(false);

        // Datatables
        $columns = array_column($this->request->getVar('columns') ?? [], 'name');
        $search = $this->request->getVar('search')['value'] ?? null;
        dataTablesSearch($columns, $search, $select, $base_query);

        $order = $this->request->getVar('order')[0] ?? null;
        if (isset($order['column'], $order['dir']) && !empty($columns[$order['column']])) {
            $base_query->orderBy($columns[$order['column']], $order['dir'] === 'desc' ? 'desc' : 'asc');
        } else {
            $base_query->orderBy('urutan ASC');
        }
        // End | Datatables

        $total_rows = $base_query->countAllResults(false);
        $data       = $base_query->limit($limit, $offset)->get()->getResultArray();

        foreach ($data as $key => $v) {
            $data[$key]['no_urut'] = $offset + $key + 1;
            $data[$key]['gambar_desktop'] = webFile('image', $this->base_name, $v['gambar_desktop'], $v['updated_at']);
            $data[$key]['gambar_ponsel'] = webFile('image', $this->base_name, $v['gambar_ponsel'], $v['updated_at']);
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
            'judul'  => 'required',
            'gambar_desktop' => 'uploaded[gambar_desktop]|max_size[gambar_desktop,2048]|ext_in[gambar_desktop,png,jpg,jpeg]|mime_in[gambar_desktop,image/png,image/jpeg]|is_image[gambar_desktop]',
            'gambar_ponsel' => 'uploaded[gambar_ponsel]|max_size[gambar_ponsel,2048]|ext_in[gambar_ponsel,png,jpg,jpeg]|mime_in[gambar_ponsel,image/png,image/jpeg]|is_image[gambar_ponsel]',
            'tautan' => 'permit_empty|valid_url_strict',
            'urutan' => 'required',
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
        $gambar_desktop = $this->request->getFile('gambar_desktop');
        if ($gambar_desktop->isValid()) {
            $filename_gambar_desktop = $gambar_desktop->getRandomName();
            if ($gambar_desktop->getExtension() != 'jpg') {
                $filename_gambar_desktop = str_replace($gambar_desktop->getExtension(), 'jpg', $filename_gambar_desktop);
            }
            compressConvertImage($gambar_desktop, $this->upload_path, $filename_gambar_desktop);
        } else {
            $filename_gambar_desktop = '';
        }

        $gambar_ponsel = $this->request->getFile('gambar_ponsel');
        if ($gambar_ponsel->isValid()) {
            $filename_gambar_ponsel = $gambar_ponsel->getRandomName();
            if ($gambar_ponsel->getExtension() != 'jpg') {
                $filename_gambar_ponsel = str_replace($gambar_ponsel->getExtension(), 'jpg', $filename_gambar_ponsel);
            }
            compressConvertImage($gambar_ponsel, $this->upload_path, $filename_gambar_ponsel);
        } else {
            $filename_gambar_ponsel = '';
        }

        $data = [
            'gambar_desktop'  => $filename_gambar_desktop,
            'gambar_ponsel'  => $filename_gambar_ponsel,
            'judul'   => $this->request->getVar('judul'),
            'tautan'  => $this->request->getVar('tautan'),
            'urutan'  => $this->request->getVar('urutan'),
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
            'judul'  => 'required',
            'gambar_desktop' => 'max_size[gambar_desktop,2048]|ext_in[gambar_desktop,png,jpg,jpeg]|mime_in[gambar_desktop,image/png,image/jpeg]|is_image[gambar_desktop]',
            'gambar_ponsel' => 'max_size[gambar_ponsel,2048]|ext_in[gambar_ponsel,png,jpg,jpeg]|mime_in[gambar_ponsel,image/png,image/jpeg]|is_image[gambar_ponsel]',
            'tautan' => 'permit_empty|valid_url_strict',
            'urutan' => 'required',
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
        $gambar_desktop = $this->request->getFile('gambar_desktop');
        if ($gambar_desktop->isValid()) {
            $filename_gambar_desktop = $find_data['gambar_desktop'] ?: $gambar_desktop->getRandomName();
            if ($gambar_desktop->getExtension() != 'jpg') {
                $filename_gambar_desktop = str_replace($gambar_desktop->getExtension(), 'jpg', $filename_gambar_desktop);
            }
            compressConvertImage($gambar_desktop, $this->upload_path, $filename_gambar_desktop);
        } else {
            $filename_gambar_desktop = $find_data['gambar_desktop'];
        }

        $gambar_ponsel = $this->request->getFile('gambar_ponsel');
        if ($gambar_ponsel->isValid()) {
            $filename_gambar_ponsel = $find_data['gambar_ponsel'] ?: $gambar_ponsel->getRandomName();
            if ($gambar_ponsel->getExtension() != 'jpg') {
                $filename_gambar_ponsel = str_replace($gambar_ponsel->getExtension(), 'jpg', $filename_gambar_ponsel);
            }
            compressConvertImage($gambar_ponsel, $this->upload_path, $filename_gambar_ponsel);
        } else {
            $filename_gambar_ponsel = $find_data['gambar_ponsel'];
        }

        $data = [
            'gambar_desktop'  => $filename_gambar_desktop,
            'gambar_ponsel'  => $filename_gambar_ponsel,
            'judul'   => $this->request->getVar('judul'),
            'tautan'  => $this->request->getVar('tautan'),
            'urutan'  => $this->request->getVar('urutan'),
        ];

        model($this->model_name)->update($id, $data);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Perubahan disimpan',
            'route'   => $this->base_route,
        ]);
    }

    public function delete($id = null)
    {
        $find_data = model($this->model_name)->find($id);

        $gambar_desktop = $this->upload_path . $find_data['gambar_desktop'];
        if (is_file($gambar_desktop)) unlink($gambar_desktop);

        model($this->model_name)->delete($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }
}
