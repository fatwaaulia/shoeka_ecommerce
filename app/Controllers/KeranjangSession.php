<?php

namespace App\Controllers;

class KeranjangSession extends BaseController
{
    public function create()
    {
        $rules = [
            'varian_produk' => 'required',
            'qty' => 'required',
        ];
        if (! $this->validate($rules)) {
            $errors = array_map(fn($error) => str_replace('_', ' ', $error), $this->validator->getErrors());

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Data yang dimasukkan tidak valid!',
                'errors'  => $errors,
            ]);
        }

        $keranjang_session = json_decode(session('keranjang'), true) ?? [];

        $id_varian_produk = decode($this->request->getVar('varian_produk'));
        $route = $this->request->getVar('route');
        $qty = $this->request->getVar('qty');
        $found = false;
        foreach ($keranjang_session as &$v) {
            if ($v['id_varian_produk'] == $id_varian_produk) {
                $v['qty'] += (int)$qty;
                $found = true;
                break;
            }
        }
        unset($v);

        if (! $found) {
            $keranjang_session[] = [
                'id_varian_produk' => $id_varian_produk,
                'qty' => $qty
            ];
        }

        session()->set('keranjang', json_encode($keranjang_session));

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Berhasil masuk keranjang',
            'route'   => $route,
        ]);
    }

    public function update($id = null)
    {
        $keranjang_session = json_decode(session('keranjang'), true) ?? [];

        $tipe = $this->request->getVar('tipe');
        $qty = $this->request->getVar('qty');
        foreach ($keranjang_session as &$v) {
            if ($v['id_varian_produk'] == $id) {
                if ($tipe == 'increment') {
                    $v['qty'] += 1;
                }
                if ($tipe == 'decrement' && $v['qty'] > 1) {
                    $v['qty'] -= 1;
                }
                if ($tipe == 'input' && $v['qty'] >= 1) {
                    $v['qty'] = $qty;
                }
                break;
            }
        }
        unset($v);

        $total_qty_keranjang = 0;
        $total_belanja = 0;
        foreach ($keranjang_session as $v) {
            $varian_produk = model('VarianProduk')->select('harga_ecommerce')->find($v['id_varian_produk']);
            $total_qty_keranjang += $v['qty'];
            $total_belanja += $varian_produk['harga_ecommerce'] * $v['qty'];
        }

        session()->set('keranjang', json_encode($keranjang_session));

        return $this->response->setStatusCode(200)->setJSON([
            'status'        => 'success',
            // 'data'          => $keranjang_session,
            'total_belanja'       => $total_belanja,
            'total_qty_keranjang' => $total_qty_keranjang,
        ]);
    }

    public function delete($id = null)
    {
        $keranjang_session = json_decode(session('keranjang'), true) ?? [];

        foreach ($keranjang_session as $key => $item) {
            if ($item['id_varian_produk'] == $id) {
                unset($keranjang_session[$key]);
                $keranjang_session = array_values($keranjang_session);
                break;
            }
        }

        session()->set('keranjang', json_encode($keranjang_session));

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Item berhasil dihapus',
            'route'   => base_url('keranjang'),
        ]);
    }
}
