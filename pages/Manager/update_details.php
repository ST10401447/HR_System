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


    <div class="dashboard-container">

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
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_employee_tasks.php" class="active"><i class="fas fa-tasks"></i> Manage Employee Tasks</a>
                <a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i> Manage Leaves</a>
                <a href="update_details.php"><i class="fas fa-users"></i> Update Details</a>
                <a href="manage_employee_tasks.php"><i class="fas fa-users"></i> Manage Employee Tasks</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <a href="../Employee/dashboard.php"><i class="fas fa-sign-out-alt"></i> Switch to Employee</a>
            </nav>
        </div>
        
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
