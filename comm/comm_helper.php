<?php

function print_result($code,$data = array(),$msg = ''){
	$result = array();
	
	if($code == 'A00000'){
		$result['code']  = $code;
		$result['data']  = $data;
		
		if($msg){
			$result['msg']   = $msg;
		}
		
	}else{
		include_once ROOTPATH.'./config/error_mine.php';
		
		if(!isset($error_mine[$code])){
			$code = 'A00001';
		}
	
		$result['code'] = $code;
		$result['msg']  = $error_mine[$code];		
		
	}
	
	if($result){
		$result  = array_urlencode($result);
		
	}
	
	echo urldecode ( json_encode($result));
	exit;
}


function array_urlencode($array){
	if(is_string($array)){
		return urlencode($array);
	}
	
	if(is_array($array)){
		foreach($array as $key => $value){
			$array[$key] = array_urlencode($value);	
		}		
	}
		
	return $array;
}




function remove_xss($val) {  
   
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);  
 
 
   $search = 'abcdefghijklmnopqrstuvwxyz'; 
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';  
   $search .= '1234567890!@#$%^&*()'; 
   $search .= '~`";:?+/={}[]-_|\'\\'; 
   for ($i = 0; $i < strlen($search); $i++) { 
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);     
      $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); 
   } 

   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 
   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 
   $ra = array_merge($ra1, $ra2); 
 
   $found = true; 
   while ($found == true) { 
      $val_before = $val; 
      for ($i = 0; $i < sizeof($ra); $i++) { 
         $pattern = '/'; 
         for ($j = 0; $j < strlen($ra[$i]); $j++) { 
            if ($j > 0) { 
               $pattern .= '(';  
               $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
               $pattern .= '|';  
               $pattern .= '|(�{0,8}([9|10|13]);)'; 
               $pattern .= ')*'; 
            } 
            $pattern .= $ra[$i][$j]; 
         } 
         $pattern .= '/i';  
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
         $val = preg_replace($pattern, $replacement, $val); 
         if ($val_before == $val) {   
            $found = false;  
         }  
      }  
   }  
   return $val;  
}



function strcount($str){
	$tmp = @iconv('gbk', 'utf-8', $str);
	if(!empty($tmp)){
		$str = $tmp;
	}
	preg_match_all('/./us', $str, $match);
	return count($match[0]);
}


function getDB($database){
	static $db = null;
	if($db === null && file_exists(ROOTPATH."./config/database.php")){
		include_once ROOTPATH."./config/database.php";
		include_once ROOTPATH."./comm/mysql_class.php";
		$db = new mysql_class(@$config['mysql'][$database]);
		if(!$db->issuccess()){
			$db = null;
		}
	}
	return $db;
}


function _decrypt($encryptText,$key = null){
	$cryptText = base64_decode($encryptText);
	$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
	$decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
	return unserialize(trim($decryptText));

}


function _encrypt($plain_array,$key = null){
	if(!is_array($plain_array)){
		$plain_array = array($plain_array);	
	}
	
	$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
	$encryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, serialize($plain_array), MCRYPT_MODE_ECB, $iv);
	return trim(base64_encode($encryptText));

}

function checksign($params){
	if(!isset($params['sign'])){
		return false;
	}
	
	$sign_array = $params;
	unset($sign_array['sign']);
	ksort($sign_array);

	$sign_string = '';
	foreach($sign_array as $key=>$value){
		$sign_string .=$value;
	}

	return md5(COSSIGN.$sign_string) == $params['sign'];
}
?>