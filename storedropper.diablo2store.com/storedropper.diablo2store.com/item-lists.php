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

$pageTitle = "Diablo 2 Store - Add User";
require "myTheme/header.php";

?>

<div class="container">
	<div class="row">
		<div class="panel-heading"><h2 class="panel-title text-center">Item Lists</h2></div>
		<table class="table table-hover diablo predefTable">
			<thead>
				<tr>
					<th width="40%" class="text-left">
						Name
					</th>
					<th width="30%" class="text-left">
						Code
					</th>
					<th width="30%" class="text-left">
						Actions
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$groupq = $mysqli->query("SELECT * FROM item_lists WHERE il_store_id='".$_SESSION['storeID']."' LIMIT 0,500");
					while($row = mysqli_fetch_assoc($groupq)) {
						echo "<tr><td>" . $row['il_name'] . "</td><td>" . $row['il_code'] . "</td><td><a href=\"item-groups.php?li=" . $row['il_id'] . "\">View</a> - Get Link</td></tr>";
					}
				?>
			</tbody>
		</table>
	</div>
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

<?php require "myTheme/footer.php"; ?>