<?php
header("Content-Type: application/json");

require 'database5.php';
ini_set("session.cookie_httponly", 1);
session_start();


$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
    die("Session hijack detected");
}else{
    $_SESSION['useragent'] = $current_ua;
}

$userid = $_SESSION['id'];
$eventid = $_POST['id'];
$token = $_SESSION['token'];

$stmt = $mysqli->prepare("select title, discription, year, month, day, time1, time2, type from events where userid=? and id=?");
//$stmt = $mysqli->prepare("select id from events where userid=?");
if(!$stmt){
    $err = "Query Prep Failed: %s\n".$mysqli->error.".";
    echo json_encode(array('success' => false, 'error' => $err));
}else{
	$stmt->bind_param('ii', $userid, $eventid);
    $stmt->execute();
    $stmt->bind_result($title, $description, $year, $month, $day, $start, $end, $type);

    $stmt->fetch();

    $stmt->close();

    $date_com = "$year-$month-$day";
    echo json_encode(array('success' => true, 'eventid' => $eventid, 'title' => $title, 'description' => $description, 'date' => $date_com, 'start' => $start, 'end' => $end, 'type' => $type, 'token' => $token));
}

?>