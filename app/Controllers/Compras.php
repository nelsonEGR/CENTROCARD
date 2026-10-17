<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\ProductosModel;
use App\Models\ComprasModel;
use App\Models\DetalleComprasModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Compras extends BaseController
{
    protected $reglas, $compras, $cartController, $productos, $detalle, $session, $empresa;
    public function __construct()
    {
        helper(['form']);
        $this->productos = new ProductosModel();
        $this->compras = new ComprasModel();
        $this->detalle = new DetalleComprasModel();
        $this->cartController = new Cart();
        $this->session = session();
    }
    public function index()
    {
        if (!verificar('nueva compra', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'compras.js';
        echo view("templates/header");
        echo view("compras/index", $data);
        echo view("templates/footer");
    }

    public function addCarrito($id)
    {
        $producto = $this->productos->find($id);
        $data = $this->cartController->addToCart('compras', $id, $producto['descripcion'], $producto['precio_compra']);
        return $this->response->setJSON($data);
    }
    public function deleteCarrito($id)
    {
        $data = $this->cartController->removeFromCart('compras', $id);
        return $this->response->setJSON($data);
    }
    public function cantidadCompra($id, $cantidad)
    {
        $data = $this->cartController->updateCantidad('compras', $id, $cantidad);
        return $this->response->setJSON($data);
    }
    public function precioCompra($id, $precio)
    {
        $data = $this->cartController->updatePrice('compras', $id, $precio);
        return $this->response->setJSON($data);
    }
    public function clearCart()
    {
        $data = $this->cartController->clearCart('compras');
        return $this->response->setJSON($data);
    }
    public function detalle()
    {
        $cart['productos'] = session()->get('compras');
        $cart['total'] = number_format($this->cartController->getTotalPrice('compras'), 2);
        echo json_encode($cart, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function historial()
    {
        if (!verificar('listar compras', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'compHistorial.js';
        echo view("templates/header");
        echo view("compras/historial_compra", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar compras', $this->session->permisos)) {
            $data = $this->compras->findAll();
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-success">Completado</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <a href="#" onclick="verTicket('.$data[$i]['id'].')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                    <button class="btn btn-info" onclick="Anular(' . $data[$i]['id'] . ', 1)"><i class="fas fa-ban"></i></button>
                </div>';
                } else {
                    $data[$i]['estado'] = '<span class="badge bg-danger">Anulado</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <a href="#" onclick="verTicket('.$data[$i]['id'].')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function buscar()
    {
        $valor = $this->request->getGet('pro');
        $data = $this->productos->like('codigo', $valor)->where('estado', 1)->orLike('descripcion', $valor)->where('estado', 1)->findAll();
        $datos = array();
        foreach ($data as $row) {
            $data['id'] = $row['id'];
            $data['label'] = $row['codigo'] . ' - ' . $row['descripcion'];
            $data['value'] = $row['codigo'];
            $data['precio'] = $row['precio_compra'];
            $data['descripcion'] = $row['descripcion'];
            array_push($datos, $data);
        }
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generar()
    {
        $id_compra = $this->compras->insert([
            'total' => $this->cartController->getTotalPrice('compras'),
            'id_usuario' => $this->session->id_usuario
        ]);
        if ($id_compra) {
            $datos = session()->get('compras');
            $detalles = array();
            $items = array();
            foreach ($datos as $row) {
                $detalles[] = [
                    'id_producto' => $row['id'],
                    'precio' => $row['price'],
                    'cantidad' => $row['quantity'],
                    'id_compra' => $id_compra
                ];
                $items[] = ['id' => $row['id'], 'cantidad' => $row['quantity']];
            }
            if (!empty($detalles)) {
                $this->detalle->insertBatch($detalles);
                $this->ajustarStock($items, 1);
            }
            $clear = $this->cartController->clearCart('compras');
            if ($clear['status'] == 'success') {
                $msg = array('msg' => 'Compra generada', 'icono' => 'success', 'estado' => true, 'id' => $id_compra);
            }
        } else {
            $msg = array('msg' => 'Error al realizar la compra', 'icono' => 'error', 'estado' => false);
        }
        echo json_encode($msg);
        die();
    }
    public function anular($id)
    {
        if (!verificar('anular compra', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'get') {
            $existe = $this->detalle->where(['id_compra' => $id])->findAll();
            if (!empty($existe)) {
                $this->ajustarStock(array_map(function ($row) {
                    return ['id' => $row['id_producto'], 'cantidad' => $row['cantidad']];
                }, $existe), -1);
                $this->compras->update($id, ['estado' => 0]);
                $msg = array('msg' => 'Compra anulado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error anulado', 'icono' => 'error');
            }
            echo json_encode($msg);
            die();
        }
    }

    public function generarTicket($id_compra)
    {
        if (verificar('nueva compra', $this->session->permisos) || verificar('listar compras', $this->session->permisos)) {

            $admin = new AdminModel();
            $data['empresa'] = $admin->first();
            $data['venta'] = $this->compras->where('id', $id_compra)->first();
            $data['productos'] = $this->detalle->select('detalle_compra.*, p.descripcion')
                ->join('productos AS p', 'detalle_compra.id_producto = p.id')->where('detalle_compra.id_compra', $id_compra)->findAll();
            // Configurar opciones de dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            // Crear una instancia de Dompdf con las opciones
            $dompdf = new Dompdf($options);

            // Contenido HTML del ticket
            $html = view('compras/ticket', $data); // Asegúrate de tener una vista llamada 'ticket_template.php'

            // Cargar el contenido HTML en dompdf
            $dompdf->loadHtml($html);

            // (Opcional) Establecer tamaño personalizado para la página (ancho: 80 mm, alto: automático)
            $dompdf->setPaper(array(0, 0, 226.77, 500)); // 80 mm = 226.77 pt, 283.46 pt ajustado para proporción
            $this->response->setHeader('Content-Type', 'application/pdf');
            // Renderizar el PDF
            $dompdf->render();

            // Enviar el PDF al navegador
            $dompdf->stream('ticket.pdf', array('Attachment' => 0));
        } else {
            echo view("permisos");
            exit;
        }
    }

    public function buscarNombre()
    {
        $valor = $this->request->getGet('term');
        $data = $this->productos->like('descripcion', $valor)->where('estado', 1)->findAll(10);
        $datos = array();
        foreach ($data as $row) {
            $data['id'] = $row['id'];
            $data['label'] = $row['codigo'] . ' - ' . $row['descripcion'];
            $data['value'] = $row['codigo'];
            $data['precio_venta'] = $row['precio_venta'];
            $data['precio_compra'] = $row['precio_compra'];
            $data['descripcion'] = $row['descripcion'];
            array_push($datos, $data);
        }
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function buscarPorCodigo($valor)
    {
        $data = $this->productos->like('codigo', $valor)->where('estado', 1)->first();
        $array = array('estado' => false, 'datos' => '');
        if (!empty($data)) {
            $array['estado'] = true;
            $array['datos'] = $data;
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }
}
