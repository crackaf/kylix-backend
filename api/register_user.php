<?php
require '../api_lock.php';

$type = $_POST['type'] ?? "1";
//1: patient, 2: doctor
$name = $_POST['full_name'] ?? null;
$phone = $_POST['phone'] ?? null;
$password = $_POST['password'] ?? null;
$address = $_POST['address'] ?? null;
$gender = $_POST['gender'] ?? null;
$dob = $_POST['dob'] ?? null;

if(check_null($type, $name, $phone, $password, $name, $address, $gender, $dob))
    json_response_return(400,'NULL VALUES');

if($type !== "1" && $type !== "2")
    json_response_return(400,'INVALID TYPE');

$password = md5($password);

$sql = "INSERT INTO users(`user_type`, `full_name`, `phone`, `password`, `address`, `gender`, `dob`, `ver_status`) VALUES ('$type','$name','$phone','$password','$address','$gender','$dob','unverified')";
runSQLCommandAPI($sql);

json_response_return(200,'OK');