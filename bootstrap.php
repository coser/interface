<?php

error_reporting(E_ALL);
ini_set('display_errors','on');


date_default_timezone_set('Asia/Shanghai');
define('ROOTPATH', str_replace(pathinfo(__FILE__, PATHINFO_BASENAME), '', __FILE__));

//sign私钥
define('COSSIGN', 'cos!@#$%^&');  

//公共函数文件
include_once ROOTPATH."./comm/comm_helper.php";

$params = array_map('remove_xss',$_POST);

//position配置，position为了区别请求接口的位置，便于统计
include_once ROOTPATH."./config/position_mine.php";

if(!isset($params['position'])  || !in_array($params['position'],$position_mine)){
	print_result('A00003');
	
}


if(!checksign($params)){
	print_result('A00009');
	
}


?>