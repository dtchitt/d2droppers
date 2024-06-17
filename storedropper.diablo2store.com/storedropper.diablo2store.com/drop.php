<?php
	require 'config.php';
	global $authorized;
	require_once 'functions.php';
	checkUserAuth(false, true);
	
	if($currUser === "demo") {
		die("Dropping items for demo account is disabled.");
	}

	if (!empty($_POST['info'])) {
		if(isDropper()) {
			if (count($authorized[$currUser]) == 0) {
				die("No droppers assigned to your account!");
			}
			
			$logSaleData = []; //json_encode
			$logSaleData['items'] = [];
			
			$items = json_decode($_POST['info']);
			
			if (empty($items)) {
				$items = json_decode(stripslashes($_POST['info']));  //fix in case of escape slashes
			}
			
			
			$final = array();
			$splitJob = array();
			$active = 0;
			if (file_exists('users/lastused_'.$currUser.'.txt')) {
				$active = intval(file_get_contents('users/lastused_'.$currUser.'.txt'));
			}
			if (!$active) {
				$active = 0;
			}
			
			$count_items = 0;
			$count_fg = intval($_POST['fg']);
			$logSaleData['amount'] = $count_fg;
			$gameName = $_POST['game'];
			$logSaleData['gameName'] = $gameName;
			$gamePass = $_POST['pass'];
			$logSaleData['gamePass'] = $gamePass;
			
			$nName = "";
			
			$itemList = [];
			foreach ($items as $entry) { 
				$tItem = [];
				$temp = json_decode($entry);
				$temp = (array)$temp;
				$itemList[] = getItemName($temp['itemID']);
				$temp['requester'] = $currUser;
				$temp['gameName'] = $gameName;
				$temp['gamePass'] = $gamePass;
				$temp['password'] = getPass($temp['realm'], $temp['account']);
				$temp['fgvalue'] = $count_fg;
				$tItem['requester'] = $currUser;
				$tItem['realm'] = $temp['realm'];
				$tItem['itemID'] = $temp['itemID'];
				$logSaleData['items'][] = $tItem;
				if(!isset($splitJob[$temp['account']])){
					if($active >= count($authorized[$currUser])) {
						$active = 0;
					}
					$splitJob[$temp['account']] = $authorized[$currUser][$active];
					$active++;
				}
				$temp['whoWork'] = $splitJob[$temp['account']];
				
				$finish = json_encode($temp);
				
				if (!isset($final[$temp['whoWork']])) {
					$final[$temp['whoWork']] = array();
				}
				
				array_push($final[$temp['whoWork']], $finish);
				
				$count_items++;
			}
			
			$myfile = fopen('users/lastused_'.$currUser.'.txt', "w+");
			fwrite($myfile, $active);
			fclose($myfile);
			
			foreach ($final as $who => $what) {
				$savestring = implode("\n", $what);
				$fname = "../d2StoreDrops/" . $_SESSION['storeID'] . "drop_".$who.".json";
				$file = fopen($fname, 'a');
				fwrite($file, $savestring."\n");
				fclose($file);
			}
			
			$il = implode("|", $itemList);
			logSales($currUser, $count_fg, $count_items, $il);
			echo "Scheduled Item Drop [Count: " . $count_items . "] [Game: " . $gameName . "/" . $gamePass . "] [Amount: " . $count_fg . "].";
		
		} else {
			echo "You do not have permission to drop items.";
		}
	} else {
		echo "You must select a item to drop.";
	}
	
	function getItemName($itemID) {
		try {
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
			$query = /** @lang text */
				'SELECT itemName FROM muleItems WHERE itemId = "'.$itemID.'"';
			$results = $conn->query($query);
			$count = $results->fetchAll(PDO::FETCH_ASSOC);
			return @$count[0]["itemName"];
		} catch(PDOException $e) {
			$conn = NULL;
			return false;
		}
		
	}

	function getPass($realm, $acc) {
		try {			
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
			$realmnames	= array("uswest", "useast", "asia", "europe");
			$key = array_search($realm, $realmnames);
			
			$query = /** @lang text */
				'SELECT accountPasswd FROM muleAccounts WHERE accountLogin = "'.$acc.'" AND accountRealm = '.$key;

			$results = $conn->query($query);
			
			//$conn = NULL;
			
			$count = $results->fetchAll(PDO::FETCH_ASSOC);
			
			return @$count[0]["accountPasswd"];
			
		} catch(PDOException $e) {
			$conn = NULL;
			return false;
		}
	}

	function MakePickitLine($id) {
		try {
            $lineB = array();
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");

    		$query = 'SELECT * FROM muleItemsStats WHERE statsItemId == '.$id;
			$results = $conn->query($query);
			$conn = NULL;
			$stats = $results->fetchAll(PDO::FETCH_ASSOC);
            foreach ($stats as $stat) {
                $pickL = "[".$stat['statsName']."] == ".$stat['statsValue'];
                //print $pickL."<BR>";
                array_push($lineB, $pickL);
            }
            $pickit = implode(" && ", $lineB);
			return $pickit;

		} catch(PDOException $e) {
			$conn = NULL;
			return false;
		}
	}

    function MakeItemEval($id) {
		try {
            $lineA = array();
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");

            $query = 'SELECT * FROM muleItems WHERE itemId == '.$id;
            $results = $conn->query($query);
            $first = $results->fetchAll(PDO::FETCH_ASSOC);
            foreach ($first as $info) {
                $str = "items[i].getFlags() == ".$info['itemFlag'];
                array_push($lineA, $str);
                $str = "items[i].classid == ".$info['itemClassid'];
                array_push($lineA, $str);
                $str = "items[i].quality == ".$info['itemQuality'];
                array_push($lineA, $str);
                $str = "ItemDB.getImage(items[i]) == '".$info['itemImage']."'";
                array_push($lineA, $str);
            }

            $query = 'SELECT * FROM muleItemsStats WHERE statsItemId == '.$id;
            $results = $conn->query($query);
            $conn = NULL;
            $stats = $results->fetchAll(PDO::FETCH_ASSOC);
            foreach ($stats as $stat) {
				if (is_numeric($stat['statsValue'])) { // some values are objects or undefined
                    $pickL = "items[i].getStatEx(NTIPAliasStat['".$stat['statsName']."']) == ".$stat['statsValue'];
                    array_push($lineA, $pickL);
                }
            }

            $pickit = implode(" && ", $lineA);
			return $pickit;

		} catch(PDOException $e) {
			$conn = NULL;
			return false;
		}
	}
	
	function logSales($who, $value, $count, $items) {
		global $mysqli;
		
		$sq = "INSERT INTO salelog (sl_by, sl_amount, sl_item, sl_items) VALUES ('" . $who . "', '" . $value . "', '" . $count . "', '" . $items . "')";
		$mysqli->query($sq);
		
		// logs fg value declared by seller
		$filename = "users/_FG_$who.log";
		$ammount = 0;
		if (file_exists($filename)) {
			$ammount = intval(file_get_contents($filename));
		}
		if (!$ammount) {
			$ammount = 0;
		}
		$ammount += $value;
		$myfile = fopen($filename, "w+");
		fwrite($myfile, $ammount);
		fclose($myfile);
		
		// logs item quantity requested by seller
		$filename = "users/_DROP_$who.log";
		$ammount = 0;
		if (file_exists($filename)) {
			$ammount = intval(file_get_contents($filename));
		}
		if (!$ammount) {
			$ammount = 0;
		}
		$ammount += $count;
		$myfile = fopen($filename, "w+");
		fwrite($myfile, $ammount);
		fclose($myfile);
	}
?>