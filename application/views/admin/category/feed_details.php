<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Feed Category</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Feed Detail</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Feed Detail</h3>
                        <?php if ($feedData[0]->status == 1) { ?>
                            <span class="pull-right"><b>Status : </b><span style="margin-top: 3px;margin-left: 5px;" class="label label-success pull-right">Approved</span></span>
                        <?php } if ($feedData[0]->status == 0) { ?>
                            <span class="pull-right"><b>Status : </b><span style="margin-top: 3px;margin-left: 5px;" class="label label-default pull-right">New</span></span>
                        <?php } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-group" id="accordion">
                            <table class="table table-bordered table-striped">
                                <?php
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
                                $string = str_replace($badchar, '', $feedData[0]->description);
                                $sub_string = str_replace($badchar, '', $feedData[0]->subject);
                                ?>
                                <tbody>
                                    <tr>
                                        <td>Category Name(English)</td>
                                        <td><?php echo $feedData[0]->category_name_en; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Category Name(Russian)</td>
                                        <td><?php echo $feedData[0]->category_name_ru; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Doctor Name</td>
                                        <td><?php echo $feedData[0]->name; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td><?php echo date('d M, Y', $feedData[0]->created_at); ; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Subject</td>
                                        <td><?php echo json_decode('"' . $sub_string . '"'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Description</td>
                                        <td><?php echo json_decode('"' . $string . '"'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Image</td>
                                        <td>
                                            <?php if ($feedData[0]->image != '') { ?>
                                                <img src="<?php echo $feedData[0]->image; ?>" height="100" width="150">
                                                <?php
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </td>
                                    </tr>
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