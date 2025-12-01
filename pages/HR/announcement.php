<?php
// Connect to the database
$hostname = 'localhost'; // database host
$dbname = 'users1'; // database name
$username = 'root'; // database username
$password = ''; // database password

$conn = new mysqli($hostname, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to add a new announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_announcement'])) {
    $date = $_POST['date'];
    $text = $_POST['text'];
    $description = $_POST['description'];
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO announcements (date, text, created_at, description) 
            VALUES ('$date', '$text', '$created_at', '$description')";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Announcement added successfully!</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle announcement deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM announcements WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Announcement deleted successfully!</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch all announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <style>
        .announcement-container {
            max-width: 700px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f4f4f9;
        }
        .announcement {
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .announcement .date {
            font-weight: bold;
            color: #333;
        }
        .announcement .description {
            margin-top: 5px;
            font-style: italic;
            color: #555;
        }
        form {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
        form input, form textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        form button {
            background-color: #ff9500;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #ff7f00;
        }
        .delete-button {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="announcement-container">
        <h2>Announcements</h2>

        <!-- Display Announcements -->
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="announcement">
                <p class="date">Date: <?php echo $row['date']; ?></p>
                <p><?php echo $row['text']; ?></p>
                <p class="description">Description: <?php echo $row['description']; ?></p>
                <p>Created At: <?php echo $row['created_at']; ?></p>
                <!-- Delete Button -->
                <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement?')">
                    <button class="delete-button">Delete</button>
                </a>
            </div>
        <?php endwhile; ?>

        <!-- Add Announcement Form -->
        <h3>Add New Announcement</h3>
        <form method="post">
            <input type="date" name="date" required placeholder="Select Date">
            <textarea name="text" required placeholder="Enter announcement text"></textarea>
            <textarea name="description" required placeholder="Enter a short description"></textarea>
            <button type="submit" name="add_announcement">Add Announcement</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
