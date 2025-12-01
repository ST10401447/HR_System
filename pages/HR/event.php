<?php
// Database connection
$servername = "localhost";
$username = "root";  // Update with your DB username
$password = "";      // Update with your DB password
$dbname = "users1";  // Update with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events from the database
$sql = "SELECT id, title, start, end FROM events";
$result = $conn->query($sql);

$events = array();
while($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Output events in JSON format
echo json_encode($events);

$conn->close();
?>
