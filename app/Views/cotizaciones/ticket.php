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
            $dato = $cotizacion['fecha'];
            $fecha = date('d/m/Y', strtotime($dato));
            $hora = date('h:i A', strtotime($dato));
            echo $fecha . ' ' . $hora; ?></p>
            <p>Folio: <?php echo $cotizacion['id']; ?></p>
            <hr>
            <p>Cliente: <?php echo $cotizacion['nombre']; ?></p>
            <p>Teléfono: <?php echo $cotizacion['telefono']; ?></p>
            <p>Dirección: <?php echo $cotizacion['direccion']; ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Cant</th>
                        <th>Producto</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $articulos = 0;
                    foreach ($productos as $producto) {
                        $articulos += $producto['cantidad'];
                        ?>
                        <tr>
                            <td><?php echo $producto['cantidad']; ?></td>
                            <td><?php echo $producto['descripcion']; ?></td>
                            <td><?php echo $producto['precio']; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3">N° de articulos: <?php echo $articulos; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="text-right"><h3>Total: </h3></td>
                        <td><h3><?php echo number_format($cotizacion['total'], 2); ?></h3></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>