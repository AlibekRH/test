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
                        <h3 class="box-title">Doctor Earnings</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?php if ($this->session->flashdata('error_msg')) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo $this->session->flashdata('error_msg'); ?>
                            </div>
                        <?php } if ($this->session->flashdata('success_msg')) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo $this->session->flashdata('success_msg'); ?>
                            </div>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>UID</th>
                                        <th>Doctor Name</th>
                                        <th>Total Amount</th>
                                        <th>Payable Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($doctor_data) {
                                        foreach ($doctor_data as $key => $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->user_uid; ?></td>
                                                <td><a href="<?php echo base_url() . 'DoctorDetail/' . $row->id; ?>"><?php echo $row->title . " " . $row->name; ?></a></td>
                                                <td><?php echo $row->total_amount . " ₸"; ?></td>
                                                <td><?php echo $row->payable_amount . " ₸"; ?></td>
                                                <td><?php echo $row->paid_amount . " ₸"; ?></td>
                                                <td><?php echo ($row->payable_amount - $row->paid_amount) . " ₸"; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'EarningAppointment/' . $row->id; ?>" title="List" class="btn btn-success">
                                                        <i class="fa fa-list"></i>
                                                    </a>&nbsp;
                                                    <a data-id="<?php echo $row->id; ?>" href="" title="Make Payment" class="btn btn-success action_btn MakePayment" data-toggle="modal">
                                                        <i class="fa fa-money"></i>
                                                    </a>&nbsp;
                                                    <a href="<?php echo base_url() . 'EarningsDetails/' . $row->id; ?>" title="Info" class="btn btn-success action_btn">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
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