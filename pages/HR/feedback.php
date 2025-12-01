<?php
include 'confirm_admin.php';  // Restrict access to HR only

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "users1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback from the database
$query = "SELECT employee_id, target_employee_id, feedback_text, submission_date FROM feedback ORDER BY submission_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Fetch votes and calculate results
$vote_query = "SELECT employee_id, COUNT(*) AS vote_count FROM votes WHERE DATE_FORMAT(vote_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') GROUP BY employee_id";
$vote_result = mysqli_query($conn, $vote_query);

if (!$vote_result) {
    die("Vote query failed: " . mysqli_error($conn));
}

// Prepare an array to hold vote counts
$vote_counts = [];
while ($row = mysqli_fetch_assoc($vote_result)) {
    $vote_counts[$row['employee_id']] = $row['vote_count'];
}

// Determine the winner
$winner_id = null;
$max_votes = 0;
foreach ($vote_counts as $id => $count) {
    if ($count > $max_votes) {
        $max_votes = $count;
        $winner_id = $id;
    }
}

// Fetch winner's name
$winner_name = '';
if ($winner_id) {
    $winner_query = "SELECT name FROM users WHERE employee_id = $winner_id";
    $winner_result = mysqli_query($conn, $winner_query);
    if ($winner_result) {
        $winner_row = mysqli_fetch_assoc($winner_result);
        $winner_name = $winner_row['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="../../css/emp_dashboard.css">    
    <link rel="stylesheet" href="../../css/dashBstyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Dashboard</title>    
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Sidebar styles */
        .sidebar {
            width: 300px; 
            background-color: #ff9500;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 30px;
            box-sizing: border-box;
            transition: transform 0.3s ease-in-out;
            font-size: 18px;
            position: fixed; /* Keep sidebar fixed */
            left: 0;
            top: 0;
            z-index: 1000;
        }

        /* Main content styles */
        .dashboard-container {
            margin-left: 300px; /* Space for the sidebar */
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Feedback table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center; /* Center align text in table */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        th {
            background-color: #ff9500;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        /* Hamburger menu button styles */
        .hamburger {
            display: none;
            position: absolute;
            top : 20px;
            left: 20px;
            cursor: pointer;
            z-index: 1100;
        }

        .hamburger div {
            width: 35px;
            height: 5px;
            background-color: black;
            margin: 6px 0;
            transition: 0.4s;
        }

        /* Responsive styles */
        @media screen and (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100vh;
                background-color: #ff9500;
                padding: 20px;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            }

            .dashboard-container {
                margin-left: 0; /* No margin for mobile */
            }

            .hamburger {
                display: block; /* Show hamburger menu on mobile */
            }
        }

        /* Show sidebar when active */
        .sidebar.active {
            transform: translateX(0);
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
            <a href="manage_employees.php"><i class="fas fa-users"></i> Manage Employees</a>
            <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            <a href="view_report.php"><i class="fas fa-calendar-check"></i> View Report</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="feedback-container">
        <h2>Complaints and Compliments</h2>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Target Employee ID</th>
                    <th>Feedback</th>
                    <th>Submission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['target_employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                        <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


        <br>
        <br>
        <br>
        <h2>Voting Results for Employee of the Month</h2>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee/Manager Name</th>
                    <th>Vote Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($vote_counts as $id => $count): 
                    $employee_query = "SELECT name FROM users WHERE employee_id = $id";
                    $employee_result = mysqli_query($conn, $employee_query);
                    $employee_name = '';
                    if ($employee_result) {
                        $employee_row = mysqli_fetch_assoc($employee_result);
                        $employee_name = $employee_row['name'];
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($id); ?></td>
                        <td><?php echo htmlspecialchars($employee_name); ?></td>
                        <td><?php echo htmlspecialchars($count); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <br>
        <br>
        <?php if ($winner_name): ?>
            <h3>Winner of the Month: <?php echo htmlspecialchars($winner_name); ?> (Employee ID: <?php echo htmlspecialchars($winner_id); ?>) with <?php echo htmlspecialchars($max_votes); ?> votes!</h3>
        <?php else: ?>
            <h3>No votes have been cast this month yet.</h3>
        <?php endif; ?>
    </div>
</div>
</body>
</html>