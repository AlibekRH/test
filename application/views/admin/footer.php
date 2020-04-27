<footer class="main-footer">
    <strong>Copyright &copy; 2014-2016 <a style="color: #00a65a !important;" href="<?php echo base_url() . 'Dashboard'; ?>">ZumCare</a>.</strong> All rights
    reserved.
</footer>

<!-- jQuery UI 1.11.4 -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/moment/min/moment.min.js"></script>
<script src="<?php echo INCLUDE_ASSETS; ?>other/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo INCLUDE_ASSETS; ?>js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo INCLUDE_ASSETS; ?>js/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo INCLUDE_ASSETS; ?>js/demo.js"></script>
<!-- DataTables -->
<!-- DataTables -->
<script src="<?php echo INCLUDE_ASSETS; ?>other/datatables.net/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/responsive/1.0.2/js/dataTables.responsive.js"></script>
<script src="<?php echo INCLUDE_ASSETS; ?>other/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script>
    $(function () {
        $('.datatable').DataTable({
            responsive: true
        });
    });

    $('#datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        startDate: new Date()
    });

    $('#date').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        endDate: new Date()
    });



</script>


</body>
</html>