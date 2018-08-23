<?php

// <-- 대구 날씨
$url = "http://www.kma.go.kr/wid/queryDFS.jsp?gridx=89&gridy=90";
$result = simplexml_load_file($url);

$results = $result->body;


foreach($results->data as $item)
{
        $temp=$item->temp; //현재온도
        $maxTemp = $item->tmx; // 최고기온
        $minTEmp = $item->tmn; // 최저기온
        $sky=$item->wfKor; //날씨상태

}

echo $temp;


?>
<html>
<body>
</body>
</html>