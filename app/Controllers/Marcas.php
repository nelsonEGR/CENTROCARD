<?php

namespace App\Controllers;

use App\Models\MarcasModel;

class Marcas extends BaseController
{
    protected $reglas, $marcas, $session;
    public function __construct()
    {
        helper(['form']);
        $this->marcas = new MarcasModel();
        $this->session = session();
        $this->reglas = [
            'id'    => 'is_natural',
            'marca' => [
                'rules' => 'required|is_unique[marcas.marca,idmarca,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'is_unique' => 'El {field} debe ser único'

                ]
            ]
        ];
    }
    public function index()
    {
        if (!verificar('listar marcas', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'marcas.js';
        echo view("templates/header");
        echo view("marcas/index", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar marcas', $this->session->permisos)) {
            $data = $this->marcas->where('estado', 1)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                <a href="' . base_url("marcas/editar/" . $data[$i]['idmarca']) . '" class="btn btn-dark"><i class="fas fa-edit"></i></a>
                <button class="btn btn-danger" onclick="btnEliminarMarca(' . $data[$i]['idmarca'] . ')"><i class="fas fa-trash-alt"></i></button>
                </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function registrar()
    {
        if (!verificar('crear marca', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $marca = $this->request->getVar('marca');
            $data = $this->marcas->save([
                'marca' => $marca
            ]);
            return redirect()->to(base_url() . '/marcas')->with('message', 'Marca registrado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            echo view("templates/header");
            echo view("marcas/nuevo", $data);
            echo view("templates/footer");
        }
    }
    public function nuevo()
    {
        if (!verificar('crear marca', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        echo view("templates/header");
        echo view("marcas/nuevo");
        echo view("templates/footer");
    }
    public function editar($id)
    {
        if (!verificar('editar marca', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['marca'] = $this->marcas->where('idmarca', $id)->first();
        echo view("templates/header");
        echo view("marcas/editar", $data);
        echo view("templates/footer");
    }
    public function actualizar()
    {
        if (!verificar('editar marca', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $data = $this->marcas->update(
                $this->request->getVar('id'),
                [
                    'marca' => $this->request->getVar('marca')
                ]
            );
            return redirect()->to(base_url() . '/marcas')->with('message', 'Marca modificado con exíto.');
        } else {
            $data['marca'] = $this->marcas->where('idmarca', $this->request->getVar('id'))->first();
            $data['validation'] = $this->validator;
            echo view("templates/header");
            echo view("marcas/editar", $data);
            echo view("templates/footer");
        }
    }
    public function eliminar($id)
    {
        if (!verificar('eliminar marca', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->marcas->update($id, ['estado' => 0]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Marca eliminado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al eliminar la marca');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reciclaje()
    {
        if (!verificar('eliminar marca', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'marcas.js';
        echo view("templates/header");
        echo view("marcas/reciclaje", $data);
        echo view("templates/footer");
    }
    public function vaciar()
    {
        if (verificar('eliminar marca', $this->session->permisos)) {
            $data = $this->marcas->where('estado', 0)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 0) {
                    $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <button class="btn btn-success" onclick="btnreingresar_marca(' . $data[$i]['idmarca'] . ')"><i class="fas fa-reply"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function restaurar($id)
    {
        if (!verificar('eliminar marca', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->marcas->update($id, ['estado' => 1]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Marca reingresado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al reingresar la marca');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
}
