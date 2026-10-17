<?php

namespace App\Controllers;

use App\Models\AbonoventasModel;
use App\Models\AdminModel;
use App\Models\CajasCierreModel;
use App\Models\CajasModel;
use App\Models\CredventasModel;
use App\Models\VentasModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Credventas extends BaseController
{
    protected $reglas, $ventas, $cajas, $abonos, $session, $credventas, $empresa, $clientes;
    public function __construct()
    {
        $this->ventas = new VentasModel();
        $this->credventas = new CredventasModel();
        $this->abonos = new AbonoventasModel();
        $this->cajas = new CajasCierreModel();
        $this->session = session();
        helper(['function']);
        if (!verificar('creditos ventas', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
    }
    public function index()
    {
        $data['script'] = 'credventas.js';
        echo view("templates/header");
        echo view("ventas/creditos", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        $data = $this->credventas->select('credventas.*, c.nombre, COALESCE((SELECT SUM(abono) FROM pagosventas WHERE id_credito = credventas.id), 0) AS abonado')
            ->join('ventas AS v', 'credventas.id_venta = v.id')
            ->join('clientes AS c', 'v.id_cliente = c.id')
            ->where('credventas.estado != 2')->findAll();
        for ($i = 0; $i < count($data); $i++) {
            $totalAbono = (float) $data[$i]['abonado'];
            $data[$i]['abonado'] = number_format($totalAbono, 2);
            $data[$i]['restante'] = number_format($data[$i]['monto'] - $totalAbono, 2);
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge bg-warning">Pendiente</span>';
                $data[$i]['acciones'] = '<div class"text-center">
                <a href="#" onclick="verTicket(' . $data[$i]['id'] . ')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                    <button class="btn btn-info" onclick="abonoCredito(' . $data[$i]['id'] . ', 0)">Abonar</button>
                </div>';
            } else {
                $data[$i]['estado'] = '<span class="badge bg-success">Finalizado</span>';
                $data[$i]['acciones'] = '<div class"text-center">
                    <a href="#" onclick="verTicket(' . $data[$i]['id'] . ')" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                </div>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function abonar()
    {
        //verificar caja
        $caja = $this->cajas->where(['id_caja' => $this->session->id_caja, 'status' => 1])->first();
        if (empty($caja)) {
            $res = array('msg' => 'No hay caja abierta', 'icono' => 'warning', 'estado' => false);
        } else {
            $abonado = $this->abonos->selectSum('abono')->where('id_credito', $this->request->getVar('id_credito'))->first();
            $credito = $this->credventas->find($this->request->getVar('id_credito'));
            $totalAbono = $abonado['abono'] + $this->request->getVar('monto');
            if ($totalAbono <= $credito['monto']) {
                $data = $this->abonos->insert([
                    'abono' => $this->request->getVar('monto'),
                    'fecha' => date('Y-m-d H:i:s'),
                    'id_credito' => $this->request->getVar('id_credito'),
                    'id_caja' => $this->session->id_caja
                ]);
                if ($data > 0) {
                    if ($totalAbono == $credito['monto']) {
                        $this->ventas->update($credito['id_venta'], ['estado' => 1]);
                        $this->credventas->update($this->request->getVar('id_credito'), ['estado' => 0]);
                    }
                    $res = ['msg' => 'Abono registrado', 'icono' => 'success', 'id' => $this->request->getVar('id_credito')];
                } else {
                    $res = ['msg' => 'Error al abonar', 'icono' => 'error'];
                }
            } else {
                $res = ['msg' => 'Ingrese el monto menor o igual a restante', 'icono' => 'warning'];
            }
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function generarTicket($id_credito)
    {
        $admin = new AdminModel();
        $data['empresa'] = $admin->first();
        $data['credito'] = $this->credventas->where('id', $id_credito)->first();
        $data['abonos'] = $this->abonos->where('id_credito', $id_credito)->findAll();
        // Configurar opciones de dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Crear una instancia de Dompdf con las opciones
        $dompdf = new Dompdf($options);

        // Contenido HTML del ticket
        $html = view('ventas/ticketcredito', $data); // Asegúrate de tener una vista llamada 'ticket_template.php'

        // Cargar el contenido HTML en dompdf
        $dompdf->loadHtml($html);

        // (Opcional) Establecer tamaño personalizado para la página (ancho: 80 mm, alto: automático)
        $dompdf->setPaper(array(0, 0, 226.77, 500)); // 80 mm = 226.77 pt, 283.46 pt ajustado para proporción
        $this->response->setHeader('Content-Type', 'application/pdf');
        // Renderizar el PDF
        $dompdf->render();

        // Enviar el PDF al navegador
        $dompdf->stream('ticket.pdf', array('Attachment' => 0));
    }
}
