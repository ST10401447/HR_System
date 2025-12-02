<?php
// manage_employees.php
include 'confirm_admin.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Allows long words to wrap */
        }

        th {
            background-color: #ffab40;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons button, .action-buttons a {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-danger {
            background-color: #d9534f;
            color: white;
            border: none;
        }

        .btn-primary {
            background-color: orange;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c9302c;
        }

        .btn-primary:hover {
            background-color: #ffab40;
        }
    </style>
</head>
<body>


<style>
    /* Default styles for the sidebar */
.sidebar {
    width: 300px; /* Increased the sidebar width */
    background-color: #ff9500;
    height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 30px;
    box-sizing: border-box;
    transition: transform 0.3s ease-in-out;
    font-size: 18px;
}

/* Hide the sidebar by default on small screens */
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
        z-index: 1000;
    }
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

/* Show sidebar when active */
.sidebar.active {
    transform: translateX(0);
}

@media screen and (max-width: 768px) {
    .hamburger-menu {
        display: block;
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

<!-- NEW SIDEBAR -->
<div class="layout">

<!-- MOBILE MENU BUTTON -->
<div class="hamburger" id="hamburger">
    <i class="fas fa-bars"></i>
</div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">

            <div class="profile-section">
                <div class="profile-image">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic">
                    <input type="file" id="imageUpload" hidden accept="image/*" onchange="updateProfilePic()">
                </div>

                <p class="profile-name"><?php echo htmlspecialchars($user_name); ?> - Admin</p>

                <button class="profile-btn" onclick="document.getElementById('imageUpload').click()">
                    Change Picture
                </button>
            </div>

            <nav class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="manage_employee_tasks.php"><i class="fas fa-tasks"></i><span>Manage Employee Tasks</span></a>
                <a href="#"><i class="fas fa-calendar-alt"></i><span>Manage Leaves</span></a>
                <a href="view_employee_profiles.php"><i class="fas fa-users"></i><span>View Employee Profiles</span></a>
                <a href="manage_employees.php"><i class="fas fa-users-cog"></i><span>Manage Employees</span></a>
                <a href="admin_approve_registrations.php"><i class="fas fa-user-check"></i><span>Approve Registrations</span></a>
                <a href="../Employee/dashboard.php"><i class="fas fa-exchange-alt"></i><span>Switch to Employee</span></a>
                <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>

        </div>
    </aside>


        <div class="main-content">
            <h1>Manage Employee List</h1>
            <br>
            <a class="btn btn-primary custom-button" href="add_employee.php" role="button" data-toggle="modal" data-target="#addEmployeeModal">New Employee</a>

<style>
.custom-button {
    background-color: #ff9500;
    border-color: #ff9500;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.custom-button:hover {
    background-color: black;
    border-color: black;
}
</style>
            <br>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Company ID</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $host = 'localhost';
                    $dbname = 'users1';
                    $username = 'root';
                    $password = '';

                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = "SELECT * FROM users";
                        $stmt = $conn->query($sql);

                        if ($stmt) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['employee_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='btn btn-danger' onclick=\"location.href='delete.php?id=" . htmlspecialchars($row['employee_id']) . "'\">Delete</button>";
                                echo "<a class='btn btn-primary' href='view_employee_profiles.php?id=" . htmlspecialchars($row['employee_id']) . "'>Edit</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "Error: Query failed.";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                </tbody>
            </table>
            <br><br>
        </div>
    </div>
</body>
</html>