<?php
    session_start();

    if (!isset($_SESSION['employee_id'])) {
        header("Location: ../login.php");
        exit();
    }

    //Get the user's name from the session.
    $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    $profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '';
    //Check if the switch button info is in the session. If not, use false
    $display = isset($_SESSION['switch_button']) ? $_SESSION['switch_button'] : false;
    // Get the user location from the session. If not set, use empty text
    $location = isset($_SESSION['location']) ? $_SESSION['location'] : '';
    $employee_id = isset($_SESSION['employee_id']) ? $_SESSION['employee_id'] : null;
?>