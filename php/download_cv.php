<?php
if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];
    $cv_path = "../resources/documents/cv/" . $employee_id . "_cv.docx";

    if (file_exists($cv_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($cv_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($cv_path));
        readfile($cv_path);
        exit;
    } else {
        echo "CV file not found.";
    }
} else {
    echo "Invalid request.";
}
?>
