<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Promotion</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Promotion List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Promotion List</h3>
                        <a href="<?php echo base_url() . 'AddPromotion'; ?>" class="btn btn-success pull-right">Add Promotion</a>
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
                                        <th>Title</th>
                                        <th>Promo Code</th>
                                        <th>Expiry Date</th>
                                        <th>Discount Amount(in %)</th>
                                        <th>Promo Uses(No. of users)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($promotion) {
                                        foreach ($promotion as $key => $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->title; ?></td>
                                                <td><?php echo $row->promo_code; ?></td>
                                                <td><?php echo $row->expiry_date; ?></td>
                                                <td><?php echo $row->amount; ?></td>
                                                <td><?php echo $row->promotion_applied_user_count; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'UpdatePromotion/' . $row->id; ?>" title="Edit" class="btn btn-success action_btn">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url() . 'PromoDetails/' . $row->id; ?>" title="Info" class="btn btn-primary action_btn">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
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