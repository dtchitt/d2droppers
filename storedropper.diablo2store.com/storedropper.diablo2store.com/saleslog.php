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

$pageTitle = "Diablo 2 Store - Sale Log";
require "myTheme/header.php";
?>
    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<table id="myCustomTable">
				<tr>
					<td>When</td>
					<td>Seller</td>
					<td>Sale Amount</td>
					<td>Items Sold</td>
					<td>Items</td>
				</tr>
			<?php
			$totalFG = 0;
			$totalItems = 0;
			$salesQ = $mysqli->query("SELECT * FROM salelog WHERE sl_by='" . $mysqli->real_escape_string($_SESSION['userInfo']['uname']) . "' ORDER BY sl_id DESC");
			while($sale = $salesQ->fetch_assoc()) {
				$si = str_replace("|", ", ", $sale['sl_items']);
				echo '<tr><td>' . $sale['sl_on'] . '</td><td>'. $sale['sl_by'] . '</td><td>' . $sale['sl_amount'] . ' FG</td><td>' . $sale['sl_item'] . '</td><td>'. $si .'</td></tr>';
				$totalFG = $totalFG + $sale['sl_amount'];
				$totalItems = $totalItems + $sale['sl_item'];
			}
			echo '<tr><td></td><td></td><td>Total: ' . $totalFG . '</td><td>Total: ' . $totalItems . '</td><td></td></tr>'
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
