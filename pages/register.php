<?php
// register.php - Registration Page with Admin Approval
require 'db.php';

// Set PDO to throw exceptions on errors
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create pending_registrations table if it doesn't exist
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS pending_registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        department VARCHAR(255) NOT NULL,
        company_id VARCHAR(255) NOT NULL,
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        admin_notes TEXT,
        INDEX (status)
    )");
} catch (PDOException $e) {
    die("Error creating pending_registrations table: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $department = $_POST['department'];
    $company_id = $_POST['company_id'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // Validate that the company_id exists in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE company_id = :company_id LIMIT 1");
        $stmt->execute(['company_id' => $company_id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception("Invalid company ID. Please contact your administrator to get a valid company ID.");
        }

        // Check if email already exists in pending registrations
        $stmt = $conn->prepare("SELECT * FROM pending_registrations WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("You already have a pending registration with this email.");
        }

        // Check if email already exists in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already exists in our system.");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert into pending registrations (not users table)
        $sql = "INSERT INTO pending_registrations 
                (name, email, password, department, company_id) 
                VALUES (:name, :email, :password, :department, :company_id)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashed_password,
            'department' => $department,
            'company_id' => $company_id
        ]);

        // Commit transaction
        $conn->commit();

        // Redirect to pending approval page instead of login
        header("Location: registration_pending.php");
        exit();

    } catch (Exception $e) {
        // Roll back transaction if error occurs
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error_message = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../resources/TTG-Logo.png">
    <title>Register</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 1000px;
            height: 800px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            overflow: hidden;
            background-color: #fff;
            animation: fadeInScale 1s ease-in-out;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .login-image {
            width: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #ff9500, #ff9500);
            display: flex;
            justify-content: center;
            align-items: center;
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
            animation: slideInLeft 1s ease-in-out;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .login-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            animation: rotateImage 10s linear infinite;
        }

        @keyframes rotateImage {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .login-form {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            animation: slideInRight 1s ease-in-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .login-logo {
            width: 80px;
            margin-bottom: 15px;
            animation: pulseLogo 2s ease-in-out infinite;
        }

        @keyframes pulseLogo {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            animation: textPop 1s ease-in-out;
        }

        @keyframes textPop {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        form {
            width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            width: 100%;
            text-align: left;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="company_id"],
        select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="company_id"]:focus,
        select:focus {
            border-color: #ff9500;
            outline: none;
        }

        input[type="submit"] {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 20px 0;
            background-color: #ff9500;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 25px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            animation: buttonGlow 2s ease-in-out infinite;
        }

        @keyframes buttonGlow {
            0%, 100% {
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            }
            50% {
                box-shadow: 0 8px 15px rgba(255, 149, 0, 0.4);
            }
        }

        input[type="submit"]:hover {
            background-color: black;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 90%;
                height: auto;
            }

            .login-image {
                width: 100%;
                height: 200px;
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
            }

            .login-form {
                width: 100%;
            }
        }
    </style>   
</head>
<body>
    <div class="login-container">
        <div class="login-image">
            <img src="../resources/unsplash.jpeg" alt="Register Image">
        </div>
        <div class="login-form">
    <img src="../resources/TTG-Logo.png" alt="The Tech Giants Logo" class="login-logo">
    <h2>REGISTER</h2>

    <?php if (!empty($error_message)): ?>
        <div style="color: red; margin-bottom: 15px; text-align: center;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" autocomplete="off">
        <label for="company_id">Company ID</label>
        <input type="text" name="company_id" placeholder="Enter your company id" required autocomplete="off">
        
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" required autocomplete="off">

        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Email" required autocomplete="off">

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Password" required autocomplete="off">

        <label for="department">Department</label>
        <select name="department" id="department" required>
            <option value="IT">Select Department</option>
            <option value="IT">IT</option>
            <option value="HR">HR</option>
            <option value="Public Relations">Public Relations</option>
            <option value="Graphic Design">Graphic Design</option>
        </select>
        
        <input type="submit" value="REGISTER">
    </form>

    <!-- 🔴 Login Redirection Button -->
    <button
        type="button"
        onclick="window.location.href='login.php'"
        style="
            margin-top: 15px;
            background-color: #ff9500;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        "
        onmouseover="this.style.backgroundColor='black'"
        onmouseout="this.style.backgroundColor='#ff9500'"
    >
        Log In
    </button>
</div>

    </div>
</body>
</html>