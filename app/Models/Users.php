<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{
    protected $table         = 'users';
    protected $protectFields = false;
    protected $useTimestamps = true;
}
