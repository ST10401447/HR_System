<?php
// Database connection
$servername = "localhost";
$username = "root";  // Update with your DB username
$password = "";      // Update with your DB password
$dbname = "users1";  // Update with your DB name

$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement
$sql = "INSERT INTO events (title, start, end) VALUES ('$title', '$start', '$end')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("status" => "success"));
} else {
    echo json_encode(array("status" => "error", "message" => $conn->error));
}

$conn->close();
?>
