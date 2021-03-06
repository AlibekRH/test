<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Sub Speciality Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sub Speciality List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sub Speciality List</h3>
                        <a href="<?php echo base_url() . 'AddSubSpeciality'; ?>" class="btn btn-success pull-right">Add Sub Speciality</a>
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
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Specility Name</th>
                                    <th>Sub Specility Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($sub_specility_details) {
                                    foreach ($sub_specility_details as $key => $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $key + 1; ?></td>
                                            <td><?php echo $row->main_speciality_name; ?></td>
                                            <td><?php echo $row->specility_name; ?></td>
                                            <td>
                                                <a href="<?php echo base_url() . 'UpdateSubSpeciality/' . $row->id; ?>" title="Edit" class="btn btn-success">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <!--a href="<?php //echo base_url() . 'DeleteSpeciality/' . $row->id; ?>" title="Delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">
                                                    <i class="fa fa-trash"></i>
                                                </a-->
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
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