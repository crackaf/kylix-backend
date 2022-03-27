<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;

if(check_null($authcode, $doctor_id))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$sql = "SELECT * FROM `doctor_clinics` WHERE doctor_id = $doctor_id";
$result = runSQLCommandAPI($sql);
$jsn = mysql_result_to_json($result);

json_response_return(200,'OK', $jsn);