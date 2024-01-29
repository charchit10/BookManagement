<?php

$servername = 'localhost';
$username = 'root';
$password = '';

// Connecting to the database.
try {
    $conn = new PDO("mysql:host=$servername;dbname=bookstore", $username, $password);
    // Set the PDO error mode to exception.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    // Remove this line: $error_message = $e->getMessage();
    die();
}
