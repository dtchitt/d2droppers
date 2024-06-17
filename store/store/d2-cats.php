<?php

require "config.php";
require "inc/auth.php";
require "inc/realm.php";

checkAuth();
checkRealm();

$tName = $_GET['name'];

$group = "Ring";
$multiple = "Rings";
if($tName === "ring") {
	$group = "Ring";
	$multiple = "Rings";
}
elseif($tName === "amulet") {
	$group = "Amulet";
	$multiple = "Amulets";
}
elseif($tName === "armor") {
	$group = "Armor";
	$multiple = "Armors";
}
elseif($tName === "belt") {
	$group = "Belt";
	$multiple = "Belts";
}

$page = [];
$page['title'] = "Unique Diablo 2 " . $multiple . " for Sale";
$page['desc'] = "Looking for great deals on unique diablo 2 " . $multiple . "? Come check out our great prices on 100% legit items.";

require "theme/header.php";

?>
	
	<h1 class="colorWhite">Diablo 2 <?php echo $multiple; ?></h1>
	
	<div class="row">
<?php
$sql = "SELECT * FROM d2_full_items WHERE fi_count > 0 AND fi_type = 'Unique' AND fi_group = '" . $group . "'"; //East
if($_SESSION['realm'] == "west")
	$sql = "SELECT * FROM d2_full_items WHERE fi_count_west > 0 AND fi_type = 'Unique' AND fi_group = '" . $group . "'"; //West
$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()) {
	?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="innerItemListBox">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 imageBox centerAll">
					<img src="/images/items/<?php echo $row['fi_img']; ?>" alt="<?php echo $row['fi_name']; ?>">
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 centerAll">
					<a href="/diablo-2-item/<?php echo urlencode($row['fi_name']); ?>.html">
						<?php echo $row['fi_name']; ?><br>
						<b>Price:</b> $<?php echo $row['fi_price_usd']; ?>
					</a><br>
					<button type="button" class="btn btn-outline-success purchaseItemButton" data-id="<?php echo $row['fi_id']; ?>">Purchase Item</button>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 centerAll">
					<?php echo $row['fi_descript']; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
	?></div><?php


require "theme/footer.php";