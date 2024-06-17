<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <title><?php echo $pageTitle; ?></title>
	
	<!--Extra Fonts-->
	<link href="https://fonts.googleapis.com/css?family=Heebo|Inconsolata|Laila" rel="stylesheet">
	
    <!-- Bootstrap Core CSS -->
    <link id="layout1" rel="stylesheet" href="css/bootstrap.css">

    <!-- Custom CSS -->
	<link id="layout2" rel="stylesheet" type="text/css" href="css/itemManager.css">
	<link id="layout3" rel="stylesheet" type="text/css" href="css/tooltipster.css">
    <link id="layout4" rel="stylesheet" type="text/css" href="css/jquery-ui.css">
	<link id="layout5" rel="stylesheet" type="text/css" href="css/custom.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="js/jquery.js"></script>
    <script src="js/jquery-ui.min.js"></script>
</head>
<body>
    <div class="modal fade" id="changelogMod" tabindex="-1" role="dialog" aria-labelledby="changelogMod" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title color4" id="changelogLabel">Changelog</h4>
                </div>
                <div class="modal-body" id="changes">
                </div>
            </div>
        </div>
    </div>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="dropdown-toggle navbar-brand" id="themes" data-toggle="dropdown" href="#">Hello <?php echo $_SESSION['userInfo']['uname']; ?> <b class="caret"></b></a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="themes" style="left:35px;">
					<li role="presentation"><a href="pickstore.php" tabindex="-1" class="themeselect">Change Stores</a></li>
					<?php
					if(isAdmin()) {
						?>
						<li role="presentation"><a href="manageusers.php" tabindex="-1" class="themeselect">Manage Users</a></li>
						<li role="presentation"><a href="item-lists.php" tabindex="-1" class="themeselect">Manage Auto Shops</a></li>
						<?php
					}
					?>
				</ul>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php buildMenu(); ?>
                </ul>
				<ul class="nav navbar-nav navbar-right">
					<?php userAccess(); ?>
				</ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
		<!-- /.container -->
    </nav>