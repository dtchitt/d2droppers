<?php

require "config.php";
require "inc/auth.php";

$authed = checkAuth();

if($authed) {
	header("Location: /dash.html");
	exit;
}

$page = [];
$page['title'] = "Diablo 2 Item Store - Login Page";
$page['desc'] = "Login to diablo 2 item store.";

require "theme/header.php";

?>
	
	<h1 class="colorWhite">Game Service Online - Login Page</h1>
	
	<form method="POST" class="loginForm">
		
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
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 centerAll">
				<button type="button" class="btn btn-primary loginButton">Login</button> <button type="button" class="btn btn-warning">Forgot Password</button> <a href="/create-account.html"><button type="button" class="btn btn-warning">Create Account</button></a>
			</div>
		</div>
	
	</form>
	
	<script type="text/javascript">
		jQuery(function($) {
			$('.loginButton').click(function() {
				$('.loginForm').submit();
			});
		});
	</script>
	
<?php

require "theme/footer.php";