<?php
/*
 * Doctor will send his auth_code to the server, along with the appointment_id.
 * In return, it will return medical data of
 * Patient who is in that appointment_id
 */
$authcode = $_POST['auth_code'] ?? null;
$appointment_id = $_POST['appointment_id'] ?? null;

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$sql = "SELECT patient_id FROM `doctor_appointment` WHERE doctor_id = $userid AND appointment_id = $appointment_id";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $res = $result->fetch_assoc();
    $patient_id = $res['patient_id'];
    $sql = "SELECT `medical_type`, `data` FROM `patient_medical_history` pmh JOIN `medical_types` mt ON mt.medical_id = pmh.medical_type_id WHERE patient_id = $patient_id";
    $result = runSQLCommandAPI($sql);
    $jsn = mysql_result_to_json($result);
    json_response_return(200,'OK', $jsn);
}else{
    json_response_return(400,'INVALID APPOINTMENT');
}