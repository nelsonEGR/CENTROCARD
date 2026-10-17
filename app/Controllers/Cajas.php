<?php

namespace App\Controllers;

use App\Models\AbonoventasModel;
use App\Models\CajasCierreModel;
use App\Models\CajasModel;
use App\Models\VentasModel;

class Cajas extends BaseController
{
    protected $reglas, $cajas, $cierre, $abonos, $ventas, $session;
    public function __construct()
    {
        helper(['form']);
        $this->cajas = new CajasModel();
        $this->cierre = new CajasCierreModel();
        $this->cierre = new CajasCierreModel();
        $this->ventas = new VentasModel();
        $this->abonos = new AbonoventasModel();
        $this->reglas = [
            'id'    => 'is_natural',
            'caja' => [
                'rules' => 'required|is_unique[cajas.caja,id,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'is_unique' => 'El {field} debe ser único'

                ]
            ]
        ];
        $this->session = session();
    }

    public function index()
    {
        if (!verificar('listar cajas', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'cajas.js';
        echo view("templates/header");
        echo view("cajas/index", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar cajas', $this->session->permisos)) {
            $data = $this->cajas->where('estado', 1)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-info">Activo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                <a href="' . base_url("cajas/editar/" . $data[$i]['id']) . '" class="btn btn-dark"><i class="fas fa-edit"></i></a>
                <button class="btn btn-danger" onclick="btnEliminarCaja(' . $data[$i]['id'] . ')"><i class="fas fa-trash-alt"></i></button>
                </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function registrar()
    {
        if (!verificar('crear caja', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $caja = $this->request->getVar('caja');
            $data = $this->cajas->save([
                'caja' => $caja
            ]);
            return redirect()->to(base_url() . '/cajas')->with('message', 'Caja registrado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            echo view("templates/header");
            echo view("cajas/nuevo", $data);
            echo view("templates/footer");
        }
    }
    public function nuevo()
    {
        if (!verificar('crear caja', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        echo view("templates/header");
        echo view("cajas/nuevo");
        echo view("templates/footer");
    }
    public function editar($id)
    {
        if (!verificar('editar caja', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['caja'] = $this->cajas->where('id', $id)->first();
        echo view("templates/header");
        echo view("cajas/editar", $data);
        echo view("templates/footer");
    }
    public function actualizar()
    {
        if (!verificar('editar caja', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $data = $this->cajas->update(
                $this->request->getVar('id'),
                [
                    'caja' => $this->request->getVar('caja')
                ]
            );
            return redirect()->to(base_url() . '/cajas')->with('message', 'Caja registrado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['caja'] = $this->cajas->where('id', $this->request->getVar('id'))->first();
            echo view("templates/header");
            echo view("cajas/editar", $data);
            echo view("templates/footer");
        }
    }
    public function eliminar($id)
    {
        if (!verificar('eliminar caja', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->cajas->update($id, ['estado' => 0]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Caja eliminado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al eliminar la caja');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reciclaje()
    {
        if (!verificar('eliminar caja', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'cajas.js';
        echo view("templates/header");
        echo view("cajas/reciclaje", $data);
        echo view("templates/footer");
    }
    public function vaciar()
    {
        if (verificar('eliminar caja', $this->session->permisos)) {
            $data = $this->cajas->where('estado', 0)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 0) {
                    $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <button class="btn btn-success" onclick="btnreingresar_caja(' . $data[$i]['id'] . ')"><i class="fas fa-reply"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function restaurar($id)
    {
        if (!verificar('eliminar caja', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->cajas->update($id, ['estado' => 1]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Caja reingresado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al reingresar la caja');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    // Cierre Caja
    public function cierre()
    {
        if (!verificar('apertura y cierre', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $id_caja = $this->session->id_caja;
        $this->cierre->select('caja_cierre.*, cajas.caja, usuarios.nombre');
        $this->cierre->join('cajas', 'caja_cierre.id_caja = cajas.id');
        $this->cierre->join('usuarios', 'caja_cierre.id_usuario = usuarios.id');
        if ($this->session->id_usuario == 1) {
            $data['cierre'] = $this->cierre->orderBy('caja_cierre.id', 'desc')->findAll();
        } else {
            $data['cierre'] = $this->cierre->where('caja_cierre.id_caja', $id_caja)
                ->orderBy('caja_cierre.id', 'desc')->findAll();
        }
        $data['estado'] = $this->cierre->where(['id_caja' => $id_caja, 'status' => 1])->first();
        $data['id_caja'] = $id_caja;
        $data['script'] = 'cajas.js';
        echo view('templates/header');
        echo view('cajas/cierre', $data);
        echo view('templates/footer');
    }
    public function abrir()
    {
        if (!verificar('apertura y cierre', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['id'] = $this->session->id_caja;
        echo view('templates/header');
        echo view('cajas/abrir', $data);
        echo view('templates/footer');
    }
    public function cerrar()
    {
        if (!verificar('apertura y cierre', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $id_caja = $this->session->id_caja;
        $data = $this->verficarCaja($id_caja);
        echo view('templates/header');
        echo view('cajas/cerrar', $data);
        echo view('templates/footer');
    }
    public function verficarCaja()
    {
        $id_caja = $this->session->id_caja;
        $sql = "SELECT
            (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE id_caja = ? AND caja = 1 AND estado = 1 AND metodo = 'Contado') AS total_contado,
            (SELECT COALESCE(SUM(abono), 0) FROM pagosventas WHERE id_caja = ? AND caja = 1) AS total_abonos,
            (SELECT COUNT(*) FROM ventas WHERE id_caja = ? AND caja = 1 AND estado = 1) AS total_ventas,
            (SELECT monto_inicial FROM caja_cierre WHERE id_caja = ? AND status = 1 LIMIT 1) AS monto_inicial";
        $datos = (array) \Config\Database::connect()->query($sql, [$id_caja, $id_caja, $id_caja, $id_caja])->getRow();
        $mensaje = array("monto_final" => ($datos['total_contado'] + $datos['total_abonos']), "monto_inicial" => $datos['monto_inicial'], "ventas" => $datos['total_ventas'], "id" => $id_caja);
        return $mensaje;
    }
    public function registrarCajaCierre()
    {
        if (!verificar('apertura y cierre', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == "POST") {
            $fecha = date("Y-m-d H:i:s");
            $monto_inicial = (!empty($this->request->getVar('monto_inicial'))) ? $this->request->getVar('monto_inicial') : 0;
            if (empty($this->request->getVar('id'))) {
                $existe = $this->cierre->where(['id_caja' => $this->session->id_caja, 'status' => 1])->first();
                if (!empty($existe)) {
                    return redirect()->to(base_url() . '/cajas/cierre/' . $this->session->id_caja)->with('message', 'La caja ya esta abierta');
                } else {
                    $data = $this->cierre->save([
                        'id_caja' => $this->session->id_caja,
                        'id_usuario' => $this->session->id_usuario,
                        'fecha_inicio' => $fecha,
                        'monto_inicial' => $monto_inicial,
                        'total_ventas' => 0
                    ]);
                    if ($data > 0) {
                        return redirect()->to(base_url() . '/cajas/cierre/' . $this->session->id_caja)->with('message', 'Caja abierta con éxito');
                    } else {
                        return redirect()->to(base_url() . '/cajas/cierre/' . $this->session->id_caja)->with('message', 'Error al abrir la caja');
                    }
                }
            } else {
                if ($this->request->getMethod() == "POST") {
                    $datos = $this->verficarCaja($this->request->getVar('id'));
                    $this->cierre->set([
                        'fecha_fin' => $fecha,
                        'monto_fin' => $datos['monto_final'],
                        'total_ventas' => $datos['ventas'],
                        'status' => 0
                    ]);
                    $this->cierre->where(['id_caja' => $this->request->getVar('id'), 'status' => 1]);
                    $data = $this->cierre->update();
                    if ($data > 0) {
                        $this->abonos->set(['caja' => 0]);
                        $this->abonos->where(['id_caja' => $this->session->id_caja, 'caja' => 1]);
                        $this->abonos->update();

                        $this->ventas->set(['caja' => 0]);
                        $this->ventas->where(['id_caja' => $this->session->id_caja, 'caja' => 1]);
                        $this->ventas->update();
                        return redirect()->to(base_url() . '/cajas/cierre/' . $this->session->id_caja)->with('message', 'Caja cerrado con éxito');
                    } else {
                        return redirect()->to(base_url() . '/cajas/cierre/' . $this->session->id_caja)->with('message', 'Error al cerrar con éxito');
                    }
                }
            }
        }
    }
}
