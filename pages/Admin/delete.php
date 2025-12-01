<?php
// delete.php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete by employee_id or email
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = trim(urldecode($_GET['id']));
    $id = mysqli_real_escape_string($conn, $id);

    // Check if ID is likely an employee_id (numeric)
    if (is_numeric($id)) {
        $sql = "DELETE FROM users WHERE employee_id = '$id'";
        $successMessage = "Employee with ID '$id' successfully deleted.";
        $errorMessage = "Error deleting employee ID '$id': " . $conn->error;
    } else {
        // Assume ID is an email
        $sql = "DELETE FROM users WHERE email = '$email'";
        $successMessage = "User with email '$email' successfully deleted.";
        $errorMessage = "Error deleting user with email '$iemail': " . $conn->error;
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('" . $successMessage . "'); window.location.href='manage_employees.php';</script>";
        echo "<script>alert('Employee Successfully Deleted.'); window.location.href='manage_employees.php';</script>";
        exit;
    } else {
        echo "<script>alert('" . $errorMessage . "'); window.location.href='manage_employees.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid request or empty ID.'); window.location.href='manage_employees.php';</script>";
    exit;
}

$conn->close();

?>