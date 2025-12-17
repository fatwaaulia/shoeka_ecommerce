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

        $stok_gudang = model('VarianProduk')->baseQuery()
        ->where('a.id', $id_varian_produk)
        ->get()->getRow()->stok;

        $keranjang = json_decode(session('keranjang'), true) ?? [];
        $qty_keranjang = array_column($keranjang, 'qty', 'id_varian_produk');
        $qty_keranjang = $qty_keranjang[$id_varian_produk] ?? null;

        if ($qty_keranjang) {
            $total_qty_keranjang = $qty_keranjang + $qty;
            if ($stok_gudang < $total_qty_keranjang) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Sisa stok ' . $stok_gudang,
                ]);
            }
        } else {
            if ($stok_gudang < $qty) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Sisa stok ' . $stok_gudang,
                ]);
            }
        }

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
        $is_stok_kurang = false;
        foreach ($keranjang_session as &$v) {
            $varian_produk = model('VarianProduk')->baseQuery()
                ->where('a.id', $v['id_varian_produk'])
                ->get()->getRow();

            $harga_ecommerce = $varian_produk->harga_ecommerce;
            $stok_gudang = $varian_produk->stok;

            if ($stok_gudang < $v['qty']) {
                $is_stok_kurang = true;
            }

            $qty_keranjang = min($stok_gudang, $v['qty']);

            $v['qty'] = $qty_keranjang;

            $total_qty_keranjang += $qty_keranjang;
            $total_belanja += $harga_ecommerce * $qty_keranjang;
        }
        unset($v);

        session()->set('keranjang', json_encode($keranjang_session));

        return $this->response->setStatusCode(200)->setJSON([
            'status'        => 'success',
            // 'data'          => $keranjang_session,
            'is_stok_kurang'   => $is_stok_kurang,
            'qty_keranjang'       => $qty_keranjang,
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
