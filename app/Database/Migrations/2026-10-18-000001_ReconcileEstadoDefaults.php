<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReconcileEstadoDefaults extends Migration
{
    /**
     * Columnas que deben tener un valor por defecto segun el esquema
     * declarado en CreateCentrocardSchema. Se aplican de forma
     * idempotente para reparar bases creadas sin los DEFAULT.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private array $columns = [
        ['cajas', 'estado'],
        ['categorias', 'estado'],
        ['clientes', 'estado'],
        ['configuracion', 'impuesto'],
        ['marcas', 'estado'],
        ['roles', 'estado'],
        ['usuarios', 'estado'],
        ['usuarios', 'id_rol'],
        ['productos', 'stock'],
        ['productos', 'imagen'],
        ['productos', 'estado'],
        ['compras', 'estado'],
        ['compras', 'fecha'],
        ['cotizaciones', 'fecha'],
        ['ventas', 'fecha'],
        ['ventas', 'estado'],
        ['ventas', 'caja'],
        ['ventas', 'pago_con'],
        ['ventas', 'metodo'],
        ['caja_cierre', 'monto_fin'],
        ['caja_cierre', 'total_ventas'],
        ['caja_cierre', 'status'],
        ['credventas', 'estado'],
        ['pagosventas', 'caja'],
    ];

    /**
     * Valor por defecto esperado para cada columna.
     *
     * @var array<string, string>
     */
    private array $defaults = [
        'cajas.estado'             => '1',
        'categorias.estado'        => '1',
        'clientes.estado'          => '1',
        'configuracion.impuesto'   => '0',
        'marcas.estado'            => '1',
        'roles.estado'             => '1',
        'usuarios.estado'          => '1',
        'usuarios.id_rol'          => '1',
        'productos.stock'          => '0',
        'productos.imagen'         => "'default.jpg'",
        'productos.estado'         => '1',
        'compras.estado'           => '1',
        'compras.fecha'            => 'CURRENT_TIMESTAMP',
        'cotizaciones.fecha'       => 'CURRENT_TIMESTAMP',
        'ventas.fecha'             => 'CURRENT_TIMESTAMP',
        'ventas.estado'            => '1',
        'ventas.caja'              => '1',
        'ventas.pago_con'          => '0',
        'ventas.metodo'            => "'Contado'",
        'caja_cierre.monto_fin'    => '0',
        'caja_cierre.total_ventas' => '0',
        'caja_cierre.status'       => '1',
        'credventas.estado'        => '1',
        'pagosventas.caja'         => '1',
    ];

    public function up()
    {
        foreach ($this->columns as [$table, $column]) {
            $default = $this->defaults[$table . '.' . $column];
            $this->db->query("ALTER TABLE {$table} ALTER COLUMN {$column} SET DEFAULT {$default}");
        }
    }

    public function down()
    {
        foreach ($this->columns as [$table, $column]) {
            $this->db->query("ALTER TABLE {$table} ALTER COLUMN {$column} DROP DEFAULT");
        }
    }
}
