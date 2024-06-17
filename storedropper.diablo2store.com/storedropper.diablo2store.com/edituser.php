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

$pageTitle = "Diablo 2 Store - Edit User";
require "myTheme/header.php";
?>
    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<h2 class="niceBlueColor">Edit User</h2>
			<form method="POST">
				<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="itemtype" class="searchFormLabel">Roll:</label>
						<select id="itemtype" class="form-control" name="newRoll">
							<option>view</option>
							<option>drop</option>
							<option>admin</option>
						</select>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<input type="submit" class="btn btn-default" value="Create New User">
					</div>
				</div>
			</form>
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
