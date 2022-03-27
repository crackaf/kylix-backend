<?php
require '../api_lock.php';

//patient auth code
$authcode = $_POST['auth_code'] ?? null;
$appointment_id = $_POST['appointment_id'] ?? null;

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$sql = "SELECT * FROM `doctor_appointment` da JOIN `patient_comment` pc ON da.appointment_id = pc.appointment_id JOIN `patient_rating` pr ON pc.appointment_id = pr.appointment_id JOIN `doctor_notes` dn ON dn.appointment_id = pr.appointment_id WHERE pr.appointment_id = $appointment_id";
$result = runSQLCommandAPI($sql);
$jsn = mysql_result_to_json($result);
json_response_return(200,'OK', $jsn);