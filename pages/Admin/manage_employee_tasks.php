<?php 
    include 'confirm_admin.php';

    // Database connection
    $host = 'localhost'; // database host
    $dbname = 'users1'; // database name
    $username = 'root'; // database username
    $password = ''; // database password

    // Use the correct variable names for database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle delete
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $conn->query("DELETE FROM tasks WHERE id=$delete_id");
    }

    // Handle edit
    if (isset($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $task_name = $_POST['task_name'];
        $assigned_to = $_POST['assigned_to'];
        $assigned_to_id = $_POST['employee_id'];
        $date = $_POST['date'];
        $status = $_POST['status'];
        $conn->query("UPDATE tasks SET task_name='$task_name', assigned_to='$assigned_to', employee_id='$assigned_to_id', task_date='$date', manager='$user_name' status='$status' WHERE id=$edit_id");
        $confirmation_message = "Task updated successfully!";
    }

    // Handle create
    if (isset($_POST['create_task'])) {
        $task_name = $_POST['task_name'];
        $assigned_to = $_POST['assigned_to'];
        $assigned_to_id = $_POST['employee_id'];
        $date = $_POST['date'];
        $status = $_POST['status'];
        $conn->query("INSERT INTO tasks (task_name, assigned_to, employee_id, task_date, manager, status) VALUES ('$task_name', '$assigned_to', '$assigned_to_id', '$date', '$user_name', '$status')");
        $confirmation_message = "New task created successfully!";
    }

    // Fetch tasks
    $result = $conn->query("SELECT * FROM tasks");
    $tasks = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch employees
    $result = $conn->query("SELECT * FROM users");
    $employees = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/Manage_Employee_Tasks.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <title>Manage Employee Tasks</title>
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

        <!-- Hamburger Menu -->
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-image">
                        <!-- Profile image placeholder initially -->
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                    </div>
                    <!-- Button outside the .profile-image div to change picture -->
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

        <!-- Main Content -->
        <div class="main-content">
            <h1>Manage Employee Tasks</h1>
            <section class="task-table">
                <h2>Assigning of tasks</h2>
                <div class="tabular--wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Task Name</th>
                                <th>Assigned to</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?= $task['id'] ?></td>
                                    <td><?= $task['task_name'] ?></td>
                                    <td><?= $task['assigned_to'] ?></td>
                                    <td><?= $task['task_date'] ?></td>
                                    <td class="status <?= strtolower($task['status']) ?>"><?= $task['status'] ?></td>
                                    <td>
                                        <button class="btn btn-edit" onclick="openEditModal(<?= $task['id'] ?>, '<?= $task['task_name'] ?>', '<?= $task['assigned_to'] ?>', '<?= $task['employee_id'] ?>', '<?= $task['task_date'] ?>', '<?= $task['status'] ?>')">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?= $task['id'] ?>">
                                            <button type="submit" class="btn btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Button to create new task -->
                <button class="create-task-btn" onclick="openModal()">+ Create Task</button>
            </section>

            <!-- Confirmation Message -->
            <?php if (isset($confirmation_message)): ?>
                <div class="confirmation-message">
                    <?= $confirmation_message ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal for Create Task Form -->
        <div class="modal" id="createTaskModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Create New Task</h2>
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <form method="POST">
                    <input type="hidden" name="create_task" value="1">
                    <input type="text" name="task_name" placeholder="Task Name" required>
                    <select name="assigned_to" id="assigned_to_create" required>
                        <option value="">Assign to</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?= $employee['name'] ?>" data-employee-id="<?= $employee['employee_id'] ?>"><?= $employee['name'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Hidden input to store employee_id -->
                    <input type="hidden" name="employee_id" id="employee_id_hidden">

                    <input type="date" name="date" required>
                    <select name="status" required>
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                        <option value="Incomplete">Incomplete</option>
                    </select>
                    <button type="submit">Save Task</button>
                </form>
            </div>
        </div>

        <!-- Modal for Edit Task Form -->
        <div class="modal" id="editTaskModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Task</h2>
                    <button class="modal-close" onclick="closeEditModal()">&times;</button>
                </div>
                <form method="POST">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="text" name="task_name" id="edit_task_name" placeholder="Task Name" required>
                    <select name="assigned_to" id="edit_assigned_to" required>
                        <option value="">Assign to</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?= $employee['name'] ?>" data-employee-id="<?= $employee['employee_id'] ?>"><?= $employee['name'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Hidden input to store employee_id -->
                    <input type="hidden" name="employee_id" id="employee_id_hidden_edit">

                    <input type="date" name="date" id="edit_date" required>
                    <select name="status" id="edit_status" required>
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                        <option value="Incomplete">Incomplete</option>
                    </select>
                    <button type="submit">Update Task</button>
                    <!-- <div class="confirmation-message" style="display:show"></div> -->
                </form>
            </div>
        </div>
    </div>


    <style>

body {
  margin-top: 0px;
  text-align: center;
  font-size: 17.5px;
  font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;

}

</style>

    <script src="../../js/manage_employee_tasks.js"></script>
    <script src="../../js/script.js"></script>
</body>
</html>