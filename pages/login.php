<?php
// login.php - Login Page
require 'db.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_id = $_POST['company_id'];
    $employee_id_or_email = $_POST['employee_id_or_email'];
    $password = $_POST['password'];

    try {
        // First verify the company exists
        $company_stmt = $conn->prepare("SELECT * FROM users WHERE company_id = :company_id");
        $company_stmt->execute(['company_id' => $company_id]);
        $company = $company_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$company) {
            $error_message = "Invalid company ID.";
        } else {
            // Then verify the user exists with this company
            if (filter_var($employee_id_or_email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND company_id = :company_id");
                $stmt->execute([
                    'email' => $employee_id_or_email,
                    'company_id' => $company_id
                ]);
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = :employee_id AND company_id = :company_id");
                $stmt->execute([
                    'employee_id' => $employee_id_or_email,
                    'company_id' => $company_id
                ]);
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error_message = "No user found with that ID/email in this company.";
            } else {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['employee_id'] = $user['employee_id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['profile_picture'] = $user['profile_picture'];
                    $_SESSION['company_id'] = $company_id; // Store company ID in session

                    $endpoint = '';
                    switch ($user['role']) {
                        case 'Admin':
                            $endpoint = './Admin/dashboard.php';
                            $_SESSION['switch_button'] = "block;";
                            $_SESSION['location'] = "../Admin/dashboard.php";
                            break;
                        case 'HR':
                            $endpoint = './HR/dashboard.php';
                            $_SESSION['switch_button'] = "block;";
                            $_SESSION['location'] = "../HR/dashboard.php";
                            break;
                        case 'Manager':
                            $endpoint = './Manager/dashboard.php';
                            $_SESSION['switch_button'] = "block;";
                            $_SESSION['location'] = "../Manager/dashboard.php";
                            break;
                        case 'Employee':
                            $endpoint = './Employee/dashboard.php';
                            $_SESSION['switch_button'] = "none;";
                            $_SESSION['location'] = "../Employee/dashboard.php";
                            break;
                    }

                    header("Location: $endpoint");
                    exit();
                } else {
                    $error_message = "Invalid password. Please try again.";
                }
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
            width: 900px;
            height: 600px;
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
            background: linear-gradient(135deg, #ffecb3, #ffe082);
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
            animation: pulseImage 3s ease-in-out infinite alternate;
        }

        @keyframes pulseImage {
            from {
                transform: scale(1);
            }
            to {
                transform: scale(1.05);
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
            animation: bounceLogo 1.5s ease-in-out infinite;
        }

        @keyframes bounceLogo {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            animation: fadeInText 1s ease-in-out;
        }

        @keyframes fadeInText {
            from {
                opacity: 0;
            }
            to {
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
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
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
            <img src="../resources/unsplash.jpeg" alt="Login Image">
        </div>
        <div class="login-form">
            <img src="../resources/TTG-Logo.png" alt="The Tech Giants Logo" class="login-logo">
            <h2>LOGIN</h2>
            <form method="POST" action="login.php">
                <label for="company_id">Company ID</label>
                <input type="text" name="company_id" placeholder="Company ID" required>
                <label for="employee_id_or_email">Employee ID or Email Address</label>
                <input type="text" name="employee_id_or_email" placeholder="Employee ID or Email Address" required>
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="LOGIN">
            </form>
            <?php
            // Display error message if login fails
            if (isset($error_message)) {
                echo "<div class='error-message'>$error_message</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>