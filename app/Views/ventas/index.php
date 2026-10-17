<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-center">Nueva venta</h6>
                <hr>
                <div class="alert alert-info" role="alert">
                    <div class="text-center">
                        <h5>Total a Pagar</h5>
                        <hr>
                        <h3 id="total">0.00</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-outline-primary">
                                <input type="radio" id="barcode" checked name="buscarProducto"><i class="fas fa-barcode"></i> Barcode
                            </label>
                            <label class="btn btn-outline-info">
                                <input type="radio" id="nombre" name="buscarProducto"><i class="fas fa-list"></i> Nombre
                            </label>
                        </div>
                        <!-- input para buscar codigo -->
                        <div class="input-group mb-2" id="containerCodigo">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input class="form-control" type="text" id="buscarProductoCodigo" placeholder="Ingrese Barcode - Enter" autocomplete="off">
                        </div>

                        <!-- input para buscar nombre -->
                        <div class="input-group d-none mb-2" id="containerNombre">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input class="form-control" type="text" id="buscarProductoNombre" placeholder="Buscar Producto" autocomplete="off">
                        </div>

                        <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>
                    </div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">Cant</th>
                                        <th class="text-center">Precio</th>
                                        <th class="text-center">Sub Total</th>
                                        <th class="text-center"><button class="btn btn-danger btn-sm" id="btnVaciar" type="button"><i class="fas fa-trash"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody id="tblDetalleVenta">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="form-floating mb-3">
                    <input type="hidden" id="id_cli" name="id_cli" value="1">
                    <input id="nom_cli" class="form-control" type="text" name="nom_cli" placeholder="Ingrese Ruc del Cliente">
                    <label for="nom_cli"><i class="fas fa-users"></i> Ingrese Ruc del Cliente</label>
                </div>
                <div class="form-floating mb-3">
                    <input id="dir_cli" class="form-control" type="text" disabled>
                    <label for="dir_cli"><i class="fas fa-home"></i> Dirección</label>
                </div>
                <div class="form-floating mb-3">
                    <select id="metodo" class="form-control">
                        <option value="Contado">Contado</option>
                        <option value="Credito">Credito</option>
                    </select>
                    <label for="metodo">Metodo</label>
                </div>
                <div class="form-floating mb-3">
                    <input id="pago_con" class="form-control" type="number" step="0.01" min="0.01">
                    <label for="pago_con"> Pago con</label>
                </div>
                <div class="form-floating mb-3">
                    <input id="cambio" class="form-control" type="number" disabled>
                    <label for="cambio"> Cambio</label>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-lg" type="button" onclick="generar(0)">Generar Venta (F12)</button>
                </div>
            </div>
        </div>
    </div>
</div>