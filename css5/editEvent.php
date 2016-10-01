<?php
header("Content-Type: application/json");

require 'database.php';

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
$eventid = $_POST['eventid'];
$date = $_POST['date'];
$title = $_POST['title'];
$content = $_POST['content'];
$type = $_POST['type'];
$start = $_POST['start'];
$end = $_POST['end'];
$token = $_POST['token'];

list($year, $month, $day) = split ('[/.-]', $date);

$stmt = $mysqli->prepare("UPDATE events SET title=?, discription=?, month=?, year=?, day=?, time1=?, time2=?, type=? WHERE $userid = ? and id = ?");
if(!$stmt){
    $err = "Query Prep Failed: %s\n".$mysqli->error.".";
}
else{
	//Insert the story
    $stmt->bind_param('ssssssss', $title, $content, $month, $year, $day, $start, $end, $type);
    $stmt->execute();
    $stmt->close();
    //header("Location: index5.html");
    echo json_encode(array('success' => true));
	exit;
}

?>