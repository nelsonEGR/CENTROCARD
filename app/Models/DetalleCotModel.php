<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleCotModel extends Model
{
    protected $table      = 'detalle_cotizacion';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['precio', 'cantidad', 'id_cotizacion', 'id_producto'];
    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
