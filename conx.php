<?php

$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "proj";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $dbpassword);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
