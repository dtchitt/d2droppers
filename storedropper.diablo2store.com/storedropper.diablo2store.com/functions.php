<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function _sqliteRegexp($string, $pattern) {
	//die($pattern);
    if(preg_match('/'.$pattern.'/', $string)) {
		//die("matched " . $pattern . " to " . $string);
        return true;
    }
    return false;
}

session_start();
require 'config.php';
require 'theme.php';
// define global variables
$currUser 	= null;//$_SERVER['PHP_AUTH_USER'];
$realms		= array("West", "East", "Asia", "Euro");
$types		= array("SC", "HC");
$ladder		= array("Non-Ladder", "Ladder");
$exp		= array("Classic", "Expansion");
$random		= array("Amazon","Assassin","Barbarian","Charsi","DeckardCain","Druid","Flavie","Gheed","Kashya","KashyaRogue","Necromancer","Paladin","Sorceress","Warriv");
$showthat	= "";
$itemsDB	= array();
$charsIds	= array();
$version	= "1.01.03";

if ( isset($_GET["hc"]) AND ($_GET["hc"] > 1 OR !is_numeric($_GET["hc"])) ) { $_GET["hc"] = 1; }
if ( isset($_GET["ladder"]) AND ($_GET["ladder"] > 1 OR !is_numeric($_GET["ladder"])) ) { $_GET["ladder"] = 1; }
if ( isset($_GET["exp"]) AND ($_GET["exp"] > 1 OR !is_numeric($_GET["exp"])) ) { $_GET["exp"] = 1; }
if ( isset($_GET["realm"]) AND ($_GET["realm"] > 3 OR !is_numeric($_GET["realm"])) ) { $_GET["realm"] = 3; }

$inGameColor   = array("","","","black","lightblue","darkblue","crystalblue","lightred","darkred","crystalred","","darkgreen","crystalgreen","lightyellow","darkyellow","lightgold","darkgold","lightpurple","","orange","white");

// create empty db
if (!file_exists("../d2storesItemDBs/".@$_SESSION['storeID']."ItemDB.s3db")) {
	try {
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
		$data = [
			"PRAGMA main.page_size=4096;",
			"PRAGMA main.cache_size=10000;",
			"PRAGMA main.locking_mode=EXCLUSIVE;",
			"PRAGMA main.synchronous=NORMAL;",
			"PRAGMA main.journal_mode=WAL;",
			"PRAGMA main.temp_store = MEMORY;",
			"CREATE TABLE IF NOT EXISTS [muleAccounts] ([accountId] INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT, [accountRealm] INTEGER NULL, [accountLogin] VARCHAR(32) NULL, [accountPasswd] VARCHAR(32) NULL);",
			"CREATE TABLE IF NOT EXISTS [muleChars] ([charId] INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, [charAccountId] INTEGER NULL, [charName] VARCHAR(32) NULL, [charExpansion] BOOLEAN NULL, [charHardcore] BOOLEAN NULL, [charLadder] BOOLEAN NULL, [charClassId] INTEGER NULL);",
			"CREATE TABLE IF NOT EXISTS [muleItems] ([itemId] INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, [itemCharId] INTEGER NULL, [itemName] VARCHAR(64) NULL, [itemType] INTEGER NULL, [itemClass] INTEGER NULL, [itemClassid] INTEGER NULL, [itemQuality] INTEGER NULL, [itemFlag] INTEGER NULL, [itemColor] INTEGER NULL, [itemImage] VARCHAR(8) NULL, [itemMD5] VARCHAR(32) NULL, [itemDescription] TEXT NULL, [itemLocation] INTEGER NULL, [itemX] INTEGER NULL, [itemY] INTEGER NULL);",
			"CREATE TABLE IF NOT EXISTS [muleItemsStats] ([statsItemId] INTEGER NULL, [statsName] VARCHAR(50) NULL, [statsValue] INTEGER NULL);",
			"CREATE UNIQUE INDEX IF NOT EXISTS [IDX_MULEACCOUNTS_ACCOUNTID] ON [muleAccounts]([accountRealm] ASC, [accountLogin] ASC);",
			"CREATE UNIQUE INDEX IF NOT EXISTS [IDX_MULECHARS_CHARID] ON [muleChars]([charAccountId] ASC, [charName] ASC);",
			"CREATE UNIQUE INDEX IF NOT EXISTS [IDX_MULEITEMS_ITEMID] ON [muleItems]([itemId] ASC, [itemCharId] ASC);",
			"CREATE UNIQUE INDEX IF NOT EXISTS [IDX_MULEITEMSSTATS_STATSITEMID] ON [muleItemsStats]([statsItemId] ASC,[statsName] ASC);",
			"CREATE TRIGGER [ON_TBL_MULEACCOUNTS_DELETE] BEFORE DELETE ON [muleAccounts] FOR EACH ROW BEGIN DELETE FROM muleChars WHERE charAccountId = OLD.accountId; END",
			"CREATE TRIGGER [ON_TBL_MULECHARS_DELETE] BEFORE DELETE ON [muleChars] FOR EACH ROW BEGIN DELETE FROM muleItems WHERE itemCharId = OLD.charId; END",
			"CREATE TRIGGER [ON_TBL_MULEITEMS_DELETE] BEFORE DELETE ON [muleItems] FOR EACH ROW BEGIN DELETE FROM muleItemsStats WHERE statsItemId = OLD.itemId; END"
		];
		
		for ($i = 0; $i < count($data); $i++) {
			$conn->query($data[$i]);
		}
		
		$conn = NULL;
	} catch(PDOException $e) {
		$conn = NULL;
		print 'Exception : '.$e->getMessage();
		return false;
	}
}

function makeRandomString($n) { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i < $n; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  
    return $randomString; 
} 

function sendEmail($to, $subject, $message) {
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: <dropper@storedrop.diablo2store.com>' . "\r\n";
	mail($to,$subject,$message,$headers);
}

// functions
function isAdmin() {
	if(@$_SESSION['storeRoll'] == "admin") return true;
	return false;
}

function isDropper($uname = '') {
	if($_SESSION['storeRoll'] == "drop" || $_SESSION['storeRoll'] == "admin") return true;
	return false;
}

