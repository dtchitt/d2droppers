<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
require 'functions.php';
require 'config.php';
checkUserAuth();
$themeName = getTheme($currUser);

$pageTitle = "Diablo 2 Store - Change Log";
require "myTheme/header.php";
?>


    <!-- Page Content -->
    <div class="container">
        <div class="row" style="color:#000;">
			<?php
			require "myTheme/changeLog.php";
			?>
        </div>
		
        <!-- /.row -->
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
    <!-- /.container -->
	
<?php require "myTheme/footer.php"; ?>
