<?php
ini_set("display_errors", 0);
error_reporting(0);
date_default_timezone_set("UTC");
$conn = mysqli_connect("localhost", "root", "Zumcare@321", "ZumcareApp");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$result = 0;

$response = ($_POST['response']) ? $_POST['response'] : "";


$result = simplexml_load_string($response);
//echo '<pre>';
//print_r($result);
if (isset($result->bank)) {
    $bank_name = $result->bank['name'];
    $customer_name = $result->bank->customer['name'];
    $customer_email = $result->bank->customer['mail'];
    $customer_phone = $result->bank->customer['phone'];
    $uid = $result->bank->customer->merchant->order['order_id'];
    $merchant_id = $result->bank->results->payment['merchant_id'];
    $amount = $result->bank->results->payment['amount'];
    $reference = $result->bank->results->payment['reference'];
    $bank_sign = $result->bank_sign;
    $today = date('Y-m-d');
    $time = strtotime(date('h:i A'));
    $today_date = strtotime(date('Y-m-d H:i:s'));
    $sql = $conn->query("INSERT INTO transaction (type, uid, appointment_id, merchant_id, amount, reference, bank_sign, date)
VALUES ('enquiry', '$uid', '', '$merchant_id', '$amount', '$reference', '$bank_sign', '$today')");

    $conn->close();
} else {
    $order_id = $result['order_id'];
    $error = $result->error;
}