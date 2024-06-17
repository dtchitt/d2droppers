<?php
require_once 'functions.php';
require_once 'config.php';
checkUserAuth(false);
$linecount = 0;
try {
if(isset($authorized[$currUser])) {
	for ($i=0; $i<count($authorized[$currUser]);$i++) {
		$file="drop_".$authorized[$currUser][$i].".json";
		if(file_exists($file)) {
			$handle = fopen($file, "r");
			if ($handle) {
				while(!feof($handle)){
					$line = fgets($handle);
					$linecount++;
				}
				$linecount--;
				fclose($handle);
			}
		}
	}
}
echo $linecount;
} catch(Exception $e) { echo 0; }