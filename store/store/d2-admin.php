<?php

require "config.php";
require "inc/auth.php";

checkAuth(true, true);

$page = [];
$page['title'] = "Diablo 2 Item Store - Dashboard";

require "theme/admin-header.php";

?>
	

			Admin Dashboard

	
<?php

require "theme/admin-footer.php";