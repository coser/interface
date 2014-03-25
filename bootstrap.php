<?php

error_reporting(E_ALL);
ini_set('display_errors','on');

date_default_timezone_set('Asia/Shanghai');
define('ROOTPATH', str_replace(pathinfo(__FILE__, PATHINFO_BASENAME), '', __FILE__));
define('COSSIGN', 'cos!@#$%^&');

include_once ROOTPATH."./comm/comm_helper.php";




$params = array_map('remove_xss',$_POST);

include_once ROOTPATH."./config/position_mine.php";

if(!isset($params['position'])  || !in_array($params['position'],$position_mine)){
	print_result('A00003');
	
}


if(!checksign($params)){
	print_result('A00009');
	
}


?>