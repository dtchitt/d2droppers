<?php

require "config.php";
require "inc/auth.php";
require "inc/realm.php";

checkAuth();
checkRealm();

$name = urldecode($_GET['name']);
$sql = "SELECT * FROM d2_full_items WHERE fi_name = '" . mysqli_real_escape_string($mysqli, $name) . "' LIMIT 0,1";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$ias = "https://schema.org/InStock";
if($row['fi_count'] === 0)
	$ias = "https://schema.org/OutOfStock";

$page = [];
$page['title'] = "Diablo 2 Items - " . $row['fi_name'];
$page['desc'] = "Cheap Diablo 2 " . $row['fi_name'] . " in stock and ready for you!";
$page['jsonld'] = '<script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "Product", 
  "name": "' . $row['fi_name'] . '",
  "image": "https://gameservice.online/images/items/'.$row['fi_img'].'",
  "description": "' . str_replace("<br>", "", $row["fi_descript"]) . '",
  "offers": {
    "@type": "Offer",
    "url": "https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '",
    "priceCurrency": "USD",
    "price": "'. $row['fi_price_usd'] .'",
    "availability": "' . $ias . '",
    "itemCondition": "https://schema.org/NewCondition"
  }
}
</script>';

require "theme/header.php";

?>
	
	<h1 class="colorWhite">Viewing Item: <?php echo $row['fi_name']; ?></h1>
	
	<div class="row">
		
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="singleitemInner singleItemLeft">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<b>Item Information</b>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<b>Item Name:</b>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<?php echo $row['fi_name']; ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<b>Quality:</b>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<?php echo $row['fi_type']; ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<b>Item Group:</b>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<?php echo $row['fi_group']; ?>
					</div>
				</div>
			</div>
			
			<div class="singleitemInner singleItemLeft">
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<b>Price Per Item:</b>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						$<?php echo $row['fi_price_usd']; ?>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<b>Total:</b>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						$<span class="totalPrice"><?php echo $row['fi_price_usd']; ?></span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						Quantity: <input type="number" class="qtyCounter" id="quantity" name="quantity" min="1" max="<?php echo $row['fi_count']; ?>" value="1">
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<?php
						if($_SESSION['realm'] == "east") {
							if($row['fi_count'] > 0)
								echo '<button type="button" class="btn btn-outline-success">Purchase Item</button>';
							else
								echo '<b>Out of Stock</b>';
						} elseif($_SESSION['realm'] == "west") {
							if($row['fi_count_west'] > 0)
								echo '<button type="button" class="btn btn-outline-success">Purchase Item</button>';
							else
								echo '<b>Out of Stock</b>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 singleItemBox">
			<div class="singleitemInner">
				<img src="/images/items/<?php echo $row['fi_img']; ?>" alt="<?php echo $row['fi_name']; ?>"><br>
				<span class="title"><?php echo $row['fi_name']; ?></span>
				<p class="itemHomepageDescription"><?php echo $row['fi_descript']; ?></p>
			</div>
		</div>
		
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3 class="colorWhite">Items Often Purchased With This One</h3>
		</div>
	</div>
	
	<div class="row">
	<?php
	$sql = "SELECT * FROM d2_full_items WHERE fi_count > 0 AND fi_type = '" . $row['fi_type'] . "' AND fi_group = '" . $row['fi_group'] . "' AND fi_id != '" . $row['fi_id'] . "' ORDER BY RAND() LIMIT 0,4"; //East
	if($_SESSION['realm'] == "west")
		$sql = "SELECT * FROM d2_full_items WHERE fi_count_west > 0 AND fi_type = '" . $row['fi_type'] . "' AND fi_group = '" . $row['fi_group'] . "' AND fi_id != '" . $row['fi_id'] . "' ORDER BY RAND() LIMIT 0,4"; //West
	
	$result = $mysqli->query($sql);
	$count = 1;
	while($rowa = $result->fetch_assoc()) {
	?>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 homepageItem">
		<div class="imgBox">
			<a href="/diablo-2-item/<?php echo urlencode($rowa['fi_name']); ?>.html">
				<img src="/images/items/<?php echo $rowa['fi_img']; ?>" alt="<?php echo $rowa['fi_name']; ?>">
				<span class="title"><?php echo $rowa['fi_name']; ?> <span class="price">$<?php echo $rowa['fi_price_usd']; ?></span></span>
			</a>
			<p class="itemHomepageDescription"><?php echo $rowa['fi_descript']; ?></p>
		</div>
		<p class="itemHomepageDescription"><button type="button" class="btn btn-outline-success purchaseItemButton" data-id="<?php echo $rowa['fi_id']; ?>">Purchase Item</button></p>
	</div>
	<?php } ?>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3 class="colorWhite">Similar Items</h3>
		</div>
	</div>
	
	<div class="row">
	<?php
	$sql = "SELECT * FROM d2_full_items WHERE fi_count > 0 AND fi_group = '" . $row['fi_group'] . "' AND fi_id != '" . $row['fi_id'] . "' ORDER BY RAND() LIMIT 0,4"; //East
	if($_SESSION['realm'] == "west")
		$sql = "SELECT * FROM d2_full_items WHERE fi_count_west > 0 AND fi_group = '" . $row['fi_group'] . "' AND fi_id != '" . $row['fi_id'] . "' ORDER BY RAND() LIMIT 0,4"; //West
	$result = $mysqli->query($sql);
	$count = 1;
	while($rowa = $result->fetch_assoc()) {
	?>
	<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 homepageItem">
		<div class="imgBox">
			<a href="/diablo-2-item/<?php echo urlencode($rowa['fi_name']); ?>.html">
				<img src="/images/items/<?php echo $rowa['fi_img']; ?>" alt="<?php echo $rowa['fi_name']; ?>">
				<span class="title"><?php echo $rowa['fi_name']; ?> <span class="price">$<?php echo $rowa['fi_price_usd']; ?></span></span>
			</a>
			<p class="itemHomepageDescription"><?php echo $rowa['fi_descript']; ?></p>
		</div>
		<p class="itemHomepageDescription"><button type="button" class="btn btn-outline-success purchaseItemButton" data-id="<?php echo $rowa['fi_id']; ?>">Purchase Item</button></p>
	</div>
	<?php } ?>
	</div>
	
<?php

require "theme/footer.php";