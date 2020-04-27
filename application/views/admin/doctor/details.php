<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Doctor Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Doctor Detail</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Doctor Detail (<?php echo $doctor_data[0]->user_uid; ?>)</h3>
                        <?php if ($doctor_data[0]->activate_status == 1) { ?>
                            <span class="pull-right"><b>Account Status : </b><span style="margin-top: 3px;margin-left: 5px;" class="label label-success pull-right">Active</span></span>
                        <?php } if ($doctor_data[0]->activate_status == 0) { ?>
                            <span class="pull-right"><b>Account Status : </b><span style="margin-top: 3px;margin-left: 5px;" class="label label-default pull-right">Deactive</span></span>
                        <?php } ?>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-group" id="accordion">
                            <!-- Personal Details -->
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" class="text-primary" data-parent="#accordion" href="#personal">
                                            Personal Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="personal" class="panel-collapse collapse in">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Image</td>
                                                    <td>
                                                        <?php if ($doctor_data[0]->profile_image != '') { ?>
                                                            <img src="<?php echo $doctor_data[0]->profile_image; ?>" height="40px" width="40px">
                                                            <?php
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Name</td>
                                                    <td><?php echo $doctor_data[0]->title . " " . $doctor_data[0]->name; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><?php echo $doctor_data[0]->email; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Phone Number</td>
                                                    <td><?php echo '+' . $doctor_data[0]->country_code . " - " . $doctor_data[0]->phone_number; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Gender</td>
                                                    <td><?php echo $doctor_data[0]->gender; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>DOB</td>
                                                    <td><?php echo $doctor_data[0]->dob; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Speciality</td>
                                                    <td><?php echo $doctor_data[0]->speciality_name; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Diseases</td>
                                                    <td><?php echo $doctor_data[0]->diseases; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>City</td>
                                                    <td><?php echo ($doctor_data[0]->city) ? $doctor_data[0]->city : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>ID Number</td>
                                                    <td><?php echo ($doctor_data[0]->id_number) ? $doctor_data[0]->id_number : "N/A"; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Education Details -->
                            <div class="panel box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" class="text-warning" data-parent="#accordion" href="#education">
                                            Education Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="education" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Degree</th>
                                                    <th>University</th>
                                                    <th>Graduation Year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($doctor_data[0]->education_details) {
                                                    foreach ($doctor_data[0]->education_details as $key => $row) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $key + 1; ?></td>
                                                            <td><?php echo $row->degree; ?></td>
                                                            <td><?php echo $row->university; ?></td>
                                                            <td><?php echo $row->graduation_year; ?></td>
                                                        </tr>

                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <tr>
                                                        <td colspan="4" style="text-align:center;">No Data Available</td>
                                                    </tr>                                                    
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Clinic Details -->
                            <div class="panel box box-danger">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" class="text-danger" data-parent="#accordion" href="#clinic">
                                            Clinic Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="clinic" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Clinic Name</td>
                                                    <td><?php echo ($doctor_data[0]->clinic_name) ? $doctor_data[0]->clinic_name : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Clinic Phone Number</td>
                                                    <td><?php echo ($doctor_data[0]->clinic_phone_number) ? $doctor_data[0]->clinic_phone_number : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Clinic Address</td>
                                                    <td><?php echo ($doctor_data[0]->clinic_address) ? $doctor_data[0]->clinic_address : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Clinic City</td>
                                                    <td><?php echo ($doctor_data[0]->clinic_city) ? $doctor_data[0]->clinic_city : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Clinic State</td>
                                                    <td><?php echo ($doctor_data[0]->clinic_state) ? $doctor_data[0]->clinic_state : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Clinic Pincode</td>
                                                    <td><?php echo ($doctor_data[0]->clinic_pincode) ? $doctor_data[0]->clinic_pincode : "N/A"; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Details -->
                            <div class="panel box box-success">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" class="text-success" data-parent="#accordion" href="#document">
                                            Document Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="document" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Medical Registration Proof</td>
                                                    <td>
                                                        <?php if ($doctor_data[0]->medical_registration_proof != '') { ?>
                                                            <img class="ImagePopup" src="<?php echo $doctor_data[0]->medical_registration_proof; ?>" height="40px" width="40px">
                                                            <?php
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Degree Proof</td>
                                                    <td>
                                                        <?php if ($doctor_data[0]->degree_proof != '') { ?>
                                                            <img class="ImagePopup" src="<?php echo $doctor_data[0]->degree_proof; ?>" height="40px" width="40px">
                                                            <?php
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Photo ID Proof</td>
                                                    <td>
                                                        <?php if ($doctor_data[0]->photo_id_proof != '') { ?>
                                                            <img class="ImagePopup" src="<?php echo $doctor_data[0]->photo_id_proof; ?>" height="40px" width="40px">
                                                            <?php
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Signature</td>
                                                    <td>
                                                        <?php if ($doctor_data[0]->signature != '') { ?>
                                                            <img class="ImagePopup" src="<?php echo $doctor_data[0]->signature; ?>" height="40px" width="40px">
                                                            <?php
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Services Details -->
                            <div class="panel box box-default">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" style="color: #6b6c6d !important;" data-parent="#accordion" href="#services">
                                            Services Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="services" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Clinic Visit</td>
                                                    <td><?php echo ($doctor_data[0]->consultation_settings->offline_consult_status == 1) ? "Available" : "Not Available"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Online Consultation</td>
                                                    <td><?php echo ($doctor_data[0]->consultation_settings->online_consult_status == 1) ? "Available" : "Not Available"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Invite Specialist Home</td>
                                                    <td><?php echo ($doctor_data[0]->consultation_settings->invite_consult_status == 1) ? "Available" : "Not Available"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Ask a Question</td>
                                                    <td><?php echo ($doctor_data[0]->consultation_settings->enquiry_consult_status == 1) ? "Available" : "Not Available"; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
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


<!-- The Modal -->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" data-dismiss="modal">
        <div class="modal-content"  >              
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <img src="" id="modal_image" style="width: 100%;">
            </div> 
        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $('.ImagePopup').click(function (f) {
            var img_src = $(this).attr('src');
            console.log(img_src);
            $('#modal_image').attr('src', img_src);
            $('#imagemodal').modal('show');

        });
    });
</script>