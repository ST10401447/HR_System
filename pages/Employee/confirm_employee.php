<?php
    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // Retrieve the user's name and profile picture from the session
    $user_name = $_SESSION['user_name'];
    $profile_picture = $_SESSION['profile_picture'];
    $display = $_SESSION['switch_button'];
    $location = $_SESSION['location'];
    $employee_id = $_SESSION['employee_id']; 
?>