<?php

namespace App\Models;

use CodeIgniter\Model;

class Customer extends Model
{
    protected $table         = 'customer';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
