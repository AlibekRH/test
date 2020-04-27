<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Promotion</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Dashboard'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Promotion Details</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Promotion Details(<?php echo $promotion[0]->promo_code; ?>)</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Service Name</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($UsesData) {
                                        foreach ($UsesData as $key => $row) {
                                            
                                            ?>
                                            <tr>
                                                <td><?php echo $key + 1; ?></td>
                                                <td><a href="<?php echo base_url() . 'PatientDetail/' . $row->patient_id; ?>"><?php echo $row->patient_name; ?></a></td>
                                                <td><?php echo date('d M, Y', $row->promo_uses_date); ?></td>
                                                <td><?php echo $row->service_type; ?></td>
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