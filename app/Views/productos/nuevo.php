<div class="card radius-10 border-start border-0 border-3 border-info">
    <form action="<?php echo base_url('productos/registrar') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" value="0" id="id" name="id">
        <div class="card-body">
            <h6 class="card-title text-center">Nuevo producto</h6>
            <hr>
            <div class="alert border-0 border-start border-5 border-info alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-info"><i class='bx bx-info-square'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-info">Advertencia</h6>
                        <div>Todo los campos con <b class="text-danger">*</b> son obligatorios</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php csrf_field(); ?>
            <div class="row p-4">
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" placeholder="Código de barras" id="codigo" name="codigo" value="<?= set_value('codigo') ?>">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('codigo'); ?></span>
                        <?php } ?>
                        <label for="codigo"><i class="fas fa-barcode"></i> Codigo de barras</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" placeholder="Descripción" name="descripcion" value="<?= set_value('descripcion') ?>">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('descripcion'); ?></span>
                        <?php } ?>
                        <label for="descripcion"><i class="fas fa-align-left"></i> Nombre <span class="text-danger fw-bold">*</span></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" placeholder="Precio compra" id="precio_compra" name="precio_compra" value="<?= set_value('precio_compra') ?>" oninput="calcularPrecioVenta()">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger"><?php echo $validation->getError('precio_compra'); ?></span>
                        <?php } ?>
                        <label for="precio_compra"><b>Gs</b> Precio Compra <span class="text-danger fw-bold">*</span></label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="precio_venta" placeholder="Precio Venta" name="precio_venta" value="<?= set_value('precio_venta') ?>">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('precio_venta'); ?></span>
                        <?php } ?>
                        <label for="precio_venta"><b>Gs</b> Precio Venta <span class="text-danger fw-bold">*</span></label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" placeholder="Stock Actual" name="stock" value="<?= set_value('stock') ?>">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('stock'); ?></span>
                        <?php } ?>
                        <label for="">Stock Actual <span class="text-danger fw-bold">*</span></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" placeholder="Stock Mínimo" name="stock_minimo" value="<?= set_value('stock_minimo') ?>">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('stock_minimo'); ?></span>
                        <?php } ?>
                        <label for="">Stock Alerta <span class="text-danger fw-bold">*</span></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select id="marca" name="marca" class="form-control">
                            <?php foreach ($marcas as $row) { ?>
                                <option value="<?php echo $row['idmarca']; ?>"><?php echo $row['marca']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="marca"><i class="fas fa-copyright"></i> Marca <span class="text-danger fw-bold">*</span></label>
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('marca'); ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select id="categoria" name="categoria" class="form-control">
                            <?php foreach ($categorias as $row) { ?>
                                <option value="<?php echo $row['idcat']; ?>"><?php echo $row['categoria']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="categoria">Categorias <span class="text-danger fw-bold">*</span></label>
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('categoria'); ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="custom-file">
                        <label for="imagen" class="custom-file-label"><i class="fas fa-image"></i> Imagen (Opcional)</label>
                        <input id="imagen" class="form-control" type="file" name="imagen">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger "><?php echo $validation->getError('imagen'); ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-primary" type="submit"><i class="fas fa-registered"></i> Registrar</button>
            <a href="<?php echo base_url('productos') ?>" class="btn btn-danger"><i class="fas fa-ban"></i> Atras</a>
        </div>
    </form>
</div>


<!-- <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
<script>
    new AutoNumeric('#precio_compra', {
        decimalPlaces: 0,
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        currencySymbol: '',
        currencySymbolPlacement: 'p'
    });
    new AutoNumeric('#precio_venta', {
        decimalPlaces: 0,
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        currencySymbol: '',
        currencySymbolPlacement: 'p'
    });
</script> -->