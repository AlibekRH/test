<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo base_url(); ?>images/avatar.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo $admin_details[0]->name; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <!-- Dashboard Management -->
            <li <?php if ($this->uri->segment(1) == 'Dashboard') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Dashboard'; ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <!-- Speciality Management -->
            <li <?php if ($this->uri->segment(1) == 'Speciality' || $this->uri->segment(1) == 'AddSpeciality' || $this->uri->segment(1) == 'UpdateSpeciality') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Speciality'; ?>">
                    <i class="fa fa-list"></i> <span>Speciality Management</span>
                </a>
            </li>
            <!-- Feed Category Management -->
            <li <?php if ($this->uri->segment(1) == 'FeedCategory' || $this->uri->segment(1) == 'FeedList' || $this->uri->segment(1) == 'FeedDetails' || $this->uri->segment(1) == 'AddFeedCategory' || $this->uri->segment(1) == 'UpdateFeedCategory') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'FeedCategory'; ?>">
                    <i class="fa fa-list"></i> <span>Feed Category</span>
                </a>
            </li>
            <!-- Diseases Management -->
            <!--li <?php //if($this->uri->segment(1) == 'Diseases' || $this->uri->segment(1) == 'AddDiseases' || $this->uri->segment(1) == 'UpdateDiseases'){          ?> class="active" <?php //}          ?>>
                <a href="<?php //echo base_url() . 'Diseases';          ?>">
                    <i class="fa fa-list"></i> <span>Diseases Management</span>
                </a>
            </li-->
            <!-- Sub Speciality Management -->
            <!--li <?php //if($this->uri->segment(1) == 'SubSpeciality' || $this->uri->segment(1) == 'AddSubSpeciality' || $this->uri->segment(1) == 'UpdateSubSpeciality'){          ?> class="active" <?php //}          ?>>
                <a href="<?php //echo base_url() . 'SubSpeciality';          ?>">
                    <i class="fa fa-list"></i> <span>Sub Speciality Management</span>
                </a>
            </li-->
            <!-- Treatment Management -->
            <!--li <?php //if($this->uri->segment(1) == 'Treatment' || $this->uri->segment(1) == 'AddTreatment' || $this->uri->segment(1) == 'UpdateTreatment'){          ?> class="active" <?php //}          ?>>
                <a href="<?php //echo base_url() . 'Treatment';          ?>">
                    <i class="fa fa-medkit"></i> <span>Treatment Management</span>
                </a>
            </li-->
            <!-- Doctor Management -->
            <li <?php if ($this->uri->segment(1) == 'Doctors' || $this->uri->segment(1) == 'DoctorDetail') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Doctors'; ?>">
                    <i class="fa fa-stethoscope"></i> <span>Doctor Management</span>
                </a>
            </li>
            <!-- Patient Management -->
            <li <?php if ($this->uri->segment(1) == 'Patients' || $this->uri->segment(1) == 'PatientDetail') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Patients'; ?>">
                    <i class="fa fa-users"></i> <span>Patient Management</span>
                </a>
            </li>
            <!-- Appointment Management -->
            <li <?php if ($this->uri->segment(1) == 'Appointment' || $this->uri->segment(1) == 'UserAppointment') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Appointment'; ?>">
                    <i class="fa fa-medkit"></i> <span>Appointment Management</span>
                </a>
            </li>
            <!-- Payment Management -->
            <li <?php if ($this->uri->segment(1) == 'Payment') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Payment'; ?>">
                    <i class="fa fa-money"></i> <span>Payment Management</span>
                </a>
            </li>
            <!-- Report Management -->
            <li <?php if ($this->uri->segment(1) == 'Report') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Report'; ?>">
                    <i class="fa fa-bar-chart"></i> <span>Report Management</span>
                </a>
            </li>
            <!-- Doctor Earning -->
            <li <?php if ($this->uri->segment(1) == 'Earnings' || $this->uri->segment(1) == 'EarningsDetails' || $this->uri->segment(1) == 'EarningAppointment') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Earnings'; ?>">
                    <i class="fa fa-money"></i> <span>Doctor Earnings</span>
                </a>
            </li>
            <!-- Promotion Management -->
            <li <?php if ($this->uri->segment(1) == 'Promotion' || $this->uri->segment(1) == 'PromoDetails' || $this->uri->segment(1) == 'AddPromotion' || $this->uri->segment(1) == 'UpdatePromotion') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'Promotion'; ?>">
                    <i class="fa fa-shield"></i> <span>Promotion Management</span>
                </a>
            </li>
            <!-- App Banners -->
            <li <?php if ($this->uri->segment(1) == 'AppBanners' || $this->uri->segment(1) == 'AddAppBanners') { ?> class="active" <?php } ?>>
                <a href="<?php echo base_url() . 'AppBanners'; ?>">
                    <i class="fa fa-list"></i> <span>Banner Management</span>
                </a>
            </li>
            <!-- App Home Screen Management -->
            <li <?php if ($this->uri->segment(1) == 'AppHomeScreenSetting' && ($this->uri->segment(2) == 'doctors' || $this->uri->segment(2) == 'speciality')) { ?> class="treeview active" <?php } else { ?> class="treeview" <?php } ?>>
                <a href="">
                    <i class="fa fa-cog"></i> <span>App Home Screen Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li <?php if ($this->uri->segment(1) == 'AppHomeScreenSetting' && $this->uri->segment(2) == 'doctors') { ?> class="active" <?php } ?>>
                        <a href="<?php echo base_url() . 'AppHomeScreenSetting/doctors'; ?>"><i class="fa fa-circle-o"></i> Doctors</a>
                    </li>
                    <li <?php if ($this->uri->segment(1) == 'AppHomeScreenSetting' && $this->uri->segment(2) == 'speciality') { ?> class="active" <?php } ?>>
                        <a href="<?php echo base_url() . 'AppHomeScreenSetting/speciality'; ?>"><i class="fa fa-circle-o"></i> Speciality</a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>