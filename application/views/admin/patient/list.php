<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Patient Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Patients List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Patients List</h3>
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
                                        <!--th>Image</th-->
                                        <th>UID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>DOB</th>
                                        <th>Account Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($patient_data) {
                                        foreach ($patient_data as $key => $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <!--td>
                                                <?php //if ($row->profile_image != '') { ?>
                                                        <img src="<?php //echo $row->profile_image;  ?>" height="40px" width="40px">
                                                <?php
                                                //} else {
                                                //echo "N/A";
                                                //}
                                                ?>
                                                </td-->
                                                <td><?php echo $row->user_uid; ?></td>
                                                <td><a href="<?php echo base_url().'PatientDetail/'.$row->id; ?>"><?php echo $row->title . " " . $row->name; ?></a></td>
                                                <td><?php echo $row->email; ?></td>
                                                <td><?php echo '+' . $row->country_code . " - " . $row->phone_number; ?></td>
                                                <td><?php echo ($row->dob) ? $row->dob : "N/A"; ?></td>
                                                <td>
                                                    <?php if ($row->activate_status == 1) { ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php } if ($row->activate_status == 0) { ?>
                                                        <span class="label label-default">Deactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'PatientDetail/' . $row->id; ?>" title="Info" class="btn btn-success action_btn">
                                                        <i class="fa fa-eye"></i>
                                                    </a>&nbsp;
                                                    <a href="<?php echo base_url() . 'ChangePatientAccountStatus/' . $row->id . '/' . $row->activate_status; ?>" title="Update Account Status" class="btn btn-warning action_btn">
                                                        <i class="fa fa-undo"></i>
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