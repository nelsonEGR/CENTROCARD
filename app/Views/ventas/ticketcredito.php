<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="<?php echo base_url('css/ticket.css') ?>">
</head>

<body>
    <div class="ticket">
        <div class="logo">
            <img src="<?php echo base_url('img/logo.png') ?>" alt="Logo de Mi Negocio">
        </div>
        <div class="business-info">
            <h3><?php echo $empresa['nombre']; ?></h3>
            <p><?php echo $empresa['direccion']; ?></p>
            <p><?php echo $empresa['telefono']; ?></p>
            <p><?php echo $empresa['correo']; ?></p>
        </div>
        <div class="ticket-details">
            <p>Fecha: <?php 
            $dato = $credito['fecha'];
            $fecha = date('d/m/Y', strtotime($dato));
            $hora = date('h:i A', strtotime($dato));
            echo $fecha . ' ' . $hora; ?></p>
            <p>Folio: <?php echo $credito['id']; ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalAbono = 0;
                    $item = 1;
                    foreach ($abonos as $abono) {
                        $totalAbono += $abono['abono'];
                        ?>
                        <tr>
                            <td><?php echo $item; ?></td>
                            <td><?php echo $abono['abono']; ?></td>
                            <td><?php echo $abono['fecha']; ?></td>
                        </tr>
                    <?php $item++; } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right"><h3>Total: <?php echo number_format($credito['monto'], 2); ?></h3></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right"><h3>Abonado: <?php echo number_format($totalAbono, 2); ?></h3></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-right"><h3>Pendiente: <?php echo number_format($credito['monto'] - $totalAbono, 2); ?></h3></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>