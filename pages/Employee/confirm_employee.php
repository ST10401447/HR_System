<?php
    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // Retrieve the user's name and profile picture from the session (use defaults if missing)
    $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    $profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '';
    // 'switch_button' may not be set for all users — default to false
    $display = isset($_SESSION['switch_button']) ? $_SESSION['switch_button'] : false;
    // 'location' may not be set — default to empty string
    $location = isset($_SESSION['location']) ? $_SESSION['location'] : '';
    $employee_id = isset($_SESSION['employee_id']) ? $_SESSION['employee_id'] : null;
?>