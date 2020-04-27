<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Profile</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Profile</li>
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
            <div class="col-md-4">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-green">
                        <div class="widget-user-image">
                            <img class="img-circle" src="<?php echo base_url(); ?>images/avatar.png" alt="User Avatar">
                        </div>
                        <!-- /.widget-user-image -->
                        <h3 class="widget-user-username"><?php echo $admin_details[0]->name; ?></h3>
                        <h5 class="widget-user-desc">Web Admin</h5>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a>Name <span class="pull-right badge bg-blue"><?php echo $admin_details[0]->name; ?></span></a></li>
                            <li><a>Email <span class="pull-right badge bg-aqua"><?php echo $admin_details[0]->email; ?></span></a></li>
                            <li><a>Phone Number <span class="pull-right badge bg-red"><?php echo ($admin_details[0]->phone_number) ? $admin_details[0]->phone_number : "N/A"; ?></span></a></li>
                        </ul>
                    </div>
                </div>
                <!-- /.widget-user -->
            </div>
            <div class="col-xs-8">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Profile</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <form role="form" action='<?php echo base_url() . 'Profile'; ?>' method='post' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email" required='' value="<?php echo $admin_details[0]->email; ?>">
                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" required='' value="<?php echo $admin_details[0]->name; ?>">
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="number" class="form-control" id="phone_number" placeholder="Enter Phone Number" name="phone_number" value="<?php echo $admin_details[0]->phone_number; ?>">
                                <span class="text-danger"><?php echo form_error('phone_number'); ?></span>
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
