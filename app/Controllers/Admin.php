<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\CategoriasModel;
use App\Models\ClientesModel;
use App\Models\ComprasModel;
use App\Models\CotizacionesModel;
use App\Models\MarcasModel;
use App\Models\ProductosModel;
use App\Models\UsuariosModel;
use App\Models\VentasModel;
use Config\Database;
use ZipArchive;

class Admin extends BaseController
{
    protected $reglas, $admin, $productos, $clientes, $usuarios,
        $marcas, $categorias, $ventas, $compras, $cotizaciones, $session;
    public function __construct()
    {
        helper(['form']);
        $this->admin = new AdminModel();
        $this->productos = new ProductosModel();
        $this->categorias = new CategoriasModel();
        $this->marcas = new MarcasModel();
        $this->usuarios = new UsuariosModel();
        $this->clientes = new ClientesModel();
        $this->ventas = new VentasModel();
        $this->compras = new ComprasModel();
        $this->cotizaciones = new CotizacionesModel();
        $this->session = session();
    }
    public function index()
    {
        if (verificar('actualizar', $this->session->permisos)) {
            $datos['data'] = $this->admin->first();
            echo view("templates/header");
            echo view("admin/index", $datos);
            echo view("templates/footer");
        } else {
            echo view("permisos");
            exit;
        }
    }
    public function modificar()
    {
        if (!verificar('actualizar', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $this->reglas = [
            'id'    => 'is_natural',
            'ruc' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'
                ]
            ],
            'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'
                ]
            ],
            'telefono' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'

                ]
            ],
            'direccion' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'

                ]
            ],
        ];
        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            $data = $this->admin->update(
                $this->request->getVar('id'),
                [
                    'ruc' => $this->request->getVar('ruc'),
                    'nombre' => $this->request->getVar('nombre'),
                    'telefono' => $this->request->getVar('telefono'),
                    'direccion' => $this->request->getVar('direccion'),
                    'correo' => $this->request->getVar('correo'),
                    'mensaje' => $this->request->getVar('mensaje'),
                    'impuesto' => (!empty($this->request->getVar('impuesto'))) ? $this->request->getVar('impuesto') : 0
                ]
            );
            return redirect()->to(base_url() . '/admin')->with('modificado', 'Datos modificado con exíto.');
        } else {
            $data['validation'] = $this->validator;
            $data['data'] = $this->admin->first();
            echo view("templates/header");
            echo view("admin/index", $data);
            echo view("templates/footer");
        }
    }
    public function dashboard()
    {
        $fecha = date('Y-m-d');
        $sql = "SELECT
            (SELECT COUNT(*) FROM productos    WHERE estado = 1) AS productos,
            (SELECT COUNT(*) FROM usuarios     WHERE estado = 1) AS usuarios,
            (SELECT COUNT(*) FROM clientes     WHERE estado = 1) AS clientes,
            (SELECT COUNT(*) FROM marcas       WHERE estado = 1) AS marcas,
            (SELECT COUNT(*) FROM categorias   WHERE estado = 1) AS categorias,
            (SELECT COUNT(*) FROM ventas       WHERE estado = 1 AND CAST(fecha AS date) = ?) AS ventas,
            (SELECT COUNT(*) FROM compras      WHERE estado = 1 AND CAST(fecha AS date) = ?) AS compras,
            (SELECT COUNT(*) FROM cotizaciones) AS cotizaciones";
        $data = (array) Database::connect()->query($sql, [$fecha, $fecha])->getRow();
        echo view("templates/header");
        echo view("admin/home", $data);
        echo view("templates/footer");
    }

    public function createBackup()
    {
        if (!verificar('backup', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }

        // Configuración de la base de datos
        $db = Database::connect();
        $tables = $db->listTables();

        // Generar el respaldo (PostgreSQL)
        $backupData = "-- Backup de base de datos (PostgreSQL)\n";
        $backupData .= "-- Generado: " . date('Y-m-d H:i:s') . "\n\n";
        foreach ($tables as $table) {
            $query = $db->query('SELECT * FROM "' . str_replace('"', '""', $table) . '"');
            $result = $query->getResultArray();

            if (!empty($result)) {
                $columns = array_keys($result[0]);
                $cols = '"' . implode('", "', $columns) . '"';

                $backupData .= "-- --------------------------------------------------------\n";
                $backupData .= "-- Datos de tabla para $table\n";
                $backupData .= "-- --------------------------------------------------------\n\n";

                foreach ($result as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return "'" . str_replace("'", "''", $value) . "'";
                    }, array_values($row));

                    $backupData .= 'INSERT INTO "' . $table . '" (' . $cols . ') VALUES (' . implode(', ', $values) . ");\n";
                }

                $backupData .= "\n";
            }
        }

        // Guardar el respaldo en un archivo
        $backupFilename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backupPath = WRITEPATH . 'backups/' . $backupFilename;
		
		if (!is_dir(dirname($backupPath))) {
			mkdir(dirname($backupPath), 0775, true);
		}


        if (!empty($backupData)) {
            if (file_put_contents($backupPath, $backupData) !== false) {
                if ($this->crearZip($backupPath)) {
                    unlink($backupPath);
                    // Ruta y nombre del archivo a descargar
                    $filePath = WRITEPATH . 'backups/backup.zip';
                    $fileName = 'archivo.zip';

                    // Verifica si el archivo existe
                    if (file_exists($filePath)) {
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . $fileName . '"');
                        header('Content-Length: ' . filesize($filePath));

                        // Lee y envía el contenido del archivo
                        readfile($filePath);
                        exit;
                    } else {
                        return redirect()->to(base_url('admin/dashboard'))->with('message', 'El archivo no se encontró.');
                    }
                } else {
                    return redirect()->to(base_url('admin/dashboard'))->with('message', 'Error al crear zip');
                }
            } else {
                return redirect()->to(base_url('admin/dashboard'))->with('message', 'Error al crear el respaldo.');
            }
        } else {
            return redirect()->to(base_url('admin/dashboard'))->with('message', 'No se encontraron datos para respaldar.');
        }
    }

    public function crearZip($ruta)
    {
        $zip = new ZipArchive();

        // Nombre y ruta del archivo ZIP que se va a crear
        $zipFilename = WRITEPATH . 'backups/backup.zip';

        // Abre el archivo ZIP en modo de creación
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Agrega el archivo PHP al archivo ZIP
            $zip->addFile($ruta, basename($ruta));

            // Cierra el archivo ZIP
            $zip->close();

            return true;
        } else {
            return false;
        }
    }

    //reporte graficos
    public function comparacion($anio)
    {
        $desde = $anio . '-01-01';
        $hasta = $anio . '-12-31';
        $id_usuario = $this->session->id_usuario;
        $where = "fecha BETWEEN '$desde' AND '$hasta' AND estado = 1 AND id_usuario = $id_usuario";
        $this->ventas->select("SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 1 THEN total ELSE 0 END) AS ene,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 2 THEN total ELSE 0 END) AS feb,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 3 THEN total ELSE 0 END) AS mar,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 4 THEN total ELSE 0 END) AS abr,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 5 THEN total ELSE 0 END) AS may,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 6 THEN total ELSE 0 END) AS jun,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 7 THEN total ELSE 0 END) AS jul,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 8 THEN total ELSE 0 END) AS ago,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 9 THEN total ELSE 0 END) AS sep,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 10 THEN total ELSE 0 END) AS oct,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 11 THEN total ELSE 0 END) AS nov,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 12 THEN total ELSE 0 END) AS dic");
        $data['venta'] = $this->ventas->where($where)->first();

        $this->compras->select("SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 1 THEN total ELSE 0 END) AS ene,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 2 THEN total ELSE 0 END) AS feb,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 3 THEN total ELSE 0 END) AS mar,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 4 THEN total ELSE 0 END) AS abr,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 5 THEN total ELSE 0 END) AS may,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 6 THEN total ELSE 0 END) AS jun,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 7 THEN total ELSE 0 END) AS jul,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 8 THEN total ELSE 0 END) AS ago,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 9 THEN total ELSE 0 END) AS sep,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 10 THEN total ELSE 0 END) AS oct,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 11 THEN total ELSE 0 END) AS nov,
        SUM(CASE WHEN EXTRACT(MONTH FROM fecha) = 12 THEN total ELSE 0 END) AS dic");
        $data['compra'] = $this->compras->where($where)->first();

        echo json_encode($data);
        die();
    }

    public function stockMinimo()
    {
        $where = "stock_minimo >= stock AND estado = 1";
        $data = $this->productos->where($where)->findAll();
        echo json_encode($data);
        die();
    }
}
