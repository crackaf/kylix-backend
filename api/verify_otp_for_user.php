<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$otpcode = $_POST['otp_code'] ?? null;

if(check_null($authcode, $otpcode))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,"INVALID AUTH CODE");

$sql = "SELECT * FROM `otp_auth` WHERE user_id = $userid AND otp_code = '$otpcode'";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $sql = "UPDATE `users` SET ver_status = 'verified' WHERE user_id = $userid";
    runSQLCommandAPI($sql);

    $sql = "DELETE FROM `otp_auth` WHERE user_id = $userid";
    runSQLCommandAPI($sql);

    json_response_return(200,"OK");
}else{
    json_response_return(400,"INVALID OTP CODE: $otpcode FOR USER ID: $userid");
}