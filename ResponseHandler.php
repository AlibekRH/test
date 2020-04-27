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
/*$conn->query("INSERT INTO test (result)
VALUES ('$response')");*/
$result = simplexml_load_string($response);
if (isset($result->bank)) {
    $bank_name = $result->bank['name'];
    $customer_name = $result->bank->customer['name'];
    $customer_email = $result->bank->customer['mail'];
    $customer_phone = $result->bank->customer['phone'];
    $appointment_uid = $result->bank->customer->merchant->order['order_id'];
    $merchant_id = $result->bank->results->payment['merchant_id'];
    $amount = $result->bank->results->payment['amount'];
    $reference = $result->bank->results->payment['reference'];
    $bank_sign = $result->bank_sign;

    //get appointment id
    $get_sql = mysqli_query($conn, "select * from appointment where appointment_uid = '$appointment_uid'");
    $get_data = mysqli_fetch_assoc($get_sql);
    $appointment_id = $get_data['id'];
    $user_id = $get_data['user_id'];
    $doctor_id = $get_data['doctor_id'];
    $appointment_type = $get_data['appointment_type'];
    $today = date('Y-m-d');
    $time = strtotime(date('h:i A'));
    $today_date = strtotime(date('Y-m-d H:i:s'));
    
    $sql = $conn->query("INSERT INTO transaction (type, user_id, doctor_id, appointment_type, uid, appointment_id, merchant_id, amount, reference, bank_sign, date)
VALUES ('appointment', '$user_id', '$doctor_id', '$appointment_type', '$appointment_uid', '$appointment_id', '$merchant_id', '$amount', '$reference', '$bank_sign', '$today')");

    if ($appointment_type == 0)
        $conn->query("update appointment set status = 4 where id = '$appointment_id'");
    else
        $conn->query("update appointment set status = 4, call_chat_timing = '0' where id = '$appointment_id'");

    $conn->close();
} else {
    $order_id = $result['order_id'];
    $error = $result->error;
}