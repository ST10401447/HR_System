<?php
require_once "db.php";

$leave_id = $_GET['id'] ?? null;

if ($leave_id) {
    $stmt = $pdo->prepare("UPDATE leaves SET status = 'approved', feedback = 'Leave approved by admin' WHERE id = :leave_id");
    $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header("Location: notifications.php?success=Leave request approved successfully.");
        exit();
    } else {
        echo "Error approving leave request.";
    }
} else {
    echo "No leave ID provided.";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/Manage_Employee_Tasks.css">
    <title>Dashboard</title>
</head>
<body>
<style>
    .sidebar {
            width: 300px; /* Increased the sidebar width */
            background-color: #ff9500;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 30px;
            box-sizing: border-box;
            font-size: 18px;
        }
        .sidebar.active {
        left: 0;
    }

    /* Hamburger menu styles */
    .hamburger {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        background: #ff9500;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1000;
    }

    .hamburger i {
        font-size: 24px;
        color: white;
    }

    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .hamburger {
            display: block;
        }

        .sidebar {
            width: 250px;
            left: -250px;
        }

        .sidebar nav a {
            padding: 15px;
            font-size: 18px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
        }

        .profile-info p {
            font-size: 18px;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
        }
    }
</style>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.querySelector(".hamburger");
        const sidebar = document.querySelector(".sidebar");

        hamburger.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    });
</script> -->

<div class="dashboard-container">
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-image">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                    </div>
                    <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
                    <input type="file" id="imageUpload" style="display: none;" accept="image/*" onchange="updateProfileImage(event)">
                    <div class="profile-info">
                        <p><?php echo htmlspecialchars($user_name); ?> - HR</p>
                    </div>
                </div>
            </div>
            <nav>
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_employee_tasks.php" class="active"><i class="fas fa-tasks"></i> Manage Employee Tasks</a>
                <a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i> Manage Leaves</a>
                <a href="view_employee_profiles.php"><i class="fas fa-users"></i> View Employee Profiles</a>
                <a href="view_employee_list.php"><i class="fas fa-users"></i> Manage Employees</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <a href="../Employee/dashboard.php"><i class="fas fa-sign-out-alt"></i> Switch to Employee</a>
            </nav>
        </div>

        <div class="main-content">
        <h1>List Of All Leaves</h1>