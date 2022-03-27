<?php
require '../api_lock.php';

$authcode = $_POST['auth_code'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;
$date_start = $_POST['date_start'] ?? null;
$date_end = $_POST['date_end'] ?? null;

if(check_null($authcode, $doctor_id, $date_start, $date_end))
    json_response_return(400,'NULL VALUES');

$userid = get_user_id_via_auth_code($authcode);
if($userid < 0)
    json_response_return(400,'INVALID AUTH CODE');

$data = [];
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
foreach($days as $day){
    $data[$day] = [];
}

$period = new DatePeriod(
    new DateTime($date_start),
    new DateInterval('P1D'),
    new DateTime($date_end)
);


$sql = "SELECT * FROM `doctor_schedule` WHERE doctor_id = $doctor_id";
$result = runSQLCommandAPI($sql);
while($row = $result->fetch_assoc()){
    $day = $row['day_name'];
    $start_time = explode(":", $row['start_time']);
    $end_time = explode(":", $row['end_time']);

    $obj = new stdClass();
    $obj->start = $start_time[0].".".$start_time[1];    //hr.mins
    $obj->end = $end_time[0].".".$end_time[1];    //hr.mins
    $data[$day][] = $obj;
}

$data_date = [];

$sql = "SELECT * FROM `doctor_appointment` WHERE doctor_id = $doctor_id AND `date` BETWEEN '$date_start' AND '$date_end'";
$result = runSQLCommandAPI($sql);
while($row = $result->fetch_assoc()){
    $start_time = explode(":", $row['start_time']);
    $end_time = explode(":", $row['end_time']);
    $obj = new stdClass();
    $obj->start = $start_time[0].".".$start_time[1];    //hr.mins
    $obj->end = $end_time[0].".".$end_time[1];    //hr.mins
    $data_date[$row['date']] = $obj;
}

$result = [];
foreach ($period as $key => $value) {
    $date = $value->format('Y-m-d');
    $day = date('l', strtotime($date));
    $result[$date] = $data[$day];
    if(isset($data_date[$date])){
        $len = sizeof($data[$day]);
        for($x = 0; $x < $len; $x++){
            $obj = $data[$day][$x];
            //three cases
            if($data_date[$row['date']]->start >= $obj-> start && $data_date[$row['date']]->start <= $obj->end){

            }
        }

        $range1 = new stdClass();
        $range2 = new stdClass();
        $range1->start = $data[$day]->start;
        $range1->end = $data_date[$row['date']]->end;
        $range2->start = $data_date[$row['date']]->start;
        $range2->end = $data[$day]->end;
    }
}


json_response_return(200,'OK', $jsn);