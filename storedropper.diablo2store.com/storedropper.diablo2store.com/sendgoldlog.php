<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

$storeID = $_GET['i'];
$storeKey = $_GET['k'];

$fromName = $_GET['f'];
$fromAmnt = $_GET['a'];

$realm = $_GET['r'];
$itemCode = $_GET['ic'];
$gameName = $_GET['gn'];
$gamePass = $_GET['gp'];

//
//

$sRealm = 1; //East
$sHarcore = 0;
$sLadder = 1;
$sExp = 1;

if($realm == "ESCL" || $realm == "escl") {
	$sRealm = 1;
	$sHarcore = 0;
	$sLadder = 1;
	$sExp = 1;
}
elseif($realm == "ESC" || $realm == "esc") {
	$sRealm = 1;
	$sHarcore = 0;
	$sLadder = 0;
	$sExp = 1;
}
elseif($realm == "EHCL" || $realm == "ehcl") {
	$sRealm = 1;
	$sHarcore = 1;
	$sLadder = 1;
	$sExp = 1;
}
elseif($realm == "EHC" || $realm == "ehc") {
	$sRealm = 1;
	$sHarcore = 1;
	$sLadder = 0;
	$sExp = 1;
}

//
//

$storeQ = $mysqli->query("SELECT * FROM stores WHERE sid='" . $mysqli->real_escape_string($storeID) . "' AND sapikey='" . $mysqli->real_escape_string($storeKey) . "' LIMIT 0,1");
if(!$storeQ) {
	echo "Mysql Error";
	$cem = "Error Selecting Store!";
	$mysqli->query("INSERT INTO auto_errors SET ae_group='" . $mysqli->real_escape_string(serialize($store)) . "', ae_item='" . $mysqli->real_escape_string(serialize($ig)) . "', ae_store_id='" . $mysqli->real_escape_string($storeID) . "', ae_store_key='" . $mysqli->real_escape_string($storeKey) . "', ae_from='" . $mysqli->real_escape_string($fromName) . "', ae_amount='" . $mysqli->real_escape_string($fromAmnt) . "', ae_realm='" . $mysqli->real_escape_string($realm) . "', ae_item_code='" . $mysqli->real_escape_string($itemCode) . "', ae_game='" . $mysqli->real_escape_string($gameName."//".$gamePass) . "', ae_delt='0', ae_msg='" . $mysqli->real_escape_string($cem) . "'") or die("Error 5");
	echo $cem;
	exit;
}
$store = $storeQ->fetch_assoc();

//
//

$igQ = $mysqli->query("SELECT * FROM item_groups WHERE ig_short='" . $mysqli->real_escape_string(strtolower($itemCode)) . "' LIMIT 0,1");
if(!$igQ) {
	echo "Mysql Error2";
	$cem = "Error Selecting Item Group!";
	$mysqli->query("INSERT INTO auto_errors SET ae_group='" . $mysqli->real_escape_string(serialize($store)) . "', ae_item='" . $mysqli->real_escape_string(serialize($ig)) . "', ae_store_id='" . $mysqli->real_escape_string($storeID) . "', ae_store_key='" . $mysqli->real_escape_string($storeKey) . "', ae_from='" . $mysqli->real_escape_string($fromName) . "', ae_amount='" . $mysqli->real_escape_string($fromAmnt) . "', ae_realm='" . $mysqli->real_escape_string($realm) . "', ae_item_code='" . $mysqli->real_escape_string($itemCode) . "', ae_game='" . $mysqli->real_escape_string($gameName."//".$gamePass) . "', ae_delt='0', ae_msg='" . $mysqli->real_escape_string($cem) . "'") or die("Error 6");
	echo $cem;
	exit;
}
$ig = $igQ->fetch_assoc();

$foundItem = [];

//
//

$numSold = 1;
if($fromAmnt < $ig['ig_price_fg'])
{
	$cem = "Not enough FG [" . $fromAmnt . "/" . $ig['ig_price_fg'] . "] sent!";
	$mysqli->query("INSERT INTO auto_errors SET ae_group='" . $mysqli->real_escape_string(serialize($store)) . "', ae_item='" . $mysqli->real_escape_string(serialize($ig)) . "', ae_store_id='" . $mysqli->real_escape_string($storeID) . "', ae_store_key='" . $mysqli->real_escape_string($storeKey) . "', ae_from='" . $mysqli->real_escape_string($fromName) . "', ae_amount='" . $mysqli->real_escape_string($fromAmnt) . "', ae_realm='" . $mysqli->real_escape_string($realm) . "', ae_item_code='" . $mysqli->real_escape_string($itemCode) . "', ae_game='" . $mysqli->real_escape_string($gameName."//".$gamePass) . "', ae_delt='0', ae_msg='" . $mysqli->real_escape_string($cem) . "'") or die("Error 7 " . mysqli_error($mysqli));
	echo $cem;
	exit;
}
	
$nsMath = floor($fromAmnt / $ig['ig_price_fg']);
if($nsMath > 0)
	$numSold = $nsMath;

//
//

