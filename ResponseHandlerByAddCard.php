<?php
date_default_timezone_set("UTC");
ini_set("display_errors", 0);
error_reporting(0);
$conn = mysqli_connect("localhost", "root", "Zumcare@321", "ZumcareApp");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$result = 0;

$response = ($_POST['response']) ? $_POST['response'] : "";
/* $conn->query("INSERT INTO test (result)
  VALUES ('$response')"); */
$result = simplexml_load_string($response);
$project_settings = mysqli_query($conn, 'select * from project_settings where id = 1');
$project_settings_record = mysqli_fetch_assoc($project_settings);
if (isset($result->bank)) {
    $bank_name = $result->bank['name'];
    $language = ($result->bank->customer['lang']) ? $result->bank->customer['lang'] : 'en';
    $customer_name = $result->bank->customer['name'];
    $customer_email = $result->bank->customer['mail'];
    $customer_phone = $result->bank->customer['phone'];
    $appointment_uid = $result->bank->customer->merchant->order['order_id'];
    $merchant_id = $result->bank->results->payment['merchant_id'];
    $amount = $result->bank->results->payment['amount'];
    $reference = $result->bank->results->payment['reference'];
    $bank_sign = $result->bank_sign;
    $CardHash = $result->bank->results->payment['CardHash'];
    $CardId = $result->bank->results->payment['CardId'];
    $exp_month = $result->bank->results->payment['exp_month'];
    $exp_year = $result->bank->results->payment['exp_year'];
    $user_uid = $result->bank->results->payment['abonent_id'];
    $today = date('Y-m-d');
    $time = strtotime(date('h:i A'));
    $today_date = strtotime(date('Y-m-d H:i:s'));


    //get appointment id
    $get_sql = mysqli_query($conn, "select * from appointment where appointment_uid = '$appointment_uid'");
    $get_data = mysqli_fetch_assoc($get_sql);
    $appointment_id = $get_data['id'];
    $user_id = $get_data['user_id'];
    $doctor_id = $get_data['doctor_id'];
    $appointment_type = $get_data['appointment_type'];

    $get_cards_sql = mysqli_query($conn, "select * from cards where CardId = '$CardId' AND user_id = '$user_id' AND deleted_status = 1");
    if (mysqli_num_rows($get_cards_sql) > 0) {
        $get_cards = mysqli_fetch_assoc($get_cards_sql);
        $card_id = $get_cards['id'];
    } else {
        $get_user_cards_sql = mysqli_query($conn, "select * from cards where user_id = '$user_id' AND deleted_status = 1");
        if (mysqli_num_rows($get_user_cards_sql) > 0) {
            $card_status = 0;
        } else {
            $card_status = 1;
        }
        $conn->query("INSERT INTO cards (user_id, user_uid, card_status, bank_name, CardHash, CardId, exp_month, exp_year, customer_name, customer_email, customer_phone, created_at) VALUES('$user_id', '$user_uid', '$card_status', '$bank_name', '$CardHash', '$CardId', '$exp_month', '$exp_year', '$customer_name', '$customer_email', '$customer_phone', '$today_date')");
        $card_id = $conn->insert_id;
        if ($card_status == 1) {
            $conn->query("update cards set card_status = 0 where id != '$card_id' AND user_id = '$user_id'");
        }
    }

    $sql = $conn->query("INSERT INTO transaction (type, user_id, doctor_id, appointment_type, card_id, uid, appointment_id, merchant_id, amount, reference, bank_sign, date)
VALUES ('appointment', '$user_id', '$doctor_id', '$appointment_type', '$card_id', '$appointment_uid', '$appointment_id', '$merchant_id', '$amount', '$reference', '$bank_sign', '$today')");

    if ($appointment_type == 0)
        $conn->query("update appointment set status = 4 where id = '$appointment_id'");
    else
        $conn->query("update appointment set status = 4, call_chat_timing = '0' where id = '$appointment_id'");

    //notification working
    define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
    $created = strtotime(date("Y-m-d H:i:s"));
    $notification_sql = mysqli_query($conn, "select * from notification_settings where user_id = '$doctor_id'");
    $notification_settings = mysqli_fetch_assoc($notification_sql);
    $show_notification = 1;
    $receiver_sql = mysqli_query($conn, "select * from users where id = '$doctor_id'");
    $receiver_data = mysqli_fetch_assoc($receiver_sql);
    $message = "Payment done successfully.";
    $message_ru = "Пациент оплатил.";

    $send_msg = array("message" => ($language == 'en') ? $message : $message_ru, 'notification_type' => 'payment', 'patient_id' => $user_id, 'id' => $appointment_id);
    if ($notification_settings['payment_appointment'] != 1)
        $show_notification = 0;

    if ($receiver_data['device_type'] == "android") {
        if (!empty($receiver_data['device_token'])) {
            if ($show_notification == 1) {
                $registatoin_ids = array($receiver_data['device_token']);
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
            $insert_data = array(
                'notification_type' => 'appointment',
                'sender_id' => $user_id,
                'receiver_id' => $doctor_id,
                'sender_type' => 'patient',
                'message' => $message,
                'message_ru' => (isset($message_ru) && !empty($message_ru)) ? $message_ru : '',
                'action_id' => $appointment_id,
                'status' => 4,
                'created_at' => $created
            );
            $insert_id = $this->Custom->insert_data('notifications', $insert_data);
        }
    } else {
        $app_state = $project_settings_record['app_state'];
        // = "";
        $deviceToken = $receiver_data['device_token'];
        if ($show_notification == 1) {
            $body['aps'] = array(
                'alert' => array(
                    'content-available' => 1,
                    'body' => ($language == 'en') ? $message : $message_ru,
                ),
                'badge' => 1,
                'notification_type' => 'appointment',
                'id' => $appointment_id,
                'patient_id' => $user_id,
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
        $insert_data = array(
            'notification_type' => 'appointment',
            'sender_id' => $user_id,
            'receiver_id' => $doctor_id,
            'sender_type' => 'patient',
            'message' => $message,
            'message_ru' => (isset($message_ru) && !empty($message_ru)) ? $message_ru : '',
            'action_id' => $appointment_id,
            'status' => 4,
            'created_at' => $created
        );
        $insert_id = $this->Custom->insert_data('notifications', $insert_data);
    }

    $conn->close();
} else {
    $order_id = $result['order_id'];
    $error = $result->error;
}