<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $title; ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>css/skins/_all-skins.min.css">
        <!-- Date Picker -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/bootstrap-daterangepicker/daterangepicker.css">
        <!-- jQuery 3 -->
        <script src="<?php echo INCLUDE_ASSETS; ?>other/jquery/dist/jquery.min.js"></script>
        <!-- DataTables -->
        <link rel="stylesheet" href="<?php echo INCLUDE_ASSETS; ?>other/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="http://cdn.datatables.net/responsive/1.0.2/css/dataTables.responsive.css"/>
        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <style>
            .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
                background-color: #00a65a !important;
                border-color: #008d4c !important;
            }
            .navbar-nav>.user-menu>.dropdown-menu{width:310px !important;}
            .action_btn{padding: 3px 8px !important;}
        </style>
    </head>
    <body class="hold-transition skin-green sidebar-mini">
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="<?php echo base_url() . 'Dashboard'; ?>" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>Z</b>C</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>Zum</b>Care</span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?php echo base_url(); ?>images/avatar.png" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?php echo $admin_details[0]->name; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="<?php echo base_url(); ?>images/avatar.png" class="img-circle" alt="User Image">

                                        <p>
                                            <?php echo $admin_details[0]->name; ?> - Web Admin
                                            <small>Since <?php echo date('d M Y', strtotime($admin_details[0]->created_at)); ?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left" style="margin-right:10px;">
                                            <a href="<?php echo base_url() . 'Profile'; ?>" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-left">
                                            <a href="<?php echo base_url() . 'ChangePassword'; ?>" class="btn btn-default btn-flat">Change Password</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?php echo base_url() . 'Logout'; ?>" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>