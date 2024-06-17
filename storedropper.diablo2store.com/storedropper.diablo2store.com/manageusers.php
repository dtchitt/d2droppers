<?php

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
require 'functions.php';
require 'config.php';
checkUserAuth(true);
$themeName = getTheme($currUser);

$pageTitle = "Diablo 2 Store - Manage Users";
require "myTheme/header.php";
?>
    <!-- Page Content -->
    <div class="container">
		<div class="row">
			<a href="adduser.php">Add New User</a>
			<table id="myCustomTable">
				<tr>
					<td>Username</td>
					<td>Roll</td>
					<td>Actions</td>
				</tr>
			<?php
			$storeUserQ = $mysqli->query("SELECT * FROM store_user WHERE su_store='" . $mysqli->real_escape_string($_SESSION['storeID']) . "' ORDER BY su_roll DESC");
			while($storeUser = $storeUserQ->fetch_assoc()) {
				$tUserQ = $mysqli->query("SELECT * FROM users WHERE uid='" . $storeUser['su_user'] . "'");
				$tUser = $tUserQ->fetch_assoc();
				if($storeUser['su_roll'] == 'admin')
					$storeUser['su_roll'] = "Admin";
				if($storeUser['su_roll'] == 'view')
					$storeUser['su_roll'] = "View";
				if($tUser['uid'] == $_SESSION['userInfo']['uid'])
					echo '<tr><td>' . $tUser['uname'] . '</td><td>' . $storeUser['su_roll'] . '</td><td>Nothing To Do</td></tr>';
				else
					echo '<tr><td>' . $tUser['uname'] . '</td><td>' . $storeUser['su_roll'] . '</td><td><a href="edituser.php?ui=' . $tUser['uid'] . '">Edit</a> - <a href="deleteuser.php?ui=' . $tUser['uid'] . '">Delete</a></td></tr>';
			}
			?>
			</table>
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
