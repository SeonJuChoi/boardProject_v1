<?php
// <-- dbConnect

include 'db.php';

$dbConnect = @mysql_connect(SERVERADDR, USER, PW);

mysql_select_db(DBNAME, $dbConnect);


// <-- 아이디, 비밀번호, 비밀번호, 비밀번호 확인, 닉네임 체크
$userId = $_POST['userId'];
$userPw = $_POST['userPw'];
$userAlias = $_POST['userAlias'];

// <-- 아이디, 비밀번호 , 비밀번호 확인, 닉네임 등록.

$sql = "insert into user (user_id, user_pw, user_alias) values ('".$userId."',"."'".$userPw."', ".
        "'".$userAlias."')";

$result = mysql_query($sql);

if($result) {
    echo "<script>alert('회원가입이 완료되었습니다.');</script>";
    echo "<script>history.back();</script>";
}

?>