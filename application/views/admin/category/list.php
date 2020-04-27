<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Feed Category</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Feed Category List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Feed Category List</h3>
                        <a href="<?php echo base_url() . 'AddFeedCategory'; ?>" class="btn btn-success pull-right">Add Feed Category</a>
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
                                        <th>Category Name(English)</th>
                                        <th>Category Name(Russian)</th>
                                        <th>Feed Count</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($feed_category) {
                                        foreach ($feed_category as $key => $row) {
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->category_name_en; ?></td>
                                                <td><?php echo $row->category_name_ru; ?></td>
                                                <td>
                                                    <span style="padding: 3px 10px !important;" title="New" class="badge bg-blue"><?php echo $row->NewFeedCount; ?></span>&nbsp;
                                                    <span style="padding: 3px 10px !important;" title="Total" class="badge bg-yellow"><?php echo $row->TotalFeedCount; ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'UpdateFeedCategory/' . $row->id; ?>" title="Edit" class="btn btn-success action_btn">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url() . 'FeedList/' . $row->id; ?>" title="Feed List" class="btn btn-primary action_btn">
                                                        <i class="fa fa-list"></i>
                                                    </a>
                                                    <!--a href="<?php //echo base_url() . 'DeleteSpeciality/' . $row->id;   ?>" title="Delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">
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