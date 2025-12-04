<?php
// register.php - Registration Page with Admin Approval
require 'db.php';

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create pending_registrations table
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
    die("Error creating table: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $department = $_POST['department'];
    $company_id = trim($_POST['company_id']);

    try {
        $conn->beginTransaction();

        // Validate company ID exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE company_id = :company_id LIMIT 1");
        $stmt->execute(['company_id' => $company_id]);
        if ($stmt->rowCount() == 0) {
            throw new Exception("Invalid Company ID. Please contact your administrator.");
        }

        // Check duplicate pending registration
        $stmt = $conn->prepare("SELECT * FROM pending_registrations WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("A pending registration already exists for this email.");
        }

        // Check duplicate user
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("This email is already registered.");
        }

        // Insert pending registration
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO pending_registrations 
            (name, email, password, department, company_id) 
            VALUES (:name, :email, :password, :department, :company_id)");

        $stmt->execute([
            'name'       => $name,
            'email'      => $email,
            'password'   => $hashed_password,
            'department' => $department,
            'company_id' => $company_id
        ]);

        $conn->commit();
        header("Location: registration_pending.php");
        exit();

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | The Tech Giants</title>
    <link rel="icon" href="../resources/TTG-Logo.png">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f7f8fb;
            font-family: "Poppins", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-container {
            display: flex;
            width: 900px;
            height: 750px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* Left Image Section */
        .register-image {
            width: 45%;
            background: linear-gradient(135deg, #ff9500, #e67300);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        .register-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Right Form Section */
        .register-form {
            width: 55%;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-form img {
            width: 85px;
            margin: 0 auto 10px;
        }

        h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        form {
            width: 100%;
            margin-top: 10px;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }

        input, select {
            width: 100%;
            padding: 13px;
            margin-bottom: 18px;
            border-radius: 30px;
            border: 1px solid #ccc;
            transition: 0.3s;
            font-size: 14px;
        }

        input:focus, select:focus {
            border-color: #ff9500;
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #ff9500;
            border-radius: 30px;
            border: none;
            color: white;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .login-link-btn {
            width: 100%;
            margin-top: 12px;
            padding: 12px;
            background: #1e1e1e;
            border: none;
            color: white;
            border-radius: 30px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
        }

        .login-link-btn:hover {
            background: #ff9500;
        }

        /* Error Box */
        .error-box {
            background: #ffe0e0;
            color: #d10000;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 850px) {
            .register-container {
                flex-direction: column;
                width: 95%;
                height: auto;
            }
            .register-image {
                width: 100%;
                height: 200px;
            }
            .register-form {
                width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>

<body>

<div class="register-container">

    <div class="register-image">
        <img src="../resources/unsplash.jpeg" alt="Register Image">
    </div>

    <div class="register-form">
        <img src="../resources/TTG-Logo.png" alt="TTG Logo">

        <h2>Create Your Account</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-box"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <label>Company ID</label>
            <input type="text" name="company_id" placeholder="Enter company ID" required>

            <label>Name</label>
            <input type="text" name="name" placeholder="Your name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Email address" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Password" required>

            <label>Department</label>
            <select name="department" required>
                <option value="" disabled selected>Select department</option>
                <option value="IT">IT</option>
                <option value="HR">HR</option>
                <option value="Public Relations">Public Relations</option>
                <option value="Graphic Design">Graphic Design</option>
            </select>

            <button type="submit" class="btn-submit">Register</button>

        </form>

        <button onclick="window.location.href='login.php'" class="login-link-btn">Already have an account? Log In</button>

    </div>
</div>

</body>
</html>


