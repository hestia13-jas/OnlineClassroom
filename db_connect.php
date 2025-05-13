<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "ocms"; // Make sure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
