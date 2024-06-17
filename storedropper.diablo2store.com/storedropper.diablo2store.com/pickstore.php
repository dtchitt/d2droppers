<?php

require 'functions.php';
require 'config.php';
checkUserAuth();

if(strlen(@$_GET['s']) > 0 && $_GET['s'] > 0) {
	$storeQ = $mysqli->query("SELECT * FROM stores WHERE sid='" . $mysqli->real_escape_string($_GET['s']) . "' LIMIT 0,1");
	if ($storeQ->num_rows === 0) {
		continue;
	} else {
		$store = $storeQ->fetch_assoc();
		$usQ = $mysqli->query("SELECT * FROM store_user WHERE su_user='" . $mysqli->real_escape_string($_SESSION['userInfo']['uid']) . "' AND  su_store='" . $mysqli->real_escape_string($_GET['s']) . "' LIMIT 0,1");
		if ($usQ->num_rows === 0) {
			continue;
		} else {
			$su = $usQ->fetch_assoc();
			$_SESSION['storeRoll'] = $su['su_roll'];
			$_SESSION['storeID'] = $store['sid'];
			$_SESSION['store'] = $store;
			header("Location: /index.php");
			exit;
		}
	}
}

?><html>
<head>
	<style>
		.loginBox {
			text-align:center;
			width: 300px;
			margin: auto;
			border: 3px solid green;
			padding: 10px;
		}
	</style>
</head>
<body>
	<div class="loginBox">
		<h2>Please Select A Store</h2>
		<ul>
			<?php
			$selq = $mysqli->query("SELECT * FROM store_user WHERE su_user='" . $mysqli->real_escape_string($_SESSION['userInfo']['uid']) . "'");
			while ($a = $selq->fetch_assoc()) {
				$storeq = $mysqli->query("SELECT * FROM stores WHERE sid='" . $mysqli->real_escape_string($a['su_store']) . "'");
				$store = $storeq->fetch_assoc();
				echo "<li><a href=\"pickstore.php?s=" . $a['su_store'] . "\">" . $store['sname'] . "</a></li>";
			}
			?>
		</ul>
	</div>
</body>
</html>