<?php
include "../bootstrap.php";

if(!isset($params['email']) || !isset($params['password']) || !isset($params['nickname'])){
	print_result('A00003');
}


if (!$params['email'] || !preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/",$params['email'])){
	print_result('A00004');
}

$strlen = strcount($params['password']);
if ($strlen < 6){
	print_result('A00005');
}

$strlen = strcount($params['nickname']);
if ($strlen == 0 || $strlen > 6){
	print_result('A00006');
}


if(!$db = getDB('user')){
	print_result('A00002');
	
}

$resouce = $db->query("SELECT * FROM user WHERE email=:email",array('email'=>$params['email']));
if($db->num_rows($resouce) > 0){
	print_result('A00007');
	
}

$resouce = $db->query("SELECT * FROM user WHERE nickname=:nickname",array('nickname'=>$params['nickname']));
if($db->num_rows($resouce) > 0){
	print_result('A00008');
	
}

$token = strtoupper(md5(COSSIGN.$params['email'].md5($params['password'])));

$resouce = $db->query("INSERT INTO user (email,nickname,password,token,createtime) VAlUES(:email,:nickname,:password,:token,:createtime)",
					  array(
							'email'=>$params['email'],
							'nickname'=>$params['nickname'],
							'password'=>md5($params['password']),
							'token'=>$token,
							'createtime'=>date('Y-m-d h:i:s',time()))
			);
if($db->get_insertid() > 0){
	print_result('A00000',array('P00001'=>$token));
	
}

print_result('A00001');
