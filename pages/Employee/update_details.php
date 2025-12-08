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

    // Fetch current employee data
$result = $conn->query("SELECT * FROM users WHERE employee_id = '$employee_id'");
$employee = $result->num_rows > 0 ? $result->fetch_assoc() : [];

// Handle form submission
$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_details'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $nationality = trim($_POST['nationality'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $race = trim($_POST['race'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $mobile = trim($_POST['mobile'] ?? '');
    $emergency_name = trim($_POST['emergency_name'] ?? '');
    $emergency_number = trim($_POST['emergency_number'] ?? '');

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $title = mysqli_real_escape_string($conn, $title);
    $nationality = mysqli_real_escape_string($conn, $nationality);
    $gender = mysqli_real_escape_string($conn, $gender);
    $race = mysqli_real_escape_string($conn, $race);
    $mobile = mysqli_real_escape_string($conn, $mobile);
    $emergency_name = mysqli_real_escape_string($conn, $emergency_name);
    $emergency_number = mysqli_real_escape_string($conn, $emergency_number);

    $sql = "UPDATE users SET 
            name='$name', email='$email', title='$title', dob='$dob', nationality='$nationality',
            gender='$gender', race='$race', start_date='$start_date', mobile='$mobile',
            emergency_name='$emergency_name', emergency_number='$emergency_number'
            WHERE employee_id='$employee_id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['name'] = $name;
        $user_name = $name;
        $message = "Details updated successfully!";
    } else {
        $message = "Error updating record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Details</title>
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/updatedetails.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* === CLEAN & ORGANIZED CSS === */
        :root {
            --orange: #ff9500;
            --dark-orange: #e68600;
        }

        html, body {
            margin: 0; padding: 0; height: 100%; overflow-x: hidden;
        }

        .layout { min-height: 100vh; position: relative; display: flex; }
        .sidebar {
            width: 300px; background: rgba(255,149,0,0.95); height: 100vh; position: fixed; left: 0; top: 0;
            padding: 20px 18px; box-shadow: 2px 0 15px rgba(0,0,0,0.15); z-index: 1000;
            transition: transform 0.35s ease; overflow-y: auto;
        }
        .hamburger {
            display: none; position: fixed; top: 18px; left: 18px; background: var(--orange);
            width: 50px; height: 50px; border-radius: 50%; font-size: 24px; color: white;
            z-index: 1100; cursor: pointer; align-items: center; justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 999; }
        .overlay.active { display: block; }

        @media (max-width: 768px) {
            .hamburger { display: flex !important; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }

        .main-content {
            margin-left: 300px; padding: 40px; flex: 1; min-height: 100vh;
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0 !important; padding: 90px 20px 120px !important; }
        }

        .profile-section { text-align: center; margin-bottom: 30px; }
        .profile-image { width: 120px; height: 120px; margin: 0 auto 15px; border-radius: 50%; overflow: hidden; border: 4px solid rgba(255,255,255,0.8); }
        .profile-image img { width: 100%; height: 100%; object-fit: cover; }
        .profile-name { color: white; font-size: 19px; font-weight: 600; margin: 10px 0; }
        .profile-btn { background: rgba(255,255,255,0.25); color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; }
        .profile-btn:hover { background: rgba(255,255,255,0.35); }

        .nav-links a {
            display: flex; align-items: center; gap: 14px; padding: 13px 16px;
            background: rgba(255,255,255,0.12); border-radius: 12px; color: white; text-decoration: none;
            font-size: 16px; transition: all 0.25s ease;
        }
        .nav-links a:hover { background: rgba(255,255,255,0.22); transform: translateX(5px); }
        .nav-links a.active { background: rgba(255,255,255,0.3); font-weight: bold; }
        .nav-links a.logout { margin-top: auto; background: #fff; color: red; }
        .nav-links a.logout:hover { background: var(--orange); color: white; }

        .update-details { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .update-details h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        .save-button, .download-button {
            background: var(--orange); color: white; padding: 14px 30px; border: none;
            border-radius: 8px; font-size: 16px; cursor: pointer; transition: 0.3s;
        }
        .save-button:hover, .download-button:hover { background: var(--dark-orange); }
        .success-message { color: green; text-align: center; margin-top: 20px; font-weight: bold; }
        #error_message { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>

<div class="layout">
    <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
    <div class="overlay" id="overlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?= htmlspecialchars($profile_picture) ?>" id="profilePic" alt="Profile">
            </div>
            <p class="profile-name"><?= htmlspecialchars($user_name) ?></p>
            <button class="profile-btn" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
            <input type="file" id="imageUpload" hidden accept="image/*">
        </div>

        <nav class="nav-links">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="update_details.php" class="active"><i class="fas fa-user"></i><span>Update Details</span></a>
            <a href="daily_tasks.php"><i class="fas fa-tasks"></i><span>Daily Tasks</span></a>
            <a href="timeOff.php"><i class="fas fa-calendar-alt"></i><span>Time Off</span></a>
            <a href="leave_balance.php"><i class="fas fa-calculator"></i><span>Leave Balance</span></a>
            <a href="feedback.php"><i class="fas fa-comment-dots"></i><span>Feedback</span></a>
            <a href="../Admin/dashboard.php"><i class="fas fa-exchange-alt"></i><span>Switch to Admin</span></a>
            <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Log Out</span></a>
        </nav>
    </aside>

    <div class="main-content">
        <div class="update-details">
            <h1>Update Your Details</h1>

            <?php if ($message): ?>
                <p class="success-message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" class="update-form">
                <input type="hidden" name="save_details" value="1">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($employee['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($employee['title'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= $employee['dob'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Nationality</label>
                    <input type="text" name="nationality" value="<?= htmlspecialchars($employee['nationality'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <input type="text" name="gender" value="<?= htmlspecialchars($employee['gender'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Race</label>
                    <input type="text" name="race" value="<?= htmlspecialchars($employee['race'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="<?= $employee['start_date'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" value="<?= htmlspecialchars($employee['mobile'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Emergency Contact Name</label>
                    <input type="text" name="emergency_name" value="<?= htmlspecialchars($employee['emergency_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Emergency Contact Number</label>
                    <input type="text" name="emergency_number" value="<?= htmlspecialchars($employee['emergency_number'] ?? '') ?>">
                </div>

                <button type="submit" class="save-button">Save Changes</button>
            </form>

            <br><br>
            <form action="../../php/upload_cv.php" method="post" enctype="multipart/form-data" onsubmit="return validateFile()">
                <label><strong>Upload Your CV</strong></label><br><br>
                <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" required style="padding:10px;">
                <br><br>
                <button type="submit" name="upload_cv" class="download-button">Upload CV</button>
            </form>
            <p id="error_message"></p>
        </div>
    </div>
</div>

<script>
// Hamburger Menu
document.getElementById('hamburger')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
});
document.getElementById('overlay')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('active');
    document.getElementById('overlay').classList.remove('active');
});

// CV Validation
function validateFile() {
    const fileInput = document.getElementById('cv_file');
    const filePath = fileInput.value;
    const allowedExtensions = /(\.pdf|\.doc|\.docx)$/i;
    const errorMsg = document.getElementById('error_message');

    if (!allowedExtensions.exec(filePath)) {
        errorMsg.textContent = "Please upload a PDF, DOC, or DOCX file only.";
        fileInput.value = '';
        return false;
    }
    errorMsg.textContent = '';
    return true;
}
</script>

</body>
</html>