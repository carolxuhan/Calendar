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


if($_SESSION['token'] !== $_POST['token']){
    die("Request forgery detected");
}

$userid = $_SESSION['id'];
$eventid = $_POST['eventid'];
$date = $_POST['date'];
$title = $_POST['title'];
$content = $_POST['content'];
$type = $_POST['type'];
$start = $_POST['start'];
$end = $_POST['end'];
$token = $_POST['token'];

// if($_SESSION['token'])

list($year, $month, $day) = split ('[/.-]', $date);

//echo json_encode(array('success' => false, 'message'=>$year));

$stmt = $mysqli->prepare("update events set title=?, discription=?, month=?, year=?, day=?, time1=?, time2=?, type=? where userid = ? and id = ?");
if(!$stmt){
    $err = "Query Prep Failed: %s\n".$mysqli->error.".";
}
else{
	//Insert the story
    $stmt->bind_param('ssssssssii', $title, $content, $month, $year, $day, $start, $end, $type, $userid, $eventid);
    $stmt->execute();
    $stmt->close();
    //header("Location: index5.html");
    echo json_encode(array('success' => true));
	exit;
}
echo json_encode(array('success' => false, 'message'=>$err));
?>