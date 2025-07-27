<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ebookingsystem'; // <-- Corrected to match your actual database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
