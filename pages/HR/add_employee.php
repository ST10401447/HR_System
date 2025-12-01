<?php

// add_employee.php - Add New Employee Page


// Include the database connection

require '../db.php'; // Ensure this file contains the correct PDO connection setup


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form input

    $name = $_POST['name'];

    $email = $_POST['email'];

    $employee_id = $_POST['employee_id'];

    $password = $_POST['password'];

    $department = $_POST['department'];

    $company_id = $_POST['company_id']; // Added company_id field


    // Hash the password

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);


    // Check if employee ID or email already exists

    $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = :employee_id OR email = :email");

    $stmt->execute(['employee_id' => $employee_id, 'email' => $email]);

    

    if ($stmt->rowCount() > 0) {

        // Redirect with error message

        header("Location: add_employee.php?error=Employee ID or Email already exists.");

        exit();

    } else {

        // Insert user into the database

        $sql = "INSERT INTO users (name, email, employee_id, password, department, role, company_id, profile_picture) VALUES (:name, :email, :employee_id, :password, :department, :role, :profile_picture, :company_id)";

        $stmt = $conn->prepare($sql);

        

        if ($stmt->execute([

            'name' => $name,

            'email' => $email,

            'employee_id' => $employee_id,

            'password' => $hashed_password,

            'department' => $department,

            'company_id' => $company_id,
           
            'role' => 'Employee',

            'profile_picture' => '../../resources/UserIcon.jpg',

        ])) {

            // Redirect with success message

            header("Location: add_employee.php?success=New employee successfully added.");

            exit();

        } else {

            // Redirect with general error message

            header("Location: add_employee.php?error=Error: Could not register user.");

            exit();

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">    

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="icon" href="../resources/TTG-Logo.png">

    <title>Add New Employee</title>

    <style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #e0f2f7, #f0f8ff); /* Gradient background */
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .login-container {
        max-width: 450px; /* Increased width */
        padding: 40px; /* Increased padding */
        background: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
        border-radius: 12px; /* Smoother corners */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); /* More pronounced shadow */
        animation: slideIn 0.8s ease-out, scaleUp 0.6s ease-out 0.2s; /* Add scale-up animation */
        transform-origin: top center; /* Scale from the top */
    }

    @keyframes slideIn {
        from { transform: translateY(-80px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes scaleUp {
        from { transform: scaleY(0.8); opacity: 0; }
        to { transform: scaleY(1); opacity: 1; }
    }

    .login-container h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
        animation: pulse 1.8s infinite ease-in-out;
    }

    @keyframes pulse {
        0% { transform: scale(1); color: #333; }
        50% { transform: scale(1.05); color: #ff9500; }
        100% { transform: scale(1); color: #333; }
    }

    .form-group {
        margin-bottom: 25px;
        opacity: 0; /* Initial opacity for animation */
        animation: fadeInUp 0.6s ease-out forwards; /* Fade-in and move up */
    }

    .form-group:nth-child(2) { animation-delay: 0.1s; }
    .form-group:nth-child(3) { animation-delay: 0.2s; }
    .form-group:nth-child(4) { animation-delay: 0.3s; }
    .form-group:nth-child(5) { animation-delay: 0.4s; }
    .form-group:nth-child(6) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .form-group label {
        font-weight: 600;
        color: #555;
        display: block;
        margin-bottom: 8px;
        transition: color 0.3s ease;
    }

    .form-group input, .form-group input::placeholder {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        color: #333;
    }

    .form-group input:focus {
        outline: none;
        border-color: #ff9500;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
    }
   
</style>

</head>

<body>

    

    <div class="login-container">

        <h2>Add New Employee</h2>

        

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

        

        <form method="POST" action="add_employee.php" autocomplete="off">

            <div class="form-group">

                <label for="name">Name</label>

                <input type="text" name="name" class="form-control" placeholder="Name" required>

            </div>

            <div class="form-group">

                <label for="email">Email</label>

                <input type="email" name="email" class="form-control" placeholder="Email" required>

            </div>

            <div class="form-group">

                <label for="employee_id">Employee ID</label>

                <input type="text" name="employee_id" class="form-control" placeholder="Employee ID" required>

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input type="password" name="password" class="form-control" placeholder="Password" required>

            </div>

            <div class="form-group">

                <label for="department">Department</label>

                <input type="text" name="department" class="form-control" placeholder="Department" required>

            </div>

             <div class="form-group">

                <label for="company_id">Company ID</label>

                <input type="text" name="company_id" class="form-control" placeholder="Company ID" required>

            </div>

            <input type="submit" class="btn btn-primary" value="Add Employee">
             <button class="btn btn-secondary" onclick="location.href='manage_employees.php'" style="margin-top: 20px; width: 100%;">Back</button>

        </form>

    </div>

   
     

</body>

</html> 

