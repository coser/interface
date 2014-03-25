<?php
include "../bootstrap.php";

if(!isset($params['P00001']) ){
	print_result('A00003');
}

if(!$db = getDB('user')){
	print_result('A00002');
	
}

$resouce = $db->query("SELECT * FROM user WHERE token=:token",array('token'=>$params['P00001']));

if($db->num_rows($resouce) > 0){
	print_result('A00000',array('userinfo'=>$db->getRow($resouce)));
	
}

print_result('A00010');