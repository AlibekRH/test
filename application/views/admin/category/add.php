<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Feed Category</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Feed Category</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add Feed Category</h3>
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
                        <form role="form" action='<?php echo base_url() . 'AddFeedCategory'; ?>' method='post' enctype='multipart/form-data'>
                            <div class="form-group">
                                <label>Category Name(English)</label>
                                <input type="text" class="form-control" id="category_name_en" placeholder="Enter Category Name" name="category_name_en" required='' value="<?php echo set_value('category_name_en'); ?>">
                                <span class="text-danger"><?php echo form_error('category_name_en'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Category Name(Russian)</label>
                                <input type="text" class="form-control" id="category_name_ru" placeholder="Enter Category Name" name="category_name_ru" required='' value="<?php echo set_value('category_name_ru'); ?>">
                                <span class="text-danger"><?php echo form_error('category_name_ru'); ?></span>
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
