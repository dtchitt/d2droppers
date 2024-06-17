<?php
function sendDiscordCurrentStore($message) {
	global $mysqli;
	$webhookurl = "https://discordapp.com/api/webhooks/676702604348096513/5B2Sbf81l5JtDvYU74ejKBEJxX0HAZyQYADOArrTZ0JHNe8nr0DrfItpEs-EkIV3Xud0";
	if(strlen($webhookurl) < 10) return false;
	$msg = str_replace("\n", " ", $message);
	$json_data = array ('content'=>"$msg");
	$make_json = json_encode($json_data);
	$ch = curl_init( $webhookurl );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec( $ch );
	return true;
}