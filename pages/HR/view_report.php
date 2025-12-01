<?php
include 'confirm_admin.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user information is available in the session
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '../../resources/default-profile.png';

// Handle report generation
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : '';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Initialize report data
$tasks = [];
$leaves = [];
$activities = [];

if ($report_type) {
    // Prepare date conditions
    if ($report_type == 'monthly') {
        $start_date = date('Y-m-01', strtotime("$year-$month-01"));
        $end_date = date('Y-m-t', strtotime("$year-$month-01"));
        $report_period = date('F Y', strtotime("$year-$month-01"));
    } else { // yearly
        $start_date = date('Y-01-01', strtotime("$year-01-01"));
        $end_date = date('Y-12-31', strtotime("$year-12-31"));
        $report_period = $year;
    }

    // Fetch tasks
    $task_sql = "SELECT * FROM tasks WHERE task_date BETWEEN '$start_date' AND '$end_date' ORDER BY task_date DESC";
    if ($result = $conn->query($task_sql)) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    } else {
        echo "Error fetching tasks: " . $conn->error;
    }

    // Fetch leaves from timeoff table
    $leave_sql = "SELECT * FROM timeoff WHERE (start_date BETWEEN '$start_date' AND '$end_date' OR end_date BETWEEN '$start_date' AND '$end_date') ORDER BY start_date DESC";
    if ($result = $conn->query($leave_sql)) {
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
    } else {
        echo "Error fetching leaves: " . $conn->error;
    }

    // Fetch activities
    $activity_sql = "SELECT * FROM activities WHERE timestamp BETWEEN '$start_date' AND '$end_date' ORDER BY timestamp DESC";
    if ($result = $conn->query($activity_sql)) {
        while ($row = $result->fetch_assoc()) {
            $activities[] = $row;
        }
    } else {
        echo "Error fetching activities: " . $conn->error;
    }
}

