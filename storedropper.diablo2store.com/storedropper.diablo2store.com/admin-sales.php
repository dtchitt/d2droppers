<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
require 'functions.php';
require 'config.php';
checkUserAuth(true);
$themeName = getTheme($currUser);

$pageTitle = "Diablo 2 Store - Sale Stats";
require "myTheme/header.php";


$userStoreQ = $mysqli->query("SELECT * FROM store_user WHERE su_roll='admin' AND su_store='".$_SESSION['storeID']."' ORDER BY su_user");
?>
<div class="container">
	<table id="myCustomTable">
		<tr>
			<td>Name</td>
			<td>Life Total Owed</td>
			<td>Owed</td>
			<td>Most Sold</td>
			<td>Total Items Sold</td>
			<td>Notices</td>
		</tr>
<?php
while($userStore = $userStoreQ->fetch_assoc()) {
	$userInfo = $mysqli->query("SELECT * FROM users WHERE uid='". $mysqli->real_escape_string($userStore['su_user']) . "' LIMIT 0,1");
	$user = $userInfo->fetch_assoc();
	
	$soldItems = [];
	$ti = 0;
	
	$infoMsgs = [];
	$tSales = 0;
	$oneFgCost = 0;
	$totalFG = 0;
	
	$salesQ = $mysqli->query("SELECT * FROM salelog WHERE sl_by='" . $mysqli->real_escape_string($user['uname']) . "' ORDER BY sl_id DESC");
	while($sale = $salesQ->fetch_assoc()) {
		//print_r($sale);
		if (strpos($sale['sl_items'], '|') !== false) {
			$broke = explode("|", $sale['sl_items']);
			foreach($broke as $piece) {
				if(!array_key_exists(trim($piece), $soldItems))
					$soldItems[$piece] = 1;
				else
					$soldItems[$piece]++;
				$ti++;
			}
		} else {
			if(!array_key_exists(trim($sale['sl_items']), $soldItems))
				$soldItems[trim($sale['sl_items'])] = 1;
			else
				$soldItems[trim($sale['sl_items'])]++;
			$ti++;
		}
		
		if($sale['sl_amount'] == 1)
			$oneFgCost++;
		$totalFG = $totalFG + $sale['sl_amount'];
		$tSales++;
	}
	
	$oweddiff = ($userStore['su_cut'] / 100) * $totalFG;
	
	$diff = (($tSales - $oneFgCost) / ($tSales)) * 100;
	if($diff > 90)
		$infoMsgs[] = "Over 90% of sales are 1fg. Scam?";
	
	if(count($soldItems) == 0) {
		$soldItems['None'] = 0;
		$infoMsgs[] = "No items sold";
	}
	?>
	<tr>
		<td><?php echo $user['uname']; ?></td>
		<td><?php echo $oweddiff; ?> fg</td>
		<td><?php echo $oweddiff - $userStore['su_paid']; ?> fg</td>
		<td><?php echo array_search(max($soldItems),$soldItems); ?></td>
		<td><?php echo $ti; ?></td>
		<td><?php
		if(count($infoMsgs) > 0) {
			echo "<ul>";
			foreach($infoMsgs as $msg){
				echo "<li>" . $msg . "</li>";
			}
			echo "</ul>";
		} else {
			echo "Nothing To Report";
		}
		?></td>
	</tr>
	<?php
}
?>
	</table>
</div>
        <!-- /.row -->
		<br><br><br><br><br><br>
		<div class="row">
			<div class="panel panel-default text-center">
				<div class="panel-footer niceBlueColor">
					Diablo 2 Store (<a href="/changelog.php"><?php print $version; ?></a>)<br>
					<?php
						pageTimer($start);
					?>
				</div>
			</div>
		</div>
        <!-- /.row -->
		
    </div>
    <!-- /.container -->
	
<?php require "myTheme/footer.php"; ?>
