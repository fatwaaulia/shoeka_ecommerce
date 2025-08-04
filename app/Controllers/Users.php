<?php

namespace App\Controllers;

class Users extends BaseController
{
    protected $base_name;
    protected $model_name;
    protected $upload_path;

    public function __construct()
    {
        $this->base_name   = 'users';
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
            'title'      => 'Users',
        ];

        $view['content'] = view($this->base_name . '/main', $data);
        $view['sidebar'] = view('dashboard/sidebar', $data);
        return view('dashboard/header', $view);
    }

    public function new()
    {
        $data = [
            'base_api' => $this->base_api,
            'title'    => 'Add User',
        ];

        $view['sidebar'] = view('dashboard/sidebar');
        $view['content'] = view($this->base_name . '/new', $data);
        return view('dashboard/header', $view);
    }

    public function edit($id = null)
    {
        $data = [
            'base_api'  => $this->base_api,
            'base_name' => $this->base_name,
            'data'      => model($this->model_name)->find($id),
            'title'     => 'Edit User',
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
        $array_query_key = ['role'];

        if (array_intersect(array_keys($_GET), $array_query_key)) {
            $get_role = $this->request->getVar('role');
            if ($get_role) {
                $base_query->where('id_role', $get_role);
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
            $data[$key]['foto'] = webFile('image_user', $this->base_name, $v['foto'], $v['updated_at']);
            $data[$key]['password'] = '-';
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
        $id_role = $this->request->getVar('id_role');
        $role = model('Role')->find($id_role);
        if (in_array($id_role, [1, 2])) {
            $required_username = 'required';
            $required_email = 'permit_empty';
        } else {
            $required_username = 'permit_empty';
            $required_email = 'required';
        }

        $rules = [
            'id_role'  => [ 'label' => 'role', 'rules' => 'required' ],
            'nama'     => 'required',
            'username' => "$required_username|alpha_numeric|is_unique[$this->base_name.username]",
            'email'    => "$required_email|valid_email|is_unique[$this->base_name.email]",
            'password' => 'required|min_length[3]|matches[passconf]',
            'passconf' => 'required|min_length[3]|matches[password]',
            'foto'     => 'max_size[foto,2048]|ext_in[foto,png,jpg,jpeg]|mime_in[foto,image/png,image/jpeg]|is_image[foto]',
            'alamat'   => 'max_length[255]',
            'no_hp'    => 'permit_empty|numeric|min_length[10]|max_length[20]',
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
        $foto = $this->request->getFile('foto');
        if ($foto->isValid()) {
            $filename_foto = $foto->getRandomName();
            if ($foto->getExtension() != 'jpg') {
                $filename_foto = str_replace($foto->getExtension(), 'jpg', $filename_foto);
            }
            compressConvertImage($foto, $this->upload_path, $filename_foto);
        } else {
            $filename_foto = '';
        }

        $password = trim($this->request->getVar('password'));
        $data = [
            'id_role'       => $role['id'],
            'nama_role'     => $role['nama'],
            'slug_role'     => $role['slug'],
            'nama'          => ucwords($this->request->getVar('nama')),
            'username'      => strtolower($this->request->getVar('username')),
            'email'         => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
            'password'      => password_hash($password, PASSWORD_DEFAULT),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'foto'          => $filename_foto,
            'alamat'        => $this->request->getVar('alamat'),
            'no_hp'         => $this->request->getVar('no_hp'),
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

        $id_role = $this->request->getVar('id_role');
        $role = model('Role')->find($id_role);
        if (in_array($id_role, [1, 2])) {
            $required_username = 'required';
            $required_email = 'permit_empty';
        } else {
            $required_username = 'permit_empty';
            $required_email = 'required';
        }

        $rules = [
            'id_role'  => [ 'label' => 'role', 'rules' => 'required' ],
            'nama'     => 'required',
            'username' => "$required_username|alpha_numeric|is_unique[$this->base_name.username,id,$id]",
            'email'    => "$required_email|valid_email|is_unique[$this->base_name.email,id,$id]",
            'password' => 'permit_empty|min_length[3]|matches[passconf]',
            'passconf' => 'permit_empty|min_length[3]|matches[password]',
            'foto'     => 'max_size[foto,2048]|ext_in[foto,png,jpg,jpeg]|mime_in[foto,image/png,image/jpeg]|is_image[foto]',
            'alamat'   => 'max_length[255]',
            'no_hp'    => 'permit_empty|numeric|min_length[10]|max_length[20]',
        ];
        if (!$this->validate($rules)) {
            $errors = array_map(fn($error) => str_replace('_', ' ', $error), $this->validator->getErrors());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors'  => $errors,
            ]);
        }

        // Lolos Validasi
        $foto = $this->request->getFile('foto');
        if ($foto->isValid()) {
            $filename_foto = $find_data['foto'] ?: $foto->getRandomName();
            if ($foto->getExtension() != 'jpg') {
                $filename_foto = str_replace($foto->getExtension(), 'jpg', $filename_foto);
            }
            compressConvertImage($foto, $this->upload_path, $filename_foto);
        } else {
            $filename_foto = $find_data['foto'];
        }

        $password = trim($this->request->getVar('password'));
        $data = [
            'id_role'       => $role['id'],
            'nama_role'     => $role['nama'],
            'slug_role'     => $role['slug'],
            'nama'          => ucwords($this->request->getVar('nama')),
            'username'      => strtolower($this->request->getVar('username')),
            'email'         => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
            'password'      => $password != '' ? password_hash($password, PASSWORD_DEFAULT) : $find_data['password'],
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'foto'          => $filename_foto,
            'alamat'        => $this->request->getVar('alamat'),
            'no_hp'         => $this->request->getVar('no_hp'),
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

        $foto = $this->upload_path . $find_data['foto'];
        if (is_file($foto)) unlink($foto);

        model($this->model_name)->delete($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus',
        ]);
    }

    public function hapusFoto($id = null)
    {
        $find_data = model($this->model_name)->find($id);

        $foto = $this->upload_path . $find_data['foto'];
        if (is_file($foto)) unlink($foto);

        model($this->model_name)->update($id, ['foto' => '']);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Foto berhasil dihapus',
        ]);
    }
}
