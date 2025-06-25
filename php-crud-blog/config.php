<?php
$host = "localhost";
$port = 3306; // Use your port number here
$username = "root";
$password = ""; // Use your actual password
$database = "blog";

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>