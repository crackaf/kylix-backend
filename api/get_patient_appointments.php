<?php
require '../api_lock.php';

//patient auth code
$authcode = $_POST['auth_code'] ?? null;

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$usertype = get_user_type_via_user_id($userid);
//Only patient can see his own appointments
if($usertype !== 1)
    json_response_return(400,'INVALID USER TYPE');

$sql = "SELECT `full_name` as doctor_name, `appointment_id`, `doctor_id`, `date`, `start_time`, `end_time`, `status` FROM `doctor_appointment` da JOIN `users` u ON da.doctor_id = u.user_id WHERE patient_id = $userid)";
$result = runSQLCommandAPI($sql);
$jsn = mysql_result_to_json($result);
json_response_return(200,'OK', $jsn);