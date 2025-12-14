<?php
    include 'confirm_employee.php';
    require '../db.php';

    // Handle employee document upload
    if (isset($_POST['upload_document']) && isset($_FILES['task_document'])) {
        try {
            $employee_id = $_SESSION['employee_id'];
            $file = $_FILES['task_document'];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                    $upload_dir = '../../resources/uploaded_documents/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_file_name = 'emp_' . $employee_id . '_' . time() . '.' . $file_ext;
                    $file_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file['tmp_name'], $file_path)) {
                        $stmt = $conn->prepare("INSERT INTO uploaded_documents (employee_id, document_name, document_path, status) 
                                               VALUES (:employee_id, :document_name, :document_path, 'Submitted')");
                        $stmt->execute([
                            'employee_id' => $employee_id,
                            'document_name' => $file['name'],
                            'document_path' => $new_file_name
                        ]);
                        $success_message = "Document uploaded successfully!";
                    }
                } else {
                    $error_message = "Invalid file type or size. Please upload Word or PDF files under 5MB.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Error uploading document: " . $e->getMessage();
        }
    }

    // Handle document download
    if (isset($_GET['download'])) {
        try {
            $doc_id = $_GET['download'];
            $stmt = $conn->prepare("SELECT * FROM uploaded_documents WHERE id = :id");
            $stmt->execute(['id' => $doc_id]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($file) {
                $file_path = '../../resources/uploaded_documents/' . $file['document_path'];
                if (file_exists($file_path)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($file['document_name']) . '"');
                    header('Content-Length: ' . filesize($file_path));
                    readfile($file_path);
                    exit;
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Handle manager file download
    if (isset($_GET['download_task'])) {
        try {
            $file_id = $_GET['download_task'];
            $stmt = $conn->prepare("SELECT * FROM task_files WHERE id = :id");
            $stmt->execute(['id' => $file_id]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($file) {
                $file_path = '../../resources/task_files/' . $file['file_path'];
                if (file_exists($file_path)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($file['file_name']) . '"');
                    header('Content-Length: ' . filesize($file_path));
                    readfile($file_path);
                    exit;
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    try {
        $employee_id = $_SESSION['employee_id'];
        
        // Fetch employee details
        $stmt = $conn->prepare("SELECT name, profile_picture, role FROM users WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $employee_id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($employee) {
            $user_name = $employee['name'];
            $user_role = $employee['role'];
            $profile_picture = !empty($employee['profile_picture']) ? $employee['profile_picture'] : '../../resources/default-avatar.png';
        } else {
            $user_name = "Employee";
            $user_role = "Employee";
            $profile_picture = '../../resources/default-avatar.png';
        }
        
        // Fetch employee's submitted documents
        $stmt = $conn->prepare("SELECT * FROM uploaded_documents WHERE employee_id = :employee_id ORDER BY uploaded_at DESC");
        $stmt->execute(['employee_id' => $employee_id]);
        $submitted_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch tasks assigned to employee with their files
        $stmt = $conn->prepare("
            SELECT t.*, tf.id as file_id, tf.file_name, tf.uploaded_at as file_uploaded_at
            FROM tasks t
            LEFT JOIN task_files tf ON t.id = tf.task_id
            WHERE t.employee_id = :employee_id
            ORDER BY t.task_date DESC
        ");
        $stmt->execute(['employee_id' => $employee_id]);
        $task_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/daily_tasks.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Daily Tasks</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .sidebar {
            width: 300px;
            background-color: #ff9500;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px;
            overflow-y: auto;
        }
        
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
        
        .hamburger i { font-size: 24px; color: white; }
        
        @media screen and (max-width: 768px) {
            .hamburger { display: block; }
            .sidebar { width: 250px; left: -250px; z-index: 999; transition: left 0.3s; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 80px 20px 20px 20px !important; }
        }
        
        .profile-section { text-align: center; margin-bottom: 30px; }
        .profile-image {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid white;
        }
        .profile-image img { width: 100%; height: 100%; object-fit: cover; }
        .profile-button {
            background: rgba(255,255,255,0.25);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .profile-info p { color: white; font-weight: 600; }
        
        .sidebar nav { display: flex; flex-direction: column; gap: 10px; }
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar nav a:hover { background: rgba(255,255,255,0.22); }
        .sidebar nav a.active { background: rgba(255,255,255,0.3); font-weight: bold; }
        
        .main-content {
            margin-left: 300px;
            padding: 30px;
            min-height: 100vh;
        }
        
        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 30px;
        }
        
        .section-box {
            background: white;
            border: 2px solid #ff9500;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .form-note {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #ff9500;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #e68600;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .data-table thead {
            background: #ff9500;
            color: white;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-submitted {
            background: #fff3cd;
            color: #856404;
        }
        
        .download-btn {
            background: #ff9500;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        
        .download-btn:hover {
            background: #e68600;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('active')">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="sidebar" id="sidebar">
        <div class="profile-section">
            <div class="profile-image">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile">
            </div>
            <button class="profile-button">Change Picture</button>
            <div class="profile-info">
                <p><?php echo htmlspecialchars($user_name); ?></p>
                <p style="font-size: 14px; font-weight: normal;">Employee View</p>
            </div>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="daily_tasks.php" class="active"><i class="fas fa-tasks"></i> Daily Tasks</a>
            <a href="timeOff.php"><i class="fas fa-calendar-alt"></i> Time Off</a>
            <a href="leave_balance.php"><i class="fas fa-calculator"></i> Leave Balance</a>
            <a href="update_details.php"><i class="fas fa-user"></i> Update Details</a>
            <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
            <?php if ($user_role === 'Manager' || $user_role === 'Admin'): ?>
                <a href="../Manager/dashboard.php"><i class="fas fa-exchange-alt"></i> Switch to Manager</a>
            <?php endif; ?>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h1>Daily Tasks</h1>

        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
            </div>
        <?php endif; ?>

        <!-- Submit Task Document Section -->
        <div class="section-box">
            <h2 class="section-title">Submit Task Document</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Upload Document:</label>
                    <input type="file" name="task_document" accept=".doc,.docx,.pdf" required>
                    <p class="form-note">Accepted formats: Word (.doc, .docx), PDF | Max size: 5MB</p>
                </div>
                <button type="submit" name="upload_document" class="submit-btn">
                    Upload & Submit to Manager
                </button>
            </form>
        </div>

        <!-- Your Submitted Documents Section -->
        <div class="section-box">
            <h2 class="section-title">Your Submitted Documents</h2>
            <?php if (count($submitted_documents) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Uploaded Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submitted_documents as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['document_name']) ?></td>
                                <td><?= date('M d, Y - h:i A', strtotime($doc['uploaded_at'])) ?></td>
                                <td>
                                    <span class="status-badge status-submitted">
                                        <?= htmlspecialchars($doc['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #999; text-align: center; padding: 20px;">No documents submitted yet.</p>
            <?php endif; ?>
        </div>

        <!-- Task Files from Manager Section -->
        <div class="section-box">
            <h2 class="section-title">Task Files from Manager</h2>
            <?php if (count($task_files) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Uploaded File Name</th>
                            <th>Manager</th>
                            <th>Due Date</th>
                            <th>Given Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($task_files as $task): ?>
                            <?php if ($task['file_id']): // Only show tasks with files ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['task_name']) ?></td>
                                    <td><?= htmlspecialchars($task['file_name']) ?></td>
                                    <td><?= htmlspecialchars($task['manager']) ?></td>
                                    <td><?= date('M d, Y', strtotime($task['task_date'])) ?></td>
                                    <td><?= date('M d, Y - h:i A', strtotime($task['file_uploaded_at'])) ?></td>
                                    <td>
                                        <a href="daily_tasks.php?download_task=<?= $task['file_id'] ?>" class="download-btn">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #999; text-align: center; padding: 20px;">No task files from manager yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../js/script.js"></script>
</body>
</html>