<?php

ini_set("display_errors", 0);
error_reporting(0);
date_default_timezone_set('UTC');
//create connection
$servername = "localhost";
$username = "root";
$password = "Zumcare@321";
$dbname = "ZumcareApp";
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$x = rand();
//  $conn->query("INSERT INTO test (result)
//  VALUES ($x)");
$today_date = date('Y-m-d');
$project_settings = mysqli_query($conn, 'select * from project_settings where id = 1');
$project_settings_record = mysqli_fetch_assoc($project_settings);
$result = mysqli_query($conn, 'select * from appointment where appointment_date = "' . $today_date . '" AND status = 4');
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $current_time = strtotime(date("H:i"));
        $reminder_time = strtotime(date("H:i", strtotime("-10 minutes", $row['start_time'])));
        if ($current_time == $reminder_time) {
            $user_sql = mysqli_query($conn, 'select * from users where id = "' . $row['user_id'] . '"');
            $user_record = mysqli_fetch_array($user_sql);
            $doctor_sql = mysqli_query($conn, 'select * from users where id = "' . $row['doctor_id'] . '"');
            $doctor_record = mysqli_fetch_array($doctor_sql);
            $doctor_message_content_en = "After 10 minutes you have appointment with patient.";
            $doctor_message_content = "Через 10 минут у Вас запись с пациентом.";
            $patient_message_content_en = "After 10 minutes you have appointment with doctor.";
            $patient_message_content = "After 10 minutes you have appointment with doctor.";
            $notification_type = 'reminder';
            //doctor notification
            define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
            $send_msg = array("message" => $doctor_message_content, 'notification_type' => $notification_type, 'id' => $row['id']);
            if ($doctor_record) {
                if ($doctor_record['device_type'] == "android") {
                    if (!empty($doctor_record['device_token'])) {
                        $registatoin_ids = array($doctor_record['device_token']);
                        $message_data = $send_msg;

                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $fields = array(
                            'registration_ids' => $registatoin_ids,
                            'data' => $message_data,
                        );
                        $headers = array(
                            'Authorization: key=' . API_KEY,
                            'Content-Type: application/json'
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        $result = curl_exec($ch);
                        $res_array = json_decode($result);
                        print_r($res_array);
                    }
                } else {
                    $app_state = $project_settings_record['app_state'];
                    // = "";
                    $deviceToken = $doctor_record['device_token'];
                    $body['aps'] = array(
                        'alert' => array(
                            'content-available' => 1,
                            'body' => $doctor_message_content,
                        ),
                        'badge' => 1,
                        'notification_type' => $notification_type,
                        'id' => $row['id'],
                        'sound' => 'default',
                    );
                    $passphrase = '123456789';
                    $ctx = stream_context_create();
                    if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                    } else {
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                    }
                    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                    if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    } else {
                        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }

                    $payload = json_encode($body);

                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                    $result = fwrite($fp, $msg, strlen($msg));

                    fclose($fp);
                }
            }
            if ($user_record) {
                $send_msg = array("message" => $patient_message_content, 'notification_type' => $notification_type, 'id' => $row['id']);
                if ($user_record['device_type'] == "android") {
                    if (!empty($user_record['device_token'])) {
                        $registatoin_ids = array($user_record['device_token']);
                        $message_data = $send_msg;

                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $fields = array(
                            'registration_ids' => $registatoin_ids,
                            'data' => $message_data,
                        );
                        $headers = array(
                            'Authorization: key=' . API_KEY,
                            'Content-Type: application/json'
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        $result = curl_exec($ch);
                        $res_array = json_decode($result);
                    }
                } else {
                    $app_state = "prod";
                    // = "";
                    $deviceToken = $user_record['device_token'];
                    $body['aps'] = array(
                        'alert' => array(
                            'content-available' => 1,
                            'body' => $patient_message_content,
                        ),
                        'badge' => 1,
                        'notification_type' => $notification_type,
                        'id' => $row['id'],
                        'sound' => 'default',
                    );
                    $passphrase = '';
                    $ctx = stream_context_create();
                    if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientProduction.pem');
                    } else {
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientDevelopment.pem');
                    }
                    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                    if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    } else {
                        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }

                    $payload = json_encode($body);

                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                    $result = fwrite($fp, $msg, strlen($msg));

                    fclose($fp);
                }
            }
        }
    }
}
    

