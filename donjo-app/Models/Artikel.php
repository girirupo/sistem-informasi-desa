<?php

namespace App\Models;

use CodeIgniter\Model as CI_Model;

use yidas\Model;

class Artikel extends Model
{
    protected $table      = 'artikel';
    protected $primaryKey = 'id';
    protected $timestamps = false;
}
