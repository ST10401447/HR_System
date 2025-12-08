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
        display: flex;
        min-height: 100vh;
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

    /* Hide sidebar on mobile by default */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.active {
            transform: translateX(0);
        }
    }

    
   .main-content {
    margin-left: 300px;        
    width: calc(100% - 300px);
    min-height: 100vh;
    padding: 30px;
    padding-bottom: 100px;   
    overflow-y: auto;
    height: 100vh;          
}
    


   /* Hamburger menu button styles */
.hamburger-menu {
    display: none;
    position: absolute;
    top: 20px;
    left: 20px;
    cursor: pointer;
    z-index: 1100;
}

.hamburger-menu div {
    width: 35px;
    height: 5px;
    background-color: black;
    margin: 6px 0;
    transition: 0.4s;
}
    .overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 999;
    }
    .overlay.active { display: block; }

/* PROFILE */


.profile-section {
    text-align: center;          
    margin-bottom: 30px;
    padding: 20px 0;             
}


.profile-image {
    width: 120px;                 
    height: 120px;
    margin: 0 auto 15px auto;    
    border-radius: 50%;           
    overflow: hidden;             
    border: 4px solid rgba(255,255,255,0.8);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;            
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;            
    display: block;
}

.profile-btn {
    background: rgba(255,255,255,0.25);
    color: #fff;
    border: none;
    padding: 7px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.25s;
}

.profile-btn:hover {
    background: rgba(255,255,255,0.35);
}

/* NAV LINKS */
.nav-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nav-links a span {
    flex: 1;
    white-space: nowrap;       
    overflow: hidden;
    text-overflow: ellipsis;   
    text-align: left;
    padding-left: 5px;          
}


.nav-links a {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 13px 16px;         
    background: rgba(255,255,255,0.12);
    border-radius: 12px;
    color: white;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.25s ease;
    overflow: hidden;         
}

.nav-links a i {
    font-size: 19px;
    width: 24px;
    text-align: center;
}

/* TEXT ALIGNMENT */
.nav-links a span {
    flex: 1;   
}

/* Hover */
.nav-links a:hover {
    background: rgba(255,255,255,0.22);
    transform: translateX(3px);
}

/* Logout item */
.nav-links a.logout {
    margin-top: auto;
    background: rgba(247, 247, 247, 0.88);
   color: red
}

.nav-links a.logout:hover {
    background: rgba(0,0,0,0.35);
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


html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow-x: hidden;
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
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="manage_employee_tasks.php"><i class="fas fa-tasks"></i><span>Manage Employee Tasks</span></a>
            <a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i><span>Manage Leaves</span></a>
            <a href="update_details.php"><i class="fas fa-user"></i><span>Update Details</span></a>
            <a href="../Employee/dashboard.php"><i class="fas fa-exchange-alt"></i><span>Switch to Employee</span></a>
            <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Log Out</span></a>
        </nav>
    </aside>

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