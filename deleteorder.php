<?php 
include 'config.php';

$id = $_GET['order_id'];

mysqli_query($mysqli, "UPDATE orders SET status = 'Denied!' WHERE order_id = '$id'");

header('location: admin_dashboard.php');


?>