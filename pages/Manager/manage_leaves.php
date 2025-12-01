<?php 
    include 'confirm_admin.php';

    // Database connection
    $host = 'localhost'; // database host
    $dbname = 'users1'; // database name
    $username = 'root'; // database username
    $password = ''; // database password

    // Use the correct variable names for database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle reject
    if (isset($_POST['reject_id'])) {
        $timeoff_id = $_POST['reject_id'];
        $conn->query("UPDATE timeoff SET status='Rejected' WHERE timeoff_id=$timeoff_id");
    }

    // Handle approve
    if (isset($_POST['approve_id'])) {
        $timeoff_id = $_POST['approve_id'];
        $conn->query("UPDATE timeoff SET status='Approved' WHERE timeoff_id=$timeoff_id");
    }

    function getEmployeesArray($conn) {
        $employees = [];
    
        $query = "SELECT employee_id, name FROM users";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $employees[$row['employee_id']] = $row['name'];
            }
        }
    
        return $employees;
    }

    // Usage example
    $employeesArray = getEmployeesArray($conn);
    // print_r($employeesArray); // Debugging output

    // Fetch leave requests
    $result = $conn->query("SELECT * FROM timeoff WHERE status='Pending'");
    $leave_requests = $result->fetch_all(MYSQLI_ASSOC);
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
    <link rel="stylesheet" href="../../css/modal.css">
    <title>Manage Leaves</title>
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
    <div class="dashboard-container">

        <!-- Hamburger Menu -->
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-image">
                        <!-- Profile image placeholder initially -->
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                    </div>
                    <!-- Button outside the .profile-image div to change picture -->
                    <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
                    <input type="file" id="imageUpload" style="display: none;" accept="image/*" onchange="updateProfileImage(event)">
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
        <div class="main-content">
            <h1>Manage Leaves</h1>
            <section class="task-table">
                <h2>Approve/Reject leave</h2>
                <div class="tabular--wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leave_requests as $leave_request): ?>
                                <tr>
                                    <td><?= $employeesArray[$leave_request['employee_id']] ?></td>
                                    <td><?= $leave_request['leave_type'] ?></td>
                                    <td><?= $leave_request['start_date'] ?></td>
                                    <td><?= $leave_request['end_date'] ?></td>
                                    <td><?= $leave_request['reason'] ?></td>
                                    <td class="status <?= strtolower($leave_request['status']) ?>"><?= $leave_request['status'] ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="reject_id" value="<?= $leave_request['timeoff_id'] ?>">
                                            <button type="submit" class="btn btn-delete">Reject</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="approve_id" value="<?= $leave_request['timeoff_id'] ?>">
                                            <button type="submit" class="btn btn-approve">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </section>

            <!-- Confirmation Message -->
            <?php if (isset($confirmation_message)): ?>
                <div class="confirmation-message">
                    <?= $confirmation_message ?>
                </div>
            <?php endif; ?>
        </div>
        
    </div>


    <script src="../../js/manage_employee_tasks.js"></script>
    <script src="../../js/script.js"></script>
</body>
</html>