<?php
// <-- Session Start
session_start();

// <--- db Connect
include 'db.php';

$dbConnect = @mysql_connect(SERVERADDR, USER, PW);

mysql_select_db(DBNAME, $dbConnect);

if (isset($_GET['currentPageNum']))
    $currentPage = $_GET['currentPageNum'];
else
    $currentPage = 1;

$numOfContents = 5; // 보여지는 게시글 갯수

$startContentsNum = ($currentPage - 1) * $numOfContents; // sql 게시글 갯수 설정

$searchMode = false; // 검색모드

$keyword = "";
$option = "";

// <-- 검색 키워드 O -> 검색 모드, 키워드 X -> 보여주기 모드
if (isset($_GET['searchKeyword'])) {
    $keyword = $_GET['searchKeyword']; // 검색 키워드
    $option = $_GET['searchOp']; // 검색 옵션
    $searchMode = true;
}

if ($searchMode == true) {

    $sql = "select * from board_list where board_pid = 0 and ";

    switch ($option) {
        case 'subject': {
            $sql .= "subject like '%" . $keyword . "%'";
            break;
        }
        case 'contents': {
            $sql .= "contents like '%" . $keyword . "%'";
            break;
        }
        case 'subjectContents' : {
            $sql .= "( subject like '%" . $keyword . "%' and contents like '%" . $keyword . "%' )";
            break;
        }
    }

    $rowResult = mysql_query($sql);

    $row = mysql_num_rows($rowResult);

    if ($row == 0) {
        echo "<script>alert('검색결과가 없습니다!')</script>";
        echo "<script>location.href='board_list.php?';</script>";
    }

    $allPage = ceil($row / $numOfContents); // 전체 페이지 갯수

    $sql .= " order by reg_date desc limit " . $startContentsNum . ", " . $numOfContents;

    echo $sql."<br>";

} else {
    // <-- 전체 게시글 갯수
    $sql = "select * from board_list where board_pid = 0";

    $rowResult = mysql_query($sql);

    $row = mysql_num_rows($rowResult);

    $allPage = ceil($row / $numOfContents); // 전체 페이지 갯수

    $sql = "select * from board_list where board_pid = 0 order by reg_date desc limit " .
        $startContentsNum . ", " . $numOfContents;

}

$result = mysql_query($sql);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>자유 게시판</title>
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<style>
    #boardTable tr, td, th {
        text-align: center;
    }

    #boardTable {
        table-layout: fixed;
        margin: 0 auto;
    }

    #boardBox {
        margin: 0 auto;
        text-align: center;
    }

    #currentPage {
        color: lightskyblue;
    }

    #loginBt, #logoutBt, #newBt {
        margin-top: 0px;
    }

    #idBox, #pwBox {
        width: 150px;
        height: 25px;
    }

    #pageBox {
        margin-right: 10px;
        position: relative;
    }

    #searchBox {
        float: right;
        margin-top: 30px;
        position: relative;
        left: -50%;
    }

    #searchOp {
        width: 100px;
    }

    #searchText {
        width: 400px;
    }

    #searchBt {
        margin-top: 0px;
    }

    #searchText, #searchOp, #searchBt {
        float: left;
        position: relative;
        left: 50%;
    }

    .btn {
        margin-top: -22px;
        margin-left: 5px;
        margin-right: 5px;
    }

    .pagination {
        margin-bottom: -3px;
    }

    a {
        color: black;
        text-decoration: none;
    }

    a:link {
        color: black;
        text-decoration: none;
    }

    a:visited {
        color: black;
        text-decoration: none;
    }

    a:hover {
        color: black;
        text-decoration: none;
    }

    #boardTable {
        border-top: 1px solid lightgray;
    }

    .table-striped > tbody > tr:nth-child(odd) > td {
        background-color: #bedede;
        border-bottom: 1px solid lightgray;
    }

    #writer {
        border: none;
    }

    #text {
        resize: none;
    }

    #subject {
        height: 30px;
    }

    #write, #cancel {
        margin-top: 5px;
    }

    #subject, #text, #writer {
        width: 500px;
    }

    .pagination li a{
        color: black;
    }

    #userId, #userPw, #userPwCheck, #userAlias {
        margin-bottom: 10px;
    }

    #check {
        margin-top: 0px;
        margin-bottom: 10px;
    }

    #weatherBox h3 {
        margin-top: -5px;
    }

    #weatherBox {
        font-size: 20px;
        text-align: center;
        margin: auto;
    }

    #wImg {
        width: 170px;
        height:170px;
        float: left;
    }
    .well {
        width: 500px;
    }
