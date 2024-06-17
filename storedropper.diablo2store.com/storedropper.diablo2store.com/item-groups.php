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

$id = $_GET['li'];

?>

<div class="container">
	<div class="row">
		<div class="panel-heading"><h2 class="panel-title text-center">Item Groups</h2></div>
		
		<a href="add-item-group.php?li=<?php echo $id; ?>">Add Item Group</a>
		
		<table class="table table-hover diablo predefTable">
			<thead>
				<tr>
					<th width="20%" class="text-left">
						Name
					</th>
					<th width="20%" class="text-left">
						Code
					</th>
					<th width="20%" class="text-left">
						Price
					</th>
					<th width="20%" class="text-left">
						Is Simple?
					</th>
					<th width="20%" class="text-left">
						Actions
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$groupq = $mysqli->query("SELECT * FROM item_groups WHERE ig_store_id='".$_SESSION['storeID']."' AND ig_il_id='".$id."' LIMIT 0,500");
					while($row = mysqli_fetch_assoc($groupq)) {
						$simple = "Yes";
						if($row['ig_item_simple'] == 0)
							$simple = "No";
						echo "<tr><td>" . $row['ig_name'] . "</td><td>" . $row['ig_short'] . "</td><td>" . $row['ig_price_fg'] . "</td><td>" . $simple . "</td><td><a href=\"item-group.php?li=" . $row['ig_id'] . "\">View</a> - Get Link</td></tr>";
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