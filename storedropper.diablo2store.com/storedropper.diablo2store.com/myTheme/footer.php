	<div class="loader">
	   <div style="text-align: center;">
		   <img class="loading-image" src="images/ajax-loader.gif" alt="loading..">
	   </div>
	</div>
	
	<div class="progress upload-progress">
	  <div class="progress-bar progress-bar-warning progress-bar-striped active uploading-image" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
		Uploading... Please wait...
	  </div>
	</div>
	
	<div class="bottomMenu">
		<div class="dropButton bMHover myDropMenu"><u>Drop Items</u></div>
		<div class="tradeListButton bMHover myTradeListMenu"><u>Trade List</u></div>
		<div class="selectedItems">Items: <span class="itemCount">0</span></div>
		<div class="selectedDrops">Drops: <span class="dropCount">0</span></div>
	</div>
	
	<script>var user = "<?php print $currUser; ?>";</script>

    <!-- jQuery Version 1.11.1
    <script src="js/jquery.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	-->
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.js"></script>
	
	<!-- Tooltipster 3.3.0 -->
    <script type="text/javascript" src="js/jquery.tooltipster.js"></script>
	
	<!-- Item Manager Functions -->
	<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="js/html2canvas.js"></script>
    <script type="text/javascript" src="js/itemManager.js"></script>
    <script type="text/javascript" src="js/itemManagerShow.js"></script>

	<script>
		//variable with names to display.
		var show 	= [];
		var rowsid	= [];
		var hideid	= [];
		var droparray = [];
		$('#<?php print $showthat; ?>').collapse('show');
		var myVersion="<?php print $version; ?>";
	</script>

</body>

</html>