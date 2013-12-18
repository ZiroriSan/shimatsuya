<?php
session_start();
// Yahoo!アプリケーションID
// http://developer.yahoo.co.jp/webapi/jlp/kousei/v1/kousei.html
$yappid = "dj0zaiZpPTBnUFVQNjB0dTVLNyZkPVlXazlUR0ZxYTFkV05tc21jR285TUEtLSZzPWNvbnN1bWVyc2VjcmV0Jng9MmI-";

$text = $_POST["text"];

$url="http://jlp.yahooapis.jp/KouseiService/V1/kousei";
$params = array(
    'sentence' => $text
//    'no_filter' => 17
);

$ch = curl_init($url);
curl_setopt_array($ch, array(
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT      => "Yahoo AppID: $yappid",
    CURLOPT_POSTFIELDS     => http_build_query($params),
));
 
$result = curl_exec($ch);
curl_close($ch);

$xml = simplexml_load_string($result);
$hits = $xml->Result;

//テキストの改行箇所に<br>タグを入れて画面上も改行しているように見せる
$text = nl2br(h($text));
foreach ($hits as $hit) {
    $result = "<span id='example'>$hit->Surface</span>";    
    $text = str_replace($hit->Surface, $result, $text);
}

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>
body {
	padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
}
</style>
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<title>校正支援ツール</title>
<style type="text/css">
#example { color: red; }
</style>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container"><a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="icon-bar"></span><span class="icon-bar"></span> <span class="icon-bar"></span></a><a class="brand" href="top.php">校正支援ツール</a>
			<div class="nav-collapse collapse">
				<ul class="nav">
					<li><a href="top.php">ホーム</a></li>
				</ul>
			</div>
			<!--/.nav-collapse --> 
		</div>
	</div>
</div>


<div class="container">
	<div class="row">

<div class="span12">

<h2>校正支援結果表示</h2>

</div>

<div class="span6">

<?php echo $text; ?>
<?php echo "<br>※文字数(" . mb_strlen(strip_tags($text)) . ")"; ?>
    
</div>

<div class="span6">

<!-- 結果表示するためのテーブル作成 -->
<?php 
// カウント
$i = 0;
// 連想配列初期化
$arr = array();
// バリデーション

if (!strlen(trim(mb_convert_kana($text, "s"))) == 0 || !strlen($hits) == 0 && isset($hits)) {
    foreach($hits as $hit) {
        $name = $hit->Surface;
        if (!isset($arr["$name"])) {
        $arr["$name"] = array(
                        Surface => $hit->Surface,
                        ShitekiInfo => $hit->ShitekiInfo,
                        ShitekiWord => $hit->ShitekiWord
        );
        }
    }
    if (isset($arr["$name"])) {
        echo '
        <table class="table table-striped">
        <tbody>
        <tr>
        <th>番号</th>
        <th>該当箇所</th>
        <th>指摘の詳細情報</th>
        <th>言い換え候補文字列</th>
        </tr>';
    }
 ?>
<?php foreach ($arr as $hit) { ?>
<tr><?php $i++ ?>
<td><?php echo $i; ?></td>
<td><?php echo h($hit["Surface"]); ?></td>
<td><?php echo h($hit["ShitekiInfo"]); ?></td>
<td><?php echo h($hit["ShitekiWord"]); ?></td>
</tr>
<?php }
} else {
    $_SESSION['error'] = "※文章が入力されておりません";
    header("Location: top.php");
    exit();
}
?>
<?php 
//if($i >= 5){
//    echo "<p id='example'><b>間違いが多すぎます。自分でチェックする習慣をつけましょう！！</b></p>";
//} 
if($i == 0) { ?>
    <h2 style="color:green">問題ないケロ♪♪</h2>
    <p><img src="Costa Rican Frog.jpg" alt="ケロケロくん" width="280" height="280"></p>
<?php } ?>
</tbody>
</table>

</div>

<div class="span12">
<!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
<a href="http://developer.yahoo.co.jp/about">
<img src="http://i.yimg.jp/images/yjdn/yjdn_attbtn1_125_17.gif" title="Webサービス by Yahoo! JAPAN" alt="Web Services by Yahoo! JAPAN" width="125" height="17" border="0" style="margin:15px 15px 15px 15px"></a>
<!-- End Yahoo! JAPAN Web Services Attribution Snippet -->
</div>

	</div><!-- /row -->
</div><!-- /container --> 


</body>
</html>
