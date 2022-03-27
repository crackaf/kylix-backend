<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$jsn = $_POST['json'] ?? null;

/*
 * THIS IS HOW THE USER SHOULD SEND JSON TO SERVER
 * day is 3 character
 * [ {day: Mon, start: hh:mm:ss, end: hh:mm:ss}, {day: Tue, start: hh:mm:ss, end: hh:mm:ss} ]
 */

if(check_null($authcode, $jsn))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$usertype = get_user_type_via_user_id($userid);
if($usertype !== 2)
    json_response_return(400,'INVALID USER TYPE');

if(!isJson($jsn))
    json_response_return(400,'INVALID JSON STRING');

$jsn = json_decode($jsn, true);

$sql = "DELETE FROM `doctor_schedule` WHERE doctor_id = $userid";
runSQLCommandAPI($sql);

foreach($jsn as $obj){
    $day = $obj['day'];
    $start = $obj['start'];
    $end = $obj['end'];
    $sql = "INSERT INTO `doctor_schedule`(doctor_id, day_name, start_time, end_time) VALUES($userid,'$day','$start','$end')";
    runSQLCommandAPI($sql);
}

json_response_return(200,'OK');