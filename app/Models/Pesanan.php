<?php

namespace App\Models;

use CodeIgniter\Model;

class Pesanan extends Model
{
    protected $table         = 'ecommerce_pesanan';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
