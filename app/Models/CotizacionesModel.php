<?php

namespace App\Models;

use CodeIgniter\Model;

class CotizacionesModel extends Model
{
    protected $table      = 'cotizaciones';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['fecha', 'total', 'id_usuario', 'id_cliente'];
    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
