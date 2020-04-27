<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Doctor Earnings (<?php echo $type_val; ?>)</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Appointment List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Appointment List</h3>
                        <form class="pull-right" id="searchTypeForm" method="post" action="<?php echo base_url() . 'EarningAppointment/' . $this->uri->segment(2); ?>">
                            <select name="type" id="type" class="form-control">
                                <option value="total" <?php if ($search_type == 'total') { ?> selected="" <?php } ?>>Total Amount</option>
                                <option value="payable" <?php if ($search_type == 'payable') { ?> selected="" <?php } ?>>Payable Amount</option>
                            </select>
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Appointment ID</th>
                                        <th>Appointment Type</th>
                                        <th>Doctor Name</th>
                                        <th>Patient Name</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Time</th>
                                        <th>Amount</th>
                                        <th>Appointment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($appointment_data) {
                                        foreach ($appointment_data as $key => $row) {
                                            $appointment_status = "";
                                            $appointment_type = "";
                                            switch ($row->appointment_type):
                                                case 0;
                                                    $appointment_type = "Clinic Visit";
                                                    break;
                                                case 1;
                                                    $appointment_type = "Audio Consultation";
                                                    break;
                                                case 2;
                                                    $appointment_type = "Video Consultation";
                                                    break;
                                                case 3;
                                                    $appointment_type = "Chat Consultation";
                                                    break;
                                                case 4;
                                                    $appointment_type = "Home Visit";
                                                    break;
                                            endswitch;
                                            switch ($row->status):
                                                case 0:
                                                    $appointment_status = "<label class='label label-default'>Pending</label>";
                                                    break;
                                                case 1:
                                                    $appointment_status = "<label class='label label-primary'>Accepted</label>";
                                                    break;
                                                case 2:
                                                    $appointment_status = "<label class='label label-danger'>Rejected by Doctor</label>";
                                                    break;
                                                case 3:
                                                    $appointment_status = "<label class='label label-danger'>Cancelled by Patient</label>";
                                                    break;
                                                case 4:
                                                    $appointment_status = "<label class='label label-warning'>Payment Completed</label>";
                                                    break;
                                                case 5:
                                                    $appointment_status = "<label class='label label-warning'>Waiting for Recommendation</label>";
                                                    break;
                                                case 6:
                                                    $appointment_status = "<label class='label label-success'>Completed</label>";
                                                    break;
                                                case 7:
                                                    $appointment_status = "<label class='label label-success'>Completed</label>";
                                                    break;
                                                case 8:
                                                    $appointment_status = "<label class='label label-danger'>Payment not Completed</label>";
                                                    break;
                                                case 9:
                                                    $appointment_status = "<label class='label label-danger'>Closed</label>";
                                                    break;
                                            endswitch;
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->id; ?></td>
                                                <td><?php echo $appointment_type; ?></td>
                                                <td><a href="<?php echo base_url() . 'DoctorDetail/' . $row->doctor_id; ?>"><?php echo $row->doctor_name; ?></a></td>
                                                <td><a href="<?php echo base_url() . 'PatientDetail/' . $row->user_id; ?>"><?php echo $row->patient_name; ?></a></td>
                                                <td><?php echo date('d M, Y', strtotime($row->appointment_date)); ?></td>
                                                <td><?php echo date('h:i A', $row->start_time) . " - " . date('h:i A', $row->end_time); ?></td>
                                                <td><?php echo $row->amount . " ₸"; ?></td>
                                                <td><?php echo $appointment_status; ?></td>
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

<script>
    $(document).ready(function (e) {
        $('#type').change(function () {
            $('#searchTypeForm').submit();
        });
    })
</script>