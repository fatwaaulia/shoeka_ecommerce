<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class EnsureLogin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! userSession()) {
            return service('response')->setStatusCode(200)->setJSON([
                'status'  => 'error',
                'message' => 'Anda belum login!',
                'route'   => base_url('login'),
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
