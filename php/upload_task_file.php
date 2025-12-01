<?php

    function uploadTaskFile($conn, $employee_id, $new_task_id)
    {
        if (!empty($_FILES['task_file']['name'])) {
            $upload_dir = '../../resources/documents/tasks/';
            $file_tmp = $_FILES['task_file']['tmp_name'];
            $original_name = $_FILES['task_file']['name'];
            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
            $new_file_name = $new_task_id . '_task.' . $file_extension;
            $upload_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {

                // Check if the task already has a document (task document_id is NULL or not)
                $stmt = $conn->prepare("
                    SELECT d.document_id 
                    FROM tasks t 
                    LEFT JOIN documents d ON t.document_id = d.document_id 
                    WHERE t.task_id = :task_id
                ");
                $stmt->execute(['task_id' => $new_task_id]);
                $existing_document = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_document && $existing_document['document_id'] !== null) {
                    // Document exists, so update the document record
                    $stmt = $conn->prepare("UPDATE documents SET document_name = :document_name, document_original_name = :document_original_name WHERE document_id = :document_id");
                    $stmt->execute([
                        'document_name' => $new_file_name,
                        'document_original_name' => $original_name,
                        'document_id' => $existing_document['document_id']
                    ]);
                } else {
                    // Document does not exist, so insert a new record
                    $stmt = $conn->prepare("INSERT INTO documents (employee_id, document_name, document_original_name, document_type) VALUES (:employee_id, :document_name, :document_original_name, 'Task File')");
                    $stmt->execute([
                        'employee_id' => $employee_id,
                        'document_name' => $new_file_name,
                        'document_original_name' => $original_name
                    ]);

                    // Update the task with the newly inserted document_id
                    $stmt = $conn->prepare("UPDATE tasks SET document_id = :document_id WHERE task_id = :task_id");
                    $stmt->execute([
                        'document_id' => $conn->lastInsertId(),
                        'task_id' => $new_task_id
                    ]);
                }

                return true;
            } else {
                echo 'Error uploading the file. Please try again.';
            }
        } else {
            echo 'No file was uploaded.';
        }
        return false;
    }
?>