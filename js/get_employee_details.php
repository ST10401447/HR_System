<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include '../pages/db.php'; // Ensure this contains a PDO connection

    header('Content-Type: application/json');

    $response = ["success" => false, "error" => "Unknown error"]; // Default response

    if (isset($_GET['employee_id'])) {
        $employee_id = $_GET['employee_id'];

        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = :employee_id");
            $stmt->bindValue(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $response = ["success" => true, "data" => $result];
            } else {
                $response = ["success" => false, "error" => "No employee found"];
            }
        } catch (PDOException $e) {
            $response = ["success" => false, "error" => $e->getMessage()];
        }
    } else {
        $response = ["success" => false, "error" => "No employee_id provided"];
    }

    echo json_encode($response);
    exit();
?>
