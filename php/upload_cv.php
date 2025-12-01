<?php
session_start();
include 'confirm_employee.php';

// Database connection
$host = "localhost";
$dbname = "users1";
$username = "root";
$password = "";
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

if (isset($_POST['upload_cv'])) {
    $employee_id = $_SESSION['employee_id'];  // Get the employee's ID from session
    $target_dir = "../resources/documents/cv/";
    $file_name = $employee_id . "_cv." . pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION);
    $target_file = $target_dir . $file_name;

    // File upload logic
    if (move_uploaded_file($_FILES["cv_file"]["tmp_name"], $target_file)) {
        $message = "CV uploaded successfully!";
    } else {
        $message = "Error uploading CV.";
    }
}
?>

<script>
    alert("<?php echo $message; ?>");
    window.location.href = "../Employee/update_details.php";
</script>
