<div class="card radius-10 border-start border-0 border-3 border-info">
    <form action="<?php echo base_url('roles/actualizar') ?>" method="POST" autocomplete="off">
        <div class="card-body">
            <h6 class="card-title text-center">Modificar rol</h6>
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
            <input type="hidden" value="<?php echo $rol['id']; ?>" name="id">

            <div class="row p-4">
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" placeholder="Nombre" name="nombre" value="<?= set_value('nombre', $rol['nombre']); ?>">
                        <?php if (isset($validation)) { ?>
                            <span class="text-danger"><?php echo $validation->getError('nombre'); ?></span>
                        <?php } ?>
                        <label for="">Nombre <span class="text-danger fw-bold">*</span></label>
                    </div>
                </div>
                <label for="">Permisos <span class="text-danger">*</span></label>
                <hr>
                <div class="accordion accordion-flush row" id="accordionFlushExample">
                    <?php foreach ($permisos as $permiso) {
                        //$campos = 
                    ?>
                        <div class="col-md-4">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-<?php echo $permiso['modulo']; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $permiso['modulo']; ?>" aria-expanded="false" aria-controls="<?php echo $permiso['modulo']; ?>">
                                        <?php echo ucfirst($permiso['modulo']); ?>
                                    </button>
                                </h2>
                                <div id="<?php echo $permiso['modulo']; ?>" class="accordion-collapse collapse" aria-labelledby="flush-<?php echo $permiso['modulo']; ?>" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <?php $lista = json_decode($permiso['campos'], true);
                                        for ($i = 0; $i < count($lista); $i++) { ?>
                                            <div class="btn-group-toggle mb-1" data-toggle="buttons">
                                                <label class="btn btn-outline-primary">
                                                    <input type="checkbox" name="permisos[]" value="<?php echo $lista[$i]; ?>" <?php echo (verificar($lista[$i], $activos)) ? 'checked' : ''; ?>> <?php echo ucfirst($lista[$i]); ?>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-outline-primary" type="submit"><i class="fas fa-registered"></i> Modificar</button>
                <a href="<?php echo base_url('roles'); ?>" class="btn btn-outline-danger"><i class="fas fa-ban"></i> Atras</a>
            </div>
        </div>
    </form>
</div>