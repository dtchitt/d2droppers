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

//if(@$_SESSION['userInfo']['uname'] !== "azero")
//	die("<html><body><center>I think my proxy provider changed something, I will have to try and find a new one tonight. If anyone knows a good provider let me know, dropper rentals will be paused until fixed.</center></body></html>");

if(!(strlen(@$_SESSION['storeID']) > 0)) {
	header("Location: /pickstore.php");
	exit;
}

$pageTitle = "Diablo 2 Store - Item Database";
require "myTheme/header.php";
?>


    <!-- Page Content -->
    <div class="container">
        <div class="row">
			<?php
			if(!isset($showAccounts)) {
				$showAccounts = true;
			}
			if ($showAccounts == true) {?>
				<div class="col-md-12 form-group">
					<?php 
						if(getRealmCount()) {
							//	accounts
							print '<div class="col-md-3">';
								print '<div class="panel panel-default">';
									print '<div class="panel-heading">';
										print '<h1 class="panel-title niceBlueColor">Accounts</h1>';
									print '</div>';
									getAccounts();
								print '</div>';
							print '</div>';
							
							// items
							print '<div class="col-md-9">';
								print '<div class="panel panel-default" id="itemsoutput">';
									showCurrentItems();
								print '</div>';
							print '</div>';
						}
					?>
				</div>
			<?php 
			} else {
				// dont show accounts.
				if(getRealmCount()) {
					print '<div class="panel panel-default" id="itemsoutput">';
						print '<div class="panel-heading">';
							print '<h1 class="panel-title">Helpful Information</h1>';
						print '</div>';
						?>
							<!--<span class='top' style="margin-left:7px">Search Help<br></span>-->
							<span class='top' style="margin-left:7px"><b>Example:</b> You can use <u>jordan,ring</u> to search for all items with "jordan" and "ring". To search for all rings besides sojs try "ring,!stone of jordan".<br></span>
							<span class='top' style="margin-left:7px">Use search filters at the top to narrow down your search and find specific items.<br></span>
							<span class='top' style="margin-left:7px">Be careful when typeing in forum gold amounts and game information.<br></span>
						<?php
					print '</div>';
				}				
			}
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
