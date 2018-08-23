<?php
include 'db.php';

session_start();

$input_id = $_POST['id']; // 입력한 아이디
$input_pw = $_POST['passwd']; // 입력한 비밀번호

// <-- db 연결 설정
$dbConnect = @mysql_connect(SERVERADDR, USER, PW);
mysql_select_db(DBNAME, $dbConnect);

// <-- 현재 아이디의 정보를 가져 오기.
$sql = "select user_id, user_pw, user_alias  from user where user_id='".$input_id."'";

$loginResult = mysql_query($sql);

$loginData = mysql_fetch_row($loginResult);

$user_id = $loginData[0];
$user_pw = $loginData[1];

// <-- id & pw 확인
if($user_id == null || $input_pw != $user_pw)
    echo "loginFailed";
// <-- 로그인 완료 되면 세션 생성
else {
    $user_alias = $loginData[2];

    if(!isset($_SESSION))
        session_start();

    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_pw'] = $user_pw;
    $_SESSION['user_alias'] = $user_alias;

    echo $_SESSION['user_alias'];

}
