<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Doctor Earnings</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Doctor Earnings</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Doctor Earnings(<?php echo $doctor_data->name; ?>)</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($doctor_data->payment_history) {
                                        foreach ($doctor_data->payment_history as $key => $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->amount . " ₸"; ?></td>
                                                <td><?php echo date('d M, Y', strtotime($row->payment_date)); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<!-- MAke Payment Modal -->
<div id="MakePaymentModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" style="margin-top: 0 !important;" data-dismiss="modal">&times;</button>
                <h4 class="modal-title green">Make Payment</h4>
            </div>
            <form id="AddSerForm" method="post" action="<?php echo base_url() . 'MakePaymentToDoctor'; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" min="1" step="0.01" class="form-control" name="amount" id="amount" value="" required="">
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="date" name="date" required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="doctor_id" id="doctor_id" value="">
                        <input type="submit" class="btn btn-success pull-right" name="sub_btn" id="sub_btn" value="Pay">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.MakePayment').click(function () {
            var doc_id = $(this).attr('data-id');
            $('#doctor_id').val(doc_id);
            $('#MakePaymentModal').modal('show');
        });
    });
</script>