<?php

require_once 'config.php';
require_once 'functions.php';

$dropper = @$_GET['d'];
$id = @$_GET['i'];
$key = @$_GET['k'];

//DROPPER

if(strlen($dropper) < 1)
	die("Invalid request 1.");

$safeDropper = $mysqli->real_escape_string($dropper);

if($safeDropper != $dropper)
	die("Nope 1");

//STORE ID

if(strlen($id) < 1)
	die("Invalid request 2.");

$safeID = $mysqli->real_escape_string($id);

if($safeID != $id)
	die("Nope 2");

//STORE API KEY

if(strlen($key) < 1)
	die("Invalid request 3.");

$safeKey = $mysqli->real_escape_string($key);

if($safeKey != $key)
	die("Nope 3");

//all our data should be clean now, for sql useage
//if you add anything make sure you filter it like above to prevent SQL injection and deny people who try

$q = "SELECT * FROM stores WHERE sid='" . $safeID . "' AND sapikey='" . $safeKey . "' LIMIT 0,1";
$storeQ = $mysqli->query($q);

if(mysqli_num_rows($storeQ) < 1) {
	die("Invalid info");
}

$store = $storeQ->fetch_assoc();

if(!is_numeric($safeDropper)) {
	die("Nope 4");
}

if(file_exists("../d2StoreDrops/" . $store['sid'] . "drop_Dropper".$safeDropper.".json")) {
	echo file_get_contents("../d2StoreDrops/" . $store['sid'] . "drop_Dropper".$safeDropper.".json");
	unlink("../d2StoreDrops/" . $store['sid'] . "drop_Dropper".$safeDropper.".json");
}