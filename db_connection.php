<?php
//Connecting to the database

$server = "localhost:3307", "omondi";
$user = "omondi";
$password = "olive";
$databasedb = "vat_db";

$con = mysqli_connect($server, $user, $password , $databasedb) or die('Not connected as ' . mysqli_connect_error());

?>