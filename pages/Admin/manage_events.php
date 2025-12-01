<?php
// Include database connection
$hostname = 'localhost'; // Database host
$dbname = 'users1'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

$conn = new mysqli($hostname, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add event
if (isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $sql = "INSERT INTO events (title, start, end) VALUES ('$title', '$start', '$end')";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Event added successfully!</p>";
        header('Location: dashboard.php'); // Redirect to the dashboard
        exit;
    }
}

// Handle update event
if (isset($_POST['update_event'])) {
    $id = $_POST['event_id'];
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $sql = "UPDATE events SET title = '$title', start = '$start', end = '$end' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Event updated successfully!</p>";
        header('Location: dashboard.php'); // Redirect to the dashboard
        exit;
    }
}

// Handle delete event
if (isset($_POST['delete_event'])) {
    $id = $_POST['event_id'];
    $sql = "DELETE FROM events WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Event deleted successfully!</p>";
        header('Location: dashboard.php'); // Redirect to the dashboard
        exit;
    }
}

// Fetch existing events for editing
$events = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link href='./lib/fullcalendar.min.css' rel='stylesheet'/>
    <link href='./lib/fullcalendar.print.css' rel='stylesheet' media='print'/>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container, .event-list {
            margin-bottom: 30px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #ff9500;
        }

        .event-list .event-item {
            border-bottom: 1px solid #ddd;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-list .event-item input {
            width: 40%;
        }

        .event-item button {
            background-color: #f44336;
            margin-left: 10px;
        }

        .event-item button:hover {
            background-color: #e53935;
        }

        .success {
            color: green;
            font-size: 18px;
            text-align: center;
        }

        .error {
            color: red;
            font-size: 18px;
            text-align: center;
        }

        .event-item .action-buttons {
            display: flex;
            gap: 10px;
        }

        .event-item .action-buttons button {
            flex-grow: 1;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Manage Events</h2>

    <!-- Add Event Form -->
    <div class="form-container">
        <h3>Add New Event</h3>
        <form method="POST">
            <input type="text" name="title" placeholder="Event Title" required>
            <input type="datetime-local" name="start" required>
            <input type="datetime-local" name="end" required>
            <button type="submit" name="add_event">Add Event</button>
        </form>
    </div>

    <!-- Update/Delete Event Form -->
    <div class="event-list">
        <h3>Update/Delete Existing Events</h3>
        <?php while ($row = $events->fetch_assoc()): ?>
            <form method="POST" class="event-item">
                <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
                <input type="datetime-local" name="start" value="<?php echo date('Y-m-d\TH:i', strtotime($row['start'])); ?>" required>
                <input type="datetime-local" name="end" value="<?php echo date('Y-m-d\TH:i', strtotime($row['end'])); ?>" required>
                <div class="action-buttons">
                    <button type="submit" name="update_event">Update Event</button>
                    <button type="submit" name="delete_event">Delete Event</button>
                </div>
            </form>
        <?php endwhile; ?>
    </div>

    <button onclick="window.location.href='dashboard.php'" class="add-announcement-btn" style="
    background-color: #ff9500; /* Primary blue */
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none; /* Remove underline */
    transition: background-color 0.3s ease; /* Smooth hover effect */
    display: inline-block; /* Allows padding and margin */
    text-align: center; /* Center text */
">Back</button>

</div>

</body>
</html>

<?php
$conn->close();
?>
