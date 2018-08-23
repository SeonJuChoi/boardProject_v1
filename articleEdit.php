<?php
// <-- Session Start
session_start();

include 'db.php';

$currentUser = $_SESSION['user_id'];

$articleId = $_GET['articleId']; // 게시글 번호
$currentPage = $_GET['page']; // 현재 페이지

$dbConnect = @mysql_connect(SERVERADDR, USER, PW);
mysql_select_db(DBNAME, $dbConnect);

$sql = "select user_id from board_list where board_id=".$articleId;

$writerResult = mysql_query($sql);

$idData = mysql_fetch_row($writerResult);

// <-- 글쓴이만 편집 가능
if($currentUser != $idData[0]){
    echo "<script>alert('글쓴이만 수정이 가능합니다.')</script>";
    echo "<script>history.back()</script>";
}



// <-- 게시글을 조회하여서 그 내용을 수정할 수 있도록...

$sql = "select subject, contents, user_name from board_list where board_id=".$articleId;

$readResult = mysql_query($sql);

$articleData = mysql_fetch_row($readResult);

mysql_close($dbConnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<style>
    textarea {
        width: 300px;
        height:200px;
        resize: none;
    }
</style>

<body>
<div id="writeBox">
    <form action="edit.php" method="post" id="writeForm">
        <table>
            <?

                echo "<tr>";
                echo "<td> 제목 : </td>";
                echo "<td>";

                echo "<input type='text' name='subject' value='$articleData[0]'>";
                echo  "<input type='hidden' name = 'articleId' value=$articleId>";
                echo  "<input type='hidden' name = 'page' value=$currentPage>";
                echo "<td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td>작성자 : </td>";
                echo "<td>$articleData[2]</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td>내용 : </td>";
                $contents = html_entity_decode($articleData[1]);
                echo "<td><textarea name='contents'>$contents</textarea></td>";
                echo "</tr>";
            ?>
        </table>
        <input type="submit" value="수정">
        <?
        $link = 'articleRead.php?page='.$currentPage.'&articleId='.$articleId.'&edit='.$articleId;
            echo "<input type='button' value='취소' onclick=location.href='$link'>";

         ?>
    </form>
</div>
</body>
</html>
