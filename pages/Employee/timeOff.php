<?php
include 'confirm_employee.php';
$display = $_SESSION['switch_button'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_type = $_POST["leave_type"] ?? '';
    $start_date = $_POST["start_date"] ?? '';
    $end_date = $_POST["end_date"] ?? '';
    $reason = $_POST["reason"] ?? '';
    $status = "Pending";

    $host = "localhost";
    $dbname = "users1";
    $username = "root";
    $password = "";

    // Connect to the database
    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Connection error: " . mysqli_connect_error());
    }

    // Prepare and execute the SQL query to insert data
    $sql = "INSERT INTO timeoff (leave_type, start_date, end_date, reason, employee_id, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("SQL Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssis", $leave_type, $start_date, $end_date, $reason, $employee_id, $status);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Request Successfully Submitted";
    } else {
        $message = "Error saving record: " . mysqli_stmt_error($stmt);
    }

    // Close the prepared statement and the database connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Request Time off</title>    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .dashboard-container {
            display: flex;
            flex-grow: 1;
            height: 100%;
        }

        .sidebar {
            width: 250px;
            background-color: #ff9500;
            height: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .dashboard-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .success-message {
            color: green;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        input, select, textarea, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        button {
            background-color: #ff9500;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #e58900;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }
        }
    </style>
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
        <!-- Hamburger Menu -->
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        
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
                            <p><?php echo htmlspecialchars($user_name); ?></p>
                        </div>
                    </div>
                </div>
                <nav>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="update_details.php"><i class="fas fa-user"></i> Update Details</a>
                <a href="daily_tasks.php"><i class="fas fa-tasks"></i> Daily Tasks</a>
                <a href="timeOff.php"><i class="fas fa-calendar-alt"></i> Time Off</a>
                <a href="leave_balance.php"><i class="fas fa-calculator"></i> Leave Balance</a>
                <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
            </div>


        <!-- Main Content -->
        <div class="main-content">
    <h2>Request Time Off</h2>
    <div class="dashboard-content">
        <form action="timeOff.php" method="post" class="vertical-form">
            <label for="leave_type">Leave Type:</label>
            <select name="leave_type" id="leave_type">
                <option value="casual">Casual</option>
                <option value="sick">Sick</option>
                <option value="maternity">Maternity</option>
                <option value="study">Study</option>
                <option value="compassionate">Compassionate</option>
                <option value="unpaid">Unpaid</option>
            </select>

            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date">

            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date">

            <label for="reason">Reason:</label>
            <textarea name="reason" id="reason" rows="4"></textarea>

            <button type="submit">Submit</button>
        </form>

        <!-- Success Message -->
        <?php if (!empty($message)) : ?>
            <p class="success-message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</div>

<style>
    .success-message {
        margin-top: 10px;
        color: green;
        font-weight: bold;
        text-align: center;
    }
</style>

    </div>

    <script src="../../js/script.js"></script>

</body>
</html>
