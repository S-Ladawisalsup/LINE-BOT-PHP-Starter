<?php
$address="127.0.0.1";
$port="3222";
$sock=socket_create(AF_INET,SOCK_STREAM,0) or die("Cannot create a socket");
socket_bind($sock,$address,$port) or die("Couldnot bind to socket");
socket_listen($sock) or die("Couldnot listen to socket");
$accept=socket_accept($sock) or die("Couldnot accept");
$read=socket_read($accept,1024) or die("Cannot read from socket");
echo $read;
socket_write($accept,"Hello client");
socket_close($sock);

/*****************************************************************************************************
<?php
$address="127.0.0.1";
$port="3222";
$msg="Hello server";

$sock=socket_create(AF_INET,SOCK_STREAM,0) or die("Cannot create a socket");
socket_connect($sock,$address,$port) or die("Could not connect to the socket");
socket_write($sock,$msg);

$read=socket_read($sock,1024);
echo $read;
socket_close($sock);
?>
*****************************************************************************************************/
?>