function checkUserAuth($reqAdmin = false, $reqDrop = false) {
	global $currUser, $mysqli;
	if(strlen(@$_POST['login_user']) > 0 && strlen(@$_POST['login_pass']) > 0) {
		$userQ = $mysqli->query("SELECT * FROM users WHERE uname='" . $mysqli->real_escape_string($_POST['login_user']) . "' LIMIT 0,1");
		if($userQ) {
			$nPass = md5($_POST['login_pass']);
			$user = $userQ->fetch_assoc();
			if($user['upass'] == $nPass) {
				$_SESSION['userInfo'] = $user;
				$currUser = $_SESSION['userInfo']['uname'];
				setcookie( 'd2sUN', $_SESSION['userInfo']['uname'], 0, '/', 'storedropper.diablo2store.com', isset($_SERVER["HTTPS"]), true);
				setcookie( 'd2sUP', base64_encode($_SESSION['userInfo']['upass']), 0, '/', 'storedropper.diablo2store.com', isset($_SERVER["HTTPS"]), true);
				if(($reqAdmin && isAdmin($_SESSION['userInfo']['uname'])) || (!$reqAdmin && !$reqDrop))
					return true;
				elseif(($reqDrop && isDropper($_SESSION['userInfo']['uname'])) || (!$reqAdmin && !$reqDrop))
					return true;
			}
		}
	}
	
	if(strlen(@$_SESSION['userInfo']['uname']) > 0) {
		$userQ = $mysqli->query("SELECT * FROM users WHERE uname='" . $mysqli->real_escape_string($_SESSION['userInfo']['uname']) . "' LIMIT 0,1");
		if($userQ) {
			$user = $userQ->fetch_assoc();
			if($user['upass'] == $_SESSION['userInfo']['upass']) {
				$_SESSION['userInfo'] = $user;
				$currUser = $_SESSION['userInfo']['uname'];
				setcookie( 'd2sUN', $_SESSION['userInfo']['uname'], 0, '/', 'storedropper.diablo2store.com', isset($_SERVER["HTTPS"]), true);
				setcookie( 'd2sUP', base64_encode($_SESSION['userInfo']['upass']), 0, '/', 'storedropper.diablo2store.com', isset($_SERVER["HTTPS"]), true);
				if(($reqAdmin && isAdmin($_SESSION['userInfo']['uname'])) || (!$reqAdmin && !$reqDrop))
					return true;
				elseif(($reqDrop && isDropper($_SESSION['userInfo']['uname'])) || (!$reqAdmin && !$reqDrop))
					return true;
			}
		}
	}
	
	if(strlen(@$_COOKIE['d2sUN']) > 0) {
		$userQ = $mysqli->query("SELECT * FROM users WHERE uname='" . $mysqli->real_escape_string($_COOKIE['d2sUN']) . "' LIMIT 0,1");
		if($userQ) {
			$user = $userQ->fetch_assoc();
			if($user['upass'] == base64_decode($_COOKIE['d2sUP'])) {
				$_SESSION['userInfo'] = $user;
				$currUser = $_SESSION['userInfo']['uname'];
				setcookie( 'd2sUN', $_SESSION['userInfo']['uname'], 0, '/', 'storedropper.diablo2store.com', isset($_SERVER["HTTPS"]), true);
				setcookie( 'd2sUP', base64_encode($_SESSION['userInfo']['upass']), 0, '/', 'storedropper.diablo2store.com', isset($_SERVER["HTTPS"]), true);
				if(($reqAdmin && isAdmin($_SESSION['userInfo']['uname'])) || (!$reqAdmin && !$reqDrop))
					return true;
				elseif(($reqDrop && isDropper($_SESSION['userInfo']['uname'])) || (!$reqAdmin && !$reqDrop))
					return true;
			}
		}
	}
	
	header("Location: /login.php");
	exit;
}

function userAccess() {
	global $currUser, $authorized, $admin;
	
	//$authorized = array_map('strtolower', $authorized);
	//$currUser = strtolower ($currUser);
	
	if (array_key_exists($currUser, $authorized)) {
	?>
		<!-- -->
		<li><a class="newFont" id="homemenu" href="/">Home Page</a></li>
		<li><a class="newFont" id="homemenu" href="/saleslog.php">Sales Logs</a></li>
		<li><a class="newFont" id="tradelistmenu">Trade List</a></li>
		
			<div class="modal fade" id="tradeListModal" tabindex="-1" role="dialog" aria-labelledby="tradeListLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title newFont" id="tradeListLabel">Items For Trade List</h4>
						</div>
						<div class="modal-body" id="tradelist">

						</div>
						<div class="modal-footer form-inline">
							<form class="listfunction" action="tradelistmaker.php" method="post">
								<div class="form-group pull-left">
									<input class="form-control" name="tradeinfo" type="hidden" id="listinfo" required>
									<input type="checkbox" name="charinfo" value="showinfo">Show Acc/Char Info
									<input type="checkbox" name="sorttype" value="sort">Sort By Item Type<br>
									<input class="form-control" name="maxwidth" type="text" id="listwidth" text="600" placeholder="Max Picture Width" required>
									<button id="listeem" type="submit" class="btn btn-default">Create list</button>
								</div>
							</form>
							<br>
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		<li><a class="newFont" id="opendropmenu">Drop Items</a></li>
			<!-- drop modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title newFont" id="myModalLabel">Items To Drop</h4>
						</div>
						<div class="modal-body" id="droplist">
							<!-- AUTOMATIC UPDATE CONTENT -->
						</div>
						<div class="modal-footer form-inline">
							<form class="dropfunction" action="drop.php" method="post">
								<div class="form-group pull-left">
									<input class="form-control" name="info" type="hidden" id="dropitem" required>
									<input class="form-control" name="game" type="text" id="dropgmname" placeholder="Game Name" required>
									<input class="form-control" name="pass" type="text" id="dropgmpass" placeholder="Game Pass">
									<input class="form-control" name="fg" type="number" min="1" id="dropgmfg" style="width: 100px" placeholder="value" required>
									<button id="dropthem" type="submit" class="btn btn-default">Drop</button>
								</div>
							</form>
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	if (strtolower($admin) == strtolower($currUser)) {
		print '<li><a class="newFont" href="admin.php">Admin</span></a></li>';
	}
}

function pageTimer($s) {
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $s), 4);
	if($total_time > 0.15)
		echo "<span style=\"color:#FF846A\">Page loaded in " . $total_time . " seconds.</span>";
	elseif($total_time > 0.25)
		echo "<span style=\"color:#FF2D00\">Page loaded in " . $total_time . " seconds.</span>";
	else
		echo "<span style=\"color:#6CFF00\">Page loaded in " . $total_time . " seconds.</span>";
}

function buildMenu() {
	//access global variables
	global $realms, $types, $random;
	
	foreach ($realms as $realmnum => $realm) {
		foreach ($types as $typenum => $type) {
			if(countItems($realmnum, $typenum, false, false)) {
				print '<li>';
				print '<a class="dropdown-toggle" id="'.$realm.''.$type.'" data-toggle="dropdown" href="#">'.$realm.''.$type.' <b class="caret"></b></a>';
							
				print '<ul class="dropdown-menu" role="menu" aria-labelledby="'.$realm.''.$type.'">';
				
				if(countItems($realmnum, $typenum, "1", "1")) {
					print /** @lang text */
						'<li role="presentation"><a role="menuitem" tabindex="-1" href="runelist.php?realm='.$realmnum.'&a='.$typenum.'&ladder=1&e=1"> Rune List</a></li>';
					print /** @lang text */
						'<li role="presentation"><a role="menuitem" tabindex="-1" href="gemlist.php?realm='.$realmnum.'&a='.$typenum.'&ladder=1&e=1"> Gem List</a></li>';
				}
				
				if(countItems($realmnum, $typenum, "1", false)) {
					print '<li role="presentation" class="disabled"><a role="menuitem" tabindex="-1">Ladder</a></li>';
					if(countItems($realmnum, $typenum, "1", "0")) {
						print /** @lang text */
							'<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?realm='.$realmnum.'&hc='.$typenum.'&ladder=1&exp=0"><img src="images/classic.png" width="18" height="18" /> Classic</a></li>';
					}
					if(countItems($realmnum, $typenum, "1", "1")) {
						print /** @lang text */
							'<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?realm='.$realmnum.'&hc='.$typenum.'&ladder=1&exp=1"><img src="images/expansion.png" width="18" height="18" /> Expansion </a></li>';
					}
					if(countItems($realmnum, $typenum, "0", false)) {
						print '<li role="presentation" class="divider"></li>';
					}
				}
				if(countItems($realmnum, $typenum, "0", false)) {
					print '<li role="presentation" class="disabled"><a role="menuitem" tabindex="-1">Non-Ladder</a></li>';
					if(countItems($realmnum, $typenum, "0", "0")) {
						print /** @lang text */
							'<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?realm='.$realmnum.'&hc='.$typenum.'&ladder=0&exp=0"><img src="images/classic.png" width="18" height="18" /> Classic</a></li>';
					}
					if(countItems($realmnum, $typenum, "0", "1")) {
						print /** @lang text */
							'<li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?realm='.$realmnum.'&hc='.$typenum.'&ladder=0&exp=1"><img src="images/expansion.png" width="18" height="18" /> Expansion</a></li>';
					}							
				}
				print '</ul>';
				print '</li>';
			}
		}
	}	
}

