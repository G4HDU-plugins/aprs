<?php


#!/usr/bin/php5
$callsign = 'G4HDU-9';
$passcode = 10642;
$aprs_dsts = array('G4HDU-9', 'G8IIS'); // destinations
// Reading the message from stdin
// $f = fopen('php://stdin', 'r');
// while ($line = fgets($f)) {
// if (preg_match('/^Subject:/', $line))
// $msg = trim(str_replace('Subject: ', '', $line));
// if (preg_match('/^X-Original-Sender:/', $line))
// $from = trim(str_replace('X-Original-Sender: ', '', $line));
// if (isset($msg) && isset($from))
// break;
// }
// fclose($f);
// if (!isset($msg) || !isset($from))
// die();
// $msg = "$from: $msg";
// $msg = substr($msg, 0, 67); // Trimming string to max. 67 chars
$msg = "Testing PHP aprs class";
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket){
	$result = socket_connect($socket, 'euro.aprs2.net', 14580);
	if ($result){
		// Authenticating
		$tosend = "user $callsign pass $passcode\n";
		socket_write($socket, $tosend, strlen($tosend));
		$authstartat = time();
		$authenticated = false;
		while ($msgin = socket_read($socket, 1000, PHP_NORMAL_READ)){
			if (strpos($msgin, "$callsign verified") !== false){
				$authenticated = true;
				break;
			}
			// Timeout handling
			if (time() - $authstartat > 5)
				break;
		}
		if ($authenticated){
			foreach ($aprs_dsts as $dst){
				$tosend = "$callsign>APRS,TCPIP*::" . str_pad($dst, 9, ' ') . ":$msg\n";
				socket_write($socket, $tosend, strlen($tosend));
			}
		}
	}
	socket_close($socket);
}




?>