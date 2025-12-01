<?php
require '../db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];
    $notes = $_POST['notes'] ?? '';
    $employee_id = $_POST['employee_id'] ?? null;

    try {
        $conn->beginTransaction();

        // Get the request
        $stmt = $conn->prepare("SELECT * FROM pending_registrations WHERE id = :id");
        $stmt->execute(['id' => $request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            throw new Exception("Pending registration not found.");
        }

        if ($action === 'approve') {
            // Make sure admin filled in employee ID
            if (empty($employee_id)) {
                throw new Exception("Employee ID is required to approve.");
            }

            // Check that employee_id is unique
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE employee_id = :employee_id");
            $stmt->execute(['employee_id' => $employee_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("This employee ID is already in use. Please choose another.");
            }

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users 
                (name, email, employee_id, password, department, role, profile_picture, company_id) 
                VALUES 
                (:name, :email, :employee_id, :password, :department, :role, :profile_picture, :company_id)");
            $stmt->execute([
                'name' => $request['name'],
                'email' => $request['email'],
                'employee_id' => $employee_id,
                'password' => $request['password'],
                'department' => $request['department'],
                'role' => 'Employee',
                'profile_picture' => '../../resources/UserIcon.jpg',
                'company_id' => $request['company_id']
            ]);

            // Leave balance
            $stmt = $conn->prepare("INSERT INTO leave_balance (employee_id) VALUES (:employee_id)");
            $stmt->execute(['employee_id' => $employee_id]);

            // Mark as approved in pending_registrations
            $stmt = $conn->prepare("UPDATE pending_registrations 
                SET status = 'approved', admin_notes = :notes, employee_id = :employee_id 
                WHERE id = :id");
            $stmt->execute([
                'notes' => $notes,
                'employee_id' => $employee_id,
                'id' => $request_id
            ]);

            // TODO: Send email notification if needed
        } else {
            // Rejection path
            $stmt = $conn->prepare("UPDATE pending_registrations 
                SET status = 'rejected', admin_notes = :notes 
                WHERE id = :id");
            $stmt->execute([
                'notes' => $notes,
                'id' => $request_id
            ]);
        }

        $conn->commit();
        header("Location: admin_approve_registrations.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}

// Get all pending registrations
$stmt = $conn->prepare("SELECT * FROM pending_registrations WHERE status = 'pending' ORDER BY request_date ASC");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Registrations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 30px;
        }
        .back-btn {
            position: absolute;
            left: 0;
            padding: 10px 20px;
            background-color: #ff9500;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .back-btn:hover {
          background-color:rgb(255, 153, 11);
        }
        h1 {
            color: #333;
            margin: 0;
        }
        .request {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .request-info {
            margin-bottom: 10px;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .approve {
            background-color: #4CAF50;
            color: white;
        }
        .reject {
            background-color: #f44336;
            color: white;
        }
        textarea {
            width: 100%;
            margin-top: 10px;
            padding: 5px;
        }
        .no-requests {
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <button class="back-btn" onclick="location.href='manage_employees.php';">← Back</button>
        <h1>Pending Registration Requests</h1>
    </div>
    
    <?php if (!empty($error)): ?>
        <div style="color: red; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (empty($requests)): ?>
        <div class="no-requests">No pending registration requests.</div>
    <?php else: ?>
        <?php foreach ($requests as $request): ?>
            <div class="request">
                <div class="request-info">
                    <strong>Name:</strong> <?= htmlspecialchars($request['name']) ?><br>
                    <strong>Email:</strong> <?= htmlspecialchars($request['email']) ?><br>
                    <strong>Department:</strong> <?= htmlspecialchars($request['department']) ?><br>
                    <strong>Company ID:</strong> <?= htmlspecialchars($request['company_id']) ?><br>
                    <strong>Request Date:</strong> <?= htmlspecialchars($request['request_date']) ?>
                </div>
                
                <form method="POST" action="admin_approve_registrations.php">
    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
    
    <label><strong>Assign Employee ID:</strong></label>
    <input type="text" name="employee_id" placeholder="e.g. 1001" required>
    
    <label><strong>Admin Notes (optional):</strong></label>
    <textarea name="notes" placeholder="Notes (optional)"></textarea>
    
    <div class="actions">
        <button type="submit" name="action" value="approve" class="approve">Approve</button>
        <button type="submit" name="action" value="reject" class="reject">Reject</button>
    </div>
</form>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>