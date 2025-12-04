<?php
// login.php - Login Page
require 'db.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id_or_email = $_POST['employee_id_or_email'];
    $password = $_POST['password'];

    try {
        // Login using Email OR Employee ID (new TTG-IT-2025-01 format)
        if (filter_var($employee_id_or_email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $employee_id_or_email]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = :employee_id");
            $stmt->execute(['employee_id' => $employee_id_or_email]);
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error_message = "No user found with that Employee ID or Email.";
        } else {
            if (password_verify($password, $user['password'])) {

                // Store essential session info
                $_SESSION['employee_id'] = $user['employee_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['profile_picture'] = $user['profile_picture'];
                $_SESSION['role'] = $user['role'];

                // Redirect by role
                switch ($user['role']) {
                    case 'Admin':
                        header("Location: ./Admin/dashboard.php");
                        break;
                    case 'HR':
                        header("Location: ./HR/dashboard.php");
                        break;
                    case 'Manager':
                        header("Location: ./Manager/dashboard.php");
                        break;
                    case 'Employee':
                        header("Location: ./Employee/dashboard.php");
                        break;
                }
                exit();
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../resources/TTG-Logo.png">
    <title>Login</title>

<style>
/* === SAME MODERNISED DESIGN YOU HAD === */
body {
    margin: 0;
    padding: 0;
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
    width: 900px;
    height: 600px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    border-radius: 15px;
    overflow: hidden;
    background-color: #fff;
    animation: fadeInScale 1s ease-in-out;
}

@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.9); }
    to   { opacity: 1; transform: scale(1); }
}

.login-image {
    width: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #ffecb3, #ffe082);
    display: flex;
    justify-content: center;
    align-items: center;
    clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
    animation: slideInLeft 1s ease-in-out;
}

@keyframes slideInLeft {
    from { transform: translateX(-100%); }
    to   { transform: translateX(0); }
}

.login-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    animation: pulseImage 3s ease-in-out infinite alternate;
}

@keyframes pulseImage {
    from { transform: scale(1); }
    to   { transform: scale(1.05); }
}

.login-form {
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
    animation: slideInRight 1s ease-in-out;
}

@keyframes slideInRight {
    from { transform: translateX(100%); }
    to   { transform: translateX(0); }
}

.login-logo {
    width: 80px;
    margin-bottom: 15px;
    animation: bounceLogo 1.5s ease-in-out infinite;
}

@keyframes bounceLogo {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-10px); }
}

h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

form {
    width: 80%;
    display: flex;
    flex-direction: column;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 25px;
}

input[type="submit"] {
    padding: 12px;
    width: 100%;
    background-color: #ff9500;
    color: #fff;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 15px;
    transition: 0.3s;
}

input[type="submit"]:hover {
    background-color: #000;
}

.error-message {
    color: red;
    margin-top: 10px;
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
        clip-path: none;
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
        <img src="../resources/unsplash.jpeg" alt="Login Image">
    </div>

    <div class="login-form">
        <img src="../resources/TTG-Logo.png" class="login-logo">
        <h2>LOGIN</h2>

        <form method="POST">
            <label>Employee ID or Email</label>
            <input type="text" name="employee_id_or_email" placeholder="e.g. TTG-IT-2025-01 OR email@example.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Password" required>

            <input type="submit" value="LOGIN">
        </form>

        <?php 
        if (isset($error_message)) {
            echo "<div class='error-message'>$error_message</div>";
        }
        ?>
    </div>
</div>

</body>
</html>
