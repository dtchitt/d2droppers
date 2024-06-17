<?php

require "config.php";
require "inc/auth.php";

$page = [];
$page['title'] = "Diablo 2 Item Store - Create New Account";
$page['desc'] = "Login to diablo 2 item store create a new account.";

$errors = [];

if(strlen($_POST['login_email']) > 0 && strlen($_POST['login_password']) > 0 && strlen($_POST['login_password_again']) > 0) {
	
	$sql = "SELECT * FROM d2_cart_users WHERE cu_email = '" . mysqli_real_escape_string($mysqli, $_POST['login_email']) . "'";
	$result = $mysqli->query($sql);
	
	if ($result->num_rows !== 0) {
		$errors[] = "Error Username Already Exists, Try <a href=\"login.html\">Logging In</a>.";
	} else {
		$sql = "INSERT INTO d2_cart_users SET cu_email = '" . mysqli_real_escape_string($mysqli, $_POST['login_email']) . "', cu_pass = '" . mysqli_real_escape_string($mysqli, md5($_POST['login_password'])) . "', cu_type = 'user', cu_email_ver = '0'";
		if(!$result = $mysqli->query($sql)) {
			$errors[] = "Failed to create new account, please try again.";
			$errors[] = $result->error;
		} else {
			header("Location: /login.html");
			exit;
		}
	}
	
}

$authed = checkAuth();

if($authed) {
	header("Location: /dash.html");
	exit;
}

require "theme/header.php";

?>
	
	<h1 class="colorWhite">Game Service Online - Create New Account</h3>
	
	<?php
	if(count($errors) > 0) {
		foreach($errors as $error) {
			echo "<div class=\"row\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll colorWhite\">" . $error . "</div></div>";
		}
	}
	?>
	
	<form method="POST" action="/create-account.html" class="loginForm createAccountForm">
		
		<div class="row">
			<div class="col-xs-0 col-sm-2 col-md-4 col-lg-4"></div>
			<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 centerAll colorWhite">
				Email:
			</div>
			<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 centerAll colorWhite">
				<input type="text" name="login_email">
			</div>
			<div class="col-xs-0 col-sm-2 col-md-4 col-lg-4"></div>
		</div>
		
		<div class="row">
			<div class="col-xs-0 col-sm-2 col-md-4 col-lg-4"></div>
			<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 centerAll colorWhite">
				Password:
			</div>
			<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 centerAll colorWhite">
				<input type="password" name="login_password">
			</div>
			<div class="col-xs-0 col-sm-2 col-md-4 col-lg-4"></div>
		</div>
		
		<div class="row">
			<div class="col-xs-0 col-sm-2 col-md-4 col-lg-4"></div>
			<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 centerAll colorWhite">
				Password Again:
			</div>
			<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 centerAll colorWhite">
				<input type="password" name="login_password_again">
			</div>
			<div class="col-xs-0 col-sm-2 col-md-4 col-lg-4"></div>
		</div>
		
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll colorWhite">
				*We will never sell your email!*
			</div>
		</div>
		
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll">
				<input type="submit" class="btn btn-primary" value="Create Account">
			</div>
		</div>
	
	</form>
	
	<script type="text/javascript">
		jQuery(function($) {
			$('.createAccountButton').click(function() {
				$('.createAccountForm').submit();
			});
		});
	</script>
	
<?php

require "theme/footer.php";