<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = ""; // исправлено
$dbname = "sexshop";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbname);
if(!$conn) {
    die("Something went wrong;");
}

?>
