<?php

    include '../pages/db.php';
    session_start();

    header('Content-Type: application/json'); // Ensure JSON response

    try {        
        if (!isset($_SESSION['employee_id'])) {
            echo json_encode(["success" => false, "error" => "User not logged in."]);
            exit();
        }

        $employee_id = $_SESSION['employee_id'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $target_dir = "../resources/uploads/"; // Folder where images will be stored

            // Check if the directory exists, if not, create it
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create directory with full permissions
            }
            
            $file_name = uniqid() . "_" . basename($_FILES["image"]["name"]); // Unique file name
            $target_file = $target_dir . $file_name;            
            
            // Move the uploaded file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

                $relative_path = "../" . $target_file; // Path to be stored in DB
                $_SESSION['profile_picture'] = $relative_path;

                // Update database with the new image path
                $sql = "UPDATE users SET profile_picture = :profile_picture WHERE employee_id = :employee_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['profile_picture' => $relative_path, 'employee_id' => $employee_id]);

                echo json_encode(["success" => true, "image_path" => $relative_path]);
            } else {
                echo json_encode(["success" => false, "error" => "File upload failed."]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "No valid image uploaded."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    $conn = null;
?>