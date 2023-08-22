<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "customer";

$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
