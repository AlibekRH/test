<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>App Banners</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add App Banners</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add App Banners</h3>
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
                        <form role="form" action='<?php echo base_url() . 'AddAppBanners'; ?>' method='post' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>URL</label>
                                <input type="url" class="form-control" id="banner_url" name="banner_url" required="">
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" id="image" name="image" accept="image/*" required="">
                            </div>
                            <input type="submit" class="btn btn-success" name='add_btn' value='Add'>
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