function getRealmCount() {
	$hasStatFilter = [
		'faster cast rate',
		'faster hit recovery',
		'strength',
		'dexterity',
		'vitality',
		'mana',
		'lightning resist',
		'fire resist',
		'cold resist',
		'all resistances',
		'poison skill damage',
		'all skills',
		'defense',
		'to curses',
		'getting magic items',
		'mana stolen per hit'
	];
	//access global variables
	global $realms, $types, $ladder, $exp;
	
	if (!isset($_GET["realm"]) OR !isset($_GET["hc"]) OR !isset($_GET["ladder"]) OR !isset($_GET["exp"])) {
		require "myTheme/home.php";
		return false;
	} else {

		// redefine address variables if someone try manual change
		if ($_GET["hc"] > 1     OR !is_numeric($_GET["hc"])) 		{ $_GET["hc"] 		= 1; }
		if ($_GET["ladder"] > 1 OR !is_numeric($_GET["ladder"])) 	{ $_GET["ladder"] 	= 1; }
		if ($_GET["exp"] > 1    OR !is_numeric($_GET["exp"])) 		{ $_GET["exp"] 		= 1; }
		if (!is_numeric($_GET["realm"])) { $_GET["realm"] = 1; }
		
		// define variables
		$queryR 	= $_GET["realm"];
		$queryHC	= $_GET["hc"];
		$queryLD	= $_GET["ladder"];
		$queryEXP	= $_GET["exp"];
		
		$oRealm = $realms[$queryR];
		$oType = $types[$queryHC];
		$oLadder = $ladder[$queryLD];
		
		//output
		if($realms[$queryR] == "East")
			$oRealm = "US East ";
		
		if($types[$queryHC] == "SC")
			$oType = "Softcore ";
		
		$howmany = countItems($queryR, $queryHC, $queryLD, $queryEXP);
		
		echo '<form class="input-group searchform col-sm-12" id="searchform">
			<div class="form-row">
				<div class="form-group col-md-12">
					<h1 class="niceBlueColor">'.$oRealm.' '.$oType.' '.$oLadder.'</h1> <span class="niceBlueColor">'.number_format($howmany).' Items In Database</span>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="itemtype" class="searchFormLabel">Quality:</label>
					<select id="itemtype" class="form-control" name="itemtype">
						<option></option><option>white</option><option>magic</option><option>set</option><option>rare</option><option>unique</option><option>craft</option><option>runes</option><option>runeword</option><option>torch</option><option>annihilus</option><option>uberkeys</option><option>organs</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="itemtype2" class="searchFormLabel">Type:</label>
					<select id="itemtype2" class="form-control" name="itemtype2">
						<option></option><option>ring</option><option>amulet</option><option>jewel</option><option>helm</option><option>circlet</option><option>armor</option><option>shield</option><option>auricshields</option><option>voodooheads</option><option>boots</option><option>gloves</option><option>belt</option><option>small charm</option><option>large charm</option><option>grand charm</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="eth" class="searchFormLabel">Ethereal:</label>
					<select id="eth" class="form-control" name="eth">
						<option></option><option>true</option><option>false</option>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="identified" class="searchFormLabel">Identified:</label>
					<select id="identified" class="form-control" name="identified">
						<option></option><option>true</option><option>false</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="colorIt" class="searchFormLabel">Color:</label>
					<select id="colorIt" class="form-control" name="colorIt">
						<option></option>';
						global $inGameColor;
						for($c = 0; $c<count($inGameColor); $c++) {
							if($inGameColor[$c] != "") {
								echo '<option>'.$inGameColor[$c].'</option>';
							}
						}
					echo '</select>
				</div>
				<div class="form-group col-md-4">
					<label for="itemlimit" class="searchFormLabel">Max Results:</label>
					<select id="itemlimit" class="form-control" name="itemlimit">
						<option>100</option><option>200</option><option>300</option><option>400</option><option>500</option><option>1000</option><option>2000</option><option>5000</option>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="maxlevel" class="searchFormLabel">Max Level:</label>
					<select id="maxlevel" class="form-control" name="maxlevel">
						<option></option><option>18</option><option>24</option><option>30</option><option>40</option><option>50</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="hasstat" class="searchFormLabel">Has Stat:</label>
					<select id="hasstat" class="form-control" name="hasstat">
						<option></option>';
					foreach($hasStatFilter as $fil) {
						echo '<option>'.$fil.'</option>';
					}
					echo '</select>
				</div>
				<div class="form-group col-md-4">
					<label for="sock" class="searchFormLabel">Socketed:</label>
					<select id="sock" class="form-control" name="sock">
						<option></option><option>true</option><option>false</option>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-11">
					<input type="text" class="form-control" placeholder="Search for..." id="searchtext" name="search" required>
				</div>
				<div class="form-group col-md-1">
					<button class="btn btn-default searchbut" type="button" url="show.php?realm='.$queryR.'&hc='.$queryHC.'&ladder='.$queryLD.'&exp='.$queryEXP.'">Search</button>
				</div>
			</div>
		</form>';
		/*
		print '<div class="panel panel-default" style="border: 0 solid;">';
			print '<div class="panel-heading">';
				print /** @lang text */
		/*			'<h1 id="header" class="panel-title niceBlueColor">'.$oRealm.' '.$oType.' '.$oLadder.'</h1> <span class="niceBlueColor">'.$howmany.' Items Logged</span>';
			print '</div>';
			//print "<span id='itemCounter' class='top'>There are currently <span class='color1'>".$howmany."</span> items logged. Please make sure to reload the page to update the stock after time has passed.</span>";
			print '<div id="dropCounts"></div>';
			//print '<script type="text/javascript">CheckDrops();</script>';
			print '<br><br>';
			print '<form class="input-group searchform col-sm-12" id="searchform">'; // action="'.$_SERVER['REQUEST_URI'].'" method="post"
				print '<div class="col-sm-12">';
					print '<div class="input-group">';
						print '<span class="input-group-btn">';
							print '<div class="filterProp col-md-12">Quality: <select id="search_parameter" name="itemtype" style="width:120px!important;min-width:120px;max-width:120px;"><option></option><option>white</option><option>magic</option><option>set</option><option>rare</option><option>unique</option><option>craft</option><option>runes</option><option>runeword</option><option>torch</option><option>annihilus</option><option>uberkeys</option><option>organs</option></select></div>';
						print '</span>';
						print '<span class="input-group-btn">';
							print '<div class="btn btn-default col-md-12">Type: <select name="itemtype2" style="width:120px!important;min-width:120px;max-width:120px;"><option></option><option>ring</option><option>amulet</option><option>jewel</option><option>helm</option><option>circlet</option><option>armor</option><option>shield</option><option>auricshields</option><option>voodooheads</option><option>boots</option><option>gloves</option><option>belt</option><option>small charm</option><option>large charm</option><option>grand charm</option></select></div>';
						print '</span>';
						print '<span class="input-group-btn">';
							print '<div class="btn btn-default col-md-12">Ethereal: <select name="eth" style="width:120px!important;min-width:120px;max-width:120px;"><option></option><option>true</option><option>false</option></select></div>';
						print '</span>';
					print '</div>';
				print '</div>';
				print '<div class="col-sm-12">';
					print '<div class="input-group">';
						print '<span class="input-group-btn">';
							print '<div class="btn btn-default col-md-12">Identified: <select name="identified" style="width:120px!important;min-width:120px;max-width:120px;"><option></option><option>true</option><option>false</option></select></div>';
						print '</span>';
						print '<span class="input-group-btn">';
							print '<div class="btn btn-default col-md-12">Color: <select name="colorIt" style="width:120px!important;min-width:120px;max-width:120px;"><option></option>';
								global $inGameColor;
								for($c = 0; $c<count($inGameColor); $c++) {
									if($inGameColor[$c] != "") {
										print '<option>'.$inGameColor[$c].'</option>';
									}
								}
							print '</select></div>';
						print '</span>';
						print '<span class="input-group-btn">';
							print '<div class="btn btn-default col-md-12">Max Results: <select name="itemlimit" style="width:120px!important;min-width:120px;max-width:120px;"><option>100</option><option>200</option><option>300</option><option>400</option><option>500</option><option>1000</option><option>2000</option><option>5000</option></select></div>';
						print '</span>';
					print '</div>';
				print '</div>';
				print '<div class="col-sm-12">';
					print '<div class="input-group">';
						print '<input type="text" class="form-control" placeholder="Search for..." id="searchtext" name="search" required>';
						print '<span class="input-group-btn">';
							print '<button class="btn btn-default searchbut" type="button" url="show.php?realm='.$queryR.'&hc='.$queryHC.'&ladder='.$queryLD.'&exp='.$queryEXP.'">find items</button>';
						print '</span>';
					print '</div>';
				print '</div>';
			print '</form>';
			print '<br>';
		print '</div>';*/
		return true;
	}
}

