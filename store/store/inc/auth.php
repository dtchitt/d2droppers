<?php

@session_start();

function checkAuth($reqAuth = false, $reqAdmin = false) {
	global $mysqli;
	if(strlen($_POST['login_email']) > 0 && strlen($_POST['login_password']) > 0) {
		$le = mysqli_real_escape_string($mysqli, $_POST['login_email']);
		$lp = mysqli_real_escape_string($mysqli, $_POST['login_password']);
		
		$findUserSQL = "SELECT * FROM d2_cart_users WHERE cu_email = '" . $le . "' LIMIT 0,1";
		if (!$result = $mysqli->query($findUserSQL)) {
			header("Location: /login.php");
			exit;
		}
		if ($result->num_rows === 0) {
			header("Location: /login.php");
			exit;
		} else {
			$user = $result->fetch_assoc();
			if($user['cu_pass'] === md5($lp)) {
				setcookie("GSCL", base64_encode(json_encode([base64_encode($user['cu_email']), md5($user['cu_pass'])])), time()+((3600 * 24) * 7));
				$_SESSION['user'] = $user;
				return true;
			} else {
				return false;
			}
		}
	}
	
	if(strlen($_COOKIE['GSCL']) > 0) {
		$cookieInfo = base64_decode($_COOKIE['GSCL']);
		$cookieInfo = json_decode($cookieInfo);
		
		$cookieUser = mysqli_real_escape_string($mysqli, base64_decode($cookieInfo[0]));
		$cookiePass = $cookieInfo[1];
		
		$sql = "SELECT * FROM d2_cart_users WHERE cu_email = '" . $cookieUser . "'";
		if (!$result = $mysqli->query($sql)) {
			header("Location: /login.php");
			exit;
		}
		if ($result->num_rows === 0) {
			header("Location: /login.php");
			exit;
		} else {
			$user = $result->fetch_assoc();
			if(md5($user['cu_pass']) === $cookiePass) {
				setcookie("GSCL", base64_encode(json_encode([base64_encode($user['cu_email']), md5($user['cu_pass'])])), time()+((3600 * 24) * 7));
				$_SESSION['user'] = $user;
				return true;
			} else {
				return false;
			}
		}
	}
	
	if(strlen($_SESSION['user']['cu_email']) > 0 && strlen($_SESSION['user']['cu_pass']) > 0) {
		$sql = "SELECT * FROM d2_cart_users WHERE cu_email = '" . mysqli_real_escape_string($mysqli, $_SESSION['user']['cu_email']) . "'";
		if (!$result = $mysqli->query($sql)) {
			header("Location: /login.php");
			exit;
		}
		if ($result->num_rows === 0) {
			header("Location: /login.php");
			exit;
		} else {
			$user = $result->fetch_assoc();
			if($_SESSION['user']['cu_pass'] === $user['cu_pass']) {
				setcookie("GSCL", base64_encode(json_encode([base64_encode($user['cu_email']), md5($user['cu_pass'])])), time()+((3600 * 24) * 7));
				$_SESSION['user'] = $user;
				return true;
			} else {
				header("Location: /login.php");
				exit;
			}
		}
	}
}