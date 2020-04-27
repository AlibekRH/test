<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Treatment Management</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Update Treatment</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Update Treatment</h3>
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
                        <form role="form" action='<?php echo base_url() . 'UpdateTreatment/' . $this->uri->segment(2); ?>' method='post' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>Treatment Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter Treatment Name" name="name" required='' value="<?php echo ($treatment_details) ? $treatment_details[0]->name : set_value('name'); ?>">
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>
                            <input type="submit" class="btn btn-success" name='edit_sub' value='Update'>
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
