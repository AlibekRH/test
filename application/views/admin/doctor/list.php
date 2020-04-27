<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 25px;
        float: right;
        margin-right: 10px;
    }

    .switch input {display:none;}

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #00a65a;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #00a65a;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Doctor Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Doctors List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Doctors List</h3>
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
                                        <th>Speciality</th>
                                        <th style="width: 75px !important;">Account Status</th>
                                        <th style="width: 75px !important;">Approve Status</th>
                                        <th>Featured</th>
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
                                                <!--td>
                                                <?php //if ($row->profile_image != '') { ?>
                                                        <img src="<?php //echo $row->profile_image;      ?>" height="40px" width="40px">
                                                <?php
                                                //} else {
                                                //echo "N/A";
                                                //}
                                                ?>
                                                </td-->
                                                <td><?php echo $row->user_uid; ?></td>
                                                <td><?php echo $row->title . " " . $row->name; ?></td>
                                                <td><?php echo $row->email; ?></td>
                                                <td><?php echo '+' . $row->country_code . " - " . $row->phone_number; ?></td>
                                                <td><?php echo $row->dob; ?></td>
                                                <td><?php echo $row->speciality_name; ?></td>
                                                <td>
                                                    <?php if ($row->activate_status == 1) { ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php } if ($row->activate_status == 0) { ?>
                                                        <span class="label label-default">Deactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($row->approve_status == 1) { ?>
                                                        <span class="label label-success">Approved</span>
                                                    <?php } if ($row->approve_status == 0) { ?>
                                                        <span class="label label-default">Unapproved</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <form method="post" action="<?php echo base_url() . 'ChangeDoctorFeatureStatus/' . $row->id; ?>">
                                                        <?php if ($row->feature_status == 1) { ?>
                                                            <label class="switch">
                                                                <input name="feature_status" type="checkbox" checked="checked" onchange="this.form.submit()">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        <?php } else { ?>
                                                            <label class="switch">
                                                                <input name="feature_status" type="checkbox" onchange="this.form.submit()">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        <?php } ?>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'DoctorDetail/' . $row->id; ?>" title="Info" class="btn btn-success action_btn">
                                                        <i class="fa fa-eye"></i>
                                                    </a>&nbsp;
                                                    <a href="<?php echo base_url() . 'ChangeDoctorAccountStatus/' . $row->id . '/' . $row->activate_status; ?>" title="Update Account Status" class="btn btn-warning action_btn">
                                                        <i class="fa fa-undo"></i>
                                                    </a>&nbsp;
                                                    <?php if ($row->approve_status == 0) { ?>
                                                        <a href="<?php echo base_url() . 'ChangeDoctorApproveStatus/' . $row->id . '/1'; ?>" title="Update Approve Status" class="btn btn-warning action_btn" onclick="return confirm('Are you sure you want to approve docor?');">
                                                            <i class="fa fa-check-square-o"></i>
                                                        </a>
                                                    <?php } if ($row->approve_status == 1) { ?>
                                                        <a href="<?php echo base_url() . 'ChangeDoctorApproveStatus/' . $row->id . '/0' ?>" title="Update Approve Status" class="btn btn-warning action_btn" onclick="return confirm('Are you sure you want to unapprove docor?');">
                                                            <i class="fa fa-close"></i>
                                                        </a>
                                                    <?php } ?>
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