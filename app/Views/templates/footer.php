</div>
</div>
<!--end page wrapper -->
<!--start overlay-->
<div class="overlay toggle-icon"></div>
<!--end overlay-->
<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<!--End Back To Top Button-->
<footer class="page-footer">
    <p class="mb-0">Copyright © <?php echo date('Y'); ?>. All right reserved.</p>
</footer>
</div>
<!--end wrapper-->
<!--start switcher (deshabilitado: personalizador de tema del template)-->
<div class="modal fade" id="modalTicket" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed id="ticketReporte" width="100%" height="400">
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
<!--plugins-->
<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/simplebar/js/simplebar.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/metismenu/js/metisMenu.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/chartjs/js/Chart.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/chartjs/js/Chart.extension.js'); ?>"></script>
	<script src="<?php echo base_url('assets/plugins/sparkline-charts/jquery.sparkline.min.js'); ?>"></script>
<!--app JS-->
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/all.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/DataTables/datatables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/botones-perzonalizados.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/sweetalert2.all.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo base_url('js/notie.min.js'); ?>"></script>
<script>
    const base_url = "<?php echo base_url(); ?>";
    const ganaciaPorcentaje = "<?= env('GANANCIA'); ?>";
</script>
<script src="<?php echo base_url('js/funciones.js'); ?>"></script>
<?php if (!empty($script)) { ?>
    <script src="<?php echo base_url('js/' . $script); ?>"></script>
<?php } ?>
</body>
</html>