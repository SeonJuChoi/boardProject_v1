<?php
session_start();
include 'db.php';

$dbConnect = @mysql_connect(SERVERADDR,USER,PW);
mysql_select_db(DBNAME, $dbConnect);

if(! isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인 후 글 읽기가 가능합니다')</script>";
    echo "<script>history.back();</script>";
}

// <-- 현재 페이지와 게시글 번호를 이용해 목록보기 링크 설정
$currentPage = @$_GET['page']; // 현재 페이지
$articleId = @$_GET['articleId']; // 게시글 번호
$searchMode = false;
$option = "";
$keyword = "";

if(isset($_GET['searchOp']) && isset($_GET['searchKeyword'])) {
    $option = @$_GET['searchOp'];
    $keyword = @$_GET['searchKeyword'];
    $searchMode = true;
}

if($searchMode == true)
    $listLink = 'board_list.php?searchOp='.$option.'&searchKeyword='.
          $keyword.'&currentPageNum='.$currentPage;
else
    $listLink = 'board_list.php?currentPageNum='.$currentPage; // 목록보기 링크

// <-- 수정 후는 조회수가 증가 X 글 읽기 모드인 경우에만 조회수가 증가.
if(!isset($_GET['edit']) && isset($_SESSION['user_id'])) {
    // <-- 조회수 조회후 조회수 증가
    $sql = "select hits from board_list where board_id=".$articleId;

    $hitResult = mysql_query($sql);

    $hitRow = mysql_fetch_row($hitResult);

    $hits = $hitRow[0];

    $hits++;

    // <-- 조회수 업데이트
    $sql = "update board_list set hits=".$hits." where board_id=".$articleId;

    mysql_query($sql);
}

// <-- 게시물 조회 쿼리 실행
$sql = "select * from board_list where board_id=".$articleId;

$selectResult = mysql_query($sql);

$resultData = mysql_fetch_row($selectResult);

// <-- 덧글 조회

$sql = "select user_name, contents, reg_date, board_pid ,board_id from board_list where board_pid=".$articleId.
        " order by reg_date asc";

$pResult = mysql_query($sql);

$pRowCount = mysql_num_rows($pResult);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<style>

    #articleTable {
        text-align: left;
        border-bottom: 1px solid lightgray;
    }

    #commentTable td {
        border-bottom: 1px solid lightgray;
    }

    #articleTable th {
        background-color: #ececec;
    }

    textarea {
        width: 400px;
        resize: none;
    }

    #date {
        font-size: 10px;
        text-align: right;
    }

    a {
        color:black;
        text-decoration : none;
    }

    a:hover {
        color: black;
        text-decoration: none;
    }

    .btn {
        margin-right: 5px;
    }


    #pBt {
        margin-top: 10px;
    }

    #menuBox {
        text-align: left;
        margin-top: -10px;
        margin-bottom: 10px;
    }

    #comment {
        resize: none;
        width: 750px;
    }

    #subject, #text, #writer {
        width: 500px;
    }

    #writer {
        border: none;
    }

</style>
<script language="JavaScript" src="js/jquery-3.2.1.js"></script>
<script language="JavaScript" src="js/bootstrap.js"></script>
<body>
<div class="container">
<h2>게시글 보기</h2>
<table class="table" id="articleTable" width='600px'>
    <?
        // <-- 게시물 출력

        // <-- 제목
        echo "<tr>";
            echo "<thead>";
            echo "<th colspan='4'  id='subject'>제목 : ".$resultData[4]."</th>";
            echo "</thead>";
        echo "</tr>";
        // <-- 작성자
        echo "<tr>";
            echo "<td>작성자 : ".$resultData[3]."</td>";
        // <-- 작성일
            echo "<td>작성일 : ".$resultData[7]."</td>";
        // <-- 조회수
            echo "<td>조회 수 : ".$resultData[6]."</td>";
        // <-- 덧글수
            echo "<td>덧글 수 : ".$pRowCount."</td>";
        echo "</tr>";
        echo "<tr>";
            $contents = nl2br($resultData[5]);
            echo "<td colspan='4'>$contents</td>";
        echo "</tr>";

    ?>
