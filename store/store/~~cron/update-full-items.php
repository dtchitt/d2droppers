<?php

set_time_limit(0);

$debug = false;
if($_GET['d'] === "yes") $debug = true;

require "../config.php";
require "../inc/discord.php";
require "../inc/item-alias.php";
require "../inc/pickit.php";

$lowStockStr = "Item(s) have low stock [";
$lowStock = [];

$totalDollarAmount = 0;

$realms = ["uswest", "useast"];
foreach($realms as $realm) {
	$sql = "SELECT * FROM d2_full_items";
	$result = $mysqli->query($sql);
	while($row = $result->fetch_assoc()) {
		if($debug) file_put_contents("log.txt", " \r\n", FILE_APPEND);
		if($debug) file_put_contents("log.txt", "Parsing Item [" . $row['fi_name'] . "]\r\n", FILE_APPEND);
		$fg = $row['fi_price_usd'] * $fgRatio;
		
		$jsp = round($fg - ($fg * ($jspDiscount/100)));
		
		$num = 0;
		if($debug) file_put_contents("log.txt", "Trying to parse items\r\n", FILE_APPEND);
		$fi = findItems($row['fi_line'], $realm);
		if($debug) file_put_contents("log.txt", "Parsed items.\r\n", FILE_APPEND);
		if($fi !== false) $num = count($fi);
		
		if($row['fi_price_fg'] !== $fg) {
			if($realm == "useast") {
				$mysqli->query("UPDATE d2_full_items SET fi_price_fg='" . $fg . "', fi_price_jsp='" . $jsp . "', fi_count='" . $num . "' WHERE fi_id = '" . $row['fi_id'] . "'");
			} elseif ($realm == "uswest") {
				$mysqli->query("UPDATE d2_full_items SET fi_price_fg='" . $fg . "', fi_price_jsp='" . $jsp . "', fi_count_west='" . $num . "' WHERE fi_id = '" . $row['fi_id'] . "'");
			}
		} else {
			if($realm == "useast") {
				$mysqli->query("UPDATE d2_full_items SET fi_count='" . $num . "' WHERE fi_id = '" . $row['fi_id'] . "'");
			} elseif($realm == "uswest") {
				$mysqli->query("UPDATE d2_full_items SET fi_count_west='" . $num . "' WHERE fi_id = '" . $row['fi_id'] . "'");
			}
		}
		
		if($num < 2) {
			$expTime = 3600 * 6;
			if($row['fi_last_auto_update'] === 0 || time() > $row['fi_last_auto_update'] + $expTime) {
				$lowStock[] = $row['fi_name'];
				$mysqli->query("UPDATE d2_full_items SET fi_last_auto_update='" . time() . "' WHERE fi_id = '" . $row['fi_id'] . "'");
			}
		}
		echo "<br>";
		
		$totalDollarAmount = $totalDollarAmount + ($num * $row['fi_price_usd']);
	}
}

$lowStockStr .= implode(", ", $lowStock) . "]!";

if(count($lowStock) > 0) {
	sendDiscordCurrentStore($lowStockStr);
} else {
	if(rand(1,20) === 1)
		sendDiscordCurrentStore("Total Item's Value [$" . $totalDollarAmount . "] (Only Added)");
}