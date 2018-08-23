<?php
// <-- Session Start
session_start();

include 'db.php';

$currentUser = $_SESSION['user_id']; // user ID
$currentPage = $_GET['page']; // 현재 페이지
$link = ''; // 목록보기 링크
$articleId = $_GET['articleId'];  // 게시글 id

// <-- 쿼리 연결
$dbConnect = @mysql_connect(SERVERADDR, USER,PW);
mysql_select_db(DBNAME, $dbConnect);

// <-- 글쓴이를 조회하여 글쓴이만 삭제 가능.
if(isset($_GET['pid'])) {
    $pid=$_GET['pid'];
    $sql = 'select user_id from board_list where board_id='.$pid;
}
else {
    $sql = 'select user_id from board_list where board_id='.$articleId;
}

$writerResult = mysql_query($sql);
$writerData = mysql_fetch_row($writerResult);

if($currentUser != $writerData[0]) {
    echo "<script>alert('글쓴이만 삭제가 가능합니다.')</script>";
    echo "<script>history.back()</script>";
}
else {
    // <-- 덧글일 경우
    if(isset($_GET['pid'])) {
        $link = 'articleRead.php?page=' . $currentPage . '&articleId=' . $articleId;
        echo $link;
        $sql = 'delete from board_list where board_id='.$pid;
    }
    // 테이블 스키마 txt파일 , html서식파일
    // <-- 덧글이 아닐경우
    else {
        // <-- 덧글이 있는지 확인 후 pid를 이용해서 덧글 삭제...
        $sql = "select * from board_list where board_pid=".$articleId;

        $cResult = mysql_query($sql);

        $cRow = mysql_num_rows($cResult);

        if($cRow > 0) {
            $sql = "delete from board_list where board_pid=".$articleId;

            mysql_query($sql);
        }
        // <-- 게시글 삭제 쿼리 설정
        $link = 'board_list.php?currentPageNum='.$currentPage;
        $sql = 'delete from board_list where board_id='.$articleId;
    }


    // <-- 게시글 id를 이용하여 게시글 삭제 쿼리 실행

    $deleteResult = mysql_query($sql);


    // <-- 쿼리 실행후 삭제가 완료되면 리스트로 돌아감..
    if($deleteResult) {
        echo "<script>alert('삭제되었습니다.')</script>";
        echo "<script>document.location.href = '$link';</script>";
    }

}


?>