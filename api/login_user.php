<?php
require '../api_lock.php';

$phone = $_POST['phone'] ?? null;
$password = $_POST['password'] ?? null;

if(check_null($phone,$password))
    json_response_return(400,'NULL VALUES');

$password = md5($password);

$sql = "SELECT * FROM `users` WHERE phone = '$phone' AND password = '$password' LIMIT 1";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $auth_code = create_randomcode(150);
    $row = $result->fetch_assoc();
    $userid = $row['user_id'];
    $sql = "UPDATE `users` SET auth_code = '$auth_code' WHERE user_id = '$userid'";
    $result = runSQLCommandAPI($sql);
    json_response_return(200,'OK', $auth_code);
}else{
    json_response_return(400,'OK', "INVALID CREDENTIALS");
}