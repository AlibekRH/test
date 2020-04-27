<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>css/iCheck/square/green.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page">
        <?php
        if ($this->session->flashdata('modal_success_msg'))
            $show_modal = 1;
        else if ($this->session->flashdata('modal_error_msg'))
            $show_modal = 1;
        else
            $show_modal = 0;
        ?>
        <div class="login-box">
            <div class="login-logo">
                <a href=""><b>Zum</b>Care</a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <?php if ($this->session->flashdata('error_msg')) { ?>
                    <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo $this->session->flashdata('error_msg'); ?>
                    </div>
                <?php } ?>
                <form action="<?php echo base_url() . 'Admin'; ?>" method="post">
                    <div class="form-group has-feedback">
                        <input type="email" class="form-control" placeholder="Email" name="email" required="required">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" placeholder="Password" name="password" required="">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <a style="color: #00a65a !important;" data-toggle="modal" data-target="#modal-default">I forgot my password</a>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" name="login_sub_btn" class="btn btn-success btn-block btn-flat">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->

        <!-- jQuery 3 -->
        <script src="<?php echo INCLUDE_ASSETS; ?>other/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="<?php echo INCLUDE_ASSETS; ?>other/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="<?php echo INCLUDE_ASSETS; ?>css/iCheck/icheck.min.js"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                    increaseArea: '20%' /* optional */
                });
            });
        </script>

        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="<?php echo base_url() . 'ForgotPassword' ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Forgot Password</h4>
                        </div>
                        <div class="modal-body">
                            <?php if ($this->session->flashdata('modal_error_msg')) { ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <?php echo $this->session->flashdata('modal_error_msg'); ?>
                                </div>
                            <?php } if ($this->session->flashdata('modal_success_msg')) { ?>
                                <div class="alert alert-success alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <?php echo $this->session->flashdata('modal_success_msg'); ?>
                                </div>
                            <?php } ?>
                            <div class="form-group has-feedback">
                                <label>Email</label>
                                <input type="email" class="form-control" placeholder="Email" name="email" required="">
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                            </div>
                            <button type="submit" class="btn btn-success">Send</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <script>
            $(window).on('load', function () {
                var modal_show = '<?php echo $show_modal; ?>';
                if (modal_show == 1)
                    $('#modal-default').modal('show');
            });
            $(document).load(function (e) {

            });
        </script>
    </body>
</html>