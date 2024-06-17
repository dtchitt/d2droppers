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
		$conn = new PDO('sqlite:../d2storesItemDBs/'.$_SESSION['storeID'].'ItemDB.s3db') or die("Unable to connect");

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

$pageTitle = "Diablo 2 Store - Rune List";
require "myTheme/header.php";
?>

    <!-- Page Content -->
    <div class="container">
        <div class="row">
			<?php
				$modes = array("SOFTCORE", "HARDCORE");
				print '<div class="panel-heading"><h2 class="panel-title text-center">Runes ('.$modes[0].')</h2></div>';
				print '<table class="table table-hover diablo predefTable">';
				print '<thead><tr>';
				print '<th width="30%" class="text-left exocet"> </th>';
				print '<th width="*" class="text-center exocet"><strong>East Ladder</strong></th>';
				print '</tr></thead>';
				$array = array("El", "Eld", "Tir", "Nef", "Eth", "Ith", "Tal", "Ral", "Ort", "Thul", "Amn", "Sol", "Shael", "Dol", "Hel", "Io", "Lum", "Ko", "Fal", "Lem", "Pul", "Um", "Mal", "Ist", "Gul", "Vex", "Ohm", "Lo", "Sur", "Ber", "Jah", "Cham", "Zod");
				
				$arg = $_GET['a'];
				$exp = $_GET['e'];
				
				for ($y = 0; $y < count($array); $y++) {
					$nx = $y + 1;
					if($nx < 10)
						$nx = "0".$nx;
					print '<tr><td width="30%" class="text-left rune"><img src="images/items/r'.$nx.'.png"> '.$array[$y].'</td><td class="niceBlueColor">';
					print Runes(1, $arg, 1, $exp, 610 + $y);
					print '</td></tr>';
				}
				
				print '</table>';
			?>
        </div>
		
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