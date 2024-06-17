<?php

require "config.php";

if($_GET['c'] != "7007") exit;

if(strlen($_GET['k']) > 0) {
	$sql = "SELECT * FROM stores WHERE sapikey = '".$mysqli->real_escape_string($_GET['k'])."'";
	if (!$result = $mysqli->query($sql)) {
		echo "Failed";
		exit;
	}
	
	if ($result->num_rows === 0) {
		echo "Failed";
		exit;
	}
	
	$a = $result->fetch_assoc();
	
	if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["file"]["tmp_name"];
		$name = $_FILES["file"]["name"];
		move_uploaded_file($tmp_name, "../d2storesItemDBs/" . $a['sid'] . "ItemDB.s3db");
		echo "Upload Complete";
	}
}