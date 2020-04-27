<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Patient Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Patient Detail</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Patient Detail (<?php echo $patient_data[0]->user_uid; ?>)</h3>
                        <?php if ($patient_data[0]->activate_status == 1) { ?>
                            <span class="pull-right"><span style="margin-top: 3px;margin-left: 5px;padding: 0.5em .6em .5em !important;" class="label label-success pull-right">Account Status : Active</span></span>
                        <?php } if ($patient_data[0]->activate_status == 0) { ?>
                            <span class="pull-right"><span style="margin-top: 3px;margin-left: 5px;padding: 0.5em .6em .5em !important;" class="label label-default pull-right">Account Status : Deactive</span></span>
                        <?php } ?>
                        <a href="<?php echo base_url() . 'UserAppointment/' . $this->uri->segment(2) . '/patient'; ?>" class="pull-right">
                            <span style="margin-top: 3px;margin-left: 5px;padding: 0.5em .6em .5em !important;" class="label label-warning pull-right">Appointment</span>
                        </a>
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
                                                        <?php if ($patient_data[0]->profile_image != '') { ?>
                                                            <img src="<?php echo $patient_data[0]->profile_image; ?>" height="40px" width="40px">
                                                            <?php
                                                        } else {
                                                            echo "N/A";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Name</td>
                                                    <td><?php echo $patient_data[0]->name; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><?php echo $patient_data[0]->email; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Phone Number</td>
                                                    <td><?php echo '+' . $patient_data[0]->country_code . " - " . $patient_data[0]->phone_number; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Gender</td>
                                                    <td><?php echo ($patient_data[0]->gender) ? $patient_data[0]->gender : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>DOB</td>
                                                    <td><?php echo ($patient_data[0]->dob) ? $patient_data[0]->dob : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Height</td>
                                                    <td><?php echo ($patient_data[0]->height) ? $patient_data[0]->height : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Weight</td>
                                                    <td><?php echo ($patient_data[0]->weight) ? $patient_data[0]->weight : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>BMI</td>
                                                    <td><?php echo ($patient_data[0]->bmi) ? $patient_data[0]->bmi : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Language</td>
                                                    <td><?php echo ($patient_data[0]->language) ? $patient_data[0]->language : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Blood Group</td>
                                                    <td><?php echo ($patient_data[0]->blood_group) ? $patient_data[0]->blood_group : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Language Spoken</td>
                                                    <td><?php echo ($patient_data[0]->language) ? $patient_data[0]->language : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Address</td>
                                                    <td><?php echo ($patient_data[0]->address) ? $patient_data[0]->address : "N/A"; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Medical Details -->
                            <div class="panel box box-danger">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" class="text-danger" data-parent="#accordion" href="#medical">
                                            Medical Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="medical" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Allergies</td>
                                                    <td><?php echo ($patient_data[0]->allergies) ? $patient_data[0]->allergies : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Current Medication</td>
                                                    <td><?php echo ($patient_data[0]->current_medication) ? $patient_data[0]->current_medication : "N/a"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Past Medication</td>
                                                    <td><?php echo ($patient_data[0]->past_medication) ? $patient_data[0]->past_medication : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Diseases</td>
                                                    <td><?php echo ($patient_data[0]->diseases) ? $patient_data[0]->diseases : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Injuries</td>
                                                    <td><?php echo ($patient_data[0]->injuries) ? $patient_data[0]->injuries : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Surgeries</td>
                                                    <td><?php echo ($patient_data[0]->surgeries) ? $patient_data[0]->surgeries : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Special Needs</td>
                                                    <td><?php echo ($patient_data[0]->specialNeeds) ? $patient_data[0]->specialNeeds : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Blood Transfusion</td>
                                                    <td><?php echo ($patient_data[0]->bloodTransfusion) ? $patient_data[0]->bloodTransfusion : "N/A"; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Personal Details -->
                            <div class="panel box box-warning">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" class="text-warning" data-parent="#accordion" href="#lifestyle">
                                            Lifestyle Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="lifestyle" class="panel-collapse collapse">
                                    <div class="box-body">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <td>Smoking Habit</td>
                                                    <td><?php echo ($patient_data[0]->smoking_habit) ? $patient_data[0]->smoking_habit : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Alcohol Consumption	</td>
                                                    <td><?php echo ($patient_data[0]->alcohol_consumption ) ? $patient_data[0]->alcohol_consumption : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Activity Level</td>
                                                    <td><?php echo ($patient_data[0]->activity_level) ? $patient_data[0]->activity_level : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Food Preference</td>
                                                    <td><?php echo ($patient_data[0]->food_preference) ? $patient_data[0]->food_preference : "N/A"; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Occupation</td>
                                                    <td><?php echo ($patient_data[0]->occupation) ? $patient_data[0]->occupation : "N/A"; ?></td>
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