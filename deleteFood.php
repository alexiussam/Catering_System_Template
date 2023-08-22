<?php
    include 'config.php';
    $id = $_GET["id"];

    mysqli_query($mysqli,  "DELETE FROM orders where order_id='$id'");

    header("location: place_order.php");

    ?>