function countItems($queryR, $queryHC, $queryLD, $queryEXP) {
	global $itemsDB;
	try {
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");

		$tempA	= " AND charHardcore = ".$queryHC;
		$tempB	= "";
		$tempC	= "";
		$tempD	= "";
		
		if ($queryEXP === "0" OR $queryEXP === "1") {
			$tempB = " AND charExpansion = ".$queryEXP;
		}
		if ($queryLD === "0" OR $queryLD === "1") {
			$tempC = " AND charLadder = ".$queryLD;
		}
		
		$sql = /** @lang text */
			'SELECT COUNT() AS "count" FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountId WHERE accountRealm = '.$queryR.' '.$tempA.' '.$tempB.' '.$tempC.' '.$tempD.'';
		$results = $conn->query($sql);
		$conn = NULL;
		$itemsDB = $results->fetchAll(PDO::FETCH_ASSOC);

		return $itemsDB[0]["count"];
	} catch(PDOException $e) {
		$conn = NULL;
		print 'Exception : '.$e->getMessage();
		return false;
	}
}

function getAccounts() {
	try {
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
		// define variables
		$queryR 	= $_GET["realm"];
		$queryHC	= $_GET["hc"];
		$queryLD	= $_GET["ladder"];
		$queryEXP	= $_GET["exp"];
		$tempA	= " AND charHardcore = ".$queryHC;
		$tempB 	= " AND charExpansion = ".$queryEXP;
		$tempC 	= " AND charLadder = ".$queryLD;
		$tempD	= " GROUP BY accountLogin ORDER BY accountId DESC";
		//$tempD	= " GROUP BY accountLogin";
		
		//$select	= "itemName, itemQuality, itemImage, itemMD5, itemFlag, itemDescription";
		$select	= "accountId, accountLogin";
		
		$query = 'SELECT '.$select.' FROM muleAccounts LEFT JOIN muleChars ON charAccountId = accountId WHERE accountRealm = '.$queryR.' '.$tempA.' '.$tempB.' '.$tempC.' '.$tempD.'';

		$results = $conn->query($query);
		$conn = NULL;
		
		$accounts = $results->fetchAll(PDO::FETCH_ASSOC);
		
		print '<ul class="list-group">';
		
		foreach ($accounts as $nr => $account) {
			print '<li class="list-group-item"><a href="javascript:;" class="mainmenu" data-toggle="collapse" data-target="#acc'.$account["accountId"].'">'.$account["accountLogin"].'</a>';
			getChars($account["accountId"]);
			print '</li>';
		}
		
		print '</ul>';
		
	} catch(PDOException $e) {
		$conn = NULL;
		print 'Exception : '.$e->getMessage();
	}
}

function getChars($accid) {
	global $charsIds;
	try {
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
		
		// define variables
		$queryR 	= $_GET["realm"];
		$queryHC	= $_GET["hc"];
		$queryLD	= $_GET["ladder"];
		$queryEXP	= $_GET["exp"];
		$tempA	= " AND charHardcore = ".$queryHC;
		$tempB 	= " AND charExpansion = ".$queryEXP;
		$tempC 	= " AND charLadder = ".$queryLD;
		$select	= "charId, charName, charClassId";
		
		$query = 'SELECT '.$select.' FROM muleChars WHERE charAccountId = '.$accid.' '.$tempA.' '.$tempB.' '.$tempC.' ';

		$results = $conn->query($query);
					
		$chars = $results->fetchAll(PDO::FETCH_ASSOC);
		
		$conn = NULL;
		
		$classes 	=	array("Amazon", "Sorceress", "Necromancer", "Paladin", "Barbarian", "Druid", "Assassin");
		
		print '<ul id="acc'.$accid.'" class="collapse list-unstyled">';
		
		foreach ($chars as $nr => $char) {
			array_push($charsIds, $char["charId"]);
			global $showthat;
			
			if (isset($_GET["charid"])) {
				if ($_GET["charid"] == $char["charId"]) {
					$showthat = "acc".$accid;
				}
			}
			
			if ($showthat == "") {
				$showthat = "acc".$accid;
			}
			
			print /** @lang text */
				'<li><a href="show.php?realm='.$queryR.'&hc='.$queryHC.'&ladder='.$queryLD.'&exp='.$queryEXP.'&charid='.$char["charId"].'" class="submenu"><img src="images/icons/'.$classes[$char["charClassId"]].'.ico" width="16" height="16" /> '.$char["charName"].' <span class="label alert-warning pull-right">'.countItemsOnChar($char["charId"]).'</span></a></li>';
		}
		print '</ul>';
		
	} catch(PDOException $e) {
		$conn = NULL;
		print 'Exception : '.$e->getMessage();
	}
}

function countItemsOnChar($charId) {
	try {
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
		
		$query = /** @lang text */
			'SELECT COUNT() AS "count" FROM muleItems Where itemCharId = '.$charId;

		$results = $conn->query($query);
		
		$conn = NULL;
		
		$count = $results->fetchAll(PDO::FETCH_ASSOC);
		
		return $count[0]["count"];
		
	} catch(PDOException $e) {
		$conn = NULL;
		print 'Exception : '.$e->getMessage();
		return false;
	}
}

