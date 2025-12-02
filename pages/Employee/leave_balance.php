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

     .layout {
        min-height: 100vh;
        position: relative;
    }

    
    .sidebar {
        width: 300px;
        background: rgba(255, 149, 0, 0.95);
        backdrop-filter: blur(5px);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding: 20px 18px;
        box-shadow: 2px 0 15px rgba(0,0,0,0.15);
        z-index: 1000;
        transition: transform 0.35s ease;
        overflow-y: auto;
    }

    .profile-section { text-align: center; margin-bottom: 30px; padding: 20px 0; }
    .profile-image {
        width: 120px; height: 120px;
        margin: 0 auto 15px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid rgba(255,255,255,0.8);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .profile-image img { width: 100%; height: 100%; object-fit: cover; }
    .profile-name { color: white; font-size: 19px; font-weight: 600; margin: 10px 0; }
    .profile-btn {
        background: rgba(255,255,255,0.25);
        color: white; border: none; padding: 8px 16px;
        border-radius: 8px; cursor: pointer; font-size: 14px;
    }
    .profile-btn:hover { background: rgba(255,255,255,0.35); }

    .nav-links { display: flex; flex-direction: column; gap: 10px; flex: 1; }
    .nav-links a {
        display: flex; align-items: center; gap: 14px;
        padding: 13px 16px; background: rgba(255,255,255,0.12);
        border-radius: 12px; color: white; text-decoration: none;
        font-size: 16px; transition: all 0.25s ease;
    }
    .nav-links a i { font-size: 19px; width: 24px; text-align: center; }
    .nav-links a span { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .nav-links a:hover { background: rgba(255,255,255,0.22); transform: translateX(5px); }
    .nav-links a.active { background: rgba(255,255,255,0.3); font-weight: bold; }
    .nav-links a.logout {
        margin-top: auto;
        background: rgba(247,247,247,0.95);
        color: red;
        font-weight: bold;
    }
    .nav-links a.logout:hover { background: #ff9500; color: white; }

    /* HAMBURGER – MOBILE ONLY */
    .hamburger {
        display: none;
        position: fixed;
        top: 18px;
        left: 18px;
        background: #ff9500;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        z-index: 1100;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    @media (max-width: 768px) {
        .hamburger { display: flex !important; }
    }

    .overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        z-index: 999;
    }
    .overlay.active { display: block; }

    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.active { transform: translateX(0); }
    }

    
    .main-content {
        margin-left: 300px;
        min-height: 100vh;
        padding: 30px 40px 150px 40px;
        overflow-y: auto;                    
        -webkit-overflow-scrolling: touch;
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0 !important;
            padding: 90px 20px 150px 20px !important;  /* space for hamburger */
        }
    }

   
    .circular-slide {
        font-size: 2.5em;
        color: #000;
        display: block;
        text-align: center;
        margin: 20px 0 40px 0;
        animation: none; 
    }
    .circular-slide i { margin-right: 15px; color: #ff9500; }

    @media (max-width: 768px) {
        .circular-slide { font-size: 1.8em; }
    }
    @media (max-width: 480px) {
        .circular-slide { font-size: 1.5em; }
    }

  
    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: space-between;
    }
    .left, .right {
        flex: 1;
        min-width: 300px;
    }
html, body {
    margin: 0;
    padding: 0;
   
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 80px 15px 50px 15px !important;  
    }
    
    .hamburger {
        display: block !important;
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

<div class="layout">

    <!-- HAMBURGER & OVERLAY -->
    <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
    <div class="overlay" id="overlay"></div>

    <!-- SIDEBAR - EXACT ADMIN STYLE -->
    <aside class="sidebar" id="sidebar">
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?php echo htmlspecialchars($profile_picture ?? '../../resources/default-avatar.png'); ?>" 
                     id="profilePic" alt="Profile">
            </div>
            <p class="profile-name"><?php echo htmlspecialchars($user_name); ?></p>
            <button class="profile-btn" onclick="document.getElementById('imageUpload').click()">
                Change Picture
            </button>
            <input type="file" id="imageUpload" hidden accept="image/*">
        </div>

        <nav class="nav-links">
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="update_details.php"><i class="fas fa-user"></i><span>Update Details</span></a>
            <a href="daily_tasks.php"><i class="fas fa-tasks"></i><span>Daily Tasks</span></a>
            <a href="timeOff.php"><i class="fas fa-calendar-alt"></i><span>Time Off</span></a>
            <a href="leave_balance.php"><i class="fas fa-calculator"></i><span>Leave Balance</span></a>
            <a href="feedback.php"><i class="fas fa-comment-dots"></i><span>Feedback</span></a>
            <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Log Out</span></a>
        </nav>
    </aside>

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