<?php

namespace App\Models;

use CodeIgniter\Model;

class Keranjang extends Model
{
    protected $table         = 'ecommerce_keranjang';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
