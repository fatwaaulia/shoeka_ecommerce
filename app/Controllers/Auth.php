<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $model_name;
    protected $app_settings;

    public function __construct()
    {
        $this->model_name   = 'Users';
        $this->app_settings = model('AppSettings')->find(1);
    }

    /*--------------------------------------------------------------
    # Front-End
    --------------------------------------------------------------*/
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }

    public function login()
    {
        if (session()->isLogin) return redirect()->to(base_url(userSession('slug_role')) . '/dashboard');

        $data['title'] = 'Login | ' . $this->app_settings['nama_aplikasi'];

        $view['content'] = view('auth/login', $data);
        return view('dashboard/header', $view);
    }

    /*--------------------------------------------------------------
    # API
    --------------------------------------------------------------*/
    public function loginProcess()
    {
        if (session()->isLogin) return redirect()->to(base_url(userSession('slug_role')) . '/dashboard');

        $rules = [
            'username'  => 'required',
            'password'  => 'required',
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
        $username = $this->request->getVar('username');
        $password = trim($this->request->getVar('password'));

        $user = model($this->model_name)
        ->select(['id', 'id_role', 'nama', 'username', 'password'])
        ->where('id_role', 1)
        ->where('username', $username)
        ->first();

        // Log Login
        $log = [
            'id_user'    => $user['id'] ?? '',
            'id_role'    => $user['id_role'] ?? '',
            'nama_user'  => $user['nama'] ?? '',
            'username'   => $user['username'] ?? $username,
            'ip_address' => $this->request->getIPAddress(),
        ];

        if ($user && password_verify($password, $user['password'])) {
            $log['status'] = 'Success';
            model('LogLogin')->insert($log);

            $session = [
                'isLogin'   => true,
                'id_user'   => $user['id'],
            ];
            session()->set($session);
            $role = model('Role')->where('id', $user['id_role'])->first();

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Login berhasil',
                'route'   => base_url($role['slug']) . '/dashboard',
            ]);
        }

        $log['status'] = 'Failed';
        model('LogLogin')->insert($log);

        return $this->response->setStatusCode(401)->setJSON([
            'status'  => 'error',
            'message' => 'Username atau password salah!',
        ]);
    }
}
