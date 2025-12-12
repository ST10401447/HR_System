PHP<?php
    include 'confirm_employee.php';
$employee_id = $_SESSION['employee_id'] ?? null;

if (!$employee_id) {
    die("Error: Employee ID not found in session.");
}

    
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

    // === HR REQUIRED DOCUMENTS (ONLY YOUR 4) ===
$document_types = [
    'ID'             => 'ID / Passport',
    'CONTRACT'        => 'Employment Contract',
    'QUALIFICATIONS'  => 'Certificates / Qualifications',
    'OTHER'           => 'Other Document'
];

// Handle document upload
$upload_message = '';
if (isset($_POST['upload_document'])) {
    $doc_type = $_POST['doc_type'] ?? '';
    $file = $_FILES['document_file'] ?? null;

    if ($file && $file['error'] === 0 && array_key_exists($doc_type, $document_types)) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $file['size'] <= 10_000_000) { // 10MB max
            $filename = $employee_id . "_" . $doc_type . "_" . time() . "." . $ext;
            $upload_dir = "../../uploads/documents/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                $stmt = $conn->prepare("
                    INSERT INTO employee_documents (employee_id, doc_type, filename, upload_date) 
                    VALUES (?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE filename = VALUES(filename), upload_date = NOW()
                ");
                $stmt->bind_param("iss", $employee_id, $doc_type, $filename);
                $stmt->execute();
                $upload_message = "<div class='success-message'>✓ $document_types[$doc_type] uploaded successfully!</div>";
            } else {
                $upload_message = "<div class='error-message'>Upload failed. Please try again.</div>";
            }
        } else {
            $upload_message = "<div class='error-message'>Invalid file. Only PDF, DOC, DOCX, JPG, PNG allowed (max 10MB).</div>";
        }
    }
}
    // Fetch employees
  $result = $conn->query("SELECT * FROM users WHERE employee_id='$employee_id'");


if (!$result) {
    die("SQL Error: " . $conn->error);
}

$employee_pre = $result->fetch_all(MYSQLI_ASSOC);

if (empty($employee_pre)) {
    die("Error: Employee not found.");
}

$employee = $employee_pre[0];


    $docs = [];
$doc_query = $conn->prepare("
    SELECT doc_type, filename, upload_date 
    FROM employee_documents 
    WHERE employee_id = ?
");
$doc_query->bind_param("i", $employee_id);
$doc_query->execute();
$result_docs = $doc_query->get_result();

while ($row = $result_docs->fetch_assoc()) {
    $docs[$row['doc_type']] = $row;
}

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

.uploaded-documents {
    margin-top: 25px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.doc-item {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.doc-item:last-child {
    border-bottom: none;
}

.doc-link {
    color: #0077cc;
    margin-left: 10px;
}

.missing-doc {
    color: red;
    font-weight: 600;
    margin-left: 10px;
}

.upload-date {
    margin-left: 10px;
    color: #777;
    font-size: 0.9em;
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
            <a href="../Admin/dashboard.php"><i class="fas fa-exchange-alt"></i><span>Switch to Admin</span></a>
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
                        <select name="title" required>
                            <option value="">Select Title</option>
                            <option <?= ($employee['title'] ?? '')=='Mr' ? 'selected' : '' ?>>Mr</option>
                            <option <?= ($employee['title'] ?? '')=='Mrs' ? 'selected' : '' ?>>Mrs</option>
                            <option <?= ($employee['title'] ?? '')=='Miss' ? 'selected' : '' ?>>Miss</option>
                            <option <?= ($employee['title'] ?? '')=='Ms' ? 'selected' : '' ?>>Ms</option>
                            <option <?= ($employee['title'] ?? '')=='Dr' ? 'selected' : '' ?>>Dr</option>
                            <option <?= ($employee['title'] ?? '')=='Prof' ? 'selected' : '' ?>>Prof</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="<?= $employee['dob']?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nationality</label>
                        <select name="nationality">
                            <option value="">Select Nationality</option>
                            <option <?= ($employee['nationality'] ?? '')=='South African' ? 'selected' : '' ?>>South African</option>
                            <option <?= ($employee['nationality'] ?? '')=='Nigerian' ? 'selected' : '' ?>>Nigerian</option>
                            <option <?= ($employee['nationality'] ?? '')=='Kenyan' ? 'selected' : '' ?>>Kenyan</option>
                            <option <?= ($employee['nationality'] ?? '')=='Ghanaian' ? 'selected' : '' ?>>Ghanaian</option>
                            <option <?= ($employee['nationality'] ?? '')=='Zimbabwean' ? 'selected' : '' ?>>Zimbabwean</option>
                            <option <?= ($employee['nationality'] ?? '')=='Indian' ? 'selected' : '' ?>>Indian</option>
                            <option <?= ($employee['nationality'] ?? '')=='British' ? 'selected' : '' ?>>British</option>
                            <option <?= ($employee['nationality'] ?? '')=='American' ? 'selected' : '' ?>>American</option>
                            <option <?= ($employee['nationality'] ?? '')=='Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option <?= ($employee['gender'] ?? '')=='Male' ? 'selected' : '' ?>>Male</option>
                            <option <?= ($employee['gender'] ?? '')=='Female' ? 'selected' : '' ?>>Female</option>
                            <option <?= ($employee['gender'] ?? '')=='Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Race</label>
                        <select name="race">
                            <option value="">Select Race</option>
                            <option <?= ($employee['race'] ?? '')=='Black' ? 'selected' : '' ?>>Black</option>
                            <option <?= ($employee['race'] ?? '')=='White' ? 'selected' : '' ?>>White</option>
                            <option <?= ($employee['race'] ?? '')=='Coloured' ? 'selected' : '' ?>>Coloured</option>
                            <option <?= ($employee['race'] ?? '')=='Indian' ? 'selected' : '' ?>>Indian</option>
                            <option <?= ($employee['race'] ?? '')=='Asian' ? 'selected' : '' ?>>Asian</option>
                            <option <?= ($employee['race'] ?? '')=='Other' ? 'selected' : '' ?>>Other</option>
                        </select>
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

                <div class="documents-section">

            <?= $upload_message ?>

            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div>
                    <label><strong>Document Type</strong></label>
                    <select name="doc_type" required>
                        <option value="">Choose Document</option>
                        <?php foreach ($document_types as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label><strong>Select File</strong></label>
                    <input type="file" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                </div>
                <div>
                    <button type="submit" name="upload_document" class="upload-btn">
                        Upload Document
                    </button>
                </div>
            </form>

                  
                </form>
                <p id="error_message" style="color:red;"></p>
                <?php if (!empty($message)) : ?>
                    <p class="success-message"><?php echo $message; ?></p>
                <?php endif; ?>
            </div>
             <h3>Uploaded Documents</h3>

<div class="uploaded-documents">
    <?php foreach ($document_types as $key => $label): ?>
        <div class="doc-item">
            <strong><?= $label ?>:</strong>

            <?php if (isset($docs[$key])): ?>
                <a href="../../uploads/documents/<?= $docs[$key]['filename'] ?>" 
                   target="_blank" class="doc-link">
                   View / Download
                </a>
                <span class="upload-date">
                    (Uploaded: <?= date('Y-m-d', strtotime($docs[$key]['upload_date'])) ?>)
                </span>
            <?php else: ?>
                <span class="missing-doc">Not Uploaded</span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

        </div>
    </div>

   
    <script src="../../js/script.js"></script>

</body>
</html>