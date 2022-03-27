<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$appointmentid = $_POST['appointment_id'] ?? null;
$note = $_POST['note'] ?? null;

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid !== 2)
    json_response_return(400,'INVALID AUTH CODE');

$sql = "INSERT INTO `doctor_contact`(appointment_id, note) VALUES($appointmentid, '$note')";
runSQLCommandAPI($sql);

json_response_return(200,'OK');