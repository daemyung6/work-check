<?php
include "config.php";
if(
    (!isset($_POST['user_id'])) ||
    (!isset($_POST['user_pw'])) ||
    (!isset($_POST['user_name']))
) {
    $arr = array(
        "err" => true,
        "msg" => "인자값이 부족합니다.",
    );
    $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
    echo $json;
    return;
};

$user_id = $_POST['user_id'];
$user_pw = $_POST['user_pw'];
$user_name = $_POST['user_name'];

$conn = mysqli_connect(
    'localhost',
    $dbid,
    $dbpw,
    'worklog'
);

$sql = "SELECT * FROM user WHERE id = '$user_id';";

$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result)) {
    $arr = array(
        "err" => true,
        "msg" => "동일한 아이디가 있습니다.",
    );
    $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
    echo $json;
    return;
}


$sql = "INSERT INTO user(id, password, name) VALUES('$user_id', password('$user_pw'), '$user_name');";

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
    "msg" => "done",
);
$json = json_encode($arr, JSON_UNESCAPED_UNICODE);
echo $json;
return;

?>