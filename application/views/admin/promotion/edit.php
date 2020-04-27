<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Promotion</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Update Promotion</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Update Promotion</h3>
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
                        <form role="form" action='<?php echo base_url() . 'UpdatePromotion/' . $this->uri->segment(2); ?>' method='post'>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Enter Title" name="title" required='' value="<?php echo ($promotion) ? $promotion[0]->title : set_value('title'); ?>">
                                <span class="text-danger"><?php echo form_error('title'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Promo Code</label>
                                <input type="text" class="form-control" id="promo_code" placeholder="Enter Promo Code" name="promo_code" required='' readonly="" value="<?php echo $promotion[0]->promo_code; ?>">
                                <span class="text-danger"><?php echo form_error('promo_code'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" id="description" placeholder="Enter Description" name="description"><?php echo ($promotion) ? $promotion[0]->description : set_value('description'); ?></textarea>
                                <span class="text-danger"><?php echo form_error('description'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>amount(in %)</label>
                                <input type="number" class="form-control" id="amount" placeholder="Enter Amount" name="amount" required='' value="<?php echo ($promotion) ? $promotion[0]->amount : set_value('amount'); ?>">
                                <span class="text-danger"><?php echo form_error('amount'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="datepicker" name="expiry_date" value="<?php echo $promotion[0]->expiry_date; ?>">
                                </div>
                                <span class="text-danger"><?php echo form_error('expiry_date'); ?></span>
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
