<?php
require '../db.php'; // Your PDO connection

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action      = $_POST['action'];
    $request_id  = $_POST['request_id'];
    $notes       = $_POST['notes'] ?? '';
    $employee_id = trim($_POST['employee_id'] ?? '');

    try {
        $conn->beginTransaction();

        // Get pending request
        $stmt = $conn->prepare("SELECT * FROM pending_registrations WHERE id = ? AND status = 'pending'");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            throw new Exception("Request not found or already processed.");
        }

        if ($action === 'approve') {
            if (empty($employee_id)) {
                throw new Exception("Employee ID is required.");
            }

            // Check if employee_id already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE employee_id = ?");
            $stmt->execute([$employee_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Employee ID '$employee_id' is already in use.");
            }

            // CREATE USER IN DATABASE
         $stmt = $conn->prepare("
    INSERT INTO users 
    (name, email, employee_id, password, department, role, profile_picture)
    VALUES (?, ?, ?, ?, ?, 'Employee', '../../resources/UserIcon.jpg')
");
$stmt->execute([
    $request['name'],
    $request['email'],
    $employee_id,
    $request['password'],
    $request['department']
]);

            // Create leave balance
            $stmt = $conn->prepare("INSERT INTO leave_balance (employee_id, study, sick, maternity, annual, unpaid, compassionate) VALUES (?, 5, 10, 30, 15, 0, 3)");
            $stmt->execute([$employee_id]);

            $new_status = 'approved';
            $success = "APPROVED! Employee created with ID: <strong>$employee_id</strong>";

        } else {
            $new_status = 'rejected';
            $success = "Request rejected.";
        }

        // Update pending_registrations — NO employee_id column here!
        $stmt = $conn->prepare("UPDATE pending_registrations SET status = ?, admin_notes = ? WHERE id = ?");
        $stmt->execute([$new_status, $notes, $request_id]);

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}

// Fetch pending requests
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; position: relative; }
        .back-btn { 
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            background: #ff9500; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: bold;
        }
        .back-btn:hover { background: #e68600; }
        h1 { color: #333; font-size: 2em; }
        .container { max-width: 1000px; margin: 0 auto; }
        .request { 
            background: white; padding: 25px; margin: 20px 0; border-radius: 15px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-left: 6px solid #ff9500;
        }
        .info { margin-bottom: 20px; line-height: 1.8; }
        .form-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: end; margin: 15px 0; }
        input, textarea { padding: 12px; border: 1px solid #ddd; border-radius: 10px; font-size: 16px; }
        textarea { width: 100%; resize: vertical; }
        button { padding: 12px 30px; border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; font-size: 16px; }
        .approve { background: #28a745; }
        .reject { background: #dc3545; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 12px; margin: 20px 0; text-align: center; font-size: 18px; }
        .error { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 12px; margin: 20px 0; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="manage_employees.php" class="back-btn">Back</a>
        <h1>Pending Registration Requests</h1>
    </div>

    <?php if ($error): ?>
        <div class="error"><strong>Error:</strong> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
        <div style="text-align:center; padding:50px; color:#666; font-size:18px;">
            <i class="fas fa-inbox" style="font-size:60px; color:#ccc;"></i><br><br>
            No pending registrations.
        </div>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="request">
                <div class="info">
                    <strong>Name:</strong> <?= htmlspecialchars($r['name']) ?><br>
                    <strong>Email:</strong> <?= htmlspecialchars($r['email']) ?><br>
                    <strong>Department:</strong> <?= htmlspecialchars($r['department']) ?><br>
                    <strong>Date:</strong> <?= date('d M Y H:i', strtotime($r['request_date'])) ?>
                </div>

                <form method="POST">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <div class="form-row">
                        <div style="flex:1;">
                            <label><strong>Assign Employee ID:</strong></label>
                            <input type="text" name="employee_id" placeholder="e.g. 2025" required>
                        </div>
                        <div style="flex:2;">
                            <label>Admin Notes (optional):</label>
                            <textarea name="notes" rows="2" placeholder="Welcome to the team!"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve">APPROVE & CREATE ACCOUNT</button>
                    </div>
                </form>

                <form method="POST" style="margin-top:15px;">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <div class="form-row">
                        <div>
                            <label>Rejection Reason:</label>
                            <textarea name="notes" rows="2" placeholder="Reason for rejection..."></textarea>
                        </div>
                        <button type="submit" class="reject">REJECT REQUEST</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>