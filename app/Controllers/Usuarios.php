<?php

namespace App\Controllers;

use App\Models\CajasModel;
use App\Models\RolesModel;
use App\Models\UsuariosModel;

class Usuarios extends BaseController
{
    protected $reglas, $usuarios, $cajas, $session, $roles;
    public function __construct()
    {
        helper(['form']);
        $this->usuarios = new UsuariosModel();
        $this->cajas = new CajasModel();
        $this->roles = new RolesModel();
        $this->session = session();
        $this->reglas = [
            'id'    => 'is_natural',
            'usuario' => [
                'rules' => 'required|is_unique[usuarios.usuario,id,{id}]',
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
            'correo' => [
                'rules' => 'required|valid_email|is_unique[usuarios.correo,id,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'valid_email' => 'Ingresa un correo valido',
                    'is_unique' => 'El {field} debe ser único'
                ]
            ],
            'clave' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'La {field} debe contener mínimo 6 caracter'
                ]
            ],
            'confirmar' => [
                'rules' => 'required|min_length[6]|matches[clave]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'La {field} debe contener mínimo 6 caracter',
                    'matches' => 'Las contraseñas no coinciden'
                ]
            ],
            'caja' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido'
                ]
            ],
        ];
        //'email' => 'permit_empty|valid_email|is_unique[contacts.email,id,{users.id}]' 
    }
    public function index()
    {
        if (!verificar('listar usuarios', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'usuarios.js';
        echo view("templates/header");
        echo view("usuarios/index", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar usuarios', $this->session->permisos)) {
            $data = $this->usuarios->select('usuarios.*, r.nombre AS rol')
                ->join('roles AS r', 'usuarios.id_rol = r.id')->where('usuarios.estado', 1)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 1) {
                        $data[$i]['estado'] = '<span class="badge bg-success px-1">Activo</span>';
                        $data[$i]['acciones'] = '<div class"text-center">
                    <a href="' . base_url("usuarios/editar/" . $data[$i]['id']) . '" class="btn btn-outline-info"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-outline-danger" onclick="btnEliminarUser(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function registrar()
    {
        if (!verificar('crear usuario', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $usuario = $this->request->getVar('usuario');
            $nombre = $this->request->getVar('nombre');
            $correo = $this->request->getVar('correo');
            $clave = password_hash($this->request->getVar('clave'), PASSWORD_DEFAULT);
            $caja = $this->request->getVar('caja');
            $data = $this->usuarios->save([
                'usuario' => $usuario,
                'nombre' => $nombre,
                'correo' => $correo,
                'clave' => $clave,
                'id_caja' => $caja,
                'id_rol' => $this->request->getVar('rol')
            ]);
            return redirect()->to(base_url() . '/usuarios')->with('message', 'Usuario registrado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['cajas'] = $this->cajas->where('estado', 1)->findAll();
            $data['roles'] = $this->roles->where('estado', 1)->findAll();
            echo view("templates/header");
            echo view("usuarios/nuevo", $data);
            echo view("templates/footer");
        }
    }
    public function nuevo()
    {
        if (!verificar('crear usuario', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['cajas'] = $this->cajas->where('estado', 1)->findAll();
        $data['roles'] = $this->roles->where('estado', 1)->findAll();
        echo view("templates/header");
        echo view("usuarios/nuevo", $data);
        echo view("templates/footer");
    }
    public function editar($id)
    {
        if (!verificar('editar usuario', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['usuario'] = $this->usuarios->where('id', $id)->first();
        $data['cajas'] = $this->cajas->where('estado', 1)->findAll();
        $data['roles'] = $this->roles->where('estado', 1)->findAll();
        echo view("templates/header");
        echo view("usuarios/editar", $data);
        echo view("templates/footer");
    }
    public function actualizar()
    {
        if (!verificar('editar usuario', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $data = $this->usuarios->update(
                $this->request->getVar('id'),
                [
                    'usuario' => $this->request->getVar('usuario'),
                    'nombre' => $this->request->getVar('nombre'),
                    'correo' => $this->request->getVar('correo'),
                    'id_caja' => $this->request->getVar('caja'),
                    'id_rol' => $this->request->getVar('rol')
                ]
            );
            return redirect()->to(base_url() . '/usuarios')->with('message', 'Usuario modificado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['usuario'] = $this->usuarios->where('id', $this->request->getVar('id'))->first();
            $data['cajas'] = $this->cajas->where('estado', 1)->findAll();
            $data['roles'] = $this->roles->where('estado', 1)->findAll();
            echo view("templates/header");
            echo view("usuarios/editar", $data);
            echo view("templates/footer");
        }
    }
    public function eliminar($id)
    {
        if (!verificar('eliminar usuario', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->usuarios->update($id, ['estado' => 0]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Usuario eliminado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al eliminar el usuario');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reciclaje()
    {
        if (!verificar('eliminar usuario', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'usuarios.js';
        echo view("templates/header");
        echo view("usuarios/reciclaje", $data);
        echo view("templates/footer");
    }
    public function vaciar()
    {
        if (verificar('eliminar usuario', $this->session->permisos)) {
            $data = $this->usuarios->where('estado', 0)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 0) {
                    $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <button class="btn btn-outline-success" onclick="btnreingresar_user(' . $data[$i]['id'] . ')"><i class="fas fa-reply"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function restaurar($id)
    {
        if (!verificar('eliminar usuario', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->usuarios->update($id, ['estado' => 1]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Usuario reingresado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al reingresar el usuario');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function verificar()
    {
        $this->reglas = [
            'usuario' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido'

                ]
            ],
            'clave' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido'
                ]
            ]
        ];
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $usuario = $this->request->getVar('usuario');
            $clave = $this->request->getVar('clave');
            $datosUser = $this->usuarios->select('usuarios.*, r.permisos AS rol_permisos')
                ->join('roles AS r', 'usuarios.id_rol = r.id', 'left')
                ->where(['usuarios.usuario' => $usuario, 'usuarios.estado' => 1])->first();
            if ($datosUser != null) {
                if (password_verify($clave, $datosUser['clave'])) {
                    $permisos = ($datosUser['rol_permisos'] != null) ? json_decode($datosUser['rol_permisos'], true) : null;
                    if ($permisos != null) {
                        $datosSesion = [
                            'id_usuario' => $datosUser['id'],
                            'id_caja' => $datosUser['id_caja'],
                            'usuario' => $datosUser['usuario'],
                            'nombre' => $datosUser['nombre'],
                            'permisos' => $permisos
                        ];
                        $session = session();
                        $session->set($datosSesion);
                        return redirect()->to(base_url() . 'admin/dashboard')->with('success', 'Has iniciado sesion correctamente.');
                    } else {
                        return redirect()->to(base_url())->with('fail', 'No tienes permisos.');
                    }
                } else {
                    return redirect()->to(base_url())->with('fail', 'Contraseña incorrecta.');
                }
            } else {
                return redirect()->to(base_url())->with('fail', 'El usuario no existe');
            }
        } else {
            $data['validation'] = $this->validator;
            echo view("login", $data);
        }
    }
    public function salir()
    {
        $this->session->destroy();
        return redirect()->to(base_url());
    }

    public function perfil()
    {
        echo view("templates/header");
        echo view("usuarios/perfil");
        echo view("templates/footer");
    }
    public function cambiar()
    {
        $this->reglas = [
            'clave_actual' => [
                'rules' => 'required|min_length[4]',
                'errors' => [
                    'required' => 'La clave actual es requerido',
                    'min_length' => 'La clave debe contener mínimo 6 caracter'
                ]
            ],
            'clave_nueva' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La clave nueva es requerido',
                    'min_length' => 'La clave nueva debe contener mínimo 6 caracter'
                ]
            ],
            'confirmar' => [
                'rules' => 'required|min_length[6]|matches[clave_nueva]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'El {field} debe contener mínimo 6 caracter',
                    'matches' => 'Las contraseñas no coinciden'
                ]
            ]
        ];
        if ($this->request->getMethod() == "POST" && $this->validate($this->reglas)) {
            $session = session();
            $id = $session->id_usuario;
            $clave = $this->request->getVar('clave_actual');
            $user = $this->usuarios->where('id', $id)->first();
            if (password_verify($clave, $user['clave'])) {
                $hash = password_hash($this->request->getVar('clave_nueva'), PASSWORD_DEFAULT);
                $data = $this->usuarios->update($id, ['clave' => $hash]);
                if ($data > 0) {
                    $mensaje = 'ok';
                } else {
                    $mensaje = 'error';
                }
            } else {
                $mensaje = 'incorrecta';
            }
            return redirect()->to(base_url() . '/usuarios/perfil')->with('perfil', $mensaje);
        } else {
            $data['validation'] = $this->validator;
            echo view("templates/header");
            echo view("usuarios/perfil", $data);
            echo view("templates/footer");
        }
    }
}
