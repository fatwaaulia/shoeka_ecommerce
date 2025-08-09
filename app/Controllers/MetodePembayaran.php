<?php

namespace App\Controllers;

class MetodePembayaran extends BaseController
{
    protected $base_name;
    protected $model_name;

    public function __construct()
    {
        $this->base_name  = 'metode_pembayaran';
        $this->model_name = 'KasirMetodePembayaran';
    }

    /*--------------------------------------------------------------
    # API
    --------------------------------------------------------------*/
    public function index()
    {
        $data = model($this->model_name)->findAll();

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'data' => $data,
        ]);
    }
}
