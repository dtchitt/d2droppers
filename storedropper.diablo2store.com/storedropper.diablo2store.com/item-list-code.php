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

$pageTitle = "Diablo 2 Store - JSP Code";
require "myTheme/header.php";

$id = $_GET['li'];

?>

<div class="container">
	<div class="row">
		<div class="panel-heading"><h2 class="panel-title text-center">JSP Code</h2></div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<textarea>
[CENTER][COLOR=red][SIZE=14]Please make sure to refresh the page (Ctrl + F5) to make sure the image is updated![/SIZE][/COLOR][/CENTER]

[CENTER][IMG]https://img.diablo2store.com/escl-charms.png[/IMG][/CENTER]

[CENTER][SIZE=10]If you have any questions feel free to post or PM me.

Copy-able message format: BUY/ESCL/ITEM CODE/GAME NAME/GAME PASSWORD[/SIZE][/CENTER]

[SIZE=1]KEYWORDS[/SIZE]
			</textarea>
		</div>
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