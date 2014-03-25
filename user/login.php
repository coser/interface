<?php
include "../bootstrap.php";

if(!isset($params['email']) || !isset($params['password'])){
	print_result('A00003');
}

if(!$db = getDB('user')){
	print_result('A00002');
	
}

$resouce = $db->query("SELECT * FROM user WHERE email=:email and password=:password",array('email'=>$params['email'],'password'=>md5($params['password'])));

if($db->num_rows($resouce) > 0){
	print_result('A00000',array('userinfo'=>$db->getRow($resouce)));
	
}

print_result('A00010');