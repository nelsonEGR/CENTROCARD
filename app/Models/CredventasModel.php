<?php

namespace App\Models;

use CodeIgniter\Model;

class CredventasModel extends Model
{
    protected $table      = 'credventas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['abono', 'monto', 'estado', 'fecha', 'id_venta'];
    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
