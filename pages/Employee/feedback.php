<?php
include 'confirm_employee.php';
require '../db.php';

$success_message = '';
$error_message = '';

try {
    $employee_id = $_SESSION['employee_id'];

    // Fetch feedback for this employee
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE employee_id = :employee_id ORDER BY submission_date DESC");
    $stmt->execute(['employee_id' => $employee_id]);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all employees for the dropdown
    $stmt = $conn->prepare("SELECT employee_id, name FROM users");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Handle feedback submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['feedback'])) {
            $feedback_text = trim($_POST['feedback']);
            $target_employee_id = intval($_POST['target_employee_id']);

            if (!empty($feedback_text) && !empty($target_employee_id)) {
                $stmt = $conn->prepare("INSERT INTO feedback (employee_id, target_employee_id, feedback_text, submission_date) VALUES (:employee_id, :target_employee_id, :feedback_text, NOW())");
                $stmt->execute(['employee_id' => $employee_id, 'target_employee_id' => $target_employee_id, 'feedback_text' => $feedback_text]);
                $success_message = "Feedback submitted successfully!";
            }
        } elseif (isset($_POST['vote'])) {
            $vote_employee_id = intval($_POST['vote_employee_id']);
            $current_month = date('Y-m');

            // Check if the user has already voted this month
            $stmt = $conn->prepare("SELECT * FROM votes WHERE employee_id = :employee_id AND voter_id = :voter_id AND DATE_FORMAT(vote_date, '%Y-%m') = :current_month");
            $stmt->execute(['employee_id' => $vote_employee_id, 'voter_id' => $employee_id, 'current_month' => $current_month]);
            if ($stmt->rowCount() > 0) {
                $error_message = "You have already voted for this employee this month.";
            } else {
                // Insert the vote
                $stmt = $conn->prepare("INSERT INTO votes (employee_id, voter_id, vote_date) VALUES (:employee_id, :voter_id, NOW())");
                $stmt->execute(['employee_id' => $vote_employee_id, 'voter_id' => $employee_id]);
                $success_message = "Vote submitted successfully!";
            }
        }
    }
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
    <title>Feedback and Voting</title>
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            display: flex;
            height: 100vh;
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
        .main-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            height: 50vh;
        }
        textarea {
            width: 100%;
            margin-bottom: 140px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }
        select {
            width: 100%;
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #ff9500;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #e68a00;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
            width: 100%;
            max-width: 500px;
        }
        li {
            background: #f9f9f9;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success-message {
            color: green;
            margin-bottom: 20px;
        }
        .error-message {
            color: red;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .dashboard-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
            }
            .main-content {
                padding: 10px;
            }
            form {
                max-width: 100%;
            }
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
        <h2>Feedback and Voting</h2>

        <?php if ($success_message): ?>
            <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="post">
            <select name="target_employee_id" required>
                <option value="">Select Target Employee</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= htmlspecialchars($employee['employee_id']) ?>"><?= htmlspecialchars($employee['name']) ?> (ID: <?= htmlspecialchars($employee['employee_id']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <textarea name="feedback" placeholder="Write your feedback here..." required></textarea><br>
            <button type="submit">Submit Feedback</button>
        </form>

        <br>
        <br>
        <h3>Vote for Employee of the Month</h3>
        <br>
        <form method="post">
            <select name="vote_employee_id" required>
                <option value="">Select Employee to Vote</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= htmlspecialchars($employee['employee_id']) ?>"><?= htmlspecialchars($employee['name']) ?> (ID: <?= htmlspecialchars($employee['employee_id']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="vote">Vote</button>
        </form>


        <br>
        <br>
        <h3>Your Previous Feedback</h3>
        <ul>
            <?php foreach ($feedbacks as  $feedback): ?>
                <li><strong><?= htmlspecialchars($feedback['submission_date']) ?>:</strong> <?= htmlspecialchars($feedback['feedback_text']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
</body>
</html>