<?php

error_reporting( E_ALL );
set_time_limit ( 20 );

echo "<h2>TCP/IP Connection</h2>\n";

/* Get the port for the WWW service. */
$service_port = 14580;

/* Get the IP address for the target host. */
$address = gethostbyname( 'euro.aprs2.net' );

/* Create a TCP/IP socket. */
$socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
if ( $socket === false ) {
    echo "socket_create() failed: reason: " . socket_strerror( socket_last_error() ) . "<br />";
} else {
    echo "Socket OK.<br />";
}

echo "Attempting to connect to '$address' on port '$service_port'...<br />";
$result = socket_connect( $socket, $address, $service_port );
if ( $result === false ) {
    echo "socket_connect() failed.<br />Reason: ($result) " . socket_strerror( socket_last_error( $socket ) ) . "<br />";
} else {
    echo "OK.<br />";
}
// $in = "HEAD / HTTP/1.1\r\n";
// $in .= "Host: g4hdu.co.uk\r\n";
// $in .= "Connection: Close\r\n\r\n";
$out = '';
// echo "Sending HTTP HEAD request...";
// socket_write($socket, $in, strlen($in));
// echo "OK.<br />";
//echo "Reading response:<br /><br />";
//while ( $out = socket_read( $socket, 2048,PHP_NORMAL_READ  ) ) {
//    echo $out;
//}
//flush();
echo "connect OK.<br />";
// # aprsc 2.0.14-g28c response from server on trying to connect.
// $in = "HEAD / HTTP/1.1\r\n";
// $in .= "Host: g4hdu.co.uk\r\n";
//$in = "user G4HDU pass 10642 vers testphpclass 1.1.1 filter r/53.50617/-2.94883/50\r\n";
$in = "user G4HDU pass 10642 vers testphpclass 1.1.1 filter b/G4HDU-9\r\n";
// $in .= "Connection: Close\r\n";
$out = '';

echo "Sending HTTP HEAD request...";
socket_write( $socket, $in, strlen( $in ) );
echo "OK.<br />";
$i = 0;
socket_set_timeout( $socket, 0, 500);
echo "Reading response:<br /><br />";
while ( $i < 100 ) {
    $out = socket_read( $socket, 2048  );
    echo " - $i - $out <br />";
    $i++;
}

echo "<br />Closing socket...";
socket_close( $socket );
echo "OK.<br /><br />";

?>