<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    $arr = array(
        "err" => true,
        "msg" => "로그인이 필요합니다.",
    );
    $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
    echo $json;
    return;
}
$user_id = $_SESSION['user_id'];
include "config.php";


if($_SERVER["REQUEST_METHOD"] == "GET") {
    $conn = mysqli_connect(
        'localhost',
        $dbid,
        $dbpw,
        'worklog'
    );
    
    $sql = "SELECT is_work FROM user WHERE id = '$user_id';";
    $result = mysqli_query($conn, $sql);

    

    while($row = mysqli_fetch_array($result)) {
        $arr = array(
            "err" => false,
            "msg" => $row["is_work"]
        );
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $json;
        return;
    }
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!isset($_POST['work'])) {
        $arr = array(
            "err" => true,
            "msg" => "인자가 부족합니다.",
        );
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $json;
        return;
    }
    $conn = mysqli_connect(
        'localhost',
        $dbid,
        $dbpw,
        'worklog'
    );

    $workvalue = (int) $_POST['work'];
    

    $sql = "SELECT is_work, start_time FROM user WHERE id = '$user_id';";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $last_start_time = $row['start_time'];
    if(
        ($row['is_work'] == "true") && ($workvalue == 1) ||
        ($row['is_work'] == "false") && ($workvalue == 0)
    ) {
        $arr = array(
            "err" => true,
            "msg" => "이미 반영된 값입니다.",
        );
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $json;
        return;
    }
    
    //근무 시작
    if($workvalue == 1) {
        $block_start = date("H:i:s", strtotime("18:00:00"));
        $block_end = date("H:i:s", strtotime("05:59:59"));
        $now = date("H:i:s");
        if(
            ($block_start < $now) ||
            ($now < $block_end)
        ) {
            $arr = array(
                "err" => true,
                "msg" => "출근이 불가한 시간입니다. \n'18:00:00' - '05:59:59'",
            );
            $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
            echo $json;
            return;
        }


        $nowTime = date("Y-m-d H:i:s");

        $sql = "UPDATE user SET is_work = 'true', start_time = '$nowTime' WHERE id = '$user_id';";

        $result = mysqli_query($conn, $sql);
    
        if($result === false) {
            $arr = array(
                "err" => true,
                "msg" => "DB error",
            );
            $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
            echo $json;
            return;
        }
    
        $arr = array(
            "err" => false,
            "msg" => "$nowTime 근무 시작",
        );
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $json;
        return;
    }
    //퇴근
    if($workvalue == 0) {
        $nowTime = date("Y-m-d H:i:s");
        $sub_time = (strtotime(date('Y-m-d H:i:s')) - strtotime($last_start_time)) / 60;
        $sub_time = (int) $sub_time;

        $sql = "INSERT INTO log(user_id, start_time, end_time, sub_min) VALUES('$user_id', '$last_start_time', '$nowTime', '$sub_time');";

        $result = mysqli_query($conn, $sql);
    
        if($result === false) {
            $arr = array(
                "err" => true,
                "msg" => "log DB error",
            );
            $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
            echo $json;
            return;
        }

        $sql = "UPDATE user SET is_work = 'false' WHERE id = '$user_id';";

        $result = mysqli_query($conn, $sql);
    
        if($result === false) {
            $arr = array(
                "err" => true,
                "msg" => "user DB error",
            );
            $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
            echo $json;
            return;
        }
    
        $arr = array(
            "err" => false,
            "msg" => "$nowTime - 퇴근",
        );
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        echo $json;
        return;
    }
}
?>