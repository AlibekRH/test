<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Feed Category</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Feed List</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Feed List</h3>
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
                                        <th>Doctor Name</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($feed_list) {
                                        foreach ($feed_list as $key => $row) {
                                            $badchar = array(
                                                // control characters
                                                chr(0), chr(1), chr(2), chr(3), chr(4), chr(5), chr(6), chr(7), chr(8), chr(9), chr(10),
                                                chr(11), chr(12), chr(13), chr(14), chr(15), chr(16), chr(17), chr(18), chr(19), chr(20),
                                                chr(21), chr(22), chr(23), chr(24), chr(25), chr(26), chr(27), chr(28), chr(29), chr(30),
                                                chr(31),
                                                // non-printing characters
                                                chr(127)
                                            );

                                            //replace the unwanted chars
                                            //$string = str_replace($badchar, '', $row->description);
                                            $sub_string = str_replace($badchar, '', $row->subject);
                                            //$string = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $str2);
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php echo json_decode('"' . $sub_string . '"'); ?></td>
                                                <td>
                                                    <?php
                                                    if ($row->status == 1)
                                                        echo "<span class='label label-success'>Approved</span>";
                                                    else
                                                        echo "<span class='label label-default'>Unapproved</span>";
                                                    ?>
                                                </td>
                                                <td><?php echo date('d M, Y', $row->created_at); ?></td>
                                                <td>
                                                    <?php if ($row->status == 0) { ?>
                                                        <a href="<?php echo base_url() . 'ApproveFeed/1/' . $row->feed_category_id . '/' . $row->id; ?>" title="Update Status" class="btn btn-success action_btn">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo base_url() . 'ApproveFeed/0/' . $row->feed_category_id . '/' . $row->id; ?>" title="Update Status" class="btn btn-success action_btn">
                                                            <i class="fa fa-undo"></i>
                                                        </a>
                                                    <?php } ?>
                                                    &nbsp;
                                                    <a href="<?php echo base_url() . 'FeedDetails/' . $row->id; ?>" title="Details" class="btn btn-warning action_btn">
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