<?php

ini_set("display_errors", 0);
error_reporting(0);
FailurePost();

function FailurePost() {
    echo json_encode(array('status' => 400));
}
