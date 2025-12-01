<?php
// manage_employees.php
include 'confirm_admin.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/Manage_Employee_Tasks.css">
    <title>Dashboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            word-wrap: break-word; /* Allows long words to wrap */
        }

        th {
            background-color: #ffab40;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons button, .action-buttons a {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-danger {
            background-color: #d9534f;
            color: white;
            border: none;
        }

        .btn-primary {
            background-color: orange;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c9302c;
        }

        .btn-primary:hover {
            background-color: #ffab40;
        }
    </style>
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
                        <p><?php echo htmlspecialchars($user_name); ?> - Admin</p>
                    </div>
                </div>
            </div>
            <nav>
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="manage_employee_tasks.php" class="active"><i class="fas fa-tasks"></i> Manage Employee Tasks</a>
                <a href="#"><i class="fas fa-calendar-alt"></i> Manage Leaves</a>
                <a href="view_employee_profiles.php"><i class="fas fa-users"></i> View Employee Profiles</a>
                <a href="manage_employees.php"><i class="fas fa-users"></i> Manage Employees</a>
                <a href="admin_approve_registrations.php"><i class="fas fa-user-check"></i> Approve Registrations</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <a href="../Employee/dashboard.php"><i class="fas fa-sign-out-alt"></i> Switch to Employee</a>
            </nav>
        </div>

        <div class="main-content">
            <h1>Manage Employee List</h1>
            <br>
            <a class="btn btn-primary custom-button" href="add_employee.php" role="button" data-toggle="modal" data-target="#addEmployeeModal">New Employee</a>

<style>
.custom-button {
    background-color: #ff9500;
    border-color: #ff9500;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.custom-button:hover {
    background-color: black;
    border-color: black;
}
</style>
            <br>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Company ID</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $host = 'localhost';
                    $dbname = 'users1';
                    $username = 'root';
                    $password = '';

                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = "SELECT * FROM users";
                        $stmt = $conn->query($sql);

                        if ($stmt) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['employee_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                                echo "<td class='action-buttons'>";
                                echo "<button class='btn btn-danger' onclick=\"location.href='delete.php?id=" . htmlspecialchars($row['employee_id']) . "'\">Delete</button>";
                                echo "<a class='btn btn-primary' href='view_employee_profiles.php?id=" . htmlspecialchars($row['employee_id']) . "'>Edit</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "Error: Query failed.";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                </tbody>
            </table>
            <br><br>
        </div>
    </div>
</body>
</html>