<?php

namespace App\Controllers;

use App\Models\MarcasModel;
use App\Models\CategoriasModel;
use App\Models\ProductosModel;

class Productos extends BaseController
{
    protected $reglas, $productos, $marcas,
        $categorias, $session;
    public function __construct()
    {
        $this->productos = new ProductosModel();
        $this->marcas = new MarcasModel();
        $this->categorias = new CategoriasModel();
        $this->session = session();
        helper(['form']);
        $this->reglas = [
            'id'    => 'is_natural',
            'codigo' => [
                'rules' => 'permit_empty|is_unique[productos.codigo,id,{id}]',
                'errors' => [
                    'required' => 'El {field} es requerido',
                    'is_unique' => 'El {field} debe ser único'
                ]
            ],
            'descripcion' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'La {field} es requerido'
                ]
            ],
            'precio_compra' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'
                ]
            ],
            'precio_venta' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'
                ]
            ],
            'stock_minimo' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'
                ]
            ],
            'stock' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El {field} es requerido'
                ]
            ],
            'marca' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'La {field} es requerido'
                ]
            ],
            'categoria' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'La {field} es requerido'
                ]
            ]

        ];
    }
    public function index()
    {
        if (!verificar('listar productos', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'productos.js';
        echo view("templates/header");
        echo view("productos/index", $data);
        echo view("templates/footer");
    }
    public function listar()
    {
        if (verificar('listar productos', $this->session->permisos)) {
            $this->productos->select('*');
            $this->productos->join('marcas', 'productos.id_marca = marcas.idmarca');
            $this->productos->join('categorias', 'productos.id_categoria = categorias.idcat');
            $data = $this->productos->where('productos.estado', 1)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['precio_compra'] = number_format($data[$i]['precio_compra'], 0, '', '.');
                $data[$i]['precio_venta'] = number_format($data[$i]['precio_venta'], 0, '', '.');
                $data[$i]['imagen'] = '<img class="img-thumbnail" src="' . base_url('img/productos/' . $data[$i]['imagen']) . '" width="60">';
                if ($data[$i]['estado'] == 1) {
                    $data[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                    $data[$i]['acciones'] = '<div class"d-flex">
                    <a href="' . base_url("productos/editar/" . $data[$i]['id']) . '" class="btn btn-outline-info"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-outline-danger" onclick="btnEliminarPro(' . $data[$i]['id'] . ')"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function registrar()
    {
        if (!verificar('crear producto', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }

        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            // Obtener el código ingresado por el usuario
            $codigo = $this->request->getVar('codigo');

            if (empty($codigo)) {
                // Generar código automático basado en el ID del último producto
                $ultimoProducto = $this->productos->orderBy('id', 'DESC')->first(); // Obtener el último producto
                $ultimoId = $ultimoProducto ? $ultimoProducto['id'] : 0;
                $nuevoId = $ultimoId + 1;
                $codigo = 'PRO_' . str_pad($nuevoId, 3, '0', STR_PAD_LEFT); // Ejemplo: PRO_001
            } else {
                // Verificar que el código ingresado no exista ya en la base de datos
                $productoExistente = $this->productos->where('codigo', $codigo)->first();
                if ($productoExistente) {
                    return redirect()->back()->with('error', 'El código ya existe para otro producto.');
                }
            }

            // Procesar la imagen
            $imagen = $this->request->getFile('imagen');
            if (!empty($imagen->getName())) {
                $img = 'pro_' . date("YmdHis") . '.jpg';
            } else {
                $img = 'default.jpg';
            }

            // Guardar el producto en la base de datos
            $this->productos->save([
                'codigo' => $codigo, // Usar el código generado o proporcionado
                'descripcion' => $this->request->getVar('descripcion'),
                'precio_compra' => $this->request->getVar('precio_compra'),
                'precio_venta' => $this->request->getVar('precio_venta'),
                'stock_minimo' => $this->request->getVar('stock_minimo'),
                'stock' => $this->request->getVar('stock'),
                'id_marca' => $this->request->getVar('marca'),
                'id_categoria' => $this->request->getVar('categoria'),
                'imagen' => $img
            ]);

            // Mover la imagen al directorio correspondiente si se subió una
            if (!empty($imagen->getName())) {
                $ruta_img = FCPATH . 'img/productos/';
                if (!file_exists($ruta_img)) {
                    mkdir($ruta_img, 0777, true); // Crear el directorio si no existe
                }
                $imagen->move($ruta_img, $img);
            }

            return redirect()->to(base_url() . '/productos')->with('message', 'Producto registrado con éxito.');
        } else {
            // Cargar datos para la vista de creación de producto
            $data['marcas'] = $this->marcas->where('estado', 1)->findAll();
            $data['categorias'] = $this->categorias->where('estado', 1)->findAll();
            $data['validation'] = $this->validator;

            echo view("templates/header");
            echo view("productos/nuevo", $data);
            echo view("templates/footer");
        }
    }


    public function nuevo()
    {
        if (!verificar('crear producto', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['marcas'] = $this->marcas->where('estado', 1)->findAll();
        $data['categorias'] = $this->categorias->where('estado', 1)->findAll();
        echo view("templates/header");
        echo view("productos/nuevo", $data);
        echo view("templates/footer");
    }
    public function editar($id)
    {
        if (!verificar('editar producto', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['marcas'] = $this->marcas->where('estado', 1)->findAll();
        $data['categorias'] = $this->categorias->where('estado', 1)->findAll();
        $data['producto'] = $this->productos->where('id', $id)->first();
        echo view("templates/header");
        echo view("productos/editar", $data);
        echo view("templates/footer");
    }
    public function actualizar()
    {
        if (!verificar('editar producto', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }

        if ($this->request->getMethod() == 'POST' && $this->validate($this->reglas)) {
            // Obtener el ID del producto a actualizar
            $id = $this->request->getVar('id');

            // Verificar si el código fue ingresado por el usuario
            $codigo = $this->request->getVar('codigo');

            if (empty($codigo)) {
                // Generar código automático basado en el ID del producto
                $codigo = 'PRO_' . str_pad($id, 3, '0', STR_PAD_LEFT); // Ejemplo: PRO_001
            } else {
                // Validar que el código ingresado no exista para otro producto
                $productoExistente = $this->productos->where('codigo', $codigo)
                    ->where('id !=', $id) // Excluir el producto actual
                    ->first();

                if ($productoExistente) {
                    // Redirigir de vuelta con un mensaje de error si el código ya existe
                    return redirect()->back()->with('error', 'El código ya existe para otro producto.');
                }
            }

            // Procesar la imagen
            $imagen = $this->request->getFile('imagen');
            if (!empty($imagen->getName())) {
                $img = 'pro_' . date("YmdHis") . '.jpg';
            } else {
                $img = $this->request->getVar('foto_actual');
            }

            // Obtener la imagen actual del producto para eliminarla si es necesario
            $imgDelete = $this->productos->where('id', $id)->first();
            if ($imgDelete['imagen'] != 'default.jpg' && $imgDelete['imagen'] != $img) {
                if (file_exists(FCPATH . "img/productos/" . $imgDelete['imagen'])) {
                    unlink(FCPATH . "img/productos/" . $imgDelete['imagen']);
                }
            }

            // Actualizar el producto en la base de datos
            $data = [
                'codigo' => $codigo, // Usar el código generado o el proporcionado
                'descripcion' => $this->request->getVar('descripcion'),
                'precio_compra' => $this->request->getVar('precio_compra'),
                'precio_venta' => $this->request->getVar('precio_venta'),
                'stock' => $this->request->getVar('stock'),
                'stock_minimo' => $this->request->getVar('stock_minimo'),
                'id_marca' => $this->request->getVar('marca'),
                'id_categoria' => $this->request->getVar('categoria'),
                'imagen' => $img
            ];

            $this->productos->update($id, $data);

            // Mover la nueva imagen al directorio si se ha subido una
            if (!empty($imagen->getName())) {
                $ruta_img = FCPATH . 'img/productos/';
                if (!file_exists($ruta_img)) {
                    mkdir($ruta_img, 0777, true); // Crear el directorio si no existe
                }
                $imagen->move($ruta_img, $img);
            }

            return redirect()->to(base_url() . '/productos')->with('message', 'Producto modificado con éxito.');
        } else {
            // Cargar los datos necesarios para la vista de edición
            $id = $this->request->getVar('id');
            $data['marcas'] = $this->marcas->where('estado', 1)->findAll();
            $data['categorias'] = $this->categorias->where('estado', 1)->findAll();
            $data['producto'] = $this->productos->where('id', $id)->first();
            $data['validation'] = $this->validator;

            echo view("templates/header");
            echo view("productos/editar", $data);
            echo view("templates/footer");
        }
    }


    public function eliminar($id)
    {
        if (!verificar('eliminar producto', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->productos->update($id, ['estado' => 0]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Producto eliminado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al eliminar el producto');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reciclaje()
    {
        if (!verificar('eliminar producto', $this->session->permisos)) {
            echo view("permisos");
            exit;
        }
        $data['script'] = 'productos.js';
        echo view("templates/header");
        echo view("productos/reciclaje", $data);
        echo view("templates/footer");
    }
    public function vaciar()
    {
        if (verificar('eliminar producto', $this->session->permisos)) {
            $this->productos->select('*');
            $this->productos->join('marcas', 'productos.id_marca = marcas.idmarca');
            $this->productos->join('categorias', 'productos.id_categoria = categorias.idcat');
            $data = $this->productos->where('productos.estado', 0)->findAll();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['imagen'] = '<img class="img-thumbnail" src="' . base_url('img/productos/' . $data[$i]['imagen']) . '" width="100">';
                $data[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                $data[$i]['acciones'] = '<div class"d-flex text-center">
                    <button class="btn btn-success" onclick="ingresar_pro(' . $data[$i]['id'] . ')"><i class="fas fa-reply"></i></button>
                    </div>';
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function restaurar($id)
    {
        if (!verificar('eliminar producto', $this->session->permisos)) {
            $mensaje = array('icono' => 'error', "mensaje" => 'No tienes permisos');
        } else {
            $data = $this->productos->update($id, ['estado' => 1]);
            if ($data) {
                $mensaje = array('icono' => 'success', "mensaje" => 'Producto reingresado con éxito');
            } else {
                $mensaje = array('icono' => 'error', "mensaje" => 'Error al reingresar el producto');
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
}
