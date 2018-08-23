<?php
// <-- Session Start
session_start();

include 'db.php';

$user_id = $_SESSION['user_id']; // 유저 Id

$date = date("Y-m-d H:i:s");
$articleLink = "";

// <-- db Connect
$dbConnect = @mysql_connect(SERVERADDR, USER, PW);
mysql_select_db(DBNAME, $dbConnect);

// <-- 덧글일 경우
if (isset($_POST['pArea'])) {
   $pContents = $_POST['pArea'];
   $articleId = $_POST['articleId'];
   $writer = $_SESSION['user_alias'];
   $subject = 'comment';
   $currentPage = $_POST['page'];

   $articleLink = 'articleRead.php?page='.$currentPage.'&articleId='.$articleId;

   $pContents = htmlspecialchars($pContents, ENT_QUOTES);
   $pContents = str_replace(" ", "&nbsp;",$pContents);

   $sql = "insert into board_list (board_pid, subject, user_id, user_name, contents, reg_date) values (".
            $articleId.",'".$subject."',"."'".$user_id."',"."'".$writer."',"."'".$pContents."',"."'".$date."')";
}
// <-- 덧글이 아닐경우
else {
    $subject = $_POST['subject']; // 제목
    $writer = $_POST['writer']; // 글쓴이 (유저 별명)
    $contents = $_POST['contents']; // 글 내용

    $subject = htmlspecialchars($subject, ENT_QUOTES);

    $content = htmlspecialchars($contents, ENT_QUOTES);
    $content = str_replace(" ", "&nbsp;",$content);

    $sql = "insert into board_list (subject, user_id, user_name, contents, reg_date) values (".
        "'".$subject."',"."'".$user_id."',"."'".$writer."',"."'".$content."',"."'".$date."')";
}

$result = mysql_query($sql);


if($result) {
    echo "<script>alert('등록되었습니다.');</script>";
    if($subject == 'comment')
        echo "<script>location.href = '$articleLink';</script>";
    else
        echo "<script>location.href = 'board_list.php?';</script>";
}
else
    echo $sql;
    // echo "<script>alert('글 등록에 실패 하였습니다.');</script>";

mysql_close($dbConnect);

?>

