<?php
    include 'confirm_employee.php';

    require '../db.php';

    // Handle employee document upload
    if (isset($_POST['upload_document'])) {
        try {
            $task_id = $_POST['task_id'];
            $employee_id = $_SESSION['employee_id'];
            
            if (isset($_FILES['employee_documents']) && !empty($_FILES['employee_documents']['name'][0])) {
                $allowed_types = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
                $max_size = 10 * 1024 * 1024; // 10MB
                $upload_dir = '../../resources/uploaded_documents/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Loop through each uploaded file
                foreach ($_FILES['employee_documents']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['employee_documents']['name'][$key];
                    $file_size = $_FILES['employee_documents']['size'][$key];
                    $file_tmp = $_FILES['employee_documents']['tmp_name'][$key];
                    $file_type = $_FILES['employee_documents']['type'][$key];
                    $file_error = $_FILES['employee_documents']['error'][$key];
                    
                    if ($file_error === UPLOAD_ERR_OK && in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $new_file_name = 'emp_' . $employee_id . '_task_' . $task_id . '_' . time() . '_' . $key . '.' . $file_ext;
                        $file_path = $upload_dir . $new_file_name;
                        
                        if (move_uploaded_file($file_tmp, $file_path)) {
                            $stmt = $conn->prepare("INSERT INTO uploaded_documents (employee_id, task_id, document_name, document_path, status) VALUES (:employee_id, :task_id, :document_name, :document_path, 'Submitted')");
                            $stmt->execute([
                                'employee_id' => $employee_id,
                                'task_id' => $task_id,
                                'document_name' => $file_name,
                                'document_path' => $new_file_name
                            ]);
                        }
                    }
                }
                $success_message = "Document(s) uploaded successfully!";
            }
        } catch (PDOException $e) {
            $error_message = "Error uploading documents: " . $e->getMessage();
        }
    }

    // Handle document download/view (from manager)
    if (isset($_GET['view_file'])) {
        try {
            $file_id = $_GET['view_file'];
            $stmt = $conn->prepare("SELECT * FROM task_files WHERE id = :file_id");
            $stmt->execute(['file_id' => $file_id]);
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

    // Handle viewing employee uploaded documents
    if (isset($_GET['view_uploaded'])) {
        try {
            $doc_id = $_GET['view_uploaded'];
            $stmt = $conn->prepare("SELECT * FROM uploaded_documents WHERE id = :doc_id");
            $stmt->execute(['doc_id' => $doc_id]);
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

    try {
        $employee_id = $_SESSION['employee_id'];
        
        // Fetch employee details from users table
        $stmt = $conn->prepare("SELECT name, email, profile_picture, role FROM users WHERE employee_id = :employee_id");
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
        
        // Fetch tasks for this employee
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE employee_id = :employee_id ORDER BY task_date DESC");
        $stmt->execute(['employee_id' => $employee_id]);
        $dailyTasks_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
    .sidebar {
        width: 300px;
        background-color: #ff9500;
        height: 100vh;
        display: flex;
        flex-direction: column;
        padding: 30px;
        box-sizing: border-box;
        font-size: 18px;
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
    }
    
    .sidebar.active {
        left: 0;
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

    .hamburger i {
        font-size: 24px;
        color: white;
    }

    @media screen and (max-width: 768px) {
        .hamburger {
            display: block;
        }

        .sidebar {
            width: 250px;
            left: -250px;
            z-index: 999;
        }

        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 80px 20px 20px 20px !important;
        }
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        margin-left: 300px;
        width: calc(100% - 300px);
        padding: 30px;
        background-color: #f9f9f9;
        min-height: 100vh;
    }

    .main-content h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #f5c6cb;
    }

    .task-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }

    .task-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }

    .task-status {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .status-incomplete {
        background-color: #f8d7da;
        color: #721c24;
    }

    .task-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .task-detail {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .task-detail i {
        color: #ff9500;
        font-size: 18px;
    }

    .task-detail-label {
        font-weight: bold;
        color: #666;
    }

    .task-detail-value {
        color: #333;
    }

    .documents-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #ff9500;
    }

    .document-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }

    .doc-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        background-color: #f0f0f0;
        border-radius: 5px;
        color: #ff9500;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .doc-link:hover {
        background-color: #ff9500;
        color: white;
    }

    .doc-link i {
        font-size: 16px;
    }

    .upload-section {
        margin-top: 15px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .upload-btn {
        background-color: #ff9500;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s;
    }

    .upload-btn:hover {
        background-color: #e68600;
    }

    .file-upload-area {
        border: 2px dashed #ff9500;
        padding: 20px;
        text-align: center;
        border-radius: 8px;
        margin: 15px 0;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .file-upload-area:hover {
        background-color: #fff5e6;
    }

    .file-list {
        margin-top: 10px;
        font-size: 14px;
    }

    .file-list-item {
        padding: 5px 10px;
        background: #f0f0f0;
        margin: 5px 0;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-section {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px 0;
    }

    .profile-card {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .profile-image {
        width: 120px;
        height: 120px;
        margin: 0 auto 15px;
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
    }

    .profile-button {
        background: rgba(255,255,255,0.25);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        margin-bottom: 10px;
        transition: background 0.25s;
    }

    .profile-button:hover {
        background: rgba(255,255,255,0.35);
    }

    .profile-info p {
        color: white;
        font-size: 16px;
        font-weight: 600;
    }

    .sidebar nav {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sidebar nav a {
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
    }

    .sidebar nav a i {
        font-size: 19px;
        width: 24px;
        text-align: center;
    }

    .sidebar nav a:hover {
        background: rgba(255,255,255,0.22);
        transform: translateX(3px);
    }

    .sidebar nav a.active {
        background: rgba(255,255,255,0.3);
        font-weight: bold;
    }

    .announcement {
        background: white;
        padding: 40px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .announcement p {
        color: #666;
        font-size: 18px;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .modal-close {
        font-size: 28px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
        background: none;
        border: none;
    }

    .modal-close:hover {
        color: #000;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.querySelector(".hamburger");
        const sidebar = document.querySelector(".sidebar");

        if (hamburger && sidebar) {
            hamburger.addEventListener("click", function() {
                sidebar.classList.toggle("active");
            });
        }
    });

    function openUploadModal(taskId, taskName) {
        document.getElementById('upload_task_id').value = taskId;
        document.getElementById('upload_task_name').textContent = taskName;
        document.getElementById('uploadModal').classList.add('active');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('active');
        document.getElementById('file_list').innerHTML = '';
        document.getElementById('employee_documents').value = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('employee_documents');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const fileList = document.getElementById('file_list');
                fileList.innerHTML = '';
                
                if (this.files.length > 0) {
                    fileList.innerHTML = '<strong>Selected Files:</strong>';
                    for (let i = 0; i < this.files.length; i++) {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'file-list-item';
                        fileItem.innerHTML = `<i class="fas fa-file"></i> ${this.files[i].name}`;
                        fileList.appendChild(fileItem);
                    }
                }
            });
        }
    });
</script>   
</head>
<body>

    <div class="hamburger">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-image">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                    </div>
                    <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
                    <div class="profile-info">
                        <p><?php echo htmlspecialchars($user_name); ?></p>
                        <p style="font-size: 14px; font-weight: normal;">Employee View</p>
                    </div>
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

        <div class="main-content dailyTasks_content">
            <h1><i class="fas fa-tasks"></i> My Daily Tasks</h1>

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

            <?php if (count($dailyTasks_tasks) > 0): ?>
                <?php foreach ($dailyTasks_tasks as $task): ?>
                    <?php
                        // Fetch manager's documents for this task
                        $task_id = $task['id'];
                        $stmt = $conn->prepare("SELECT id, file_name FROM task_files WHERE task_id = :task_id");
                        $stmt->execute(['task_id' => $task_id]);
                        $manager_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Fetch employee's uploaded documents
                        $stmt = $conn->prepare("SELECT id, document_name, uploaded_at FROM uploaded_documents WHERE task_id = :task_id AND employee_id = :employee_id");
                        $stmt->execute(['task_id' => $task_id, 'employee_id' => $employee_id]);
                        $employee_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <div class="task-card">
                        <div class="task-header">
                            <div class="task-title"><?= htmlspecialchars($task['task_name']) ?></div>
                            <div class="task-status status-<?= strtolower($task['status']) ?>">
                                <?= htmlspecialchars($task['status']) ?>
                            </div>
                        </div>

                        <div class="task-details">
                            <div class="task-detail">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <div class="task-detail-label">Due Date:</div>
                                    <div class="task-detail-value"><?= htmlspecialchars($task['task_date']) ?></div>
                                </div>
                            </div>
                            <div class="task-detail">
                                <i class="fas fa-user-tie"></i>
                                <div>
                                    <div class="task-detail-label">Manager:</div>
                                    <div class="task-detail-value"><?= htmlspecialchars($task['manager']) ?></div>
                                </div>
                            </div>
                            <div class="task-detail">
                                <i class="fas fa-hashtag"></i>
                                <div>
                                    <div class="task-detail-label">Task ID:</div>
                                    <div class="task-detail-value">#<?= htmlspecialchars($task['id']) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="documents-section">
                            <?php if (count($manager_documents) > 0): ?>
                                <div class="section-title">
                                    <i class="fas fa-file-download"></i>
                                    Documents from Manager
                                </div>
                                <div class="document-list">
                                    <?php foreach ($manager_documents as $doc): ?>
                                        <a href="daily_tasks.php?view_file=<?= $doc['id'] ?>" class="doc-link">
                                            <i class="fas fa-file-download"></i>
                                            <?= htmlspecialchars($doc['file_name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (count($employee_documents) > 0): ?>
                                <div class="section-title" style="margin-top: 15px;">
                                    <i class="fas fa-file-upload"></i>
                                    My Uploaded Documents
                                </div>
                                <div class="document-list">
                                    <?php foreach ($employee_documents as $doc): ?>
                                        <a href="daily_tasks.php?view_uploaded=<?= $doc['id'] ?>" class="doc-link">
                                            <i class="fas fa-check-circle"></i>
                                            <?= htmlspecialchars($doc['document_name']) ?>
                                            <small style="color: #999;">(<?= date('M d, Y', strtotime($doc['uploaded_at'])) ?>)</small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="upload-section">
                                <button class="upload-btn" onclick="openUploadModal(<?= $task['id'] ?>, '<?= htmlspecialchars($task['task_name']) ?>')">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    Upload Documents
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="announcement">
                    <i class="fas fa-inbox" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
                    <p>No tasks assigned to you yet.</p>
                    <p style="font-size: 14px; color: #999; margin-top: 10px;">Tasks assigned by managers will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal" id="uploadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Upload Documents</h2>
                <button class="modal-close" onclick="closeUploadModal()">&times;</button>
            </div>
            <p style="margin-bottom: 15px;">Task: <strong id="upload_task_name"></strong></p>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="upload_document" value="1">
                <input type="hidden" name="task_id" id="upload_task_id">
                
                <div class="file-upload-area" onclick="document.getElementById('employee_documents').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #ff9500;"></i>
                    <p>Click to upload multiple files</p>
                    <small style="color: #666;">Accepted: Word, PDF, Images | Max 10MB each</small>
                </div>
                <input type="file" name="employee_documents[]" id="employee_documents" accept=".doc,.docx,.pdf,.jpg,.jpeg,.png" multiple style="display: none;">
                <div id="file_list" class="file-list"></div>
                
                <button type="submit" class="upload-btn" style="width: 100%; justify-content: center; margin-top: 15px;">
                    <i class="fas fa-upload"></i>
                    Submit Documents
                </button>
            </form>
        </div>
    </div>

    <script src="../../js/script.js"></script>
</body>
</html>