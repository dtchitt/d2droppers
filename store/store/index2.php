<?php

require "config.php";
require "inc/auth.php";
require "inc/realm.php";

checkAuth();
checkRealm();

$page = [];
$page['title'] = "Diablo 2 Item Store - Buy Items Today";
$page['desc'] = "Buy 100% Legit Diablo 2 Items - Cheapest Around! Uniques, Rares, Crafted, and much more. Come check out our item deals today!";
$page['jsonld'] = '<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "WebSite",
  "name": "Game Service - Diablo 2 Items",
  "url": "https://gameservice.online/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://gameservice.online/search/{search_term_string}/",
    "query-input": "required name=search_term_string"
  }
}
</script>';

require "theme/header.php";

?>
	
	<h1 class="colorWhite">Game Service Online - Diablo 2 Item Store</h1>
	
	<div class="row">
		<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 aboutGameServicesBox">
			<img src="/images/star.png" style="max-width:34px; height: auto;"> Fast Delivery - Get your items delivered by mule account or streight into your game anytime day or night.
		</div>
		
		<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 aboutGameServicesBox">
			<img src="/images/star.png" style="max-width:34px; height: auto;"> Always Updated - All items are see are in stock, no waiting to find the item it is ready to be used right now so why wait?
		</div>
		
		<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 aboutGameServicesBox">
			<img src="/images/star.png" style="max-width:34px; height: auto;"> 100% Legit Items - With us you never have to wory about unperm or duped items, all our items are 100% self found.
		</div>
		
		<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 aboutGameServicesBox">
			<img src="/images/star.png" style="max-width:34px; height: auto;"> Competative Prices - We constantly keep our prices at the lowest in the industry, giving you the best bang for your buck.
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 realmOnly">
			<h3>We currently only support the Diablo 2 US East Ladder realm, we are stocking items in other realms but they are not ready yet!</h3>
		</div>
	</div>
	
	<img src="/images/d4barb.png" class="fullWide" alt="D2 Item Shop Banner">
	
	<h2 class="colorWhite">Great Deals on Diablo 2 Items</h3>
			
<?php
$sql = "SELECT * FROM d2_full_items WHERE fi_count > 0 ORDER BY RAND() LIMIT 0,12"; //East
if($_SESSION['realm'] == "west")
	$sql = "SELECT * FROM d2_full_items WHERE fi_count_west > 0 ORDER BY RAND() LIMIT 0,12"; //West
$result = $mysqli->query($sql);
$count = 1;
while($row = $result->fetch_assoc()) {
	if($count == 1) echo '<div class="row">';
	?>
	<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 homepageItem">
		<div class="imgBox">
			<a href="/diablo-2-item/<?php echo urlencode($row['fi_name']); ?>.html">
				<img src="/images/items/<?php echo $row['fi_img']; ?>" alt="<?php echo $row['fi_name']; ?>">
				<span class="title"><?php echo $row['fi_name']; ?> <span class="price">$<?php echo $row['fi_price_usd']; ?></span></span>
			</a>
			<p class="itemHomepageDescription"><?php echo $row['fi_descript']; ?></p>
		</div>
		<p class="itemHomepageDescription"><button type="button" class="btn btn-outline-success purchaseItemButton" data-id="<?php echo $row['fi_id']; ?>">Purchase Item</button></p>
	</div>
	<?php
	if($count == 4) {
		echo '</div>';
		$count = 1;
	} else {
		$count++;
	}
}

require "theme/footer.php";