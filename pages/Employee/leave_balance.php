<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection and session check
include 'confirm_employee.php';

$servername = "localhost";
$username = "root"; // Default for WampServer/XAMPP
$password = "";
$dbname = "users1";

// Fetch leave balance data from the database
try {
    // Connect to MySQL server
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch leave balance
    $sql = "SELECT study, sick, maternity, annual, unpaid, compassionate FROM leave_balance WHERE employee_id = :employee_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['employee_id' => $_SESSION['employee_id']]);
    $leave_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch approved leave data
    $sql = "SELECT leave_type, SUM(days_requested) as total_days FROM leave_requests WHERE employee_id = :employee_id AND status = 'approved' GROUP BY leave_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['employee_id' => $_SESSION['employee_id']]);
    $approved_leave_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    die("Error fetching leave balance data: " . $e->getMessage());
}

// Define leave types and their allowances
$leave_types = [
    'Study Leave' => ['key' => 'study', 'allowance' => 8],
    'Sick Leave' => ['key' => 'sick', 'allowance' => 8],
    'Maternity/Paternity Leave' => ['key' => 'maternity', 'allowance' => 60],
    'Annual Leave' => ['key' => 'annual', 'allowance' => 21],
    'Unpaid Leave' => ['key' => 'unpaid', 'allowance' => 8],
    'Compassionate Leave' => ['key' => 'compassionate', 'allowance' => 8],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/leave_balance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Leave Balance</title>
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
<!-- 
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.querySelector(".hamburger");
            const sidebar = document.querySelector(".sidebar");

            hamburger.addEventListener("click", function() {
                sidebar.classList.toggle("active");
            });
        });
    </script> -->
</head>
<body>
  <div class="container">
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
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User  Profile">
                    <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                </div>
                <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
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
   <div class="content">
        <style>
            .content {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                padding: 10px;
            }

            .table-container {
                width: 100%;
                max-width: 1200px;
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                background-color: white;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow: hidden;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 14px;
                text-align: left;
                font-size: 18px;
            }

            th {
                background-color: rgb(240, 141, 3);
                color: black;
                text-transform: uppercase;
            }

            tr:nth-child(even) {
                background-color: #ff9500;
            }

            tr:hover {
                background-color: #ffe0b3;
                transition: 0.3s;
            }

            @media (max-width: 768px) {
                .table-container {
                    width: 100%;
                    padding: 0 10px;
                }

                table {
                    font-size: 16px;
                }

                th, td {
                    padding: 10px;
                }
            }
        </style>

        <h1 style="text-align: center;">Leave Balance</h1>

        <div class="table-container">
            <table>
                <tr>
                    <th>Leave Type</th>
                    <th>Used Days</th>
                    <th>Remaining Days</th>
                    <th>Total Allowance</th>
                </tr>
                <?php foreach ($leave_types as $leave_name => $leave_info):
                    $used_days = $approved_leave_data[$leave_info['key']] ?? 0;
                    $remaining_days = $leave_info['allowance'] - $used_days;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($leave_name); ?></td>
                    <td><?php echo htmlspecialchars($used_days); ?></td>
                    <td><?php echo htmlspecialchars($remaining_days); ?></td>
                    <td><?php echo htmlspecialchars($leave_info['allowance']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        function updateProfilePic() {
            const fileInput = document.getElementById('imageUpload');
            const profilePic = document.getElementById('profilePic');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    profilePic.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        }
 </script>
    <script src="../../js/script.js"></script>
</body>  
</html>