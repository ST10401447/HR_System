<?php include 'setup.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./resources/TTG-Logo.png">
    <title>HR System Login</title>
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
            animation: slideUp 1s ease-in-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-image {
            width: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
            display: flex;
            justify-content: center;
            align-items: center;
            clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);
            animation: imageSlide 1.5s ease-in-out infinite alternate;
        }

        @keyframes imageSlide {
            0% { transform: translateX(0); }
            100% { transform: translateX(20px); }
        }

        .login-image img {
            width: 100%; /* Make the image fill the container */
            height: 100%; /* Make the image fill the container */
            object-fit: cover; /* Cover the entire area, cropping if necessary */
            animation: imageZoom 2s ease-in-out infinite alternate;
        }

        @keyframes imageZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        .login-form {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-color: #fff;
            animation: formFadeIn 1s ease-in-out;
        }

        @keyframes formFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .login-logo {
            width: 80px;
            margin-bottom: 15px;
            animation: bounce 1.2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            animation: textShadow 2s ease-in-out infinite alternate;
        }

        @keyframes textShadow {
            0% { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2); }
            100% { text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.3); }
        }

        .login-input {
            width: 70%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .login-input:focus {
            border-color: #ff9500;
            outline: none;
        }

        .login-btn {
            display: block;
            width: 70%;
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
            animation: buttonPulse 2s ease-in-out infinite;
        }

        @keyframes buttonPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .login-btn:hover {
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
            <img src="./resources/unsplash.jpeg" alt="Login Image">
        </div>
        <div class="login-form">
            <img src="./resources/TTG-Logo.png" alt="The Tech Giants Logo" class="login-logo">
            <h2>HR System</h2>
            <a href="./pages/register.php" class="login-btn btn-two">Register</a>
            <div class="or">
                <div class="line"></div>
                <p>or</p>
                <div class="line"></div>
            </div>
            <a href="./pages/login.php" class="login-btn">Login</a>
        </div>
    </div>
</body>
</html>