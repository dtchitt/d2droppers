<?php

require "config.php";
require "inc/auth.php";

checkAuth(true);
checkRealm();

$page = [];
$page['title'] = "Diablo 2 Item Store - Dashboard";

require "theme/header.php";

?>
	
	<h1 class="colorWhite">Game Service Online - Dashboard</h1>
	
	<h3 class="colorWhite">Recemt Purchases</h3>
	
	<table class="table table-dark recentOrderTable">
		<thead>
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Total Price</th>
				<th scope="col">Date</th>
				<th scope="col">Action</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">1</th>
				<td>Mark</td>
				<td>Otto</td>
				<td>@mdo</td>
			</tr>
		</tbody>
	</table>
	
<?php

require "theme/footer.php";