</style>
<script language="JavaScript" src="js/jquery-3.2.1.js"></script>
<script language="JavaScript" src="js/bootstrap.js"></script>

<script language="JavaScript">

    var newMemberFlag = false; // 중복검사 여부

    // <-- 요청을 보내기
    function httpRequest() {
        var loginBox = document.getElementById("loginBox");
        var buttonValue = document.getElementById(event.target.id).value;
        var param = ""; // 전송할 파타미터
        var url = ""; // url

        // <-- 버튼 값에 따라 전송될 파라미터 값 설정
        switch (buttonValue) {
            case '로그인' : {
                // 파라미터 값 설정정
                var id = document.getElementById("idBox").value;
                var pw = document.getElementById("pwBox").value;
                param = "id=" + id + "&passwd=" + pw;
                url = "login.php"; // 요청을 보낼 url
                break;
            }
            case '로그아웃' : {
                url = "logout.php";
                break;
            }

            case '중복확인' : {
                url = "memberCheck.php";
                var userId = document.getElementById('userId').value;
                param = "userId=" + userId;
                break;
            }
        }


        // 요청을 보내고 응답을 받을 xmlhttpRequest 객체
        var xmlRequestObj = new XMLHttpRequest();

        if (id === "" || pw === "") {
            alert("ID 또는 비밀번호를 입력하지 않았습니다!");
        }
        else if(userId === "") {
            alert('아이디를 입력하세요!');
        }
        else {
            xmlRequestObj.open('POST', url, true);
            xmlRequestObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlRequestObj.send(param);
        }

        xmlRequestObj.onreadystatechange = function () {
            if (xmlRequestObj.readyState == 4 && xmlRequestObj.status == 200) {
                if (buttonValue == '로그인') {
                    var loginMsg = xmlRequestObj.responseText;

                    if (loginMsg == 'loginFailed') {
                        alert('잘못된 아이디나 비밀번호를 입력하셨습니다.');
                    }
                    else {
                        while (loginBox.hasChildNodes()) {
                            loginBox.removeChild(loginBox.firstChild);
                        }
                        location.reload();
                    }
                }
                else if(buttonValue == '로그아웃') {

                    alert('로그아웃 되었습니다.');
                    location.reload();
                }
                else {
                    if (xmlRequestObj.responseText == "true") {
                        newMemberFlag = true;
                        alert('사용하실 수 있는 아이디 입니다.');
                    }
                    else
                        alert('사용하실 수 없는 아이디 입니다.');
                }

            }
        };


    }

    function memberCheck() {
        var userIdObj = document.getElementById('userId').value;
        var userPwObj = document.getElementById('userPw').value;
        var userPwCheckObj = document.getElementById('userPwCheck').value;
        var userAliasObj = document.getElementById('userAlias').value;
        var memberForm = document.getElementById('memberForm');

        if (userIdObj == "" || userPwObj == "" || userPwCheckObj == "" || userAliasObj == "")
            alert('회원가입 정보를 입력하세요!');
        else if (userPwObj != userPwCheckObj)
            alert('입력하신 비밀번호가 서로 일치하지 않습니다.');
        else {
            if (newMemberFlag == true)
                memberForm.submit();
            else
                alert('아이디 중복 검사를 해주세요!');
        }
    }


