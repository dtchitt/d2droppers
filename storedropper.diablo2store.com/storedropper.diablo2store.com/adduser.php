<?php

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
require 'functions.php';
require 'config.php';
checkUserAuth(true);
$themeName = getTheme($currUser);

$pageTitle = "Diablo 2 Store - Add User";
require "myTheme/header.php";

var_dump($_POST);

if(strlen(@$_POST['t']) == 8 && strlen($_SESSION['tmpkey']) == 8) {
	if($_POST['t'] != $_SESSION['tmpkey']) { //ERROR
		$_SESSION['tmpkey'] = makeRandomString(8);
		?>
		<!-- Page Content -->
		<div class="container">
			<div class="row">
				<h2 class="niceBlueColor">Create A New User</h2>
				<form method="POST">
					<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
					<p>Error: Invalid form submission.</p>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Username:</label>
							<input type="text" id="itemtype" class="form-control" name="newUser" value="<?php echo strip_tags(trim($_POST['newUser'])); ?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Email:</label>
							<input type="text" id="itemtype" class="form-control" name="newEmail" value="<?php echo strip_tags(trim($_POST['newEmail'])); ?>">
						</div>
					</div>
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
	
		<?php 
		require "myTheme/footer.php";
		exit;
	}
	
	if(strlen($_POST['newUser']) < 5) {
		$_SESSION['tmpkey'] = makeRandomString(8);
		?>
		<!-- Page Content -->
		<div class="container">
			<div class="row">
				<h2 class="niceBlueColor">Create A New User</h2>
				<form method="POST">
					<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
					<p>Error: Username must be atleast 5 chars.</p>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Username:</label>
							<input type="text" id="itemtype" class="form-control" name="newUser" value="<?php echo strip_tags(trim($_POST['newUser'])); ?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Email:</label>
							<input type="text" id="itemtype" class="form-control" name="newEmail" value="<?php echo strip_tags(trim($_POST['newEmail'])); ?>">
						</div>
					</div>
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
	
		<?php 
		require "myTheme/footer.php";
		exit;
	}
	
	if(strlen($_POST['newUser']) > 32) {
		$_SESSION['tmpkey'] = makeRandomString(8);
		?>
		<!-- Page Content -->
		<div class="container">
			<div class="row">
				<h2 class="niceBlueColor">Create A New User</h2>
				<form method="POST">
					<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
					<p>Error: Username must be under 32 chars.</p>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Username:</label>
							<input type="text" id="itemtype" class="form-control" name="newUser" value="<?php echo strip_tags(trim($_POST['newUser'])); ?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Email:</label>
							<input type="text" id="itemtype" class="form-control" name="newEmail" value="<?php echo strip_tags(trim($_POST['newEmail'])); ?>">
						</div>
					</div>
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
	
		<?php 
		require "myTheme/footer.php";
		exit;
	}
	
	if(!filter_var(strip_tags(trim($_POST['newEmail'])), FILTER_VALIDATE_EMAIL)) {
		$_SESSION['tmpkey'] = makeRandomString(8);
		?>
		<!-- Page Content -->
		<div class="container">
			<div class="row">
				<h2 class="niceBlueColor">Create A New User</h2>
				<form method="POST">
					<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
					<p>Error: Email does not appear valid.</p>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Username:</label>
							<input type="text" id="itemtype" class="form-control" name="newUser" value="<?php echo strip_tags(trim($_POST['newUser'])); ?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Email:</label>
							<input type="text" id="itemtype" class="form-control" name="newEmail" value="<?php echo strip_tags(trim($_POST['newEmail'])); ?>">
						</div>
					</div>
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
	
		<?php 
		require "myTheme/footer.php";
		exit;
	}
	
	$userQ = $mysqli->query("SELECT * FROM users WHERE uname='" . $mysqli->real_escape_string($_POST['newUser']) . "' LIMIT 0,1");
	$userR = $userQ->fetch_assoc();
	if($userQ && @$userR['uname'] == $_POST['newUser']) {
		$_SESSION['tmpkey'] = makeRandomString(8);
		?>
		<!-- Page Content -->
		<div class="container">
			<div class="row">
				<h2 class="niceBlueColor">Create A New User</h2>
				<form method="POST">
					<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
					<p>Error: Username already taken.</p>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Username:</label>
							<input type="text" id="itemtype" class="form-control" name="newUser" value="<?php echo strip_tags(trim($_POST['newUser'])); ?>">
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="itemtype" class="searchFormLabel">Email:</label>
							<input type="text" id="itemtype" class="form-control" name="newEmail" value="<?php echo strip_tags(trim($_POST['newEmail'])); ?>">
						</div>
					</div>
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
	
		<?php 
		require "myTheme/footer.php";
		exit;
	}
	
	$genPass = makeRandomString(10);
	$ep = md5($genPass);
	
	$userQ = $mysqli->query("INSERT INTO users SET uname='" . $mysqli->real_escape_string($_POST['newUser']) . "', upass='" . $ep . "', uemail='" . $mysqli->real_escape_string($_POST['newEmail']) . "'");
	if($userQ) {
		$gNUQ = $mysqli->query("SELECT * FROM users WHERE uname='". $mysqli->real_escape_string($_POST['newUser']) . "' LIMIT 0,1");
		if($gNUQ) {
			$gNU = $gNUQ->fetch_assoc();
			$privQ = $mysqli->query("INSERT INTO store_user SET su_user='" . $mysqli->real_escape_string($gNU['uid']) . "', su_store='". $_SESSION['storeID'] ."', su_roll='". $mysqli->real_escape_string($_POST['newRoll']) ."'");
			if($privQ) {
				sendEmail($_POST['newEmail'], "AutoDrop Account Created", "<html><body>You have had a AutoDrop account created for you. This will allow you access to Diablo 2 items within the store. Please use the system responsiabley.<br>Please do note all actions and drops are logged so store admins can track sales, drops, and much more.<br><br>URL: <a href=\"https://storedropper.diablo2store.com\">https://storedropper.diablo2store.com</a><br>Username: " . $_POST['newUser'] . "<br>Password: " . $genPass . "<br>Thank you from the Diablo2Store team!");
				?>
				<!-- Page Content -->
				<div class="container">
					<div class="row">
						<h2 class="niceBlueColor">New user created!</h2>
						<p class="niceBlueColor">A email with the subject "AutoDrop Account Created" has been dispatched to the provided email address. This will tell them their username/password and give them the login link to your store.</p>
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
				<?php
			} else {
				//ERROR
				echo "ERROR 1";
				exit;
			}
		} else {
			//ERROR
			echo "ERROR 1";
			exit;
		}
	} else {
		//ERROR
		echo "ERROR 1";
		exit;
	}
} else {
	$_SESSION['tmpkey'] = makeRandomString(8);
?>
    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<h2 class="niceBlueColor">Create A New User</h2>
			<form method="POST">
				<input type="hidden" name="t" value="<?php echo $_SESSION['tmpkey']; ?>">
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="itemtype" class="searchFormLabel">Username:</label>
						<input type="text" id="itemtype" class="form-control" name="newUser">
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="itemtype" class="searchFormLabel">Email:</label>
						<input type="text" id="itemtype" class="form-control" name="newEmail">
					</div>
				</div>
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
<?php
}
?>
		
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
