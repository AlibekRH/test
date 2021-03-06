<!DOCTYPE html>
<head>
    <title>Patient Registration</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

    <div id="mailsub" class="notification" align="center">
        <table style="min-width: 320px;">
            <tr><td align="center" bgcolor="#eff3f8">
                    <table border="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 300px;">
                        <!--header -->
                        <tr><td align="center" bgcolor="#ffffff">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr><td align="center" style="border-bottom:1px solid #eee; padding: 5%;background:#2abf88">
                                            <a href="#" target="_blank" style="color: #596167; width:100%; padding:20px;text-align:center; font-size: 13px;">
                                                <font face="font-size: 13px;" size="3" color="#596167">
                                                <img src="http://18.222.189.58/Zumcare/images/logo.png" width="150" alt="" border="0"  /></font></a>
                                        </td>
                                        <td align="right"> 
                                            <!-- padding --><div style="height: 25px; line-height: 50px; font-size: 10px;"></div>
                                        </td></tr>

                                    <!--content 1 -->
                                    <tr>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr><td>
                                                <div style="line-height: 24px;">
                                                    <font face="Arial, Helvetica, sans-serif" size="4" color="#57697e" style="font-size: 15px;">
                                                    <h3 style="font-size: 18px; color: #373737; float: left; margin-left: 3%; margin-top:5%;">Hi <?php echo $name; ?>,</h3>
                                                    <span style="width:94% !important; font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #57697e; float: left; margin-left: 3%; margin-top:3%;margin-bottom:4%; width:100%;">
                                                        You are registered as patient with Patient UID <b><?php echo $patient_uid; ?> </b>.<br>
                                                        Doctor Name : <b><?php echo $doctor_name; ?></b><br>
                                                        <b>Clinic Details</b><br>
                                                        <?php echo $doctor_profile_record[0]->clinic_name; ?><br>
                                                        <?php if ($send_clinic_address == 1) { ?>
                                                            <?php if (!empty($doctor_profile_record[0]->clinic_address)) { ?>
                                                                <?php echo $doctor_profile_record[0]->clinic_address; ?><br>
                                                            <?php } ?>
                                                            <?php if (!empty($doctor_profile_record[0]->clinic_city)) { ?>
                                                                <?php echo $doctor_profile_record[0]->clinic_city; ?>,<?php } if (!empty($doctor_profile_record[0]->clinic_state)) { ?> <?php echo $doctor_profile_record[0]->clinic_state; ?><?php } ?><?php if (!empty($doctor_profile_record[0]->clinic_pincode)) { ?> - <?php echo $doctor_profile_record[0]->clinic_pincode; ?><?php } ?>
                                                        <?php } ?>
                                                    </span>
                                                    </font>
                                                </div>
                                                <!-- padding --><div style="height: 15px; line-height: 40px; font-size: 10px;"></div>
                                            </td></tr>
                                        <tr><td><font>
                                                <span style="font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #57697e; float: left; margin-left: 3%; margin-top:3%;margin-bottom:4%;">Thanks, <br> zumcare Team</span>
                                                </font>
                                            </td></tr>
                                    </table>		
                            </td></tr>
                    </table>
                </td></tr>
        </table>
    </div>
</body>
</html>