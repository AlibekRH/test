<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Payment Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Payment List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Payment List</h3>
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
                                        <th>Type</th>
                                        <th>UID</th>
                                        <th>Patient Name</th>
                                        <th>Doctor Name</th>
                                        <th>Bank Name</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Merchant Id</th>
                                        <th>Reference Id</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($payment_data) {
                                        foreach ($payment_data as $key => $row) {
                                            if ($row->type == 'appointment') {
                                                if ($row->appointment_type == 0)
                                                    $appointment_type = '(Clinic Visit)';
                                                if ($row->appointment_type == 1)
                                                    $appointment_type = '(Audio Consultation)';
                                                if ($row->appointment_type == 2)
                                                    $appointment_type = '(Video Consultation)';
                                                if ($row->appointment_type == 3)
                                                    $appointment_type = '(Chat Consultation)';
                                                if ($row->appointment_type == 4)
                                                    $appointment_type = '(Invite Specialist)';
                                            }else {
                                                $appointment_type = "";
                                            }
                                            switch ($row->appointment_status):
                                                case "":
                                                    $app_status_val = "N/A";
                                                    break;
                                                case 0:
                                                    $app_status_val = "<label class='label label-default'>Pending</label>";
                                                    break;
                                                case 1:
                                                    $app_status_val = "<label class='label label-primary'>Accepted</label>";
                                                    break;
                                                case 2:
                                                    $app_status_val = "<label class='label label-danger'>Rejected by Doctor</label>";
                                                    break;
                                                case 3:
                                                    $app_status_val = "<label class='label label-danger'>Cancelled by Patient</label>";
                                                    break;
                                                case 4:
                                                    $app_status_val = "<label class='label label-warning'>Payment Completed</label>";
                                                    break;
                                                case 5:
                                                    $app_status_val = "<label class='label label-warning'>Waiting for Recommendation</label>";
                                                    break;
                                                case 6:
                                                    $app_status_val = "<label class='label label-success'>Completed</label>";
                                                    break;
                                                case 7:
                                                    $app_status_val = "<label class='label label-success'>Completed</label>";
                                                    break;
                                                case 8:
                                                    $app_status_val = "<label class='label label-danger'>Payment not Completed</label>";
                                                    break;
                                                case 9:
                                                    $app_status_val = "<label class='label label-danger'>Closed</label>";
                                                    break;
                                            endswitch;
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo ucwords($row->type) . $appointment_type; ?></td>
                                                <td><?php echo $row->uid; ?></td>
                                                <td><a href="<?php echo base_url() . 'PatientDetail/' . $row->user_id; ?>"><?php echo $row->patient_name; ?></a></td>
                                                <td><a href="<?php echo base_url() . 'DoctorDetail/' . $row->doctor_id; ?>"><?php echo $row->title . " " . $row->doctor_name; ?></a></td>
                                                <td><?php echo $row->bank_name; ?></td>
                                                <td><?php echo $row->amount . " ₸"; ?></td>
                                                <td><?php echo date('d M, Y', strtotime($row->date)); ?></td>
                                                <td><?php echo $row->merchant_id; ?></td>
                                                <td><?php echo $row->reference; ?></td>
                                                <td><?php echo $app_status_val; ?></td>
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