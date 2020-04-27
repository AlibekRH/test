<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>App Home Screen Setting</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">App Home Screen Setting</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">App Home Screen Setting (Doctors)</h3>
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
                                        <th>Name(English)</th>
                                        <th>Name(Russian)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($app_home_screen_setting) {
                                        $specialityArr = explode(',', $app_home_screen_setting[0]->speciality);
                                    } else {
                                        $specialityArr = array();
                                    }
                                    if ($specility) {
                                        foreach ($specility as $key => $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->specility_name; ?></td>
                                                <td><?php echo $row->specility_name_ru; ?></td>
                                                <td>
                                                    <?php if (in_array($row->id, $specialityArr)) { ?>
                                                        <a href="<?php echo base_url() . 'AddSpecialityForApp/0/' . $row->id; ?>" title="Remove From App" class="btn btn-danger action_btn">
                                                            <i class="fa fa-times-rectangle-o"></i> Remove
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo base_url() . 'AddSpecialityForApp/1/' . $row->id; ?>" title="Show On App" class="btn btn-success action_btn">
                                                            <i class="fa fa-check-square-o"></i> Show
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