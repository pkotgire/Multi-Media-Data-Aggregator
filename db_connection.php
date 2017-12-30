<?php

function open_connection($db)
 {
 $dbhost = "localhost";
 $dbuser = "root";
 $dbpass = "5QlrwfT7xrD93A7u";

 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);


 return $conn;
 }

function close_connection($conn)
 {
 $conn -> close();
 }

?>
