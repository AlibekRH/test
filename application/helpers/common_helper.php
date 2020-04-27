<?php

//Check Authentication
function CheckAuthentication($token, $user_id) {
    $ci = & get_instance();
    $ci->db->select('*');
    $ci->db->from('users');
    $ci->db->where('id', $user_id);
    $ci->db->where('authentication_token', $token);
    $query = $ci->db->get();
    $result = $query->result();
    return $result;
}

//Generate Random Number
function GenerateRandomNumber($length) {
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[rand(0, $max - 1)];
    }

    return $token;
}

//Generate Random Number
function GeneratePromoCode() {
    $code = "";
    $codeAlphabet = "ABC012DEFGHIJ34KLMNOPQRST56UVW7XY8Z9";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < 5; $i++) {
        $code .= $codeAlphabet[rand(0, $max - 1)];
    }
    return $code;
}

//Generate OTP for number verification
function GenerateOTP($length) {
    $otp = "";
    $codeAlphabet = "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {
        $otp .= $codeAlphabet[rand(0, $max - 1)];
    }

    return $otp;
}

//Get Details From Table
function GetDetails($tablename, $where = NULL) {
    $ci = & get_instance();
    $ci->db->select('*');
    $ci->db->from($tablename);
    if ($where)
        $ci->db->where($where);
    $query = $ci->db->get();
    $result = $query->result();
    return $result;
}

//Get average rating
function AverageRating($user_id, $type) {
    $rating_record = GetDetails('rating_and_reviews', array('receiver_id' => $user_id, 'type' => $type));
    $rating = 0;
    if ($rating_record) {
        $total = count($rating_record);
        foreach ($rating_record as $row) {
            $rating = $rating + $row->rating;
        }
        $avg_rate = $rating / $total;
        $rating = sprintf('%02.1f', $avg_rate);
    } else {
        $rating = sprintf('%02.1f', $rating);
    }
    return $rating;
}

//Get total rater count
function TotalRatingCount($user_id, $type) {
    $rating_record = GetDetails('rating_and_reviews', array('receiver_id' => $user_id, 'type' => $type));
    return count($rating_record);
}

//Calculate Distance
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

//check Admin Login
function CheckAdminLogin() {
    $ci = & get_instance();
    if (!$ci->session->userdata('admin_data'))
        redirect(base_url() . 'Admin');
}

//Total Count
function GetTotalCount($table_name, $where = NULL) {
    if (!empty($where))
        $get_data = GetDetails($table_name, $where);
    else
        $get_data = GetDetails($table_name);
    return count($get_data);
}

//Total Amount
function GetTotalAmount() {
    $ci = & get_instance();
    $ci->db->select_sum('amount');
    $ci->db->from('transaction');
    $query = $ci->db->get();
    if ($query->row()->amount != '') {
        return $query->row()->amount;
    } else {
        return 0;
    }
}

if (!function_exists('json_encode_custom')) {

    function json_encode_custom($a = false) {
        if (is_null($a))
            return 'null';
        if ($a === false)
            return 'false';
        if ($a === true)
            return 'true';
        if (is_scalar($a)) {
            if (is_float($a)) {
                // Always use "." for floats.
                return floatval(str_replace(",", ".", strval($a)));
            }

            if (is_string($a)) {
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            } else
                return $a;
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
            if (key($a) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList) {
            foreach ($a as $v)
                $result[] = json_encode($v);
            return '[' . join(',', $result) . ']';
        } else {
            foreach ($a as $k => $v)
                $result[] = json_encode($k) . ':' . json_encode($v);
            return '{' . join(',', $result) . '}';
        }
    }

}

function ConvertTimezone($time, $toTimezone, $format) {
    $date = new DateTime($time);
    $date->setTimezone(new DateTimeZone($toTimezone));
    return $date->format($format);
}
