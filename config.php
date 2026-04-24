<?php
$servername = "sql206.infinityfree.com";
$username = "if0_41719994";
$password = "CuR8XaVTIt";
$dbname = "if0_41719994_alumni";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
