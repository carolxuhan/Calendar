<?php
header("Content-Type: application/json"); 

ini_set("session.cookie_httponly", 1);
session_start();

$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];
 
if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
	die("Session hijack detected");
}else{
	$_SESSION['useragent'] = $current_ua;
}

if(isset($_SESSION['user'])){
	echo json_encode(array('success' => true, 'username' => $_SESSION['user']));
	exit;
}
else{
	echo json_encode(array('success' => false));
	exit;
}
//echo ("string");
?>