</table>
<div id="menuBox">
<?
// <-- 하단 메뉴 설정 후 생성
echo "<input type='button' class='btn btn-info' value='목록' onclick=location.href='$listLink'>";

if($resultData[3] == @$_SESSION['user_alias']) {
    $articleEditLink = "articleEdit.php?page=".$currentPage."&articleId=".$articleId;

    echo "<input type='button' class='btn btn-default' data-toggle='modal' data-target='#editTemplate' value='수정'>";

    $deleteLink = "articleDelete.php?page=".$currentPage."&articleId=".$articleId;

    echo "<input type='button' class='btn btn-default' value='삭제' onclick=deleteCheck('$deleteLink')>";
}

?>
</div>

<table id="commentTable" class="table">
    <?
    // <-- 덧글 테이블
    echo "<tr>";
    echo "<thead>";
    echo "<th colspan='3'>덧글 달기</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tr class='active'>";
    echo "<form action='write.php' method='post'>";
    echo "<td width='150px' align='center'>".@$_SESSION['user_alias']."</td>";
    echo "<td><textarea name='pArea' class='form-control' id='comment'></textarea></td>";
    echo "<td width='200px'><input type='submit' id='pBt' class='btn btn-info' name='pBt' value='덧글 달기'></td>";
    echo "<input type='hidden' name='articleId' value='$articleId'>";
    echo "<input type='hidden' name='page' value='$currentPage'>";
    echo "</form>";
    echo "</tr>";

    // <-- 덧글 출력
    if ($pRowCount != 0) {
        for($pRow = 0 ; $pRow < $pRowCount ; $pRow++) {
            $pData = mysql_fetch_row($pResult);
            $pContents = nl2br($pData[1]);
            $deletePLink = "articleDelete.php?page=".$currentPage."&articleId=".$pData[3]."&pid=".$pData[4];
            echo "<tr>";
            echo "<td align='center'>".$pData[0]."</td>";
            echo "<td colspan='2'>".$pContents;
            echo "<div id='date'>";
            echo      "<a>작성일 : ".$pData[2]."</a>";
            echo      "<a href='$deletePLink'> | 삭제</a></td>";
            echo "</div>";
            echo "<tr>";

        }
    }
    ?>
</table>

<?
// <-- 수정하기 위한 데이터 조회

$sql = "select subject, contents, user_name from board_list where board_id=".$articleId;

$editResult = mysql_query($sql);

$editRow = mysql_fetch_row($editResult);

mysql_close($dbConnect);
?>
<!-- 수정 모달 창 -->
<div class="modal fade" id="editTemplate" role="dialog">
    <div class="modal-dialog">
        <!-- 모달 글수정 템플릿 -->
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 class="modal-title">글 수정하기</h3>
    </div>
    <div class="modal-body">
        <form action="edit.php" method="post" id="editForm">
            <table id="editTable">
                <?
                echo "<tr>";
                echo "<td>제목 : </td>";
                echo "<td><input type='text' class='form-control' name='subject' id='subject' value='$editRow[0]'></td>";
                echo "<input type='hidden' name = 'articleId' value=$articleId>";
                echo "<input type='hidden' name = 'page' value=$currentPage>";
                echo "</tr>";

                echo "<tr>";
                echo "<td>작성자 : &nbsp;</td>";
                echo "<td><input type='text' name='writer' value='$editRow[2]' id='writer' readonly></td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td>내용 : </td>";
                echo "<td><textarea name='contents' class='form-control' rows='10' id='text'>$editRow[1]</textarea></td>";
                echo "</tr>"
                ?>
            </table>
            </div>

            <div class="modal-footer">
                <input type="submit" id='write' value="수정하기" class='btn btn-info'>
                <input type="button" id='cancel' value="취소" class='btn btn-default' data-dismiss="modal">
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</body>
<script>

    function deleteCheck(link) {
        var delCheck = confirm('게시글을 삭제하시겠습니까?');

        if(delCheck == true)
                location.href = link;
            }
</script>
</html>