// Handle PDF generation request
if (isset($_GET['download_pdf'])) {
    require_once('TCPDF/tcpdf.php');
    
    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Your Company');
    $pdf->SetAuthor('HR System');
    $pdf->SetTitle(ucfirst($report_type) . ' Report - ' . $report_period);
    $pdf->SetSubject('System Report');
    
    // Add a page
    $pdf->AddPage();
    
    // Set font for title
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Title
    $pdf->Cell(0,  10, ucfirst($report_type) . ' Report for ' . $report_period, 0, 1, 'C');
    $pdf->Ln(10);
    
    // Set font for content
    $pdf->SetFont('helvetica', '', 10);
    
    // Report summary
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Report Summary', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    
    $summary = array(
        'Tasks' => count($tasks),
        'Leaves' => count($leaves),
        'Activities' => count($activities)
    );
    
    foreach ($summary as $key => $value) {
        $pdf->Cell(50, 6, $key . ':', 0, 0);
        $pdf->Cell(0, 6, $value, 0, 1);
    }
    $pdf->Ln(10);
    
    // Tasks section
    if (!empty($tasks)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Tasks (' . count($tasks) . ')', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        
        // Table header
        $pdf->SetFillColor(255, 149, 0);
        $pdf->SetTextColor(255);
        $pdf->Cell(30, 7, 'Employee', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Task Title', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Description', 1, 0, 'L', true);
        $pdf->Cell(20, 7, 'Status', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Due Date', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Created At', 1, 1, 'L', true);
        
        // Table content
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(255, 255, 255);
        foreach ($tasks as $task) {
            $pdf->Cell(30, 6, isset($task['assigned_to']) ? $task['assigned_to'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(30, 6, isset($task['task_name']) ? $task['task_name'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(40, 6, isset($task['description']) ? substr($task['description'], 0, 30) . (strlen($task['description']) > 30 ? '...' : '') : 'N/A', 1, 0, 'L');
            $pdf->Cell(20, 6, isset($task['status']) ? $task['status'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(25, 6, isset($task['due_date']) ? $task['due_date'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(25, 6, isset($task['task_date']) ? $task['task_date'] : 'N/A', 1, 1, 'L');
        }
        $pdf->Ln(10);
    }
    
    // Leaves section
    if (!empty($leaves)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Leaves (' . count($leaves) . ')', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        
        // Table header
        $pdf->SetFillColor(255, 149, 0);
        $pdf->SetTextColor(255);
        $pdf->Cell(30, 7, 'Employee', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Leave Type', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Start Date', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'End Date', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Reason', 1, 0, 'L', true);
        $pdf->Cell(20, 7, ' Status', 1, 1, 'L', true);
        
        // Table content
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(255, 255, 255);
        foreach ($leaves as $leave) {
            $pdf->Cell(30, 6, isset($leave['employee_id']) ? $leave['employee_id'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(30, 6, isset($leave['leave_type']) ? $leave['leave_type'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(25, 6, isset($leave['start_date']) ? $leave['start_date'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(25, 6, isset($leave['end_date']) ? $leave['end_date'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(40, 6, isset($leave['reason']) ? substr($leave['reason'], 0, 30) . (strlen($leave['reason']) > 30 ? '...' : '') : 'N/A', 1, 0, 'L');
            $pdf->Cell(20, 6, isset($leave['status']) ? $leave['status'] : 'N/A', 1, 1, 'L');
        }
        $pdf->Ln(10);
    }
    
    // Activities section
    if (!empty($activities)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Activities (' . count($activities) . ')', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        
        // Table header
        $pdf->SetFillColor(255, 149, 0);
        $pdf->SetTextColor(255);
        $pdf->Cell(30, 7, 'Employee', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Activity Type', 1, 0, 'L', true);
        $pdf->Cell(80, 7, 'Description', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Date', 1, 1, 'L', true);
        
        // Table content
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(255, 255, 255);
        foreach ($activities as $activity) {
            $pdf->Cell(30, 6, isset($activity['employee_id']) ? $activity['employee_id'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(30, 6, isset($activity['activity_type']) ? $activity['activity_type'] : 'N/A', 1, 0, 'L');
            $pdf->Cell(80, 6, isset($activity['description']) ? substr($activity['description'], 0, 50) . (strlen($activity['description']) > 50 ? '...' : '') : 'N/A', 1, 0, 'L');
            $pdf->Cell(30, 6, isset($activity['timestamp']) ? $activity['timestamp'] : 'N/A', 1, 1, 'L');
        }
    }
    
    // If no data found
    if (empty($tasks) && empty($leaves) && empty($activities)) {
        $pdf->Cell(0, 10, 'No data found for this period', 0, 1, 'C');
    }
    
    // Footer
    $pdf->SetY(-15);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 10, 'Generated on ' . date('Y-m-d H:i:s'), 0, 0, 'C');
    
    // Close and output PDF document
    $pdf->Output('system_report_' . strtolower($report_type) . '_' . $report_period . '.pdf', 'D');
    exit;
}

// Get years for dropdown
$years = [date('Y')]; // Default to current year

// Try to get years from tasks
if ($result = $conn->query("SELECT DISTINCT YEAR(task_date) as year FROM tasks")) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['year'], $years)) {
            $years[] = $row['year'];
        }
    }
}

// Try to get years from leaves
if ($result = $conn->query("SELECT DISTINCT YEAR(start_date) as year FROM timeoff WHERE start_date IS NOT NULL")) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['year'], $years)) {
            $years[] = $row['year'];
        }
    }
}

// Try to get years from activities
if ($result = $conn->query("SELECT DISTINCT YEAR(timestamp) as year FROM activities")) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['year'], $years)) {
            $years[] = $row['year'];
        }
    }
}

// Sort years descending
rsort($years);
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
    <title>System Reports</title>
    <style>
        /* Default styles for the sidebar */
        .sidebar {
            width: 300px; /* Increased the sidebar width */
            background-color: #ff9500;
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 30px;
            box-sizing: border-box;
            transition: transform 0.3s ease-in-out;
            font-size: 18px;
        }

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

        .sidebar.active {
            transform: translateX(0);
        }

        @media screen and (max-width: 768px) {
            .hamburger-menu {
                display: block;
            }
        }

        .report-container {
            padding: 20px;
            margin-left: 300px;
        }

        @media screen and (max-width: 768px) {
            .report-container {
                margin-left: 0;
                padding-top: 80px;
            }
        }

        .report-form {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
            max-width: 90%;
            width: 900px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #ff9500;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #e68600;
        }

        .report-section {
            margin-top: 30px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .report-table th {
            background-color: #ff9500;
            color: white;
        }

        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .report-table tr:hover {
            background-color: #f1f1f1;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        .report-title {
            color: #ff9500;
            margin-bottom: 20px;
            border-bottom: 2px solid #ff9500;
            padding-bottom: 10px;
        }

        .month-selector {
            display: none;
        }

        .month-selector.active {
            display: block;
        }
        
        .download-btn {
            background-color: #ff9500;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
        }
        
        .download-btn:hover {
            background-color: #e68600;
        }
        
        .pdf-icon {
            margin-right: 5px;
        }
    </style>
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

<div class="dashboard-container">
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-image">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                    </div>
                    <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
                    <input type="file" id="imageUpload" style="display: none;" accept="image/*" onchange="updateProfileImage(event)">
                    <div class="profile-info">
                        <p><?php echo htmlspecialchars($user_name); ?> - HR</p>
                    </div>
                </div>
            </div>
            <nav>
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_employee_tasks.php"><i class="fas fa-tasks"></i> Manage Employee Tasks</a>
                <a href="manage_leaves.php"><i class="fas fa-calendar-alt"></i> Manage Leaves</a>
                <a href="view_employee_profiles.php"><i class="fas fa-users"></i> View Employee Profiles</a>
                <a href="manage_employees.php"><i class="fas fa-users"></i> Manage Employees</a>
                <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
                <a href="view_report.php" class="active"><i class="fas fa-calendar-check"></i> View Report</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>

        <div class="report-container">
            <h1 class="report-title">System Reports</h1>
            
            <div class="report-form">
                <form action="view_report.php" method="get">
                    <div class="form-group">
                        <label for="report_type">Report Type</label>
                        <select name="report_type" id="report_type" required onchange="toggleMonthSelector()">
                            <option value="">Select Report Type</option>
                            <option value="monthly" <?php echo $report_type == 'monthly' ? 'selected' : ''; ?>>Monthly Report</option>
                            <option value="yearly" <?php echo $report_type == 'yearly' ? 'selected' : ''; ?>>Yearly Report</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select name="year" id="year" required>
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group month-selector <?php echo $report_type == 'monthly' ? 'active' : ''; ?>" id="month_selector">
                        <label for="month">Month</label>
                        <select name="month" id="month">
                            <?php 
                            $months = [
                                1 => 'January', 2 => 'February', 3 => 'March', 
                                4 => 'April', 5 => 'May', 6 => 'June', 
                                7 => 'July', 8 => 'August', 9 => 'September', 
                                10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                            foreach ($months as $num => $name): ?>
                                <option value="<?php echo $num; ?>" <?php echo $month == $num ? 'selected' : ''; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <input type="submit" value="Generate Report">
                </form>
            </div>
            
            <?php if ($report_type): ?>
                <div class="report-results">
                    <h2><?php echo ucfirst($report_type); ?> Report for 
                        <?php echo $report_type == 'monthly' ? $months[$month] . ' ' . $year : $year; ?></h2>
                    
                    <a href="view_report.php?<?php echo http_build_query($_GET); ?>&download_pdf=1" class="download-btn">
                        <i class="fas fa-file-pdf pdf-icon"></i> Download PDF Report
                    </a>
                    
                    <div class="report-section">
    <h3>Tasks</h3>
    <?php if (!empty($tasks)): ?>
        <table class="report-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Employee</th>
                    <th>Task Title</th>
                   
                    <th>Status</th>
                    
                    <th>Created At</th>
                    <th>Manager</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['id']); ?></td>
                        <td><?php echo htmlspecialchars($task['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($task['assigned_to']); ?></td>
                        <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                        
                        <td><?php echo htmlspecialchars($task['status']); ?></td>
                        
                        <td><?php echo htmlspecialchars($task['task_date']); ?></td>
                        <td><?php echo htmlspecialchars($task['manager']); ?></td>
                      
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No tasks found for this period.</p>
    <?php endif; ?>
</div>
                    
                    <div class="report-section">
                        <h3>Leaves</h3>
                        <?php if (!empty($leaves)): ?>
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaves as $leave): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($leave['employee_id']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['end_date']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="no-data">No leaves found for this period.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="report-section">
                        <h3>Activities</h3>
                        <?php if (!empty($activities)): ?>
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Activity Type</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activities as $activity): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($activity['employee_id']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['timestamp']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="no-data">No activities found for this period.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.querySelector(".hamburger-menu");
            const sidebar = document.querySelector(".sidebar");

            hamburger.addEvent .addEventListener("click", function() {
                sidebar.classList.toggle("active");
            });
            
            toggleMonthSelector();
        });
        
        function toggleMonthSelector() {
            const reportType = document.getElementById('report_type').value;
            const monthSelector = document.getElementById('month_selector');
            
            if (reportType === 'monthly') {
                monthSelector.classList.add('active');
            } else {
                monthSelector.classList.remove('active');
            }
        }
    </script>
</body>
</html>