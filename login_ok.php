<?php
include "config.php";
if(
    (!isset($_POST['user_id'])) ||
    (!isset($_POST['user_pw']))
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
$conn = mysqli_connect(
    'localhost',
    $dbid,
    $dbpw,
    'worklog'
);
$sql = "SELECT * FROM user WHERE id = '$user_id' AND password = password('$user_pw');";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result)) {
    session_start();
    $_SESSION['user_id'] = $user_id;
    $arr = array(
        "err" => false,
        "msg" => "done"
    );
    $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
    echo $json;
    return;
}

$arr = array(
    "err" => true,
    "msg" => "가입하지 않은 아이디이거나, 잘못된 비밀번호입니다.",
);
$json = json_encode($arr, JSON_UNESCAPED_UNICODE);
echo $json;
return;

?>