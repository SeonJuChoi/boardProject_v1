<?php
include 'db.php';

$articleId = $_POST['articleId'];
$subject = $_POST['subject'];
$contents = $_POST['contents'];
$currentPage = $_POST['page']; // 현재 페이지

$subject = htmlspecialchars($subject, ENT_QUOTES);
$content = htmlspecialchars($contents, ENT_QUOTES);
$content = str_replace(" ", "&nbsp;",$content);

$dbConnect = @mysql_connect(SERVERADDR, USER, PW);
mysql_select_db(DBNAME, $dbConnect);

$sql = "update board_list set subject="."'".$subject."',"."contents="."'".$content."' where board_id=".$articleId;

$result = mysql_query($sql);


$link = 'articleRead.php?page='.$currentPage.'&articleId='.$articleId.'&edit=true';

if($result) {
    echo "<script>alert('수정되었습니다.')</script>";
    echo "<script>document.location.href = '$link';</script>";
}

?>