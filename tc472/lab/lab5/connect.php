<?php
$host = "localhost";
$user = "stevenp5";
$pass = "Sasha 1994";
$database = "stevenp5_tc472lab5";    

   
        // Connecting to the Database
    $connect = @mysql_connect($host, $user, $pass)
     or die("could not connect to server");

        // Selecting the Database for use
    $db_select = @mysql_select_db($database) 
     or die("could not select the database");
?>