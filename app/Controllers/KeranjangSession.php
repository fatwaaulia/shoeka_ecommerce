<?php

namespace App\Controllers;

class KeranjangSession extends BaseController
{
    public function create()
    {
        $keranjang_session = json_decode(session('keranjang'), true) ?? [];

        $id_varian_produk = decode($this->request->getVar('id_varian_produk'));
        $slug_varian_produk = $this->request->getVar('slug_varian_produk');
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
            'route'   => base_url() . 'detail-produk/' . $slug_varian_produk,
        ]);
    }

    public function update($id = null)
    {
        $keranjang_session = json_decode(session('keranjang'), true) ?? [];

        $tipe = $this->request->getVar('tipe');
        foreach ($keranjang_session as &$v) {
            if ($v['id_varian_produk'] == $id) {
                if ($tipe == 'increment') {
                    $v['qty'] += 1;
                }
                if ($tipe == 'decrement' && $v['qty'] > 1) {
                    $v['qty'] -= 1;
                }
                break;
            }
        }
        unset($v);

        $total_belanja = 0;
        foreach ($keranjang_session as $v) {
            $varian_produk = model('VarianProduk')->select('harga_ecommerce')->find($v['id_varian_produk']);
            $total_belanja += $varian_produk['harga_ecommerce'] * $v['qty'];
        }

        session()->set('keranjang', json_encode($keranjang_session));

        return $this->response->setStatusCode(200)->setJSON([
            'status'        => 'success',
            // 'data'          => $keranjang_session,
            'total_belanja' => $total_belanja,
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
