<div class="card">
    <div class="card-body">
        <h6 class="card-title text-center">Historial ventas</h6>
        <hr>
        <div class="d-flex justify-content-center mb-3">
            <div class="form-group">
                <label for="desde">Desde</label>
                <input id="desde" class="form-control" type="date">
            </div>
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input id="hasta" class="form-control" type="date">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-light" id="t_ventas" style="width: 100%;">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Metodo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="1"></th>
                        <th>Total: 0.00</th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>