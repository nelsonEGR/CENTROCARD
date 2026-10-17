<?php

namespace App\Controllers;

use App\Models\PermisosModel;
use App\Models\RolesModel;

class Roles extends BaseController
{
    protected $reglas, $roles, $permisos, $session;
    public function __construct()
    {
        helper(['form', 'function']);
        $this->roles = new RolesModel();
        $this->permisos = new PermisosModel();
        $this->session = session();
        $this->reglas = [
            'id'    => 'is_natural',
            //'id'    => 'is_natural_no_zero',
            'nombre' => [
                'rules' => 'required|is_unique[roles.nombre,id,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'is_unique' => 'El {field} debe ser único'
                ]
            ]
        ];
    }
    public function index()
    {
        if (!verificar('listar roles', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'roles.js';
        echo view("templates/header");
        echo view("roles/index", $data);
        echo view("templates/footer");
    }
    public function nuevo()
    {
        if (!verificar('crear rol', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['permisos'] = $this->permisos->findAll();
        echo view("templates/header");
        echo view("roles/nuevo", $data);
        echo view("templates/footer");
    }
    public function registrar()
    {
        if (!verificar('crear rol', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $permisos = (!empty($this->request->getVar('permisos'))) ? $this->request->getVar('permisos') : null;
            $listaPermisos = ($permisos != null) ? json_encode($permisos) : null;
            $data = $this->roles->save([
                'nombre' => $this->request->getVar('nombre'),
                'permisos' => $listaPermisos
            ]);
            return redirect()->to(base_url() . '/roles')->with('message', 'Rol registrado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['permisos'] = $this->permisos->findAll();
            echo view("templates/header");
            echo view("roles/nuevo", $data);
            echo view("templates/footer");
        }
    }
    public function listar()
    {
        if (verificar('listar roles', $this->session->permisos)) {
            $data = $this->roles->where('estado', 1)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                $data[$i]['acciones'] = '<div class"text-center">
                <a href="' . base_url("roles/editar/" . $data[$i]['id']) . '" class="btn btn-info"><i class="fas fa-edit"></i></a>
                <button class="btn btn-danger" onclick="eliminarRol(' . $data[$i]['id'] . ')"><i class="fas fa-trash-alt"></i></button>
            </div>';
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function editar($id)
    {
        if (!verificar('editar rol', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['permisos'] = $this->permisos->findAll();
        $data['rol'] = $this->roles->where('id', $id)->first();
        $permisos = [];
        if ($data['rol']['permisos'] != null) {
            $permisos = json_decode($data['rol']['permisos'], true);
        }
        $data['activos'] = $permisos;
        echo view("templates/header");
        echo view("roles/editar", $data);
        echo view("templates/footer");
    }
    public function actualizar()
    {
        if (!verificar('editar rol', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $permisos = (!empty($this->request->getVar('permisos'))) ? $this->request->getVar('permisos') : null;
            $listaPermisos = ($permisos != null) ? json_encode($permisos) : null;
            $data = $this->roles->update(
                $this->request->getVar('id'),
                [
                    'nombre' => $this->request->getVar('nombre'),
                    'permisos' => $listaPermisos
                ]
            );
            return redirect()->to(base_url() . '/roles')->with('message', 'Rol modificado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['permisos'] = $this->permisos->findAll();
            $data['rol'] = $this->roles->where('id', $this->request->getVar('id'))->first();
            $permisos = [];
            if ($data['rol']['permisos'] != null) {
                $permisos = json_decode($data['rol']['permisos'], true);
            }
            $data['activos'] = $permisos;
            echo view("templates/header");
            echo view("roles/editar", $data);
            echo view("templates/footer");
        }
    }
    public function eliminar($id)
    {
        if (!verificar('eliminar rol', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->roles->update($id, ['estado' => 0]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Rol eliminado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al eliminar');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
}
