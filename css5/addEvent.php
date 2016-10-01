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

	if($_SESSION['token'] !== $_POST['token']){
		die("Request forgery detected");
	}

	$err = "";
	$title = $_POST['title'];
	$content = $_POST['content'];
	$date = $_POST['date'];
	// $year = $_POST['year'];
	// $month = $_POST['month'];
	// $day = $_POST['day'];
	$start = $_POST['start'];
	$end = $_POST['end'];
	$type = $_POST['type'];
	$userid = $_SESSION['id'];
	//$err = "Event title cannot be empty.";

	if(empty($title)){
		$err = "Event title cannot be empty.";
	}elseif (empty($content)) {
		$err = "Event content cannot be empty.";# code...
	}elseif (empty($date)) {
		$err = "Event date cannot be empty.";# code...
	}elseif (empty($start)||empty($end)) {
		$err = "Event time cannot be empty.";# code...
	}else{
		list($year, $month, $day) = split ('[/.-]', $date);

	    $stmt = $mysqli->prepare("insert into events (userid, title, discription, month, day, year, time1, time2, type) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	    if(!$stmt){
	        $err = "Query Prep Failed: %s\n".$mysqli->error.".";
	    }
	    else{
	        //Insert the story
	        $stmt->bind_param('sssssssss', $userid, $title, $content, $month, $day, $year, $start, $end, $type);
	        $stmt->execute();
	        $stmt->close();
	        //header("Location: index5.html");
	        echo json_encode(array('success' => true, 'title' => $title, 'date' => $date, 'time1' => $start, 'time2' => $end));
			exit;
	    }
	}
	echo json_encode(array('success' => false, 'message' => $err));

	// echo json_encode(array(
	// 	"success" => false,
	// 	"message" => $title
	// ));
	exit;
?>