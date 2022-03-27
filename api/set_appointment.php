<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;
$date = $_POST['date'] ?? null;
$start = $_POST['start'] ?? null;
$end = $_POST['end'] ?? null;

if(check_null($authcode, $doctor_id, $date, $start, $end))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$usertype = get_user_type_via_user_id($userid);
//Only patient can set an appointment
if((int)$usertype !== 1)
    json_response_return(400,'INVALID USER TYPE');

$sql = "INSERT INTO `doctor_appointment`(`doctor_id`, `patient_id`, `date`, `start_time`, `end_time`, `status`) VAlUES ($doctor_id, $userid, '$date','$start','$end','pending')";
runSQLCommandAPI($sql);

$sql = "SELECT phone FROM `users` WHERE user_id = $doctor_id";
$result = runSQLCommandAPI($sql);
$row = $result->fetch_assoc();
$doctor_phone = $row['phone'];

$sql = "SELECT phone FROM `users` WHERE user_id = $userid";
$result = runSQLCommandAPI($sql);
$row = $result->fetch_assoc();
$patient_phone = $row['phone'];

sendSMSMessage($doctor_phone, "KYLIX: You have a new appointment with $patient_phone.");
sendSMSMessage($patient_phone, "KYLIX: You have successfully set up an appointment with $doctor_phone.");

json_response_return(200,'OK');