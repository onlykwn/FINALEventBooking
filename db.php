<?php
// Start session only if none is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ebookingsystem";
$conn = mysqli_connect("localhost", "root", "", "ebookingsystem");

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
