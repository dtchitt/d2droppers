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

$pageTitle = "Diablo 2 Store - Gem List";
require "myTheme/header.php";
?>

    <!-- Page Content -->
    <div class="container">
        <div class="row">
			<?php
				$modes = array("SOFTCORE", "HARDCORE");
				print '<div class="panel-heading"><h2 class="panel-title text-center">Gems ('.$modes[0].')</h2></div>';
				print '<table class="table table-hover diablo predefTable">';
				print '<thead><tr>';
				print '<th width="30%" class="text-left exocet"> </th>';
				print '<th width="*" class="text-center exocet"><strong>East Ladder</strong></th>';
				print '</tr></thead>';
				
				$array = array(
					"<img src=\"images/items/gsva.png\"> Amethyst",
                    "<img src=\"images/items/gsvb.png\"> Amethyst",
                    "<img src=\"images/items/gsvc.png\"> Amethyst",
                    "<img src=\"images/items/gsvd.png\"> Amethyst",
                    "<img src=\"images/items/gsve.png\"> Amethyst",

					"<img src=\"images/items/gsya.png\"> Topaz",
                    "<img src=\"images/items/gsyb.png\"> Topaz",
                    "<img src=\"images/items/gsyc.png\"> Topaz",
                    "<img src=\"images/items/gsyd.png\"> Topaz",
                    "<img src=\"images/items/gsye.png\"> Topaz",

					"<img src=\"images/items/gsba.png\"> Sapphire",
                    "<img src=\"images/items/gsbb.png\"> Sapphire",
                    "<img src=\"images/items/gsbc.png\"> Sapphire",
                    "<img src=\"images/items/gsbd.png\"> Sapphire",
                    "<img src=\"images/items/gsbe.png\"> Sapphire",

					"<img src=\"images/items/gsga.png\"> Emerald",
                    "<img src=\"images/items/gsgb.png\"> Emerald",
                    "<img src=\"images/items/gsgc.png\"> Emerald",
                    "<img src=\"images/items/gsgd.png\"> Emerald",
                    "<img src=\"images/items/gsge.png\"> Emerald",

					"<img src=\"images/items/gsra.png\"> Ruby",
                    "<img src=\"images/items/gsrb.png\"> Ruby",
                    "<img src=\"images/items/gsrc.png\"> Ruby",
                    "<img src=\"images/items/gsrd.png\"> Ruby",
                    "<img src=\"images/items/gsre.png\"> Ruby",

					"<img src=\"images/items/gswa.png\"> Diamond",
                    "<img src=\"images/items/gswb.png\"> Diamond",
                    "<img src=\"images/items/gswc.png\"> Diamond",
                    "<img src=\"images/items/gswd.png\"> Diamond",
                    "<img src=\"images/items/gswe.png\"> Diamond"
				);
				$arg = $_GET['a'];
				$exp = $_GET['e'];
				
				for ($y = 0; $y < count($array); $y++) {
					$nx = $y + 1;
					if($nx < 10)
						$nx = "0".$nx;
					print '<td width="30%" class="text-left">'.$array[$y].'</td><td width="*" class="text-center niceBlueColor">';
					print Runes(1, $arg, 1, $exp, 557 + $y);
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