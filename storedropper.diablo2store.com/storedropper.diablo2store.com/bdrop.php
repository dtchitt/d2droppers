<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
require 'functions.php';
require 'config.php';
checkUserAuth();
$themeName = getTheme($currUser);

function Runes($queryR, $queryHC, $queryLD, $queryEXP, $itemId) {
	try {
		$conn = new PDO('sqlite:ItemDB.s3db') or die("Unable to connect");

		$tempA	= " AND charHardcore = ".$queryHC;
		$tempB	= " AND charExpansion = ".$queryEXP;
		$tempC	= " AND charLadder = ".$queryLD;
		$tempD	= " AND itemClassid = ".$itemId;
		
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

$pageTitle = "Diablo 2 Store - Gem List";
require "myTheme/header.php";
?>

    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<div class="col-md-12 form-group bDropForm">
				<h1>Bulk Drop Tool</h1>
			</div>
		</div>
		<?php if(@$_POST['submit'] == "Drop Items") {
			echo "<pre>";
			var_dump(json_decode(urldecode($_POST['itemInfo'])));
			echo "</pre>";
		}
		elseif(@$_POST['submit'] != "Find Item") { ?>
		<div class="row">
			<form method="POST">
				<div class="col-md-3 form-group bDropForm">
					Item: <select name="optItem">
						<option>Zod Rune</option>
						<option>Cham Rune</option>
						<option>Jah Rune</option>
						<option>Ber Rune</option>
						<option>Sur Rune</option>
						<option>Lo Rune</option>
						<option>Ohm Rune</option>
						<option>Vex Rune</option>
						<option>Gul Rune</option>
						<option>Ist Rune</option>
						<option>Mal Rune</option>
						<option>Um Rune</option>
						<option>Pul Rune</option>
						<option>Lem Rune</option>
						<option>Fal Rune</option>
						<option>Ko Rune</option>
						<option>Lum Rune</option>
						<option>Io Rune</option>
						<option>Hel Rune</option>
						<option>Dol Rune</option>
						<option>Shael Rune</option>
						<option>Sol Rune</option>
						<option>Amn Rune</option>
						<option>Thul Rune</option>
						<option>Ort Rune</option>
						<option>Ral Rune</option>
						<option>Tal Rune</option>
						<option>Ith Rune</option>
						<option>Eth Rune</option>
						<option>Nef Rune</option>
						<option>Tir Rune</option>
						<option>Eld Rune</option>
						<option>El Rune</option>
						<option>The Stone of Jordan</option>
					</select>
				</div>	
				<div class="col-md-3 form-group bDropForm">
					To Find: <input type="text" name="number" value="0">
				</div>
				<div class="col-md-4 form-group bDropForm">
					<input type="submit" name="submit" value="Find Item">
				</div>
			</form>
		</div>
		<?php } else { ?>
		
        <div class="row">
			<?php
				$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");
					
				$itemChars = [];
				
				$queryR 	= $_GET["realm"];
				$queryHC	= $_GET["hc"];
				$queryLD	= $_GET["ladder"];
				$queryEXP	= $_GET["exp"];
				$tempA	= " charHardcore = ".$queryHC;
				$tempB 	= " AND charExpansion = ".$queryEXP;
				$tempC 	= " AND charLadder = ".$queryLD;
				$select	= "charId, charName, charClassId";
				
				$total = 0;
				
				$query = 'SELECT '.$select.' FROM muleChars WHERE '.$tempA.' '.$tempB.' '.$tempC.' ';
				$results = $conn->query($query);
				$chars = $results->fetchAll(PDO::FETCH_ASSOC);
				foreach ($chars as $nr => $char) {
					$cID = $char['charId'];
					$cQuery = 'SELECT COUNT() AS "count" FROM muleItems WHERE itemName = "' . $_POST['optItem'] . '" AND itemCharId = '.$cID;
					$resultsA = $conn->query($cQuery);
					if($resultsA) {
						$count = $resultsA->fetchAll(PDO::FETCH_ASSOC);
						if($count[0]["count"] > 0) {
							$itemChars[$cID] = $count[0]["count"];
							$total = $total + $count[0]["count"];
						}
					}
				}
				
				$charsToDrop = [];
				
				if($total >= $_POST['number']) {
					$cCount = 0;
					while($cCount < $_POST['number']) {
						$value = max($itemChars);
						$key = array_search($value, $itemChars);
						unset($itemChars[$key]);
						if($cCount + $value > $_POST['number']) {
							$value = $_POST['number'] - $cCount;
						}
						$charsToDrop[] = [$key, $value];
						$cCount = $cCount + $value;
					}
					
					echo '<pre>';
					var_dump($charsToDrop);
					echo "Can be completed in [" . count($charsToDrop) . "] drops for [" . $_POST['number'] . "] items.<br>";
					echo '</pre>';
					?>
					<div class="row">
						<form method="POST">
							<input type="hidden" name="itemInfo" value="<?php echo urlencode(json_encode($charsToDrop)); ?>">
							<input type="hidden" name="optItem" value="<?php echo $_POST['optItem']; ?>">
							<div class="col-md-3 form-group bDropForm">
								Game Name: <input type="text" name="gameName">
							</div>	
							<div class="col-md-3 form-group bDropForm">
								Game Pass: <input type="text" name="gamePass">
							</div>
							<div class="col-md-4 form-group bDropForm">
								<input type="submit" name="submit" value="Drop Items">
							</div>
						</form>
					</div>
					<?php
				}
				else {
					echo "<pre>There is not enough of this item.</pre>";
				}
			?>
        </div>
		<?php } ?>
        <!-- /.row -->
		
		<div class="row">
			<div class="panel panel-default text-center">
				<div class="panel-footer niceBlueColor">
					Diablo 2 Store (<?php print $version; ?>)
					<?php
						$time = microtime();
						$time = explode(' ', $time);
						$time = $time[1] + $time[0];
						$finish = $time;
						$total_time = round(($finish - $start), 4);
						echo '<br />Page loaded in '.$total_time.' seconds.';
					?>
				</div>
			</div>
		</div>
        <!-- /.row -->
		
    </div>
    <!-- /.container -->
	
<?php require "myTheme/footer.php"; ?>