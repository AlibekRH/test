<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Change Password</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Change Password</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->session->flashdata('error_msg')) { ?>
                <div class="col-md-12">    
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $this->session->flashdata('error_msg') ?>
                    </div>
                </div>
            <?php } if ($this->session->flashdata('success_msg')) { ?>
                <div class="col-md-12"> 
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $this->session->flashdata('success_msg') ?>
                    </div>
                </div>
            <?php } ?>
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Change Password</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <form role="form" action='<?php echo base_url() . 'ChangePassword'; ?>' method='post'>
                            <div class="form-group">
                                <label>Old Password</label>
                                <input type="password" class="form-control" id="old_pass" placeholder="Enter Old password" name="old_pass" required='' value="">
                                <span class="text-danger"><?php echo form_error('old_pass'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" class="form-control" id="new_pass" placeholder="Enter New password" name="new_pass" required='' value="">
                                <span class="text-danger"><?php echo form_error('new_pass'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_pass" placeholder="Enter Confirm password" name="confirm_pass" required='' value="">
                                <span class="text-danger"><?php echo form_error('confirm_pass'); ?></span>
                            </div>
                            <input type="submit" class="btn btn-success" name='update_btn' value='Update'>
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