function showCurrentItems() {
	global $charsIds;
	global $inGameColor;
	$qualityColor  = array("", "colorb", "colorb", "colorb", "color3", "color2", "color9", "color4", "color8");
	//$inGameColor   = array("","","","black","lightblue","darkblue","crystalblue","lightred","darkred","crystalred","","darkgreen","crystalgreen","lightyellow","darkyellow","lightgold","darkgold","lightpurple","","orange","white");
	$resultcount   = "";
	
	if(isset($_GET["charid"])) {
		$show = getItemsFromDb($_GET["charid"]);
	}
	
	if(isset($charsIds[0]) AND !isset($_GET["charid"])) {
		$show = getItemsFromDb($charsIds[0]);
	}
	
	if(isset($_POST["search"])) {
		$show = getItemsFromDb(1);
		$resultcount = " ".number_format(count($show));
	}
	
	print '<div class="panel-heading">';
		//print '<h1 class="panel-title">Items list ('.$resultcount.' '.getCurrentName().' ) <span class="showhide color8 pull-right">hide equiped</span></h1>';
		$resAdd = '';
		if($resultcount == 100 || $resultcount == 200 || $resultcount == 300 || $resultcount == 400 || $resultcount == 500 || $resultcount == 1000 || $resultcount == 2000 || $resultcount == 5000)
			$resAdd = " >";
		print
			'<h1 class="panel-title niceBlueColor">Items list ('.$resAdd.$resultcount.' '.getCurrentName().' )
			<div class="form-inline pull-right" style="margin-top:-7px;">
				<input class="form-control markall" style="width:100px;" type="number" id="massMark" required>
				<button onclick="MarkThem()" class="btn btn-default markall">Select X</button>
				<button onclick="ClearAll()" class="btn btn-default markall">Clear List</button>
				<button class="btn btn-default markall showhide">Hide Equipped</button>
			</div>
		</h1>';
	print '</div>';
	print '<table id="itemstable" class="table diablo itemTable">';
		print '<thead><tr>';
		if (isset($_POST["search"]) AND !($_POST["itemtype"] == "torch" OR $_POST["itemtype"] == "annihilus" OR $_POST["itemtype"] == "rare")) {
			print '<th width="20%" class="text-left exocet"><strong>Toon</strong></th>';
		}
		if (isset($_POST["itemtype"]) AND ($_POST["itemtype"] == "torch" OR $_POST["itemtype"] == "annihilus")) {
			if($_POST["itemtype"] == "torch"){
				print '<th width="15%" class="text-center exocet"><strong>Class</strong></th>';
			}
			print '<th width="15%" class="text-center exocet"><strong>Stat</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Res</strong></th>';
			if ($_POST["itemtype"] == "annihilus"){
				print '<th width="15%" class="text-center exocet"><strong>Exp</strong></th>';
			}
		} else if (isset($_POST["itemtype2"]) AND $_POST["itemtype2"] == "grand charm") {
			print '<th width="15%" class="text-center exocet"><strong>Skin</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>HP</strong></th>';
		} elseif (isset($_POST["itemtype2"]) AND ($_POST["itemtype2"] == "amulet")) {
			print '<th width="15%" class="text-center exocet"><strong>Stat</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Skills</strong></th>';
		} elseif (isset($_POST["itemtype2"]) AND ($_POST["itemtype2"] == "ring")) {
			print '<th width="15%" class="text-center exocet"><strong>Stat</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Sub Stat</strong></th>';
		} else {
			print '<th width="15%" class="text-center exocet"><strong>ED</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Sockets</strong></th>';
			if (isset($_POST["itemtype"]) AND $_POST["itemtype"] == "rare") {
				print '<th width="15%" class="text-center exocet"><strong>Color</strong></th>';
			}
		}
		print '<th width="*" class="exocet"><strong>Name</strong></th>';
		print '</tr></thead>';
		
		print '<tbody>';
		if($show) {
			foreach ($show as $nr => $item) {
				$desc	    = $item["itemDescription"];
				$colOne		= checkStat($item["itemId"], "enhanceddefense");
				$colTwo 	= checkStat($item["itemId"], "sockets");
				$colThree   = "";
				$itCo       = "";
				$colInd     = "";

				if($item["itemColor"] != -1 and $item["itemQuality"] == 6) {
					$colInd = $item["itemColor"];
					$itCo   = "<br><br> color: ".$inGameColor[$colInd];

				}
				if ($colOne == "") {
					$colOne	= checkStat($item["itemId"], "enhanceddamage");
					if ($colOne != "") {
						$colOne = $colOne."% (dmg)";
					}					
				} else if ($colOne != "") {
					$colOne = $colOne."% (def)";
				}
				
				if (isset($_POST["itemtype2"]) AND $_POST["itemtype2"] == "grand charm") {
					//$colOne = $item["itemImage"];
					$colOne = "<img src='images/items/".$item["itemImage"].".png'>";
					$colTwo	= checkStat($item["itemId"], "maxhp");
					if ($colTwo != "") {
						$colTwo = $colTwo." hp";
					}
				}
				elseif ($colTwo != "") {
					$colTwo = $colTwo." sox";
				}
				//itemlevelreq
				if (isset($_POST["itemtype2"]) AND ($_POST["itemtype2"] == "ring")) {
					if(checkStat($item["itemId"], "fcr") > 0)
						$colOne = checkStat($item["itemId"], "fcr") . "% (fcr)";
					elseif(checkStat($item["itemId"], "ias") > 0)
						$colOne = checkStat($item["itemId"], "ias") . "% (ias)";
					elseif(checkStat($item["itemId"], "lifeleech") > 0)
						$colOne = checkStat($item["itemId"], "lifeleech") . "% (ll)";
					elseif(checkStat($item["itemId"], "manaleech") > 0)
						$colOne = checkStat($item["itemId"], "manaleech") . "% (ml)";
					elseif(checkStat($item["itemId"], "fireresist") > 0 && checkStat($item["itemId"], "fireresist") == checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "lightresist") == checkStat($item["itemId"], "coldresist") && checkStat($item["itemId"], "coldresist") == checkStat($item["itemId"], "poisonresist"))
						$colOne = checkStat($item["itemId"], "fireresist") . " (all res)";
					elseif(checkStat($item["itemId"], "fireresist") > 0 || checkStat($item["itemId"], "lightresist") > 0 || checkStat($item["itemId"], "coldresist") > 0 || checkStat($item["itemId"], "poisonresist") > 0) {
						if(checkStat($item["itemId"], "fireresist") > checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "fireresist") > checkStat($item["itemId"], "coldresist") && checkStat($item["itemId"], "fireresist") > checkStat($item["itemId"], "poisonresist"))
							$colOne = checkStat($item["itemId"], "fireresist") . " (fire res)";
						elseif(checkStat($item["itemId"], "lightresist") > checkStat($item["itemId"], "fireresist") && checkStat($item["itemId"], "lightresist") > checkStat($item["itemId"], "coldresist") && checkStat($item["itemId"], "lightresist") > checkStat($item["itemId"], "poisonresist"))
							$colOne = checkStat($item["itemId"], "lightresist") . " (light res)";
						elseif(checkStat($item["itemId"], "coldresist") > checkStat($item["itemId"], "fireresist") && checkStat($item["itemId"], "coldresist") > checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "coldresist") > checkStat($item["itemId"], "poisonresist"))
							$colOne = checkStat($item["itemId"], "coldresist") . " (cold res)";
						elseif(checkStat($item["itemId"], "poisonresist") > checkStat($item["itemId"], "fireresist") && checkStat($item["itemId"], "poisonresist") > checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "poisonresist") > checkStat($item["itemId"], "coldresist"))
							$colOne = checkStat($item["itemId"], "poisonresist") . " (psn res)";
						else
							$colOne = "N/A";
					} else {
						$colOne = "N/A";
					}
					
					if(checkStat($item["itemId"], "strength") > 0) {
						$colTwo = checkStat($item["itemId"], "strength") . " (str)";
					} elseif(checkStat($item["itemId"], "dexterity") > 0) {
						$colTwo = checkStat($item["itemId"], "dexterity") . " (dex)";
					} elseif(checkStat($item["itemId"], "maxhp") > 0) {
						$colTwo = checkStat($item["itemId"], "maxhp") . " (hp)";
					} elseif(checkStat($item["itemId"], "maxmana") > 0) {
						$colTwo = checkStat($item["itemId"], "maxmana") . " (mana)";
					} else {
						$colTwo = "N/A";
					}
				}
				
				if (isset($_POST["itemtype2"]) AND ($_POST["itemtype2"] == "amulet")) {
					if(checkStat($item["itemId"], "fcr") > 0)
						$colOne = checkStat($item["itemId"], "fcr") . "% (fcr)";
					elseif(checkStat($item["itemId"], "ias") > 0)
						$colOne = checkStat($item["itemId"], "ias") . "% (ias)";
					elseif(checkStat($item["itemId"], "fireresist") > 0 && checkStat($item["itemId"], "fireresist") == checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "lightresist") == checkStat($item["itemId"], "coldresist") && checkStat($item["itemId"], "coldresist") == checkStat($item["itemId"], "poisonresist"))
						$colOne = checkStat($item["itemId"], "fireresist") . " (all res)";
					elseif(checkStat($item["itemId"], "fireresist") > 0 || checkStat($item["itemId"], "lightresist") > 0 || checkStat($item["itemId"], "coldresist") > 0 || checkStat($item["itemId"], "poisonresist") > 0) {
						if(checkStat($item["itemId"], "fireresist") > checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "fireresist") > checkStat($item["itemId"], "coldresist") && checkStat($item["itemId"], "fireresist") > checkStat($item["itemId"], "poisonresist"))
							$colOne = checkStat($item["itemId"], "fireresist") . " (fire res)";
						elseif(checkStat($item["itemId"], "lightresist") > checkStat($item["itemId"], "fireresist") && checkStat($item["itemId"], "lightresist") > checkStat($item["itemId"], "coldresist") && checkStat($item["itemId"], "lightresist") > checkStat($item["itemId"], "poisonresist"))
							$colOne = checkStat($item["itemId"], "lightresist") . " (light res)";
						elseif(checkStat($item["itemId"], "coldresist") > checkStat($item["itemId"], "fireresist") && checkStat($item["itemId"], "coldresist") > checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "coldresist") > checkStat($item["itemId"], "poisonresist"))
							$colOne = checkStat($item["itemId"], "coldresist") . " (cold res)";
						elseif(checkStat($item["itemId"], "poisonresist") > checkStat($item["itemId"], "fireresist") && checkStat($item["itemId"], "poisonresist") > checkStat($item["itemId"], "lightresist") && checkStat($item["itemId"], "poisonresist") > checkStat($item["itemId"], "coldresist"))
							$colOne = checkStat($item["itemId"], "poisonresist") . " (psn res)";
						else
							$colOne = "N/A";
					} else {
						$colOne = "N/A";
					}
					
					if(checkStat($item["itemId"], "itemaddsorceressskills") == 1) {
						$colTwo = "+1 sorc";
					}elseif(checkStat($item["itemId"], "itemaddsorceressskills") == 2) {
						$colTwo = "+2 sorc";
					}elseif(checkStat($item["itemId"], "itemallskills") == 1) {
						$colTwo = "+1 all";
					}elseif(checkStat($item["itemId"], "itemallskills") == 2) {
						$colTwo = "+2 all";
					}elseif(checkStat($item["itemId"], "itemaddbarbarianskills") == 1) {
						$colTwo = "+1 barb";
					}elseif(checkStat($item["itemId"], "itemaddbarbarianskills") == 2) {
						$colTwo = "+2 barb";
					}elseif(checkStat($item["itemId"], "itemaddnecromancerskills") == 1) {
						$colTwo = "+1 necro";
					}elseif(checkStat($item["itemId"], "itemaddnecromancerskills") == 2) {
						$colTwo = "+2 necro";
					}elseif(checkStat($item["itemId"], "itemaddassassinskills") == 1) {
						$colTwo = "+1 sin";
					}elseif(checkStat($item["itemId"], "itemaddassassinskills") == 2) {
						$colTwo = "+2 sin";
					}elseif(checkStat($item["itemId"], "itemaddamazonskills") == 1) {
						$colTwo = "+1 zon";
					}elseif(checkStat($item["itemId"], "itemaddamazonskills") == 2) {
						$colTwo = "+2 zon";
					}elseif(checkStat($item["itemId"], "itemadddruidskills") == 1) {
						$colTwo = "+1 druid";
					}elseif(checkStat($item["itemId"], "itemadddruidskills") == 2) {
						$colTwo = "+2 druid";
					}elseif(checkStat($item["itemId"], "itemaddpaladinskills") == 1) {
						$colTwo = "+1 pally";
					}elseif(checkStat($item["itemId"], "itemaddpaladinskills") == 2) {
						$colTwo = "+2 pally";
					}elseif(checkStat($item["itemId"], "fireskilltab") == 1) {
						$colTwo = "+1 fire";
					}elseif(checkStat($item["itemId"], "fireskilltab") == 2) {
						$colTwo = "+2 fire";
					}elseif(checkStat($item["itemId"], "coldskilltab") == 1) {
						$colTwo = "+1 cold";
					}elseif(checkStat($item["itemId"], "coldskilltab") == 2) {
						$colTwo = "+2 cold";
					}elseif(checkStat($item["itemId"], "lightningskilltab") == 1) {
						$colTwo = "+1 light";
					}elseif(checkStat($item["itemId"], "lightningskilltab") == 2) {
						$colTwo = "+2 light";
					}elseif(checkStat($item["itemId"], "palicombatskilltab") == 1) {
						$colTwo = "+1 p combat";
					}elseif(checkStat($item["itemId"], "palicombatskilltab") == 2) {
						$colTwo = "+2 p combat";
					}else{
						$colTwo = "N/A";
					}
				}
				
				if (isset($_POST["itemtype"]) AND ($_POST["itemtype"] == "torch" OR $_POST["itemtype"] == "annihilus")) {
					$colOne		= checkStat($item["itemId"], "strength");
					if($colOne == "") {
						$colOne = "unid";
					}
					$colTwo 	= checkStat($item["itemId"], "fireresist");
					if($colTwo == "") {
						$colTwo = "unid";
					}
					if($_POST["itemtype"] == "annihilus") {
						$colThree 	= checkStat($item["itemId"], "itemaddexperience");
						if($colThree == "") {
							$colThree = "unid";
						}
					}
					if($_POST["itemtype"] == "torch") {
						// thank you to MrSithy <3
						if($colThree = checkStat($item["itemId"], "itemaddsorceressskills")) {
							$colThree = "sorceress";
						}else if($colThree = checkStat($item["itemId"], "itemaddbarbarianskills")) {
							$colThree = "barbarian";
						}else if($colThree = checkStat($item["itemId"], "itemaddnecromancerskills")) {
							$colThree = "necromancer";
						}else if($colThree = checkStat($item["itemId"], "itemaddassassinskills")) {
							$colThree = "assassin";
						}else if($colThree = checkStat($item["itemId"], "itemaddamazonskills")) {
							$colThree = "amazon";
						}else if($colThree = checkStat($item["itemId"], "itemadddruidskills")) {
							$colThree = "druid";
						}else if($colThree = checkStat($item["itemId"], "itemaddpaladinskills")) {
							$colThree = "paladin";
						}else{
							$colThree = "unid";
						}
					}
				}
				
				$trclass = "loc".$item["itemLocation"];
				
				$realmnames	= array("uswest", "useast", "asia", "europe");
				$realmname	= $realmnames[$item["accountRealm"]];
				
				$trinfo = ' drImage="'.$item["itemImage"].'" drID="'.$item["itemId"].'" dritemid="itemid'.$item["itemId"].'" draccount="'.$item["accountLogin"].'" dritemtype="'.$item["itemType"].'" drchar="'.$item["charName"].'" drmd5="'.$item["itemMD5"].'" drrealm="'.$realmname.'" drname="'.$item["itemName"].'"';
									
				print '<tr'.$trinfo.' class="'.$trclass.' item">';
				if (isset($_POST["search"]) AND !($_POST["itemtype"] == "torch" OR $_POST["itemtype"] == "annihilus" OR $_POST["itemtype"] == "rare")) {
					print '<td class="text-left"><b>'.$item["charName"].'</b></td>';
				}
				if(isset($_POST["itemtype"]) AND $_POST["itemtype"] == "torch"){
					print '<th width="15%" class="text-center exocet"><strong>'.$colThree.'</strong></th>';
				}
				print '<td class="text-center"><b>'.$colOne.'</b></td>';
				print '<td class="text-center"><b>'.$colTwo.'</b></td>';
				if(isset($_POST["itemtype"]) AND $_POST["itemtype"] == "annihilus"){
					print '<td class="text-center"><b>'.$colThree.'</b></td>';
				}
				if (isset($_POST["itemtype"]) AND $_POST["itemtype"] == "rare") {
					print '<th width="15%" class="text-center exocet"><strong>'.@$inGameColor[$colInd].'</strong></th>';
				}
				$tooltip = /** @lang text */
					'<center>&lt;img src=&quot;images/items/'.$item["itemImage"].'.png&quot;&gt; <br>'.$desc.''.$itCo.'</center>';
				
				print '<td><div class="'.$qualityColor[$item["itemQuality"]].' show-tooltip form-inline" title="'.$tooltip.'"><b>'.$item["itemName"].'</b></div></td>';

				print '</tr>';
			}
		}
		print '</tbody>';
		print '<tfoot><tr>';
		if (isset($_POST["search"]) AND !($_POST["itemtype"] == "torch" OR $_POST["itemtype"] == "annihilus" OR $_POST["itemtype"] == "rare")) {
			print '<td width="20%" class="text-left exocet"><strong>Char</strong></td>';
		}
		if (isset($_POST["itemtype"]) AND ($_POST["itemtype"] == "torch" OR $_POST["itemtype"] == "annihilus")) {
			if($_POST["itemtype"] == "torch"){
				print '<th width="15%" class="text-center exocet"><strong>Class</strong></th>';
			}
			print '<th width="15%" class="text-center exocet"><strong>Stat</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Res</strong></th>';
			if($_POST["itemtype"] == "annihilus"){
				print '<th width="15%" class="text-center exocet"><strong>Exp</strong></th>';
			}
		} elseif (isset($_POST["itemtype2"]) AND ($_POST["itemtype2"] == "amulet")) {
			print '<th width="15%" class="text-center exocet"><strong>Stat</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Skills</strong></th>';
		} elseif (isset($_POST["itemtype2"]) AND ($_POST["itemtype2"] == "ring")) {
			print '<th width="15%" class="text-center exocet"><strong>Stat</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Sub Stat</strong></th>';
		} else {
			print '<th width="15%" class="text-center exocet"><strong>ED</strong></th>';
			print '<th width="15%" class="text-center exocet"><strong>Sockets</strong></th>';
			if (isset($_POST["itemtype"]) AND $_POST["itemtype"] == "rare") {
				print '<th width="15%" class="text-center exocet"><strong>Color</strong></th>';
			}
		}
		print '<td width="*" class="exocet"><strong>Name</th>';
		print '<tr></tfoot>';	
	print '</table>';
}

