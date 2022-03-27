<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$sql = "SELECT user_id, phone FROM `users` WHERE auth_code = '$authcode'";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $phone = $row['phone'];
    $userid = $row['user_id'];
}else{
    json_response_return(400,'INVALID AUTH CODE');
}

$otp_code = create_randomcode(10);

$sql = "SELECT * FROM `otp_auth` WHERE user_id = $userid";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $sql = "UPDATE otp_auth SET otp_code = '$otp_code' WHERE user_id = '$userid'";
}else{
    $sql = "INSERT INTO otp_auth(user_id, otp_code) VALUES ('$userid','$otp_code')";
}
runSQLCommandAPI($sql);

$message = "";
$resp = sendSMSMessage($phone,"KYLIX: $otp_code");

json_response_return(200,'OK', $resp);