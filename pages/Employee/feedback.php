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
                        <p><?php echo htmlspecialchars($user_name); ?> </p>
                    </div>
                </div>
            </div>
        <nav>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="update_details.php"><i class="fas fa-user"></i> Update Details</a>
                <a href="daily_tasks.php"><i class="fas fa-tasks"></i> Daily Tasks</a>
                <a href="timeOff.php"><i class="fas fa-calendar-alt"></i> Time Off</a>
                <a href="leave_balance.php"><i class="fas fa-calculator"></i> Leave Balance</a>
                <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
    </div>

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