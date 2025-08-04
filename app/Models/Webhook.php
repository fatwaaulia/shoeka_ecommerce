<?php

namespace App\Models;

use CodeIgniter\Model;

class Webhook extends Model
{
    protected $table         = 'ecommerce_webhook';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
