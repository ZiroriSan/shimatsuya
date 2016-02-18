<?php 
try {
// common.phpの読み込み
require_once "./common.php";


$tpl->display("top.tpl");
} catch (Exception $e) {
	exit($e->getMessage());
}
// hoge
return;
