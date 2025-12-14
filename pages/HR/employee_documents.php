<?php

include 'confirm_admin.php'; 

// Database connection
$host = "localhost";
$dbname = "users1";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

// Fetch all employees for the dropdown
$employees_result = $conn->query("SELECT employee_id, name FROM users ORDER BY name ASC");
if (!$employees_result) {
    die("Error fetching employees: " . $conn->error);
}

$selected_employee_id = $_GET['employee_id'] ?? null;
$selected_employee_name = "Select an employee";

// Define the 4 HR-required document types
$document_types = [
    'ID'             => 'ID / Passport',
    'CONTRACT'       => 'Employment Contract',
    'QUALIFICATIONS' => 'Certificates / Qualifications',
    'OTHER'          => 'Other Document'
];

$docs = [];

if (!empty($selected_employee_id)) {
    // Get selected employee name
    $name_stmt = $conn->prepare("SELECT name FROM users WHERE employee_id = ?");
    $name_stmt->bind_param("s", $selected_employee_id);
    $name_stmt->execute();
    $name_result = $name_stmt->get_result();
    if ($row = $name_result->fetch_assoc()) {
        $selected_employee_name = htmlspecialchars($row['name']);
    }
    $name_stmt->close();

    // Fetch documents for selected employee
    $stmt = $conn->prepare("
        SELECT doc_type, filename, upload_date 
        FROM employee_documents 
        WHERE employee_id = ?
        ORDER BY upload_date DESC
    ");
    $stmt->bind_param("s", $selected_employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $docs[$row['doc_type']] = $row;
    }
    $stmt->close();
}

$conn->close();

// For sidebar profile (adjust if you store admin name/picture differently)
$admin_name = $_SESSION['user_name'] ?? 'HR Admin';
$profile_picture = $_SESSION['profile_picture'] ?? '../../resources/default-avatar.png';
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
    <title>HR - View Employee Documents</title>

    <style>
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 300px; background: rgba(255, 149, 0, 0.95); position: fixed; height: 100vh; padding: 20px; box-shadow: 2px 0 15px rgba(0,0,0,0.15); z-index: 1000; transition: transform 0.35s ease; }
        .main-content { margin-left: 300px; padding: 40px; flex: 1; background: #f8f9fa; }

        .hamburger { display: none; position: fixed; top: 18px; left: 18px; background: #ff9500; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 24px; cursor: pointer; z-index: 1100; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 999; }

        @media (max-width: 768px) {
            .hamburger { display: flex !important; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0 !important; padding: 90px 20px 50px !important; }
        }

        .page-title {
            font-size: 2.4em;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .employee-selector {
            text-align: center;
            margin-bottom: 40px;
        }

        .employee-selector select {
            padding: 12px 20px;
            font-size: 1.1em;
            border-radius: 8px;
            border: 1px solid #ccc;
            min-width: 300px;
        }

        .documents-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .employee-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ff9500;
        }

        .employee-header h2 {
            color: #ff9500;
            margin: 10px 0 5px;
        }

        .doc-list { display: grid; gap: 20px; }
        .doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            border-left: 6px solid #ff9500;
            transition: all 0.3s ease;
        }
        .doc-item:hover {
            background: #fff;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }

        .doc-info { flex: 1; }
        .doc-label { font-weight: 600; font-size: 1.2em; color: #333; }
        .doc-date { font-size: 0.95em; color: #666; margin-top: 6px; }

        .doc-actions { display: flex; gap: 12px; }

        .btn-view, .btn-download {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn-view { background: #0077cc; color: white; }
        .btn-view:hover { background: #005fa3; }
        .btn-download { background: #28a745; color: white; }
        .btn-download:hover { background: #218838; }

        .missing-doc {
            color: #dc3545;
            font-weight: 600;
            font-style: italic;
            font-size: 1.1em;
        }

        .no-selection {
            text-align: center;
            padding: 60px;
            color: #888;
            font-size: 1.3em;
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
</head>
<body>

<div class="layout">
    <!-- Hamburger & Overlay -->
    <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
    <div class="overlay" id="overlay"></div>

    <!-- Admin Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile">
            </div>
            <p class="profile-name"><?= htmlspecialchars($admin_name) ?></p>
        </div>

       <nav class="nav-links">
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="manage_employee_tasks.php"><i class="fas fa-tasks"></i><span> Employee Tasks</span></a>
            <a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i><span>Manage Leaves</span></a>
            <a href="view_employee_profiles.php"><i class="fas fa-users"></i><span> Employee Profiles</span></a>
            <a href="manage_employees.php"><i class="fas fa-users-cog"></i><span>Manage Employees</span></a>
            <a href="feedback.php"><i class="fas fa-comment-dots"></i><span>Feedback</span></a>
            <a href="view_report.php"><i class="fas fa-calendar-check"></i><span>View Report</span></a>
            <a href="employee_documents.php"><i class="fas fa-folder-open"></i><span>Employee Documents</span></a>
            <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Log Out</span></a>
        </nav>
    </aside>

    <div class="main-content">
        <h1 class="page-title"><i class="fas fa-file-contract"></i> HR: Employee Document Viewer</h1>

        <div class="employee-selector">
            <form method="GET" action="">
                <select name="employee_id" onchange="this.form.submit()" required>
                    <option value="">-- Select an Employee --</option>
                    <?php while ($emp = $employees_result->fetch_assoc()): ?>
                        <option value="<?= $emp['employee_id'] ?>" 
                            <?= $selected_employee_id == $emp['employee_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['name']) ?> (ID: <?= $emp['employee_id'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <div class="documents-card">
            <?php if (!$selected_employee_id): ?>
                <div class="no-selection">
                    Please select an employee to view their uploaded documents.
                </div>
            <?php else: ?>
                <div class="employee-header">
                    <h2>Documents for: <?= $selected_employee_name ?></h2>
                    <p>Employee ID: <?= $selected_employee_id ?></p>
                </div>

                <?php if (empty($docs)): ?>
                    <p style="text-align:center; color:#dc3545; font-size:1.2em;">
                        This employee has not uploaded any documents yet.
                    </p>
                <?php else: ?>
                    <div class="doc-list">
                        <?php foreach ($document_types as $key => $label): ?>
                            <div class="doc-item">
                                <div class="doc-info">
                                    <div class="doc-label"><?= htmlspecialchars($label) ?></div>
                                    <?php if (isset($docs[$key])): ?>
                                        <div class="doc-date">
                                            Uploaded: <?= date('d M Y', strtotime($docs[$key]['upload_date'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="missing-doc">Not uploaded</div>
                                    <?php endif; ?>
                                </div>

                                <div class="doc-actions">
                                    <?php if (isset($docs[$key])): ?>
                                        <a href="../../uploads/documents/<?= htmlspecialchars($docs[$key]['filename']) ?>" 
                                           target="_blank" class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="../../uploads/documents/<?= htmlspecialchars($docs[$key]['filename']) ?>" 
                                           download class="btn-download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#aaa;">—</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../../js/script.js"></script>
<script>
    // Hamburger toggle
    document.getElementById('hamburger').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    });
    document.getElementById('overlay').addEventListener('click', function() {
        document.getElementById('sidebar').classList.remove('active');
        this.classList.remove('active');
    });
</script>

</body>
</html>