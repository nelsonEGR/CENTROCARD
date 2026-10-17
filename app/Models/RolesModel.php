<?php 
namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model{
    protected $table      = 'roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['nombre', 'permisos', 'estado'];
    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}