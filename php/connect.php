<?php
$conn = mysqli_connect("localhost", "root", "", "ecommerce_shoes");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