function getItemsFromDb($charid) {
	global $inGameColor;
	$selectinfo = "accountLogin, accountRealm, charName, itemId, itemName, itemType, itemQuality, itemImage, itemDescription, itemMD5, itemLocation, itemColor";
	if (!isset($_POST["search"])) {
		try {
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
			
			$query = /** @lang text */
				//'SELECT '.$selectinfo.' FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountID WHERE itemCharId = '.$charid.' ORDER BY itemType DESC';
				'SELECT '.$selectinfo.' FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountID WHERE itemCharId = '.$charid.'';

			$results = $conn->query($query);
			
			$conn = NULL;
			
			$count = $results->fetchAll(PDO::FETCH_ASSOC);
			
			return $count;
			
		} catch(PDOException $e) {
			$conn = NULL;
			print 'Exception : '.$e->getMessage();
			return false;
		}
	} else {
		try {
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
			$conn->sqliteCreateFunction('regexp', '_sqliteRegexp', 2);
			
			$queryR 	= $_GET["realm"];
			$queryHC	= $_GET["hc"];
			$queryLD	= $_GET["ladder"];
			$queryEXP	= $_GET["exp"];
			
			$tempA	= " AND charHardcore = ".$queryHC;
			$tempB 	= " AND charExpansion = ".$queryEXP;
			$tempC 	= " AND charLadder = ".$queryLD;
			$tempD	= "";
			
			$pieces = explode(",", $_POST["search"]);
			
			for ($x = 0; $x < count($pieces); $x++) {
				$valid = str_replace("'", "_", $pieces[$x]);
				if(substr($valid, 0, 1) == "!")	{
					$valid = str_replace("!", "", $valid);
					$one = " AND lower(itemDescription) NOT LIKE '%".trim($valid)."%'";
					$tempD .= $one;
				} else {
					$one = " AND lower(itemDescription) LIKE '%".trim($valid)."%'";
					$tempD .= $one;
				}
				
			}
			
			$types = array();
				$types['white'] = "AND itemQuality < 4 AND (\"itemFlag\" & 0x4000000) == 0 AND (\"itemClassid\" NOT BETWEEN 610 AND 642)";
				$types['magic'] = "AND itemQuality == 4";
				$types['set'] = "AND itemQuality == 5";
				$types['rare'] = "AND itemQuality == 6";
				$types['unique'] = "AND itemQuality == 7";
				$types['craft'] = "AND itemQuality == 8";
				$types['torch'] = "AND itemQuality == 7 AND itemClassid == 604";
				$types['annihilus'] = "AND itemQuality == 7 AND itemClassid == 603";
				//$types['runeword'] = "AND (\"itemFlag\" & 0x4000000) == 0x4000000";
				$types['runeword'] = "AND \"itemFlag\" == 75499537";
				$types['runes'] = "AND \"itemClassid\" BETWEEN 610 AND 642";
				$types['uberkeys'] = "AND \"itemClassid\" BETWEEN 647 AND 649";
				$types['organs'] = "AND \"itemClassid\" BETWEEN 650 AND 652";

			$filterBy = "";
			if (!empty($_POST["itemtype"])) {
				$filterBy = $types[$_POST["itemtype"]];
			}
			
			$types2 = array();
				$types2["ring"] = "AND itemType == 10";
				$types2["amulet"] = "AND itemType == 12";
				$types2["jewel"] = "AND itemType == 58";
				$types2["helm"] = "AND itemType == 37";
				$types2["circlet"] = "AND itemType == 75";
				$types2["armor"] = "AND itemType == 3";
				$types2["shield"] = "AND itemType == 2";
				$types2["auricshields"] = "AND itemType == 70";
				$types2["voodooheads"] = "AND itemType == 69";
				$types2["boots"] = "AND itemType == 15";
				$types2["gloves"] = "AND itemType == 16";
				$types2["belt"] = "AND itemType == 19";
				$types2["small charm"] = "AND itemType == 82";
				$types2["large charm"] = "AND itemType == 83";
				$types2["grand charm"] = "AND itemType == 84";
				
			$filterBy2 = "";
			if (!empty($_POST["itemtype2"])) {
				$filterBy2 = $types2[$_POST["itemtype2"]];
			}

			$orderBy = "";
			if (!empty($_POST["eth"])) {
				if ($_POST["eth"] == "true") {
					//$orderBy = "AND (\"itemFlag\" & 0x400000) == 0x400000";
					$orderBy = "AND \"itemFlag\" == 12584976";
				}
				if ($_POST["eth"] == "false") {
					//$orderBy = "AND (\"itemFlag\" & 0x400000) == 0";
					$orderBy = "AND \"itemFlag\" != 12584976";
				}
			}

			$orderBy2 = "";
			if (!empty($_POST["identified"])) {
				if ($_POST["identified"] == "true") {
					//$orderBy2 = "AND (\"itemFlag\" & 0x10) == 0x10";
					$orderBy2 = "AND itemDescription NOT LIKE '%Unidentified%'";
				}
				if ($_POST["identified"] == "false") {
					//$orderBy2 = "AND (\"itemFlag\" & 0x10) == 0";
					$orderBy2 = "AND itemDescription LIKE '%Unidentified%'";
				}
			}
			
			$orderBy3 = "";
			if (!empty($_POST["sock"])) {
				if ($_POST["sock"] == "true") {
					//$orderBy2 = "AND (\"itemFlag\" & 0x10) == 0x10";
					$orderBy3 = "AND itemDescription LIKE '%Socketed (%'";
				}
				if ($_POST["sock"] == "false") {
					//$orderBy2 = "AND (\"itemFlag\" & 0x10) == 0";
					$orderBy3 = "AND itemDescription NOT LIKE '%Socketed (%'";
				}
			}
			
			$orderBy4 = "";
			if (!empty($_POST["hasstat"])) {
				if (strlen($_POST["hasstat"]) > 0) {
					//$orderBy2 = "AND (\"itemFlag\" & 0x10) == 0x10";
					$orderBy4 = "AND itemDescription LIKE '%" . $_POST["hasstat"] . "%'";
				}
			}
			
			$orderBy5 = "";
			if(!empty($_POST['maxlevel'])) {
				if (strlen($_POST["maxlevel"]) > 0) {
					//$orderBy2 = "AND (\"itemFlag\" & 0x10) == 0x10";
					$ml = "level: ([1-9]|1[0-8])<";
					if($_POST["maxlevel"] == 18)
						$ml = "level: ([1-9]|1[0-8])<";
					elseif($_POST["maxlevel"] == 24)
						$ml = "level: ([1-9]|1[0-9]|2[0-4])<";
					elseif($_POST["maxlevel"] == 30)
						$ml = "level: ([1-9]|1[0-9]|2[0-9]|30)<";
					elseif($_POST["maxlevel"] == 40)
						$ml = "level: ([1-9]|1[0-9]|2[0-9]|3[0-9]|40)<";
					elseif($_POST["maxlevel"] == 50)
						$ml = "level: ([1-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|50)<";
					
					$orderBy5 = "AND '" . $ml . "' REGEXP lower(itemDescription)";
				}
			}

			//$limit = "ORDER BY itemType DESC LIMIT ".$_POST["itemlimit"];

			$limit = "LIMIT ".$_POST["itemlimit"];
			
			$colorF = "";
			if (!empty($_POST["colorIt"]) AND !empty($_POST["itemtype"]) AND $_POST["itemtype"] == "rare") {
				$colorIdx = array_search($_POST["colorIt"], $inGameColor);
				$colorF = "AND itemColor == ".$colorIdx."";
			}
			
			$query = /** @lang text */
				'SELECT '.$selectinfo.' FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountID WHERE accountRealm = '.$queryR.' '.$tempA.' '.$tempB.' '.$tempC.' '.$tempD.' '.$colorF.' '.$filterBy.' '.$filterBy2.' '.$orderBy.' '.$orderBy2.' '.$orderBy3.' '.$orderBy4.' '.$orderBy5.' '.$limit.';';
			
			//die($query);
			$results = $conn->query($query);
			if($results) {
				$conn = NULL;
			
				$count = $results->fetchAll(PDO::FETCH_ASSOC);
			
				return $count;
			}
			return false;
			
		} catch(PDOException $e) {
			$conn = NULL;
			print 'Exception : '.$e->getMessage();
			return false;
		}	
	}
}

