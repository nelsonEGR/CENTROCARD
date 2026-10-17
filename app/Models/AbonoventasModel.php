<?php

namespace App\Models;

use CodeIgniter\Model;

class AbonoventasModel extends Model
{
    protected $table      = 'pagosventas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['abono', 'fecha', 'id_credito', 'id_caja', 'caja'];
    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
