<?php

namespace App\Controllers;

use App\Models\ClientesModel;

class Clientes extends BaseController
{
    protected $reglas, $clientes, $session;
    public function __construct()
    {
        helper(['form']);
        $this->clientes = new ClientesModel();
        $this->session = session();
        $this->reglas = [
            'id'    => 'is_natural',
            'dni' => [
                'rules' => 'required|is_unique[clientes.dni,id,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'is_unique' => 'El {field} debe ser único'
                ]
            ],
            'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido'
                ]
            ],
            'telefono' => [
                'rules' => 'required|is_unique[clientes.dni,id,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'is_unique' => 'El {field} debe ser único'
                ]
            ],
            'direccion' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido'
                ]
            ]
        ];
    }
    public function index()
    {
        if (!verificar('listar clientes', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'clientes.js';
        echo view("templates/header");
        echo view("clientes/index", $data);
        echo view("templates/footer");
    }
    public function nuevo()
    {
        if (!verificar('crear cliente', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        echo view("templates/header");
        echo view("clientes/nuevo");
        echo view("templates/footer");
    }
    public function registrar()
    {
        if (!verificar('crear cliente', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $dni = $this->request->getVar('dni');
            $nombre = $this->request->getVar('nombre');
            $telefono = $this->request->getVar('telefono');
            $correo = $this->request->getVar('correo');
            $direccion = $this->request->getVar('direccion');
            $data = $this->clientes->save([
                'dni' => $dni,
                'nombre' => $nombre,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'correo' => $correo
            ]);
            return redirect()->to(base_url() . '/clientes')->with('message', 'Cliente registrado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            echo view("templates/header");
            echo view("clientes/nuevo", $data);
            echo view("templates/footer");
        }
    }
    public function listar()
    {
        if (verificar('listar clientes', $this->session->permisos)) {
            $data = $this->clientes->where('estado', 1)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <a href="' . base_url("clientes/editar/" . $data[$i]['id']) . '" class="btn btn-info"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-danger" onclick="btnEliminarCli(' . $data[$i]['id'] . ')"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function editar($id)
    {
        if (!verificar('editar cliente', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['cliente'] = $this->clientes->where('id', $id)->first();
        echo view("templates/header");
        echo view("clientes/editar", $data);
        echo view("templates/footer");
    }
    public function actualizar()
    {
        if (!verificar('editar cliente', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $data = $this->clientes->update(
                $this->request->getVar('id'),
                [
                    'dni' => $this->request->getVar('dni'),
                    'nombre' => $this->request->getVar('nombre'),
                    'telefono' => $this->request->getVar('telefono'),
                    'direccion' => $this->request->getVar('direccion'),
                    'correo' => $this->request->getVar('correo')
                ]
            );
            return redirect()->to(base_url() . '/clientes')->with('message', 'Cliente modificado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['cliente'] = $this->clientes->where('id', $this->request->getVar('id'))->first();
            echo view("templates/header");
            echo view("clientes/editar", $data);
            echo view("templates/footer");
        }
    }
    public function eliminar($id)
    {
        if (!verificar('eliminar cliente', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->clientes->update($id, ['estado' => 0]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Cliente eliminado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al eliminar el cliente');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reciclaje()
    {
        if (!verificar('eliminar cliente', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'clientes.js';
        echo view("templates/header");
        echo view("clientes/reciclaje", $data);
        echo view("templates/footer");
    }
    public function vaciar()
    {
        if (verificar('eliminar cliente', $this->session->permisos)) {
            $data = $this->clientes->where('estado', 0)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 0) {
                    $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <button class="btn btn-success" onclick="btnreingresar_cli(' . $data[$i]['id'] . ')"><i class="fas fa-reply"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function restaurar($id)
    {
        if (!verificar('eliminar cliente', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->clientes->update($id, ['estado' => 1]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Cliente reingresado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al reingresar el cliente');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
}
