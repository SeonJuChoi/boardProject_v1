<?php

// <-- dbConnect

include 'db.php';

$dbConnect = @mysql_connect(SERVERADDR, USER, PW);

mysql_select_db(DBNAME, $dbConnect);

// <--- id가 있는지 확인
$userId = $_POST['userId'];

$sql = "select * from user where user_id='".$userId."'";

$result = mysql_query($sql);

$row = mysql_num_rows($result);

if($row == 0)
    echo "true";
else
    echo "false";

?>