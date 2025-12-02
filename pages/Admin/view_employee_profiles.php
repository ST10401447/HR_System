<?php
    include 'confirm_admin.php';
    
    // Database connection variables
    $host = "localhost";
    $dbname = "users1";
    $username = "root";
    $password = "";

    // Establish database connection
    $conn = mysqli_connect($host, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection error: " . mysqli_connect_error());
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data using POST method
        $name = $_POST["name"] ?? '';
        $email = $_POST["email"] ?? '';
        $employee__id = $_POST["employee__id"] ?? 0;
        $department = $_POST["department"] ?? '';
        $role = $_POST["role"] ?? '';
        $title = $_POST["title"] ?? '';
        $dob = $_POST["dob"] ?? '';
        $nationality = $_POST["nationality"] ?? '';
        $gender = $_POST["gender"] ?? '';
        $race = $_POST["race"] ?? '';
        $start_date = $_POST["start_date"] ?? '';
        $mobile = $_POST["mobile"] ?? '';        
        $emergency_name = $_POST["emergency_name"] ?? '';
        $emergency_number = $_POST["emergency_number"] ?? '';

        $conn->query("UPDATE users SET name='$name', email='$email', employee_id='$employee__id', department='$department', role='$role', title='$title', dob='$dob', nationality='$nationality', gender='$gender', race='$race', start_date='$start_date', mobile='$mobile', emergency_name='$emergency_name', emergency_number='$emergency_number' WHERE employee_id=$employee__id");                       
    }

    // Fetch employees
    $result = $conn->query("SELECT * FROM users");
    $employees = $result->fetch_all(MYSQLI_ASSOC);
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/updatedetails.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Update Details</title>    
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

<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.querySelector(".hamburger");
        const sidebar = document.querySelector(".sidebar");

        hamburger.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    });
</script> -->
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
            
            <div class="update-details">
                <h1>Update Details</h1>
                <div class="update-form">
                    <div class="form-group">
                        <label>Employee Name</label>
                        <select id="employeeDropdown" name="employee_id">
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['employee_id'] ?>">
                                    <?= $employee['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <input type="text" hidden>
                </div>

                <form method="POST" action="view_employee_profiles.php" class="update-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" id="email" required>
                    </div>   
                    
                    

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select name="department" id="department" required>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Public Relations">Public Relations</option>
                            <option value="Graphic Design">Graphic Design</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>role</label>
                        <select name="role" id="role" required>
                            <option value="Admin">Admin</option>
                            <option value="HR">HR</option>
                            <option value="Manager">Manager</option>
                            <option value="Employee">Employee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" id="title" required>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="dob" required>
                    </div>

                    <div class="form-group">
                        <label>Nationality</label>
                        <input type="text" name="nationality" id="nationality" required>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>
                        <input type="text" name="gender" id="gender" required>
                    </div>

                    <div class="form-group">
                        <label>Race</label>
                        <input type="text" name="race" id="race" required>
                    </div>

                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="start_date" required>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Emergency Name</label>
                        <input type="text" name="emergency_name" id="emergency_name" required>
                    </div>

                    <div class="form-group">
                        <label>Emergency Number</label>
                        <input type="text" name="emergency_number" id="emergency_number" required>
                    </div>

                    <div class="form-group">
                        <input type="text" name="employee__id" id="employee__id" required hidden>
                    </div>

                    <button type="submit" class="save-button">Save</button>
                </form>

                
                <a href="javascript:void(0);" onclick="downloadCV()" id="downloadCV" class="download-button" style="
    background-color: #ff9500; /* Primary blue */
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none; /* Remove underline */
    transition: background-color 0.3s ease; /* Smooth hover effect */
    display: inline-block; /* Allows padding and margin */
    text-align: center; /* Center text */
">Download CV</a>

<style>
.download-button:hover {
    background-color: #ff9500; /* Darker blue on hover */
}
</style>

                <script>
function downloadCV() {
    const employeeId = document.getElementById('employeeDropdown').value;

    if (employeeId) {
        window.location.href = `../../php/download_cv.php?employee_id=${employeeId}`;
    } else {
        alert("Please select an employee to download the CV.");
    }
}
</script>


                
                <?php if (!empty($message)) : ?>
                    <p class="success-message"><?php echo $message; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../../js/view_employee_profiles.js"></script>
    <script src="../../js/script.js"></script>

</body>
</html>


