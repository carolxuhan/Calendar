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

if(isset($_SESSION['user'])){
	$userid = $_SESSION['id'];
	$token = $_SESSION['token'];
	// $year = $_GET['year'];
	// $month = $_GET['month'];

    // echo json_encode(array('success' => 1, 'event' => array("mm" => "mm", "apple" => 1)));
    // exit;

	$stmt = $mysqli->prepare("select id, title, discription, year, month, day, time1, time2, type from events where userid=?");
    //$stmt = $mysqli->prepare("select id from events where userid=?");
    if(!$stmt){
        $err = "Query Prep Failed: %s\n".$mysqli->error.".";
        $eventinfo = array('success' => -1, 'error' => $err);
    }
    else{
    	//$eventinfo = array('success' => 1);
        //Insert the story
        $stmt->bind_param('i', $userid);
        $stmt->execute();
        $stmt->bind_result($eventid, $title, $description, $year, $month, $day, $start, $end, $type);
        //$stmt->bind_result($eventid);

        $i = 0;
        $temp = null;
        $event = array();

        // $stmt->fetch();
        // // $event = $eventid;
        // $date_arr = $year+'-'+$month+'-'+$day;
        // $event = array('eventid' => $eventid, 'title' => $title, 'description' => $description, 'date' => $date_arr, 'start' => $start, 'end' => $end, 'type' => $type, 'token' => $token);

        $judge = 0;
        while($stmt->fetch()){
        	// if ($temp == $year+'-'+$month+'-'+$day) {
        	// 	$i = $i+1;# code...
        	// }
        	// else{
        	 //	$i = 0;
        	// }
            $judge  = 1;
        	//$date_arr = 'd'+$year+$month+$day;
            $year = intval($year);
            $month = intval($month);
            $day = intval($day);
            $date_com = "$year-$month-$day";
            $element = array('eventid' => $eventid, 'title' => $title, 'description' => $description, 'date' => $date_com, 'start' => $start, 'end' => $end, 'type' => $type, 'token' => $token);
        	//$date_arr = "aa";
            $event["ele$i"] = $element;
            $i = count($event);
            //$event[$date_arr] = array("ele$i" => $element);
            //$i = count($event[$date_arr]);
            //$event = array("ele$i" => $element);
            // $event["ele$i"] = $element;
            //$event = $element;
        	//$temp = $year+'-'+$month+'-'+$day;

        }

        $stmt->close();
        //$event = array("mm" => $eventid, "apple" => 1);
        $eventinfo = array('success' => 1, 'event' => $event, 'judge' => $judge);
        // $eventinfo =array(
        //     'success' => 1,
        //      'eventid' => $eventid,
        //      'title' => $title,
        //      'description' => $description,
        //      'date' => $date_arr,
        //      'start' => $start,
        //      'end' => $end,
        //      'type' => $type,
        //      'token' => $token
        //  );
        //header("Location: index5.html");
        // echo json_encode(array('success' => true, 'title' => $title, 'date' => $date, 'time1' => $start, 'time2' => $end));
		//exit;
    }
    
}else{
	$eventinfo = array('success' => 0);
}
echo json_encode($eventinfo);
exit;
//echo json_encode(array('success' => 1, 'event' => $userid));
?>