<div class="card">
    <div class="card-body">
        <h6 class="card-title text-center">Nueva compra</h6>
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
                    <table class="table table-striped table-bordered table-hover" id="table" style="width: 100%;">
                        <thead class="border-info">
                            <tr>
                                <th>Descripción</th>
                                <th>Cant</th>
                                <th>Precio</th>
                                <th>Sub Total</th>
                                <th><button class="btn btn-danger btn-sm" id="btnVaciar" type="button"><i class="fas fa-trash"></i></button></th>
                            </tr>
                        </thead>
                        <tbody id="tblDetalle">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button class="btn btn-outline-primary" type="button" onclick="generar(1)">Generar Compra (F12)</button>
        </div>
    </div>
</div>