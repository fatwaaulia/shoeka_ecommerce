<?php

namespace App\Models;

use CodeIgniter\Model;

class KasirMetodePembayaran extends Model
{
    protected $table         = 'metode_pembayaran';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
