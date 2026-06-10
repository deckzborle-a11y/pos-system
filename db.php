<?php
// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "pos_system";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>