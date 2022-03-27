<?php
require '../api_lock.php';

//Patients auth code
$authcode = $_POST['auth_code'] ?? null;
$speciality = $_POST['speciality'] ?? null;
$rating = $_POST['rating'] ?? null;
$limit = $_POST['limit'] ?? "10";

if(check_null($authcode))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

if(is_null($speciality) && is_null($rating)){
    //return all doctors
    $sql = "SELECT user_id, full_name, phone, gender, dob, address FROM `users` WHERE user_type = 2 LIMIT $limit";
    $result = runSQLCommandAPI($sql);
    $jsn = mysql_result_to_json($result);
    json_response_return(200,'OK', $jsn);
}else{
    //filter doctors
    $doctorid = array();
    array_push($doctorid,"0");

    if(!is_null($speciality)){
        $sql = "SELECT doctor_id  FROM `doctor_speciality` ds WHERE speciality_name LIKE '%$speciality%'";
        $result = runSQLCommandAPI($sql);
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($doctorid, $row['doctor_id']);
            }
        }
    }

    if(!is_null($rating)) {
        $doctorid_join = implode(",", $doctorid);
        $sql = "SELECT doctor_id FROM `doctor_appointment` da JOIN `patient_rating` pr ON pr.appointment_id = da.appointment_id 
WHERE doctor_id IN ($doctorid_join)
GROUP BY doctor_id, rating
HAVING AVG(rating) > $rating";
        $doctorid = array();
        array_push($doctorid,"0");
        $result = runSQLCommandAPI($sql);
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($doctorid, $row['doctor_id']);
            }
        }
    }

    $doctorid_join = implode(",", $doctorid);
    $sql = "SELECT user_id, full_name, phone, gender, dob, address FROM `users` WHERE user_id IN ($doctorid_join) LIMIT $limit";
    $result = runSQLCommandAPI($sql);
    $jsn = mysql_result_to_json($result);
    json_response_return(200,'OK', $jsn);
}