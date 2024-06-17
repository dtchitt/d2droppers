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

$pageTitle = "Diablo 2 Store - Item Group";
require "myTheme/header.php";

$id = $_GET['li'];

$listq = $mysqli->query("SELECT * FROM item_lists WHERE il_id='" . $id . "' LIMIT 0,1");
$list = mysqli_fetch_assoc($listq);

$storeQ = $mysqli->query("SELECT * FROM stores WHERE sid='" . $mysqli->real_escape_string($_SESSION['storeID']) . "' LIMIT 0,1");
$store = $storeQ->fetch_assoc();

?>

<div class="container">
	<?php
	if($_POST['step'] == 1) {
		if($_POST['groupSimple'] == "Yes") { //Simple items
			$groupq = $mysqli->query("SELECT * FROM item_groups WHERE ig_short='" . $mysqli->real_escape_string($_POST['groupShort']) . "' AND ig_il_id='" . $id . "' LIMIT 0,1");
			if($groupq && mysqli_num_rows($groupq) > 0) {
					?><div class="panel-heading"><h2 class="panel-title text-center">Short code already exists in this list!</h2></div><?php
			} else {
				$r = $mysqli->query("INSERT INTO item_groups SET ig_store_id='" . $mysqli->real_escape_string($_SESSION['storeID']) . "', ig_name='" . $mysqli->real_escape_string($_POST['groupName']) . "', ig_short='" . $mysqli->real_escape_string($_POST['groupShort']) . "', ig_price_fg='". $mysqli->real_escape_string($_POST['groupPriceFG']) . "', ig_item_simple='1', ig_il_id='". $id . "'");
				if($r) {
					?><div class="panel-heading"><h2 class="panel-title text-center">Item group created!</h2></div><?php
				} else {
					?><div class="panel-heading"><h2 class="panel-title text-center">Failed to make new item! Please try again later.</h2></div><?php
				}
			}
		} else { //Advanced item
			$groupq = $mysqli->query("SELECT * FROM item_groups WHERE ig_short='" . $mysqli->real_escape_string($_POST['groupShort']) . "' AND ig_il_id='" . $id . "' LIMIT 0,1");
			if($groupq && mysqli_num_rows($groupq) > 0) {
					?><div class="panel-heading"><h2 class="panel-title text-center">Short code already exists in this list!</h2></div><?php
			} else {
				$r = $mysqli->query("INSERT INTO item_groups SET ig_store_id='" . $mysqli->real_escape_string($_SESSION['storeID']) . "', ig_name='" . $mysqli->real_escape_string($_POST['groupName']) . "', ig_short='" . $mysqli->real_escape_string($_POST['groupShort']) . "', ig_price_fg='". $mysqli->real_escape_string($_POST['groupPriceFG']) . "', ig_item_simple='0', ig_il_id='". $id . "'");
				if($r) {
					?><div class="panel-heading"><h2 class="panel-title text-center">Advanced item group created!</h2></div><?php
				} else {
					?><div class="panel-heading"><h2 class="panel-title text-center">Failed to make new item! Please try again later.</h2></div><?php
				}
			}
		}
	} else {
	?>
	<div class="row">
		<div class="panel-heading"><h2 class="panel-title text-center">Add Item Group</h2></div>
		<form method="POST">
			<input type="hidden" name="step" value="1" />
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="itemtype" class="searchFormLabel">Name:</label>
					<input type="text" id="itemtype" class="form-control" name="groupName">
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="itemtype" class="searchFormLabel">Short Code:</label>
					<input type="text" id="itemtype" class="form-control" name="groupShort">
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="itemtype" class="searchFormLabel">Price (FG):</label>
					<input type="text" id="itemtype" class="form-control" name="groupPriceFG">
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="itemtype" class="searchFormLabel">Is Simple?</label>
					<select name="groupSimple">
						<option selected>Yes</option>
						<option>No</option>
					</select>
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-12">
					<input type="submit" class="btn btn-default" value="Add Group">
				</div>
			</div>
		</form>
	</div>
	<?php } ?>
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