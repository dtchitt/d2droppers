<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
		<link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet">
		<link href="/style/global.css" rel="stylesheet">
		
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
		<script src="/js/design.js"></script>
		
		<title><?php echo $page['title']; ?></title>
		
		<?php echo $page['jsonld']; ?>
	</head>
	<body class="bg-light">
		<div class="container-fluid">
			<nav class="navbar navbar-inverse">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".js-navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Game Services</a>
				</div>
				
				<div class="collapse navbar-collapse js-navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="dropdown mega-dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Unique Items <span class="caret"></span></a>				
							<ul class="dropdown-menu mega-dropdown-menu">
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Top Sellers</li>                            
										<div id="menCollection" class="carousel slide" data-ride="carousel">
											<div class="carousel-inner">
												<div class="item active">
													<a href="#"><img src="http://placehold.it/254x150/ff3546/f5f5f5/&text=New+Collection" class="img-responsive" alt="product 1"></a>
													<h4>
														<small>Summer dress floral prints</small>
													</h4>                                        
													<button class="btn btn-primary" type="button">49,99 €</button> <button href="#" class="btn btn-default" type="button"><span class="glyphicon glyphicon-heart"></span> Add to Wishlist</button>
												</div><!-- End Item -->
												<div class="item">
													<a href="#"><img src="http://placehold.it/254x150/3498db/f5f5f5/&text=New+Collection" class="img-responsive" alt="product 2"></a>
													<h4>
														<small>Gold sandals with shiny touch</small>
													</h4>                                        
													<button class="btn btn-primary" type="button">9,99 €</button> <button href="#" class="btn btn-default" type="button"><span class="glyphicon glyphicon-heart"></span> Add to Wishlist</button>
												</div><!-- End Item -->
												<div class="item">
													<a href="#"><img src="http://placehold.it/254x150/2ecc71/f5f5f5/&text=New+Collection" class="img-responsive" alt="product 3"></a>
													<h4>
														<small>Denin jacket stamped</small>
													</h4>                                        
													<button class="btn btn-primary" type="button">49,99 €</button> <button href="#" class="btn btn-default" type="button"><span class="glyphicon glyphicon-heart"></span> Add to Wishlist</button>      
												</div><!-- End Item -->                                
											</div><!-- End Carousel Inner -->
											<!-- Controls -->
											<a class="left carousel-control" href="#menCollection" role="button" data-slide="prev">
												<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
												<span class="sr-only">Previous</span>
											</a>
											<a class="right carousel-control" href="#menCollection" role="button" data-slide="next">
												<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
												<span class="sr-only">Next</span>
											</a>
										</div><!-- /.carousel -->
										<li class="divider"></li>
										<li><a href="/all-unique-items.html">View All Uniques <span class="glyphicon glyphicon-chevron-right pull-right"></span></a></li>
									</ul>
								</li>
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">By Slot</li>
										<li><a href="#">Unique Helms</a></li>
										<li><a href="/unique-items-armor.html">Unique Armors</a></li>
										<li><a href="#">Unique Weapons</a></li>
										<li><a href="#">Unqiue Shields</a></li>
										<li><a href="/unique-items-belt.html">Unqiue Belts</a></li>
										<li><a href="/unique-items-ring.html">Unqiue Rings</a></li>
										<li><a href="/unique-items-amulet.html">Unqiue Amulets</a></li>
										<li><a href="#">Unqiue Gloves</a></li>
										<li><a href="#">Unqiue Boots</a></li>
									</ul>
								</li>
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Weapon Types</li>
										<li><a href="#">Axes</a></li>
										<li><a href="#">Bows</a></li>
										<li><a href="#">Crossbows</a></li>
										<li><a href="#">Daggers</a></li>
										<li><a href="#">Javelins</a></li>
										<li><a href="#">Maces</a></li>
										<li><a href="#">Polearms</a></li>
										<li><a href="#">Scepters</a></li>
										<li><a href="#">Spears</a></li>
										<li><a href="#">Staves</a></li>
									</ul>
								</li>
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Weapons Continued</li>
										<li><a href="#">Swords</a></li>
										<li><a href="#">Throwing Weapons</a></li>
										<li><a href="#">Wands</a></li>
										<li class="dropdown-header">Hot Deals</li>
										<li><a href="#">The Stone of Jordan</a></li>
										<li><a href="#">5% LL Bul-Kathos</a></li>
										<li><a href="#">5% LL Bul-Kathos</a></li>
										<li><a href="#">5% LL Bul-Kathos</a></li>
										<li><a href="#">5% LL Bul-Kathos</a></li>
									</ul>
								</li>
							</ul>				
						</li>
						<li class="dropdown mega-dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Runewords <span class="caret"></span></a>				
							<ul class="dropdown-menu mega-dropdown-menu">
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Armors</li>
										<li><a href="#">Enigma</a></li>
										<li><a href="#">Fortitude</a></li>
										<li><a href="#">Treachery</a></li>
										<li><a href="#">Chains of Honor</a></li>
										<li><a href="#">Duress</a></li>
										<li><a href="#">Stone</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Weapons</li>
										<li><a href="#">White</a></li>
										<li><a href="#">Beast</a></li>
									</ul>
								</li>
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Weapons Continued</li>
										<li><a href="#">Breath of the Dying</a></li>                            
										<li><a href="#">Hand of Justice</a></li>
										<li><a href="#">Heart of the Oak</a></li>
										<li><a href="#">Faith</a></li>
										<li><a href="#">Fortitude</a></li>
										<li><a href="#">Grief</a></li>
										<li><a href="#">Infinity</a></li>
										<li><a href="#">Insight</a></li>
										<li><a href="#">Spirit</a></li>
										<li><a href="#">Voice of Reason</a></li>
									</ul>
								</li>
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Shields</li>
										<li><a href="#">Exile</a></li>
										<li><a href="#">Dragon</a></li>
										<li><a href="#">Dream</a></li>
										<li><a href="#">Spirit</a></li>
										<li class="divider"></li>
										<li class="dropdown-header">Bases</li>
										<li><a href="#">Armor</a></li>
										<li><a href="#">Weapons</a></li>
										<li><a href="#">Shields</a></li>
									</ul>
								</li>
								<li class="col-sm-3">
									<ul>
										<li class="dropdown-header">Top Sellers</li>                            
										<div id="womenCollection" class="carousel slide" data-ride="carousel">
											<div class="carousel-inner">
												<div class="item active">
													<a href="#"><img src="http://placehold.it/254x150/3498db/f5f5f5/&text=New+Collection" class="img-responsive" alt="product 1"></a>
													<h4><small>Summer dress floral prints</small></h4>                                        
													<button class="btn btn-primary" type="button">49,99 €</button> <button href="#" class="btn btn-default" type="button"><span class="glyphicon glyphicon-heart"></span> Add to Wishlist</button>       
												</div><!-- End Item -->
												<div class="item">
													<a href="#"><img src="http://placehold.it/254x150/ff3546/f5f5f5/&text=New+Collection" class="img-responsive" alt="product 2"></a>
													<h4><small>Gold sandals with shiny touch</small></h4>                                        
													<button class="btn btn-primary" type="button">9,99 €</button> <button href="#" class="btn btn-default" type="button"><span class="glyphicon glyphicon-heart"></span> Add to Wishlist</button>        
												</div><!-- End Item -->
												<div class="item">
													<a href="#"><img src="http://placehold.it/254x150/2ecc71/f5f5f5/&text=New+Collection" class="img-responsive" alt="product 3"></a>
													<h4><small>Denin jacket stamped</small></h4>                                        
													<button class="btn btn-primary" type="button">49,99 €</button> <button href="#" class="btn btn-default" type="button"><span class="glyphicon glyphicon-heart"></span> Add to Wishlist</button>      
												</div><!-- End Item -->                                
											</div><!-- End Carousel Inner -->
											<!-- Controls -->
											<a class="left carousel-control" href="#womenCollection" role="button" data-slide="prev">
												<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
												<span class="sr-only">Previous</span>
											</a>
											<a class="right carousel-control" href="#womenCollection" role="button" data-slide="next">
												<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
												<span class="sr-only">Next</span>
											</a>
										</div><!-- /.carousel -->
										<li class="divider"></li>
										<li><a href="#">View All Runewords <span class="glyphicon glyphicon-chevron-right pull-right"></span></a></li>
									</ul>
								</li>
							</ul>				
						</li>
						<li><a href="/diablo-runes.html">Runes</a></li>
						<li><a href="#">Leveled Toons</a></li>
						<li><a href="#">Full Gear Packs</a></li>
						<li><a href="#">Runeword Builder</a></li>
						
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">My Account <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Profile</a></li>
								<li><a href="#">Promotions</a></li>
								<li><a href="#">Change Password</a></li>
								<li><a href="#">Recent Orders</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Payment: USD <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Payment: USD</a></li>
								<li><a href="#">Payment: Bitcoin</a></li>
								<li><a href="#">Payment: Forum Gold</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Realm: <?php echo $_SESSION['prettyRealm']; ?><span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a class="ESCLEMenuBtn">Realm: East Softcore Ladder Expansion</a></li>
								<li><a class="WSCLEMenuBtn">Realm: West Softcore Ladder Expansion</a></li>
							</ul>
						</li>
						<li><a href="#" class="myCartLink">My Cart (0) Items</a></li>
					</ul>
				</div><!-- /.nav-collapse -->
			</nav>
			
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<form class="navbar-form navbar-left searchForm" role="search">
						<input type="text" class="form-control searchBox" name="q" placeholder="Search">
						<button type="submit" class="btn btn-default searchButton">Search</button>
					</form>
				</div>
			</div>