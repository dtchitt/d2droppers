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

$groupq = $mysqli->query("SELECT * FROM item_groups WHERE ig_store_id='".$_SESSION['storeID']."' AND ig_id='".$id."' LIMIT 0,1");
$group = mysqli_fetch_assoc($groupq);

$listq = $mysqli->query("SELECT * FROM item_lists WHERE il_id='" . $group['ig_il_id'] . "' LIMIT 0,1");
$list = mysqli_fetch_assoc($listq);

$storeQ = $mysqli->query("SELECT * FROM stores WHERE sid='" . $mysqli->real_escape_string($_SESSION['storeID']) . "' LIMIT 0,1");
$store = $storeQ->fetch_assoc();

?>

<div class="container">
	<div class="row">
		<div class="panel-heading"><h2 class="panel-title text-center"><?php echo $group['ig_name']; ?> Group</h2></div>
		<?php
		if($group['ig_item_simple'] == 1) {
			?>
			<form method="POST">
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="itemtype" class="searchFormLabel">Short Code:</label>
						<input type="text" id="itemtype" class="form-control" name="shortCode" value="<?php echo $group['ig_short']; ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="itemtype" class="searchFormLabel">Price (FG):</label>
						<input type="text" id="itemtype" class="form-control" name="priceFG" value="<?php echo $group['ig_price_fg']; ?>">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<input type="submit" class="btn btn-default" value="Update Item">
					</div>
				</div>
			</form>
			
			<div class="panel-heading"><h2 class="panel-title text-center">Matching Items</h2></div>
			<table class="table table-hover diablo predefTable">
				<thead>
					<tr>
						<th width="30%" class="text-left">
							Name
						</th>
						<th width="70%" class="text-left">
							Description
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(file_exists('../d2storesItemDBs/'.$store['sid'].'ItemDB.s3db')) {
						$conn = new PDO('sqlite:../d2storesItemDBs/'.$store['sid'].'ItemDB.s3db') or die("Unable to connect");
						$tempA	= " AND charHardcore = ".$list['il_hc'];
						$tempB 	= " AND charExpansion = ".$list['il_exp'];
						$tempC 	= " AND charLadder = ".$list['il_lad'];
						$tempD  = " AND itemName = \"" . $group['ig_name'] . "\"";
						$sql = /** @lang text */
							'SELECT * FROM muleItems LEFT JOIN muleChars ON itemCharId = charId LEFT JOIN muleAccounts ON charAccountId = accountId WHERE accountRealm = '.$list['il_realm'].' '.$tempA.' '.$tempB.' '.$tempC.' '.$tempD;
						$results = $conn->query($sql);
						if(!$results){
							echo $sql ."<br>";
							print_r($conn->errorInfo());
							exit;
						}
						$itemsDB = $results->fetchAll(PDO::FETCH_ASSOC);
						$f = 0;
						foreach($itemsDB as $item) {
							if(strtolower(stripslashes($item['itemName'])) == strtolower(stripslashes($group['ig_name']))){
								$foundItem[] = $item;
								$f++;
								echo "<tr><td>" . $item['itemName'] . "</td><td>" . $item['itemDescription'] . "</td><tr>";
							}
						}
					}
					?>
				</tbody>
			</table>
			<?php
		} else {
			
		}
		?>
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