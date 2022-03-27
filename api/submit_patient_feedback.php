<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$appointmentid = $_POST['appointment_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? null;

if(check_null($authcode, $appointmentid, $rating, $comment))
    json_response_return(400,"NULL VALUES: $appointmentid, $rating, $comment");

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$usertype = get_user_type_via_user_id($userid);
//Only patient can submit a feedback to doctor
if((int)$usertype !== 1)
    json_response_return(400,"INVALID USER TYPE: $usertype FOR USER ID: $userid");

$sql = "SELECT doctor_id FROM `doctor_appointment` WHERE appointment_id = $appointmentid AND patient_id = $userid";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $doctor_id = $row['doctor_id'];
    $sql = "INSERT INTO `patient_comment`(appointment_id, comment_data) VALUES ($appointmentid, '$comment')";
    runSQLCommandAPI($sql);
    $sql = "INSERT INTO `patient_rating`(appointment_id, rating) VALUES ($appointmentid, '$rating')";
    runSQLCommandAPI($sql);
    $sql = "UPDATE `doctor_appointment` SET status = 'completed' WHERE appointment_id = $appointmentid";
    runSQLCommandAPI($sql);
    $sql = "SELECT phone FROM `users` WHERE user_id = $doctor_id";
    $result = runSQLCommandAPI($sql);
    $row = $result->fetch_assoc();
    $doctor_phone = $row['phone'];
    sendSMSMessage($doctor_phone,"KYLIX: Appointment $appointmentid has been reviewed by client.");
    json_response_return(200,'OK');
}else{
    json_response_return(400,'INVALID APPOINTMENT');
}