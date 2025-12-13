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


// ===============================
// UPDATE EMPLOYEE DETAILS
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['upload_document'])) {

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

    $conn->query("
        UPDATE users SET
            name='$name',
            email='$email',
            title='$title',
            dob='$dob',
            nationality='$nationality',
            gender='$gender',
            race='$race',
            start_date='$start_date',
            mobile='$mobile',
            emergency_name='$emergency_name',
            emergency_number='$emergency_number'
        WHERE employee_id='$employee_id'
    ");

    $_SESSION['user_name'] = $name;
}

// ===============================
// HR REQUIRED DOCUMENTS
// ===============================
$document_types = [
    'ID'             => 'ID / Passport',
    'CONTRACT'       => 'Employment Contract',
    'QUALIFICATIONS' => 'Certificates / Qualifications',
    'OTHER'          => 'Other Document'
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
                $stmt->bind_param("sss", $employee_id, $doc_type, $filename);
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
// ===============================
// FETCH EMPLOYEE DETAILS
// ===============================
$employee_id_safe = mysqli_real_escape_string($conn, $employee_id);
$sql = "SELECT * FROM users WHERE employee_id = '$employee_id_safe'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $employee = $result->fetch_assoc();
} else {
    die("Employee not found.");
}

// ===============================
// FETCH UPLOADED DOCUMENTS//
// ===============================
$docs = [];

$docQuery = $conn->prepare("
    SELECT doc_type, filename, upload_date
    FROM employee_documents
    WHERE employee_id = ?
");
$docQuery->bind_param("s", $employee_id);
$docQuery->execute();
$docResult = $docQuery->get_result();

while ($row = $docResult->fetch_assoc()) {
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

.upload-box {
    max-width: 480px;
    margin: 50px auto;
    padding: 30px;
    background: #f9fafb;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    font-family: system-ui, sans-serif;
    position: relative;
    z-index: 2
}

.upload-box h2 {
    margin: 0;
    font-size: 22px;
    color:  rgba(255, 149, 0, 0.95);;
}

.subtitle {
    margin-bottom: 25px;
    font-size: 14px;
    color: #6b7280;
}

.field {
    margin-bottom: 20px;
}

.field label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #374151;
}

.field select {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background: white;
    font-size: 14px;
}

.drop-zone {
    position: relative;
    border: 2px dashed #c7d2fe;
    border-radius: 14px;
    padding: 30px;
    text-align: center;
    background: #eef2ff;
    cursor: pointer;
    margin-bottom: 25px;
}

.drop-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

.drop-zone label {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 14px;
    color: #4338ca;
}

.drop-zone span {
    font-size: 12px;
    color: #6366f1;
}

.upload-box button {
    width: 100%;
    padding: 14px;
    background:  rgba(255, 149, 0, 0.95);
    color: white;
    font-size: 15px;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
}

.upload-box button:hover {
    background:  rgba(255, 149, 0, 0.95);
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

           <form method="POST" enctype="multipart/form-data" class="upload-box">
    <h2>Upload Employee Document</h2>
    <p class="subtitle">Securely upload required documents</p>

    <div class="field">
        <label>Document Type</label>
        <select name="doc_type" required>
            <option value="">Select document</option>
            <?php foreach ($document_types as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>">
                    <?= htmlspecialchars($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

   <div class="drop-zone" id="dropZone">
    <input type="file" name="document_file" id="document_file"
           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>

    <div class="drop-content">
        <i class="fas fa-cloud-upload-alt"></i>
        <strong id="dropText">Click to upload</strong>
        <span id="fileHint">or drag & drop (PDF, DOC, JPG, PNG)</span>
        <span id="fileName" class="file-name"></span>
    </div>
</div>
<button type="submit" name="upload_document"> Upload Document </button>
                  
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
    <script>
const dropZone = document.getElementById("dropZone");
const fileInput = document.getElementById("document_file");
const fileName = document.getElementById("fileName");
const dropText = document.getElementById("dropText");

// Show filename when selected
fileInput.addEventListener("change", () => {
    if (fileInput.files.length > 0) {
        fileName.textContent = fileInput.files[0].name;
        dropText.textContent = "File selected";
    }
});

// Drag events
dropZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZone.classList.add("dragover");
});

dropZone.addEventListener("dragleave", () => {
    dropZone.classList.remove("dragover");
});

dropZone.addEventListener("drop", (e) => {
    e.preventDefault();
    dropZone.classList.remove("dragover");

    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        fileName.textContent = e.dataTransfer.files[0].name;
        dropText.textContent = "File selected";
    }
});
</script>

</body>
</html>