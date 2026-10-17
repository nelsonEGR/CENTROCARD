<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run()
    {
        $findOrCreate = function (string $table, string $primaryKey, array $where, array $data): array {
            $row = $this->db->table($table)->where($where)->get()->getRowArray();
            if ($row !== null) {
                return $row;
            }

            $this->db->table($table)->insert($data);

            return $this->db->table($table)
                ->where($primaryKey, $this->db->insertID())
                ->get()
                ->getRowArray();
        };

        $caja = $findOrCreate('cajas', 'id', ['caja' => 'CAJA GENERAL'], [
            'caja'   => 'CAJA GENERAL',
            'estado' => 1,
        ]);

        $permissions = json_encode([
            'listar usuarios', 'crear usuario', 'editar usuario', 'eliminar usuario',
            'listar clientes', 'crear cliente', 'editar cliente', 'eliminar cliente',
            'actualizar', 'backup', 'listar roles', 'crear rol', 'editar rol', 'eliminar rol',
            'listar categorias', 'crear categoria', 'editar categoria', 'eliminar categoria',
            'listar marcas', 'crear marca', 'editar marca', 'eliminar marca',
            'listar productos', 'crear producto', 'editar producto', 'eliminar producto',
            'nueva compra', 'listar compras', 'anular compra', 'nueva venta', 'listar ventas',
            'anular venta', 'creditos ventas', 'nueva cotizacion', 'listar cotizaciones',
            'listar cajas', 'crear caja', 'editar caja', 'eliminar caja', 'apertura y cierre',
        ], JSON_THROW_ON_ERROR);

        $role = $findOrCreate('roles', 'id', ['nombre' => 'ADMINISTRADOR'], [
            'nombre'    => 'ADMINISTRADOR',
            'permisos'  => $permissions,
            'estado'    => 1,
        ]);

        $findOrCreate('usuarios', 'id', ['usuario' => 'admin'], [
            'usuario'  => 'admin',
            'nombre'   => 'Administrador local',
            'correo'   => 'admin@centrocar.dev',
            'clave'    => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',
            'id_caja'  => $caja['id'],
            'estado'   => 1,
            'id_rol'   => $role['id'],
        ]);

        $findOrCreate('clientes', 'id', ['correo' => 'publico@centrocar.dev'], [
            'dni'       => '00000000',
            'nombre'    => 'PUBLICO EN GENERAL',
            'telefono'  => '000000000',
            'direccion' => 'LOCAL',
            'correo'    => 'publico@centrocar.dev',
            'estado'    => 1,
        ]);

        $categoria = $findOrCreate('categorias', 'idcat', ['categoria' => 'GENERAL'], [
            'categoria' => 'GENERAL',
            'estado'    => 1,
        ]);

        $marca = $findOrCreate('marcas', 'idmarca', ['marca' => 'GENERICO'], [
            'marca'  => 'GENERICO',
            'estado' => 1,
        ]);

        $findOrCreate('productos', 'id', ['codigo' => 'DEMO-001'], [
            'codigo'          => 'DEMO-001',
            'descripcion'     => 'Producto de demostracion',
            'precio_compra'   => 10.00,
            'precio_venta'    => 15.00,
            'stock_minimo'    => 1,
            'stock'           => 10,
            'id_marca'        => $marca['idmarca'],
            'id_categoria'    => $categoria['idcat'],
            'imagen'          => 'default.jpg',
            'estado'          => 1,
        ]);

        $findOrCreate('configuracion', 'id', ['correo' => 'local@centrocar.dev'], [
            'ruc'       => '00000000000',
            'nombre'    => 'CENTROCARD LOCAL',
            'telefono'  => '000000000',
            'direccion' => 'LOCAL',
            'correo'    => 'local@centrocar.dev',
            'mensaje'   => 'Instalacion local de desarrollo',
            'impuesto'  => 10,
        ]);
    }
}
