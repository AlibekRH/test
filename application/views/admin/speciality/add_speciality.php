<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Speciality Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Speciality</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Speciality</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <?php if ($this->session->flashdata('error_msg')) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?php echo $this->session->flashdata('error_msg') ?>
                            </div>
                        <?php } ?>
                        <form role="form" action='<?php echo base_url() . 'AddSpeciality'; ?>' method='post' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>Speciality Name(English)</label>
                                <input type="text" class="form-control" id="speciality_name" placeholder="Enter Speciality Name" name="speciality_name" required='' value="<?php echo set_value('speciality_name'); ?>">
                                <span class="text-danger"><?php echo form_error('speciality_name'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Speciality Name(Russian)</label>
                                <input type="text" class="form-control" id="specility_name_ru" placeholder="Enter Speciality Name" name="specility_name_ru" required='' value="<?php echo set_value('specility_name_ru'); ?>">
                                <span class="text-danger"><?php echo form_error('specility_name_ru'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" id="image" name="image" accept="image/*">
                            </div>
                            <input type="submit" class="btn btn-success" name='add_speciality_btn' value='Add'>
                        </form>
                    </div>
                    <!-- /.box-body -->

                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
