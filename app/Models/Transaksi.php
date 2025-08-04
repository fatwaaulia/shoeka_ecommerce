<?php

namespace App\Models;

use CodeIgniter\Model;

class Transaksi extends Model
{
    protected $table         = 'ecommerce_transaksi';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
