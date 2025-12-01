<?php
// registration_pending.php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Pending</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #ff9500;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .icon {
            font-size: 50px;
            color: #ff9500;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⏳</div>
        <h1>Registration Submitted</h1>
        <p>Your registration has been received and is pending approval from an administrator.</p>
        <p>You will receive an email with your employee ID once your registration has been approved.</p>
        <p><a href="login.php">Return to login page</a></p>
    </div>
</body>
</html>