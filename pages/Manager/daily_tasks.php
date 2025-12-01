<?php
    include 'confirm_employee.php';

    require '../db.php';

    try {
        $employee_id = $_SESSION['employee_id'];
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $employee_id]);
        $dailyTasks_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/daily_tasks.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Daily Tasks</title> 
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.querySelector(".hamburger");
        const sidebar = document.querySelector(".sidebar");

        hamburger.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    });
</script>   
</head>
<body>

    <!-- Hamburger Menu -->
    <div class="hamburger">
        <i class="fas fa-bars"></i>
    </div>
    
    <!-- sidebar -->
    <div class="dashboard-container">
            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="profile-section">
                    <div class="profile-card">
                        <!-- Profile picture -->
                        <div class="profile-image">
                            <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                            <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                        </div>
                        <!-- Button now outside the .profile-image div -->
                        <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
                        <!-- Profile information -->
                        <div class="profile-info">
                            <p><?php echo htmlspecialchars($user_name); ?> - Manager</p>
                        </div>
                    </div>
                </div>
                <nav>
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_employee_tasks.php" class="active"><i class="fas fa-tasks"></i> Manage Employee Tasks</a>
                <a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i> Manage Leaves</a>
                <a href="update_details.php"><i class="fas fa-users"></i> Update Details</a>
                <a href="manage_employee_tasks.php"><i class="fas fa-users"></i> Manage Employee Tasks</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <a href="../Employee/dashboard.php"><i class="fas fa-sign-out-alt"></i> Switch to Employee</a>
            </nav>
            </div>
            

        
       <!-- Main Content -->
<div class="main-content dailyTasks_content">
    <h1>Daily Tasks</h1>
    <?php if (count($dailyTasks_tasks) > 0): ?>
        <div class="dailyTasks_tasksTable">
            <div class="dailyTasks_tasksHeader">
                <span>Id</span>
                <span>Task Name</span>
                <span>Due Date</span>
                <span>Status</span>
                <span>Manager</span>
            </div>
            <?php foreach ($dailyTasks_tasks as $dailyTasks_task): ?>
                <div class="dailyTasks_taskRow">
                    <span><?php echo htmlspecialchars($dailyTasks_task['id']); ?></span>
                    <span><?php echo htmlspecialchars($dailyTasks_task['task_name']); ?></span>                            
                    <span><?php echo htmlspecialchars($dailyTasks_task['task_date']); ?></span>
                    <span><?php echo htmlspecialchars($dailyTasks_task['status']); ?></span>
                    <span><?php echo htmlspecialchars($dailyTasks_task['manager']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="announcement">
            <p>No tasks found for today.</p>
        </div>
    <?php endif; ?>
</div>
<style>

/* Main Content Styles */
.main-content {
    padding: 20px;
    background-color: #f9f9f9; /* Light background for contrast */
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Title Styles */
.main-content h1 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333; /* Darker text color */
}

/* Table Styles */
.dailyTasks_tasksTable {
    width: 100%;
    border-collapse: collapse; /* Remove space between borders */
    margin-top: 10px;
}

/* Header Styles */
.dailyTasks_tasksHeader {
    display: flex; /* Use flexbox for alignment */
    background-color: #ff9500; /* Bootstrap primary color */
    color: white; /* White text for header */
    font-weight: bold;
    padding: 10px;
}

/* Header Item Styles */
.dailyTasks_tasksHeader span {
    flex: 1; /* Distribute space evenly */
    text-align: left; /* Align text to the left */
}

/* Row Styles */
.dailyTasks_taskRow {
    display: flex; /* Use flexbox for alignment */
    background-color: #fff; /* White background for rows */
    border-bottom: 1px solid #ddd; /* Light border for separation */
    padding: 10px;
}

/* Row Item Styles */
.dailyTasks_taskRow span {
    flex: 1; /* Distribute space evenly */
    text-align: left; /* Align text to the left */
    padding: 5px 0; /* Add some vertical padding */
}

/* Hover Effect */
.dailyTasks_taskRow:hover {
    background-color: #f1f1f1; /* Light gray background on hover */
}

/* Responsive Styles */
@media (max-width: 600px) {
    .dailyTasks_tasksHeader, .dailyTasks_taskRow {
        display: block; /* Stack elements on small screens */
        width: 100%; /* Full width */
    }
}

</style>
    </div>
    <script src="../../js/script.js"></script>
</body>
</html>
