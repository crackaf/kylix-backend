<?php
function runSQLCommandAPI($sql){ global $conn;
    if (!($result = $conn->query($sql))) {
        $file = 'error_log.txt';
        $time = time();
        file_put_contents($file, PHP_EOL . "(Time: $time) ".$conn->error, FILE_APPEND);
        json_response_return(500, $conn->error);
    }
    return $result;
}
function json_response_return($status = 500, $response = "Internal Server Error", $data = null){
    $jsonObj = new stdClass();
    $jsonObj->status = $status;
    $jsonObj->response = $response;
    $jsonObj->data = $data;
    echo json_encode($jsonObj);
    die();
}
function create_randomcode($length){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@<>$#';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function check_null(){
    $numargs = func_num_args();
    $arg_list = func_get_args();
    for ($i = 0; $i < $numargs; $i++) {
        if(is_null($arg_list[$i])){
            return true;
        }
    }
    return false;
}
function mysql_result_to_json($result){
    $arr = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $arr[] = $row;
    }
    return json_encode($arr);
}
function get_user_id_via_auth_code($auth_code){ global $conn;
    $sql = "SELECT user_id FROM `users` WHERE auth_code = '$auth_code'";
    $result = runSQLCommandAPI($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        return $row['user_id'];
    }
    return -1;
}
function get_user_type_via_user_id($user_id){ global $conn;
    $sql = "SELECT user_type FROM `users` WHERE user_id = $user_id";
    $result = runSQLCommandAPI($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        return (int) $row['user_type'];
    }
    return -1;
}
function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function sendSMSMessage($phone, $message){
    $url = "https://smsgateway.me/api/v4/message/send";
//    $auth_val = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY0ODMwOTkzMywiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjkzNzEyLCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.ZIncU-UaICab02AWgmvTnCQ4y4EHcQ2Ld5nCUSuVNz4";
//    $device_id = "127719";
    // Zeerak

    $auth_val = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY0ODMxMDI5OCwiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjkzNzEzLCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.ovuQyCRYIbVb3FKAR-yc1pIr95yw-ymrPNRkvHMtwWU";
    $device_id = "127720";
    // Hunzlah

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST,           1 );
    curl_setopt($curl, CURLOPT_POSTFIELDS,     "[
  {
    \"phone_number\": \"$phone\",
    \"message\": \"$message\",
    \"device_id\": $device_id
  }
]" );
    curl_setopt($curl, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
    $headers = array(
        "Accept: application/json",
        "Authorization: $auth_val",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}