</script>
<body>
<div class="container">
    <h3>로그인</h3>
    <table id="loginBox">
        <?
        if (!isset($_SESSION['user_id'])) {
            echo "<tr>";
            echo "<td>아이디 : &nbsp;</td>";
            echo "<td><input type='text' name='id' class='form-control' id='idBox'></td>";
            echo "<td>&nbsp;비밀번호 : &nbsp;</td>";
            echo "<td><input type='password' name='passwd' class='form-control' id='pwBox'></td>";
            echo "<td><input type='button' class='btn btn-info' value='로그인' id='loginBt' onclick='httpRequest()'></td>";
            echo "<td><input type='button' id='newBt' class='btn btn-info' data-toggle='modal' data-target='#memberTemplate' value='회원가입'></td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td>" . $_SESSION['user_alias'] . "님 로그인 중 입니다. </td>";
            echo "<td><input type='button' class='btn btn-info' value='로그아웃' id='logoutBt' onclick='httpRequest()'></td>";
            echo "</tr>";
        }
        ?>
    </table>
    <br>
    <div id="weatherBox" class="well">
        <h3>오늘의 날씨 - 대구</h3>
        <?
        // <-- 대구 날씨
        $url = "http://www.kma.go.kr/wid/queryDFS.jsp?gridx=89&gridy=91";
        $weatherResult = simplexml_load_file($url);

        $body = $weatherResult->body;

        foreach($body->data as $item)
        {
            if($item == 0){
                $hour = $item->hour; // 시간
                $temp=$item->temp; //현재온도
                $sky=$item->sky; // 날씨 상태
                $maxTemp = $item->tmx; // 최고기온
                $minTemp = $item->tmn; // 최저기온
                $pty = $item->pty; // 강수상태
                break;
            }

        }

        if($pty != 0) {
            switch ($pty) {
                case 1: {
                    echo "<img src='img/rain.png' id='wImg'>";
                    echo "<p>비</p>";
                    break;
                }
                case 2 : {
                    echo "<img src='img/rain.png' id='wImg'>";
                    echo "<img src='img/snow.png' id='wImg'>";
                    echo "<p>비/눈</p>";
                    break;
                }
                case 3 : {
                    echo "<img src='img/snow.png' id='wImg'>";
                    echo "<p>눈</p>";
                    break;
                }
            }
        }
        else {
            switch ($sky) {
                case 1: {
                    echo "<img src='img/sunny.png' id='wImg'>";
                    echo "<p>맑음</p>";
                    break;
                }
                case 2 : {
                    echo "<img src='img/cloudySmall.png' id='wImg'>";
                    echo "<p>구름 조금</p>";
                    break;
                }
                case 3 : {
                    echo "<img src='img/cloudyBig.png' id='wImg'>";
                    echo "<p>구름 많음</p>";
                    break;
                }
                case 4: {
                    echo "<img src='img/cloudy.png' id='wImg'>";
                    echo "<p>흐림</p>";
                    break;
                }
            }
        }
        echo "<br>";
        echo "<p>현재온도 : ".$temp."℃</p>";
        if( $maxTemp == '-999.0')
            echo "<p>최고기온 : -℃</p>";
        else
            echo "<p>최고기온 : ".$maxTemp."℃</p>";

        if( $minTemp == '-999.0')
            echo "<p>최저기온 : -℃</p>";
        else
            echo "<p>최저기온 : ".$minTemp."℃</p>";

        ?>
    </div>
    <br><br>
    <div>
        <h1>자유게시판</h1>
    </div>

    <div id="boardBox">
        <table id='boardTable' class="table table-striped">
            <thead>
            <tr>
                <th width="45px">번호</th>
                <th width='400px'>제목</th>
                <th width='80px'>작성자</th>
                <th width="45px">조회수</th>
                <th width="45px">덧글수</th>
                <th width="100px">작성일</th>
            </tr>
            </thead>
            <?
            // <-- 게시글 리스트 출력
            $rowCount = mysql_num_rows($result);

            for ($num = 0; $num < $rowCount; $num++) {
                $row = mysql_fetch_row($result);

                // <-- 덧글 수 조회
                $sql = "select * from board_list where board_pid=".$row[0];
                $commentResult = mysql_query($sql);
                $commentCount = mysql_num_rows($commentResult);

                $articleID = $row[0];
                if ($searchMode == true)
                    $articleLink = 'articleRead.php?page=' . $currentPage . '&articleId=' . $articleID .
                        '&searchOp=' . $option . '&searchKeyword=' . $keyword;
                else
                    $articleLink = 'articleRead.php?page=' . $currentPage . '&articleId=' . $articleID;

                echo "<tr>";
                echo "<td >$row[0]</td>";
                echo "<td ><a href=$articleLink>$row[4]</a></td>";
                echo "<td>$row[3]</td>";
                echo "<td>$row[6]</td>";
                echo "<td>$commentCount</td>";

                $today = date('Y-m-d');
                $dateArr = explode(" ", $row[7]);

                if($dateArr[0] != $today) {
                    $articleDate = $dateArr[0];
                    echo "<td>$articleDate</td>";
                }
                else {
                    echo "<td>$row[7]</td>";
                }

                echo "</tr>";
            }

            mysql_close($dbConnect);
            ?>
    </div>
    </table>
    <div id="pageBox">

        <?
        // <-- 페이지 네이션

        // <-- 처음 마지막 페이지 링크 설정
        // 검색 모드일 경우 처음 마지막 버튼 페이지 설정
        if ($searchMode == true) {
            $firstPageLink = 'board_list.php?searchOp=' . $option . '&searchKeyword=' .
                $keyword . '&currentPageNum=' . '1';
            $lastPageLink = 'board_list.php?searchOp=' . $option . '&searchKeyword=' .
                $keyword . '&currentPageNum=' . $allPage;
        }
        // 검색 모드 아닐 경우 처음 마지막 버튼 페이지 설정
        else {
            $firstPageLink = 'board_list.php?currentPageNum=' . '1'; // 처음 페이지
            $lastPageLink = 'board_list.php?currentPageNum=' . $allPage; // 마지막 페이지
        }

        // <-- 이전 다음 버튼 페이지 이동 단위 설정
        $pageCount = 10; // 페이지 셋의 개수
        $allPageSet = ceil($allPage / $pageCount); // 전체 페이지 셋
        $pageSet = ceil($currentPage / $pageCount); // 현재 페이지 셋
        echo $pageSet."<br>";
        $setStart = (($pageSet - 1) * $pageCount) + 1; // 현재 페이지셋 시작 번호
        $setEnd = (($pageSet - 1) * $pageCount) + $pageCount; // 현재 페이지 셋 마지막 번호


        // 현재 페이지셋 마지막 번호가 전체 페이지 보다 많을 경우 전체 페이지로 설정
        if ($setEnd > $allPage)
            $setEnd = $allPage;

        // 전체 페이지가 10페이지 이하일 경우 한 페이지 씩 이동
        if ($allPage <= 10) {
            $nextPage = $currentPage + 1;
            $previous = $currentPage - 1;

        }
        // 전체 페이지가 11페이지 이상일 경우 한 페이지 셋씩 이동
        else {
            $nextPage = $setStart + 10; // 다음페이지
            $previous = $setStart - 1; // 이전 페이지
        }

        // <-- 이전 다음 페이지 링크 설정
        // 검색 모드일 경우 이전 다음 버튼 페이지 링크 설정
        if ($searchMode == true) {
            $previousLink = 'board_list.php?searchOp=' . $option . '&searchKeyword=' .
                $keyword . '&currentPageNum=' . $previous;
            $nextPageLink = 'board_list.php?searchOp=' . $option . '&searchKeyword=' .
                $keyword . '&currentPageNum=' . $nextPage;
        }
        // 검색모드 아닐경우 이전 다음 버튼 페이지 링크 설정
        else {
            $previousLink = 'board_list.php?currentPageNum=' . $previous;
            $nextPageLink = 'board_list.php?currentPageNum=' . $nextPage;
        }

        // <-- 처음, 이전 버튼 출력
        // 1페이지 일경우는 처음 버튼 출력 X
        if ($currentPage != 1)
            echo "<input type='button' class='btn btn-info' value='처음' onclick=location.href='$firstPageLink' >";
        // 페이지셋이 1 페이지 셋일 경우 이전 버튼 출력 X
        if($pageSet != 1)
            echo "<input type='button' class='btn btn-default' value='<' onclick=location.href='$previousLink' >";

        echo "<ul class='pagination'>";

        // <-- 페이지 네이션 출력

        for ($page = $setStart; $page <= $setEnd; $page++) {
            // 검색 모드일 경우 페이지 링크설정
            if ($searchMode == true)
                $link = 'board_list.php?searchOp=' . $option . '&searchKeyword=' .
                    $keyword . '&currentPageNum=' . $page;
            else
                $link = 'board_list.php?currentPageNum=' . $page;

            // 현재 페이지일 경우는 현재페이지로의 이동 X 다른 페이지로만 이동
            if ($page != $currentPage)
                echo "<li><a href=$link>$page</a></li>";
            else
                echo "<li><a id='currentPage'>$page</a></li>";
        }
        echo "</ul>";

        // <-- 이전, 다음 버튼 출력
        // 현재 페이지 셋이 마지막 페이지 셋과 아닐 경우 다음 버튼 출력
        if($allPage <= 10 || $allPageSet != $pageSet)
            echo "<input type='button' class='btn btn-default' value='>' onclick=location.href='$nextPageLink' >";
        // 현재 페이지가 마지막 페이지가 아닐경우만 마지막 버튼 출력
        if ($currentPage != $allPage)
            echo "<input type='button' class='btn btn-info' value='마지막' onclick=location.href='$lastPageLink' >";

        ?>
    </div>

    <div id="searchBox">
        <form action="board_list.php" method="get">
            <select id="searchOp" name="searchOp" class="form-control">
                <option value="subject">제목</option>
                <option value="contents">내용</option>
                <option value="subjectContents">제목+내용</option>
            </select>
            <?
            if ($searchMode == true)
                echo "<input type='text' id='searchText' value='$keyword' class='form-control' name='searchKeyword'>";
            else
                echo "<input type='text' id='searchText' class='form-control' name='searchKeyword'>";
            ?>
            <input type="submit" id="searchBt" class='btn btn-info' value="검색">
        </form>
    </div>
    <div id="menuBox">
        <?
        if (isset($_SESSION['user_id']))
            echo "<input type='button' data-toggle='modal' data-target='#writeTemplate' class='btn btn-default pull-left' value='글쓰기';>"
        ?>
        <input type="button" class='btn btn-default pull-left' value="전체글 보기"
               onclick="location.href='board_list.php?';">

    </div>
