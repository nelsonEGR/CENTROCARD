<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\ClientesModel;
use App\Models\CotizacionesModel;
use App\Models\DetalleCotModel;
use App\Models\ProductosModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Cotizaciones extends BaseController
{
    protected $reglas, $cartController, $cotizaciones, $productos, $detalle,
        $session, $empresa, $clientes;
    public function __construct()
    {
        $this->productos = new ProductosModel();
        $this->cotizaciones = new CotizacionesModel();
        $this->detalle = new DetalleCotModel();
        $this->cartController = new Cart();
        $this->session = session();
    }
    public function index()
    {
        if (!verificar('nueva cotizacion', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'cotizaciones.js';
        echo view("templates/header");
        echo view("cotizaciones/index", $data);
        echo view("templates/footer");
    }
    public function historial()
    {
        if (!verificar('listar cotizaciones', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'cotHistorial.js';
        echo view("templates/header");
        echo view("cotizaciones/historial", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar cotizaciones', $this->session->permisos)) {
            $data = $this->cotizaciones->select('cotizaciones.*, c.nombre')
                ->join('clientes AS c', 'cotizaciones.id_cliente = c.id')->findAll();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['acciones'] = '<div class"text-center">
                    <a href="#" onclick="verTicket('.$data[$i]['id'].')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                </div>';
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function anular($id)
    {
        if ($this->request->getMethod() == 'get') {
            $estado = $this->cotizaciones->delete($id);
            if ($estado) {
                $msg = array('msg' => 'Cotizacion eliminado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error eliminar', 'icono' => 'error');
            }
            echo json_encode($msg);
            die();
        }
    }
    public function addCarrito($id)
    {
        $producto = $this->productos->find($id);
        $data = $this->cartController->addToCart('cotizaciones', $id, $producto['descripcion'], $producto['precio_venta']);
        return $this->response->setJSON($data);
    }
    public function deleteCarrito($id)
    {
        $data = $this->cartController->removeFromCart('cotizaciones', $id);
        return $this->response->setJSON($data);
    }
    public function cantidadCotizacion($id, $cantidad)
    {
        $data = $this->cartController->updateCantidad('cotizaciones', $id, $cantidad);
        return $this->response->setJSON($data);
    }
    public function precioCotizacion($id, $precio)
    {
        $data = $this->cartController->updatePrice('cotizaciones', $id, $precio);
        return $this->response->setJSON($data);
    }
    public function clearCart()
    {
        $data = $this->cartController->clearCart('cotizaciones');
        return $this->response->setJSON($data);
    }
    public function detalle()
    {
        $cart['productos'] = session()->get('cotizaciones');
        $cart['total'] = number_format($this->cartController->getTotalPrice('cotizaciones'), 2);
        echo json_encode($cart, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generar()
    {
        $id_cli = $this->request->getVar('id_cliente');
        if (empty($id_cli)) {
            $id_cli = 1;
        }
        $id_cotizacion = $this->cotizaciones->insert([
            'total' => $this->cartController->getTotalPrice('cotizaciones'),
            'id_usuario' => $this->session->id_usuario,
            'id_cliente' => $id_cli,
        ]);
        if ($id_cotizacion > 0) {
            $datos = session()->get('cotizaciones');
            $detalles = array();
            foreach ($datos as $row) {
                $detalles[] = [
                    'id_producto' => $row['id'],
                    'precio' => $row['price'],
                    'cantidad' => $row['quantity'],
                    'id_cotizacion' => $id_cotizacion
                ];
            }
            if (!empty($detalles)) {
                $this->detalle->insertBatch($detalles);
            }
            $clear = $this->cartController->clearCart('cotizaciones');
            if ($clear['status'] == 'success') {
                $msg = array('msg' => 'Cotizacion generada', 'icono' => 'success', 'estado' => true, 'id' => $id_cotizacion);
            }
        } else {
            $msg = array('msg' => 'Error al realizar', 'icono' => 'error', 'estado' => false);
        }

        echo json_encode($msg);
        die();
    }

    public function generarTicket($id_cotizacion)
    {
        if (verificar('nueva cotizacion', $this->session->permisos) || verificar('listar cotizaciones', $this->session->permisos)) {
            $admin = new AdminModel();
            $data['empresa'] = $admin->first();
            $data['cotizacion'] = $this->cotizaciones->select('cotizaciones.*, c.nombre, c.telefono, c.direccion')
                ->join('clientes AS c', 'cotizaciones.id_cliente = c.id')
                ->where('cotizaciones.id', $id_cotizacion)->first();
            $data['productos'] = $this->detalle->select('detalle_cotizacion.*, p.descripcion')
                ->join('productos AS p', 'detalle_cotizacion.id_producto = p.id')->where('detalle_cotizacion.id_cotizacion', $id_cotizacion)->findAll();
            // Configurar opciones de dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            // Crear una instancia de Dompdf con las opciones
            $dompdf = new Dompdf($options);

            // Contenido HTML del ticket
            $html = view('cotizaciones/ticket', $data); // Asegúrate de tener una vista llamada 'ticket_template.php'

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

    public function buscar()
    {
        $this->clientes = new ClientesModel();
        $valor = $this->request->getGet('cli');
        $data = $this->clientes->like('dni', $valor)->findAll();
        $datos = array();
        foreach ($data as $row) {
            $data['id'] = $row['id'];
            $data['label'] = $row['dni'] . ' - ' . $row['nombre'];
            $data['value'] = $row['nombre'];
            $data['direccion'] = $row['direccion'];
            array_push($datos, $data);
        }
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        die();
    }

    ## cotizaciones
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
