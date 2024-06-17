<html>
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
		<form method="POST" action="index.php">
			<h2>Login</h2>
			<input type="text" name="login_user" placeholder="Username"> <br>
			<input type="password" name="login_pass" placeholder="Password"><br>
			<input type="submit" value="Login">
		</form>
	</div>
</body>
</html>