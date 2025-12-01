<?php

$host = 'localhost'; // database host
$dbname = 'users1'; // database name
$username = 'root'; // database username
$password = ''; // database password


try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
