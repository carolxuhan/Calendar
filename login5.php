<?php
    header("Content-Type: application/json"); 

	require 'database5.php';

	//Get the login information
	$user = $_POST["user"];
    $pwd_guess = $_POST["password"];
    $err = "";

	
	//Check if this user is valid.
	//If username is empty, that is, user submits without inputting anything, record error.
	if($user == ""){
		$err = "*Username cannot be empty.";
	}
    else if($pwd_guess == ""){
        $err = "*Password cannot be empty.";
    }
	else{
		//Connect the database
        $stmt = $mysqli->prepare("select password, id from users where username=?");
        if(!$stmt){
            $err = "Query Prep Failed: %s\n".$mysqli->error.".";
        }
        else{
            $stmt->bind_param('s', $user);
            $stmt->execute();
            $stmt->bind_result($pwd_hash, $userid);
            $stmt->fetch();
            if($pwd_hash == ""){
                $err = "*No such user.";
            }
            else{
                if(crypt($pwd_guess, $pwd_hash)==$pwd_hash){
                    ini_set("session.cookie_httponly", 1);
                    session_start();

                    $previous_ua = @$_SESSION['useragent'];
                    $current_ua = $_SERVER['HTTP_USER_AGENT'];

                    if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
                        die("Session hijack detected");
                    }else{
                        $_SESSION['useragent'] = $current_ua;
                    }

                    $_SESSION['user'] = $user;
                    $_SESSION['id'] = $userid;
					$_SESSION['token'] = substr(md5(rand()), 0, 10);
					echo json_encode(array('success' => true, 'username' => $user, 'token' => $_SESSION['token']));
                    //header("Location: index2.php");
					exit;
                }
                else{
                    $err = "*Illegal user.";
                }
            }
            $stmt->close();
        }
	}
	
	echo json_encode(array('success' => false, 'msg' => $err));
	exit;
?>