<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MinifyViewSource implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        //
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response_body = $response->getBody();
        $compressed_body = preg_replace('/(?<!http:|https:)\/\/.*$/m', '', $response_body); // Hapus komentar //
        $compressed_body = preg_replace('/\/\*[\s\S]*?\*\//', '', $compressed_body); // Hapus komentar /* */
        $compressed_body = preg_replace('/<!--[\s\S]*?-->/', '', $compressed_body); // Hapus komentar <!-- -->
        $compressed_body = preg_replace('/\s+/', ' ', $compressed_body); // Hapus spasi berlebihan
        $response->setBody($compressed_body);
    }
}
