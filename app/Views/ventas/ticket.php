<!DOCTYPE html>
<html>

<head>
    <style>
	*{
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
}

.ticket {
    width: 226.77pt;
    margin: 1px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
    text-align: center;
    margin-bottom: 0px;
}

.logo img {
    max-width: 50px;
    height: auto;
}

.business-info {
    text-align: center;
    font-size: 14px;
}

.ticket-details {
    margin-top: 20px;
    border-top: 1px solid #ccc;
    padding-top: 10px;
}

.ticket-details h3 {
    font-size: 16px;
    margin-bottom: 10px;
}

.ticket-details p {
    font-size: 12px;
    margin: 5px 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    padding: 2px;
}

th, td {
    padding: 1px;
    text-align: left;
}
th{
    border-bottom: 3px double black;
}

.text-right{
    text-align: right;
}

/* Puedes agregar más estilos según tus necesidades */
	</style>
</head>

<body>
    <div class="ticket">
        <div class="business-info">
            <h3><?php echo $empresa['nombre']; ?></h3>
            <p><?php echo $empresa['direccion']; ?></p>
            <p><?php echo $empresa['telefono']; ?></p>
            <p><?php echo $empresa['correo']; ?></p>
        </div>
        <div class="ticket-details">
            <p>Fecha: <?php
                        $dato = $venta['fecha'];
                        $fecha = date('d/m/Y', strtotime($dato));
                        $hora = date('h:i A', strtotime($dato));
                        echo $fecha . ' ' . $hora; ?></p>
            <p>Folio: <?php echo $venta['id']; ?></p>
            <hr>
            <p>Cliente: <?php echo $venta['nombre']; ?></p>
            <p>Teléfono: <?php echo $venta['telefono']; ?></p>
            <p>Dirección: <?php echo $venta['direccion']; ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Cant</th>
                        <th>Producto</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $articulos = 0;
                    foreach ($productos as $producto) {
                        $articulos += $producto['cantidad'];
                    ?>
                        <tr>
                            <td><?php echo $producto['cantidad']; ?></td>
                            <td><?php echo $producto['descripcion']; ?></td>
                            <td><?php echo number_format($producto['precio'], 2); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Mostrar el número de artículos -->
                    <tr>
                        <td colspan="3">N° de artículos: <?php echo $articulos; ?></td>
                    </tr>

                    <?php
                    // Calcular el impuesto (10% del total)
                    $impuesto = $venta['total'] * env('IMPUESTO') / 100;
                    ?>

                    <tr>
                        <td></td>
                        <td class="text-right">
                            <h3>SubTotal:</h3>
                        </td>
                        <td class="text-right">
                            <h3><?php echo number_format(($venta['total'] - $impuesto), 0, '', '.'); ?></h3>
                        </td>
                    </tr>

                    <!-- Mostrar el impuesto -->
                    <tr>
                        <td></td>
                        <td class="text-right">
                            <h3>Impuesto (<?= env('IMPUESTO') ?>%):</h3>
                        </td>
                        <td class="text-right">
                            <h3><?php echo number_format($impuesto, 0, '', '.'); ?></h3>
                        </td>
                    </tr>

                    <!-- Mostrar el total -->
                    <tr>
                        <td></td>
                        <td class="text-right">
                            <h3>Total:</h3>
                        </td>
                        <td class="text-right">
                            <h3><?php echo number_format($venta['total'], 0, '', '.'); ?></h3>
                        </td>
                    </tr>

                    <!-- Mostrar el pago con -->
                    <tr>
                        <td></td>
                        <td class="text-right">
                            <h3>Pago con:</h3>
                        </td>
                        <td class="text-right">
                            <h3><?php echo number_format($venta['pago_con'], 0, '', '.'); ?></h3>
                        </td>
                    </tr>

                    <!-- Mostrar el cambio -->
                    <tr>
                        <td></td>
                        <td class="text-right">
                            <h3>Cambio:</h3>
                        </td>
                        <td class="text-right">
                            <h3><?php echo number_format(($venta['pago_con'] - $venta['total']), 0, '', '.'); ?></h3>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</body>

</html>