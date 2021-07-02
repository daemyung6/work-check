<?php 
    session_start();
?>
<?php if(!isset($_SESSION['user_id'])): ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 페이지</title>
    <style>
        body, html {
            margin: 0px;
            padding: 0px;
            background-color: rgb(131, 131, 131);
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }
        @font-face {
            font-family: bold;
            src: url("./font/NanumSquareB.ttf");
        }
        @font-face {
            font-family: NanumSquareL;
            src: url("./font/NanumSquareL.ttf");
        }
        .login-box {
            width: 400px;
            height: 200px;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .type {
            display: inline-block;
            width: 50px;
        }
        .title {
            font-family: 'bold';
            font-size: 23px;
        }
        .inputbox {
            margin: 20px;
        }
        button {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="title">근태관리시스템</div>
        <div class="inputbox">
            <div class="row">
                <div class="type">ID</div>
                <input id="id" type="text">
            </div>
            <div class="row">
                <div class="type">PW</div>
                <input id="pw" type="password">
            </div>
        </div>
        <div>
            <button id="loginbt">login</button>
            <button id="userAddbt">가입</button>
        </div>
        
    </div>
</body>
<script>
    const id = document.getElementById("id");
    const pw = document.getElementById("pw");
    const loginbt = document.getElementById("loginbt");
    const userAddbt = document.getElementById("userAddbt");

    userAddbt.onclick = function() {
        location.href = "./sign_up.html";
    }

   function login() {
        let form = new FormData();
        form.append('user_id', id.value);
        form.append('user_pw', pw.value);
        fetch("./login_ok.php", {
            method : "POST",
            body : form,
        })
        .then(json => json.json())
        .then(json => {
            console.log(json);
            if(json.err) {
                alert(json.msg);
                return;
            }
            
            location.reload();
        });
    }
    loginbt.onclick = login;
    document.body.addEventListener('keydown', function(e) {
        if(e.key == "Enter") {
            login()
        }
    })
</script>
</html>

<?php else: ?>

<?php 
    include "config.php";
    $conn = mysqli_connect(
        'localhost',
        $dbid,
        $dbpw,
        'worklog'
    );
    //유저 정보
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM user WHERE id = '$user_id';";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    $user_name = $row["name"];

    $is_work = $row["is_work"];
    
    $start_time = $row["start_time"];

    $nowTime = date("Y-m-d");

    //오늘 요일 0 ~ 6
    $today_num = date('w');
    $today_num = $today_num - 1;
    $timestamp = strtotime("$nowTime -$today_num days");

    $sql = "SELECT sub_min FROM log WHERE (user_id = '$user_id') AND (end_time BETWEEN '$nowTime 00:00:00' AND '$nowTime 24:59:59')";
    $result = mysqli_query($conn, $sql);
    $timeCount = 0;
    while($row = mysqli_fetch_array($result)) {
        $timeCount += (int) $row['sub_min'];
    }
    $nowWorkTime = $timeCount;
    $nowWork_h = (int) ($nowWorkTime / 60);
    $nowWork_m = (int) ($nowWorkTime % 60);

    $monday = date("Y-m-d", $timestamp);
    $sql = "SELECT sub_min FROM log WHERE (user_id = '$user_id') AND (end_time BETWEEN '$monday 00:00:00' AND '$nowTime 24:59:59')";
    $result = mysqli_query($conn, $sql);
    $timeCount = 0;
    while($row = mysqli_fetch_array($result)) {
        $timeCount += (int) $row['sub_min'];
    }
    $weekWorkTime = $timeCount;
    $weekWork_h = (int) ($weekWorkTime / 60);
    $weekWork_m = (int) ($weekWorkTime % 60);


    $firstmonth = date('Y-m-01');
    $sql = "SELECT sub_min FROM log WHERE (user_id = '$user_id') AND (end_time BETWEEN '$firstmonth 00:00:00' AND '$nowTime 24:59:59')";
    $result = mysqli_query($conn, $sql);
    $timeCount = 0;
    while($row = mysqli_fetch_array($result)) {
        $timeCount += (int) $row['sub_min'];
    }
    $monthWorkTime = $timeCount;
    $monthWork_h = (int) ($monthWorkTime / 60);
    $monthWork_m = (int) ($monthWorkTime % 60);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>근태관리시스템</title>
    <style>
        body, html {
            margin: 0px;
            padding: 0px;
            background-color: rgb(131, 131, 131);
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }
        @font-face {
            font-family: bold;
            src: url("./font/NanumSquareB.ttf");
        }
        @font-face {
            font-family: NanumSquareL;
            src: url("./font/NanumSquareL.ttf");
        }
        .box {
            position: absolute;
            width: 800px;
            height: 500px;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .box .left {
            position: absolute;
            top: 0px;
            left: 0px;
            width: calc(100% / 3);
            height: 100%;
            background: #dedede;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            text-align: center;
        }
        .bold {
            font-family: bold;
            font-size: 20px;
        }
        .box .right {
            position: absolute;
            top: 0px;
            left: calc(100% / 3);
            width: calc(100% * 2 / 3);;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .name {
            font-family: bold;
            font-size: 40px;

        }
        button {
            margin: 2%;
            width: 40%;
            height: 20%;
            font-size: 30px;
        }

    </style>
</head>
<body>
    <div class="box">
        <div class="left">
            <div class="name">
                <?php echo $user_name; ?>
            </div>
            <div class="is_work">
                <span class="bold">상태 : <?php if($is_work == 'true') { echo "근무중"; } else { echo "퇴근"; } ?></span>
            </div>
            <div class="worktime">
                금일근무시간<br />
                <span class="bold"><?php echo $nowWork_h ?>시간 <?php echo $nowWork_m ?>분</span>
            </div>
            <div class="weekworktime">
                금주누적근무시간<br />
                <span class="bold"><?php echo $weekWork_h ?>시간 <?php echo $weekWork_m ?>분</span>
            </div>
            <div class="mounthworktime">
                금월누적근무시간<br />
                <span class="bold"><?php echo $monthWork_h ?>시간 <?php echo $monthWork_m ?>분</span>
            </div>
            <a href="./logout.php">로그아웃</a>
        </div>
        <div class="right">
            <button <?php if($is_work == 'true') { echo "disabled"; } else { echo ""; } ?> id="startbt">출근</button>
            <button <?php if($is_work == 'true') { echo ""; } else { echo "disabled"; } ?> id="endbt">퇴근</button>
        </div>
        
    </div>
</body>
<script>
    const startbt = document.getElementById("startbt");
    const endbt = document.getElementById("endbt");

    startbt.onclick = function() {
        if(!confirm("출근 하시겠습니까?")) {
            return;
        }
        const form = new FormData();
        form.append('work', 1);
        fetch("./work.php", {
            method : "POST",
            body : form
        })
        .then(json => json.json())
        .then(json => {
            console.log(json);
            if(json.err) {
                alert(json.msg);
                return;
            }
            alert(json.msg);
            location.reload();
            
        })
    }

    endbt.onclick = function() {
        if(!confirm("퇴근 하시겠습니까?")) {
            return;
        }
        const form = new FormData();
        form.append('work', 0);
        fetch("./work.php", {
            method : "POST",
            body : form
        })
        .then(json => json.json())
        .then(json => {
            console.log(json);
            if(json.err) {
                alert(json.msg);
                return;
            }
            alert(json.msg);
            location.reload();
            
        })
    }

</script>
</html>

<?php endif; ?>