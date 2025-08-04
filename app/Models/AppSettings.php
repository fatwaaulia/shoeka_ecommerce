<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettings extends Model
{
    protected $table         = 'app_settings';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
