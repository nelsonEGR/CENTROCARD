<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\CajasCierreModel;
use App\Models\ClientesModel;
use App\Models\CredventasModel;
use App\Models\ProductosModel;
use App\Models\VentasModel;
use App\Models\DetalleVentasModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Ventas extends BaseController
{
    protected $reglas, $cartController, $ventas, $productos, $detalle,
        $cajas, $session, $credventas, $empresa, $clientes;
    public function __construct()
    {
        $this->productos = new ProductosModel();
        $this->ventas = new VentasModel();
        $this->detalle = new DetalleVentasModel();
        $this->cajas = new CajasCierreModel();
        $this->credventas = new CredventasModel();
        $this->cartController = new Cart();
        $this->session = session();
    }
    public function index()
    {
        if (!verificar('nueva venta', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'ventas.js';
        echo view("templates/header");
        echo view("ventas/index", $data);
        echo view("templates/footer");
    }
    public function historial()
    {
        if (!verificar('listar ventas', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'ventHistorial.js';
        echo view("templates/header");
        echo view("ventas/historial", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar ventas', $this->session->permisos)) {
            $inicio = $this->request->getGet('fecha_inicio');
            $fin = $this->request->getGet('fecha_fin');

            if ($inicio && $fin) {
                // Filtrar datos según el rango de fechas
                $inicio .= ' 00:00:00'; // Añadir el inicio del día
                $fin .= ' 23:59:59';    // Añadir el final del día
            
                // Realizar el join con la tabla clientes y filtrar por fecha
                $data = $this->ventas->select('ventas.*, clientes.nombre AS cliente_nombre')  // Seleccionar campos de ventas y el nombre del cliente
                    ->join('clientes', 'clientes.id = ventas.id_cliente')  // Join con la tabla clientes por id_cliente
                    ->where('ventas.fecha >=', $inicio)
                    ->where('ventas.fecha <=', $fin)
                    ->findAll();
            } else {
                // Obtener todos los datos si no hay filtro
                $data = $this->ventas->select('ventas.*, clientes.nombre AS cliente_nombre')  // Seleccionar campos de ventas y el nombre del cliente
                    ->join('clientes', 'clientes.id = ventas.id_cliente')  // Join con la tabla clientes por id_cliente
                    ->findAll();
            }
            
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['fecha'] = date('Y-m-d', strtotime($data[$i]['fecha']));
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-success">Completado</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                <a href="#" onclick="verTicket(' . $data[$i]['id'] . ')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                    <button class="btn btn-info" onclick="Anular(' . $data[$i]['id'] . ', 0)"><i class="fas fa-ban"></i></button>
                </div>';
                } else if ($data[$i]['estado'] == 2) {
                    $data[$i]['estado'] = '<span class="badge bg-warning">Pendiente</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <a href="#" onclick="verTicket(' . $data[$i]['id'] . ')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                    <button class="btn btn-info" onclick="Anular(' . $data[$i]['id'] . ', 0)"><i class="fas fa-ban"></i></button>
                </div>';
                } else {
                    $data[$i]['estado'] = '<span class="badge bg-danger">Anulado</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <a href="' . base_url('ventas/generarTicket/' . $data[$i]['id']) . '" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function anular($id)
    {
        if (!verificar('anular venta', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        if ($this->request->getMethod() == 'get') {
            $existe = $this->detalle->where(['id_venta' => $id])->findAll();
            if (!empty($existe)) {
                $venta = $this->ventas->find($id);
                if ($venta['metodo'] == 'Credito') {
                    $credito = $this->credventas->where('id_venta', $id)->first();
                    $this->credventas->update($credito['id'], ['estado' => '2']);
                }
                $this->ajustarStock(array_map(function ($row) {
                    return ['id' => $row['id_producto'], 'cantidad' => $row['cantidad']];
                }, $existe), 1);
                $this->ventas->update($id, ['estado' => 0]);
                $msg = array('msg' => 'Venta anulado', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error anulado', 'icono' => 'error');
            }
            echo json_encode($msg);
            die();
        }
    }
    public function addCarrito($id)
    {
        $producto = $this->productos->find($id);
        //comprobar stock
        $cart = session()->get('ventas');
        $totalCantidad = 1;

        if (!empty($cart)) {
            foreach ($cart as $product) {
                if ($product['id'] == $id) {
                    $totalCantidad = $product['quantity'] + 1;
                }
            }
        }

        if ($producto['stock'] >= $totalCantidad) {
            $data = $this->cartController->addToCart('ventas', $id, $producto['descripcion'], $producto['precio_venta']);
        } else {
            $data = [
                'status' => 'warning',
                'message' => 'Stock no disponible.'
            ];
        }

        return $this->response->setJSON($data);
    }
    public function deleteCarrito($id)
    {
        $data = $this->cartController->removeFromCart('ventas', $id);
        return $this->response->setJSON($data);
    }
    public function cantidadVenta($id, $cantidad)
    {
        //comprobar stock
        $cart = session()->get('ventas');
        $totalCantidad = 1;

        if (!empty($cart)) {
            foreach ($cart as $product) {
                if ($product['id'] == $id) {
                    $totalCantidad = $cantidad;
                }
            }
        }
        $producto = $this->productos->find($id);
        if ($producto['stock'] >= $totalCantidad) {
            $data = $this->cartController->updateCantidad('ventas', $id, $cantidad);
        } else {
            $data = [
                'status' => 'warning',
                'message' => 'Stock no disponible.'
            ];
        }

        return $this->response->setJSON($data);
    }
    public function precioVenta($id, $precio)
    {
        $data = $this->cartController->updatePrice('ventas', $id, $precio);
        return $this->response->setJSON($data);
    }
    public function clearCart()
    {
        $data = $this->cartController->clearCart('ventas');
        return $this->response->setJSON($data);
    }
    public function detalle()
    {
        $cart['productos'] = session()->get('ventas');
        $cart['total'] = number_format($this->cartController->getTotalPrice('ventas'), 2);
        echo json_encode($cart, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generar()
    {
        $caja = $this->cajas->where(['id_caja' => $this->session->id_caja, 'status' => 1])->first();
        if (empty($caja)) {
            $msg = array('msg' => 'No hay caja abierta', 'icono' => 'warning', 'estado' => false);
        } else {
            $id_cli = $this->request->getVar('id_cliente');
            if (empty($id_cli)) {
                $id_cli == 1;
            }
            $estado = ($this->request->getVar('metodo') == 'Contado') ? 1 : 2;
            $id_venta = $this->ventas->insert([
                'total' => $this->cartController->getTotalPrice('ventas'),
                'id_usuario' => $this->session->id_usuario,
                'id_caja' => $this->session->id_caja,
                'id_cliente' => $id_cli,
                'estado' => $estado,
                'pago_con' => $this->request->getVar('pago_con'),
                'metodo' => $this->request->getVar('metodo')
            ]);
            if ($id_venta > 0) {
                $datos = session()->get('ventas');
                $detalles = array();
                $items = array();
                foreach ($datos as $row) {
                    $detalles[] = [
                        'id_producto' => $row['id'],
                        'precio' => $row['price'],
                        'cantidad' => $row['quantity'],
                        'id_venta' => $id_venta
                    ];
                    $items[] = ['id' => $row['id'], 'cantidad' => $row['quantity']];
                }
                if (!empty($detalles)) {
                    $this->detalle->insertBatch($detalles);
                    $this->ajustarStock($items, -1);
                }
                //registrar metodo
                if ($this->request->getVar('metodo') == 'Credito') {
                    $this->credventas->insert([
                        'monto' => $this->cartController->getTotalPrice('ventas'),
                        'fecha' => date('Y-m-d H:i:s'),
                        'id_venta' => $id_venta
                    ]);
                }
                $clear = $this->cartController->clearCart('ventas');
                if ($clear['status'] == 'success') {
                    $msg = array('msg' => 'Venta generada', 'icono' => 'success', 'estado' => true, 'id' => $id_venta);
                }
            } else {
                $msg = array('msg' => 'Error al realizar la Venta', 'icono' => 'error', 'estado' => false);
            }
        }

        echo json_encode($msg);
        die();
    }

    public function generarTicket($id_venta)
    {
        if (verificar('nueva venta', $this->session->permisos) || verificar('listar ventas', $this->session->permisos)) {
            $admin = new AdminModel();
            $data['empresa'] = $admin->first();
            $data['venta'] = $this->ventas->select('ventas.*, c.nombre, c.telefono, c.direccion')
                ->join('clientes AS c', 'ventas.id_cliente = c.id')
                ->where('ventas.id', $id_venta)->first();

            $data['productos'] = $this->detalle->select('detalle_venta.*, p.descripcion')
                ->join('productos AS p', 'detalle_venta.id_producto = p.id')->where('detalle_venta.id_venta', $id_venta)->findAll();
            // Configurar opciones de dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            // Crear una instancia de Dompdf con las opciones
            $dompdf = new Dompdf($options);

            // Contenido HTML del ticket
            $html = view('ventas/ticket', $data); // Asegúrate de tener una vista llamada 'ticket_template.php'

            // Cargar el contenido HTML en dompdf
            $dompdf->loadHtml($html);

            // (Opcional) Establecer tamaño personalizado para la página (ancho: 80 mm, alto: automático)
            $dompdf->setPaper(array(0, 0, 226.77, 800)); // 80 mm = 226.77 pt, 283.46 pt ajustado para proporción
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

    ## ventas
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
