<?php

if($_GET['d'] == 1) {
	echo file_get_contents("drop_Dropper1.json");
	unlink("drop_Dropper1.json");
}
if($_GET['d'] == 2) {
	echo file_get_contents("drop_Dropper2.json");
	unlink("drop_Dropper2.json");
}