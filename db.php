<?php
$host = "localhost";
$user = "root"; // change if needed
$pass = "";     // your MySQL password
$db   = "store_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
