<?php

    require '../db.php';

    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $employee_id = $_SESSION['employee_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = :employee_id");
    $stmt->execute(['employee_id' => $employee_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $role = $user['role'];

    // If the user is not an Admin, show an alert and redirect
    if ($role != 'Manager') {
        echo "<script>
            alert('Access Denied: You do not have permission to access this page.');
            window.location.href = '../logout.php';
        </script>";
        exit();
    }

    // Retrieve the user's name and profile picture from the session
    $user_name = $_SESSION['user_name'];
    $profile_picture = $_SESSION['profile_picture'];
    $conn = null;
?>