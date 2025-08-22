<?php

namespace App\Models;

use CodeIgniter\Model;

class VarianProduk extends Model
{
    protected $table         = 'varian_produk';
    protected $protectFields = false;
    protected $useTimestamps = true;

    public function baseQuery($get = false)
    {
        $id_warehouse = 1; // Outlet Offline

        $select = [
            'a.*',
            'b.id_warehouse',
            'IFNULL(b.stok_in, 0) AS stok_in',
            'IFNULL(b.stok_out, 0) AS stok_out',
            'IFNULL(b.stok_out_pos, 0) AS stok_out_pos',
            'IFNULL(b.stok, 0) AS stok',
        ];
        $base_query = db_connect()->table("varian_produk a")
        ->select($select)
        ->join('stok_konfig b', "b.id_warehouse = " . $id_warehouse . " AND b.id_varian_produk = a.id", 'left')
        ->orderBy('nama ASC');


        if ($get == 'select') {
            return $select;
        } else {
            return $base_query;
        }
    }
}
