<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$sql = "SELECT user_type, full_name, phone, address, gender, dob, ver_status FROM `users` WHERE auth_code = '$authcode'";
$result = runSQLCommandAPI($sql);
if($result->num_rows > 0){
    $jsn = mysql_result_to_json($result);
    json_response_return(200,'OK',$jsn);
}else{
    json_response_return(400,'INVALID AUTH CODE');
}

/*
 * {
    "status": 200,
    "response": "OK",
    "data": "[{\"user_type\":\"1\",\"full_name\":\"Hunzlah Malik\",\"phone\":\"923076288887\",\"address\":\"abcd\",\"gender\":\"other\",\"dob\":\"3\\/26\\/2022\"}]"
}
*
 */