function checkStat($itemid, $what) {
	try {
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
		
		$query = /** @lang text */
			'SELECT statsValue FROM muleItemsStats WHERE statsItemId = "'.$itemid.'" AND statsName = "'.$what.'"';

		$results = $conn->query($query);
		
		$conn = NULL;
		
		$count = $results->fetchAll(PDO::FETCH_ASSOC);
		
		$ed	= "";
		
		if(isset($count[0]["statsValue"])){
			$ed = $count[0]["statsValue"];
		}
		
		return $ed;
		
	} catch(PDOException $e) {
		$conn = NULL;
		print 'Exception : '.$e->getMessage();
		return false;
	}
}

function getCurrentName() {
	if (!isset($_POST["search"])) {		
		try {
			global $charsIds;
			
			if(isset($_GET["charid"])) {
				$charid = $_GET["charid"];
			}
			
			if(isset($charsIds[0]) AND !isset($_GET["charid"])) {
				$charid = $charsIds[0];
			}
			
			$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
			
			$query = /** @lang text */
				'SELECT charName FROM muleChars WHERE charId = '.$charid;

			$results = $conn->query($query);
			
			$conn = NULL;
			
			$count = $results->fetchAll(PDO::FETCH_ASSOC);
			
			$name	= "";
			
			if(isset($count[0]["charName"])){
				$name = $count[0]["charName"];
			}
			
			return $name;
			
		} catch(PDOException $e) {
			$conn = NULL;
			print 'Exception : '.$e->getMessage();
			return false;
		}	
	} else {
		$name = "Items";// "search results";
		return $name;
	}
}

?>