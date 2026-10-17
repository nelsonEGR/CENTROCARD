<?php if (!empty(session()->getFlashdata('message'))) { ?>
    <div class="alert border-0 border-start border-5 border-danger alert-dismissible fade show py-2">
        <div class="d-flex align-items-center">
            <div class="font-35 text-danger"><i class='bx bxs-check-circle'></i>
            </div>
            <div class="ms-3">
                <h6 class="mb-0 text-danger">Mensaje</h6>
                <div><?php echo session()->getFlashdata('message'); ?></div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card bg-gradient-moonlit">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-white">Total Usuarios</p>
                        <h4 class="my-1 text-white"><?php echo $usuarios; ?></h4>
                        <a class="mb-0 font-13" href="<?php echo base_url('usuarios'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                        <i class='fas fa-user'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card split-bg-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-white">Total Clientes</p>
                        <h4 class="my-1 text-white"><?php echo $clientes; ?></h4>
                        <a class="mb-0 font-13 text-dark" href="<?php echo base_url('clientes'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class='fas fa-users'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card bg-gradient-cosmic">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-white">Total Productos</p>
                        <h4 class="my-1 text-white"><?php echo $productos; ?></h4>
                        <a class="mb-0 font-13 text-dark" href="<?php echo base_url('productos'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                        <i class='fas fa-list-alt'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card bg-gradient-blues">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-white">Total categorias</p>
                        <h4 class="my-1 text-white"><?php echo $categorias; ?></h4>
                        <a class="mb-0 font-13 text-dark" href="<?php echo base_url('categorias'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons rounded-circle bg-light-success text-white ms-auto"><i class="fas fa-tags"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card bg-gradient-ohhappiness">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-white">Total marcas</p>
                        <h4 class="my-1 text-white"><?php echo $marcas; ?></h4>
                        <a class="mb-0 font-13 text-dark" href="<?php echo base_url('marcas'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class="fas fa-copyright"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secwhiteondary">Cotizaciones</p>
                        <h4 class="my-1 text-white"><?php echo $cotizaciones; ?></h4>
                        <a class="mb-0 font-13 text-dark" href="<?php echo base_url('cotizaciones'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons rounded-circle bg-light-success text-white ms-auto"><i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card bg-gradient-kyoto">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-white">Compras por día</p>
                        <h4 class="my-1 text-white"><?php echo $compras; ?></h4>
                        <a class="mb-0 font-13 text-dark" href="<?php echo base_url('compras'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-kyoto text-white ms-auto"><i class="fas fa-truck"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Ventas por día</p>
                        <h4 class="my-1 text-danger"><?php echo $ventas; ?></h4>
                        <a class="mb-0 font-13" href="<?php echo base_url('ventas'); ?>">Detalle</a>
                    </div>
                    <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fas fa-cash-register"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!--end row-->

<div class="row">
    <div class="col-12 col-lg-7">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center">
                    <div class="form-group">
                        <label for="year">Año</label>
                        <select id="year" onchange="comparacion()">
                            <?php
                            $fecha = date('Y');
                            for ($i = 2020; $i <= $fecha; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php echo ($fecha == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="chart-container-1">
                    <canvas id="comparacion"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Producto con stock mínimo</h6>
                    </div>

                </div>
                <div class="chart-container-2 mt-4">
                    <canvas id="minimo"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->