if(file_exists('../d2storesItemDBs/'.$store['sid'].'ItemDB.s3db')) {
	$conn = new PDO('sqlite:../d2storesItemDBs/'.$store['sid'].'ItemDB.s3db') or die("Unable to connect");
	$tempA	= " AND charHardcore = ".$sHarcore;
	$tempB 	= " AND charExpansion = ".$sExp;
	$tempC 	= " AND charLadder = ".$sLadder;
	$sql = /** @lang text */
		'SELECT * FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountId WHERE accountRealm = '.$sRealm.' '.$tempA.' '.$tempB.' '.$tempC;
	$results = $conn->query($sql);
	if(!$results){
		print_r($conn->errorInfo());
		exit;
	}
	$itemsDB = $results->fetchAll(PDO::FETCH_ASSOC);
	if($ig['ig_item_simple'] == 1) {
		$f = 0;
		foreach($itemsDB as $item) {
			if(strtolower($item['itemName']) == strtolower($ig['ig_name'])){
				$foundItem[] = $item;
				$f++;
				if($f >= $nsMath)
					break;
			}
		}
	}
	else {
		$cem = "Advanced items not implemented! [Name: " . $ig['ig_name'] . "] [ID: " . $ig['ig_id'] . "]!";
		$mysqli->query("INSERT INTO auto_errors SET ae_group='" . $mysqli->real_escape_string(serialize($store)) . "', ae_item='" . $mysqli->real_escape_string(serialize($ig)) . "', ae_store_id='" . $mysqli->real_escape_string($storeID) . "', ae_store_key='" . $mysqli->real_escape_string($storeKey) . "', ae_from='" . $mysqli->real_escape_string($fromName) . "', ae_amount='" . $mysqli->real_escape_string($fromAmnt) . "', ae_realm='" . $mysqli->real_escape_string($realm) . "', ae_item_code='" . $mysqli->real_escape_string($itemCode) . "', ae_game='" . $mysqli->real_escape_string($gameName."//".$gamePass) . "', ae_delt='0', ae_msg='" . $mysqli->real_escape_string($cem) . "'") or die("Error 8");
		echo $cem;
		exit;
	}
	
	if($foundItem != null && count($foundItem) != $numSold) {
		$cem = "Error: Only " . count($foundItem) . " out of " . $numSold . " found [Name: " . $ig['ig_name'] . "] [ID: " . $ig['ig_id'] . "]!";
		$mysqli->query("INSERT INTO auto_errors SET ae_group='" . $mysqli->real_escape_string(serialize($store)) . "', ae_item='" . $mysqli->real_escape_string(serialize($ig)) . "', ae_store_id='" . $mysqli->real_escape_string($storeID) . "', ae_store_key='" . $mysqli->real_escape_string($storeKey) . "', ae_from='" . $mysqli->real_escape_string($fromName) . "', ae_amount='" . $mysqli->real_escape_string($fromAmnt) . "', ae_realm='" . $mysqli->real_escape_string($realm) . "', ae_item_code='" . $mysqli->real_escape_string($itemCode) . "', ae_game='" . $mysqli->real_escape_string($gameName."//".$gamePass) . "', ae_delt='0', ae_msg='" . $mysqli->real_escape_string($cem) . "'") or die("Error 9");
		echo $cem;
		exit;
	}
		
	echo "<pre>";
	var_dump($foundItem, true);
	echo "</pre><br><br>";
	
	$d = 1;
	if(file_exists("drop_Dropper1.json") && !file_exists("drop_Dropper2.json"))
		$d = 2;
	elseif(!file_exists("drop_Dropper1.json") && file_exists("drop_Dropper2.json"))
		$d = 1;
	else {
		$lastUsed = file_get_contents("lastauto.txt");
		if($lastUsed == 1) {
			$d = 2;
			file_put_contents("lastauto.txt", "2");
		}
		else {
			$d = 1;
			file_put_contents("lastauto.txt", "1");
		}
	}
	
	foreach($foundItem as $i) {
		$tdata = array();
		$tdata['dropProfile'] = '';
		$tdata['realm'] = 'useast';
		$tdata['account'] = $i['accountLogin'];
		$tdata['charName'] = $i['charName'];
		$tdata['itemType'] = $i['itemType'];
		$tdata['dropit'] = $i['itemMD5'];
		$tdata['skin'] = $i['itemImage'];
		$tdata['itemID'] = $i['itemId'];
		$tdata['requester'] = 'Auto Dropper';
		$tdata['gameName'] = $gameName;
		$tdata['gamePass'] = $gamePass;
		$tdata['password'] = $i['accountPasswd'];
		$tdata['fgvalue'] = $ig['ig_price_fg'];
		
		if ($d == 1) {
			$tdata['whoWork'] = 'Dropper1';
			$td = json_encode($tdata);
			file_put_contents("drop_Dropper1.json", $td."\n", FILE_APPEND);
			echo "Added job for dropper 1.<br>";
			$d = 2;
		} elseif ($d == 2) {
			$tdata['whoWork'] = 'Dropper2';
			$td = json_encode($tdata);
			file_put_contents("drop_Dropper2.json", $td."\n", FILE_APPEND);
			echo "Added job for dropper 2.<br>";
			$d = 1;
		}
	}
	
}
//file_put_contents("data2.txt", "i: " . $i . " - k: " . $k . " - l: " . $l . "\r\n", FILE_APPEND);