</div>

<!-- 글쓰기 모달 창 -->
<div class="modal fade" id="writeTemplate" role="dialog">
    <div class="modal-dialog">
       <!-- 모달 글쓰기 템플릿 -->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3 class="modal-title">글쓰기</h3>
        </div>
        <div class="modal-body">
            <form action="write.php" method="post" id="writeForm">
                <table id="editTable">
                    <tr>
                        <td>제목 : </td>
                        <td><input type="text" class='form-control' name="subject" id="subject"></td>
                    </tr>
                    <tr>
                        <td>작성자 : &nbsp;</td>
                        <?
                        // 작성자
                         $writer = $_SESSION['user_alias'];
                        echo "<td><input type='text' name='writer' value='$writer' id='writer' readonly></td>"
                        ?>
                    </tr>
                    <tr>
                        <td>내용 : </td>
                        <td><textarea name="contents" class='form-control' rows="10" id="text"></textarea></td>
                    </tr>
                </table>
            </div>

    <div class="modal-footer">
        <input type="button" id="write" value="작성하기" class='btn btn-info' onclick="articleCheck()">
        <input type="button" id="cancel" value="취소" class='btn btn-default' data-dismiss="modal">
        </form>
    </div>
    </div>
    </div>
</div>
<!-- 회원가입 모달 창 -->
<div class="modal fade" id="memberTemplate" role="dialog">
    <div class="modal-dialog">
    <!-- 모달 회원가입 템플릿 -->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3 class="modal-title">회원가입</h3>
        </div>
        <div class="modal-body">
            <form action="newMember.php" method="post" id="memberForm">
                <table id="memberTable">
                    <?
                    echo "<tr>";
                    echo "<td>아이디 : </td>";
                    echo "<td><input type='text' class='form-control' name='userId' id='userId'></td>";
                    echo "<td><input type='button' id='check' value='중복확인' class='btn btn-info' onclick='httpRequest()'></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td>비밀번호 : &nbsp;</td>";
                    echo "<td><input type='password' class='form-control' name='userPw' id='userPw'></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td>비밀번호 확인 : &nbsp;</td>";
                    echo "<td><input type='password' class='form-control' name='userPwCheck' id='userPwCheck'></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td>닉네임 : </td>";
                    echo "<td><input type='text' class='form-control' name='userAlias' id='userAlias'></td>";
                    echo "</tr>"
                    ?>
                </table>
        </div>

        <div class="modal-footer">
            <input type="button" id='write' value="가입하기" class='btn btn-info' onclick="memberCheck()">
            <input type="button" id='cancel' value="취소" class='btn btn-default' data-dismiss="modal">
            </form>
        </div>
    </div>
</div>
</div>
</body>
<script>

    function articleCheck() {

        var subjectObj = document.getElementById("subject");
        var writer = document.getElementById("writer");
        var formObj = document.getElementById("writeForm");

        if(subjectObj.value == "")
            alert("제목을 입력하세요!");
        else if(writer.value == "")
            alert("작성자 이름을 입력하세요!");
        else
            formObj.submit();

    }


</script>
</html>


