<?php

function checkRealm() {
	if($_GET['sr'] == "east") {
		setcookie("GSD2R", base64_encode("east"), time()+((3600 * 24) * 7));
		$_SESSION['realm'] = "east";
	} elseif ($_GET['sr'] == "west") {
		setcookie("GSD2R", base64_encode("west"), time()+((3600 * 24) * 7));
		$_SESSION['realm'] = "west";
	}
	
	if(strlen($_SESSION['realm']) === 0) {
		if(strlen($_COOKIE['GSD2R']) > 0 && (base64_decode($_COOKIE['GSD2R']) == "east" || base64_decode($_COOKIE['GSD2R']) == "west")) {
			setcookie("GSD2R", $_COOKIE['GSD2R'], time()+((3600 * 24) * 7));
			$_SESSION['realm'] = base64_decode($_COOKIE['GSD2R']);
		} else {
			setcookie("GSD2R", base64_encode("east"), time()+((3600 * 24) * 7));
			$_SESSION['realm'] = "east";
		}
	}
	
	if($_SESSION['realm'] == "east")
		$_SESSION['prettyRealm'] = "East Softcore Ladder Expansion";
	elseif($_SESSION['realm'] == "west")
		$_SESSION['prettyRealm'] = "West Softcore Ladder Expansion";
	
	if(strlen($_GET['sr']) > 0) {
		header("Location: " . $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0]);
		exit;
	}
}