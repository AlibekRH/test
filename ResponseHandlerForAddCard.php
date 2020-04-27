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
$conn->query("INSERT INTO test (result)
  VALUES ('$response')");
$result = simplexml_load_string($response);
if (isset($result->bank)) {
    $bank_name = $result->bank['name'];
    $customer_name = $result->bank->customer['name'];
    $customer_email = $result->bank->customer['mail'];
    $customer_phone = $result->bank->customer['phone'];
    $order_uid = $result->bank->customer->merchant->order['order_id'];
    $merchant_id = $result->bank->results->payment['merchant_id'];
    $amount = $result->bank->results->payment['amount'];
    $reference = $result->bank->results->payment['reference'];
    $CardHash = $result->bank->results->payment['CardHash'];
    $CardId = $result->bank->results->payment['CardId'];
    $exp_month = $result->bank->results->payment['exp_month'];
    $exp_year = $result->bank->results->payment['exp_year'];
    $user_uid = $result->bank->results->payment['abonent_id'];
    $bank_sign = $result->bank_sign;
    $today = date('Y-m-d');
    $time = strtotime(date('h:i A'));
    $today_date = strtotime(date('Y-m-d H:i:s'));

    //get order details
    $get_sql = mysqli_query($conn, "select * from users where user_uid = '$user_uid'");
    $get_data = mysqli_fetch_assoc($get_sql);
    $user_id = $get_data['id'];

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

    $conn->close();
} else {
    $order_id = $result['order_id'];
    $error = $result->error;
}