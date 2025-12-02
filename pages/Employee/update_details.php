<?php
    include 'confirm_employee.php';
    
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
        $title = $_POST["title"] ?? '';
        $dob = $_POST["dob"] ?? '';
        $nationality = $_POST["nationality"] ?? '';
        $gender = $_POST["gender"] ?? '';
        $race = $_POST["race"] ?? '';
        $start_date = $_POST["start_date"] ?? '';
        $mobile = $_POST["mobile"] ?? '';        
        $emergency_name = $_POST["emergency_name"] ?? '';
        $emergency_number = $_POST["emergency_number"] ?? '';

        $conn->query("UPDATE users SET name='$name', email='$email', title='$title', dob='$dob', nationality='$nationality', gender='$gender', race='$race', start_date='$start_date', mobile='$mobile', emergency_name='$emergency_name', emergency_number='$emergency_number' WHERE employee_id=$employee_id");                       

        $_SESSION['user_name'] = $name;
    }

    // Fetch employees
    $result = $conn->query("SELECT * FROM users WHERE employee_id=$employee_id");
    $employee_pre = $result->fetch_all(MYSQLI_ASSOC);
    $employee = $employee_pre[0];
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

<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.querySelector(".hamburger");
        const sidebar = document.querySelector(".sidebar");

        hamburger.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    });
</script> -->


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
        
        <div class="main-content">
            <div class="update-details">
                <h1>Update Details</h1>
                <form method="POST" action="update_details.php" class="update-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" value="<?= $employee['name']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= $employee['email']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?= $employee['title']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="<?= $employee['dob']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nationality</label>
                        <input type="text" name="nationality" value="<?= $employee['nationality']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>
                        <input type="text" name="gender" value="<?= $employee['gender']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Race</label>
                        <input type="text" name="race" value="<?= $employee['race']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" value="<?= $employee['start_date']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" value="<?= $employee['mobile']?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Emergency Name</label>
                        <input type="text" name="emergency_name" value="<?= $employee['emergency_name']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Emergency Number</label>
                        <input type="text" name="emergency_number" value="<?= $employee['emergency_number']?>" required>
                    </div>

                    <button type="submit" class="save-button">Save</button>
                </form>

                <form action="../../php/upload_cv.php" method="post" enctype="multipart/form-data" onsubmit="return validateFile()">
                    <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" required>
                    <br><br>
                    
                    <button type="submit" name="upload_cv"  class="download-button" style="
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
">Upload CV</button>
                </form>
                <p id="error_message" style="color:red;"></p>
                <?php if (!empty($message)) : ?>
                    <p class="success-message"><?php echo $message; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../../js/script.js"></script>

</body>
</html>
