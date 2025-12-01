<?php
// Start the session to save data across pages
session_start();

// If the form is submitted, save the updated values in the session or update your database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store updated votes in session or use them for database updates
    $_SESSION['itVotes'] = $_POST['itVotes'];
    $_SESSION['hrVotes'] = $_POST['hrVotes'];
    $_SESSION['marketingVotes'] = $_POST['marketingVotes'];
    $_SESSION['graphicDesignVotes'] = $_POST['graphicDesignVotes'];
    $_SESSION['operationsVotes'] = $_POST['operationsVotes'];
    $_SESSION['adminVotes'] = $_POST['adminVotes'];

    // Redirect to dashboard after updating
    header('Location: dashboard.php'); // Redirect to the dashboard after updating
    exit; // Make sure the script stops execution after redirecting
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adjust Department Performance</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #eaeaea;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      text-align: center;
    }

    .form-container {
      max-width: 600px;
      width: 100%;
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      box-sizing: border-box;
    }

    .form-container h2 {
      color: #333;
      font-size: 28px;
      margin-bottom: 30px;
      font-weight: bold;
    }

    label {
      display: block;
      font-size: 16px;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
      text-align: left;
      margin-left: 10px;
    }

    .input-field {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
      transition: all 0.3s ease;
      box-sizing: border-box;
      background-color: #f9f9f9;
    }

    .input-field:focus {
      border-color: #ff9500;
      background-color: #fff;
      outline: none;
    }

    .submit-button {
      width: 100%;
      padding: 14px;
      background-color: #ff9500;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .submit-button:hover {
      background-color: #e88400;
    }

    .form-container p {
      margin-top: 20px;
      font-size: 14px;
      color: #666;
    }

    .form-container p a {
      color: #ff9500;
      text-decoration: none;
    }

    .form-container p a:hover {
      text-decoration: underline;
    }

  </style>
</head>
<body>

  <div class="form-container">
    <h2>Adjust Department Performance</h2>
    <form method="POST">
      <div>
        <label for="itVotes">IT Votes:</label>
        <input type="number" id="itVotes" name="itVotes" class="input-field" value="<?= isset($_SESSION['itVotes']) ? $_SESSION['itVotes'] : 12 ?>">
      </div>
      <div>
        <label for="hrVotes">HR Votes:</label>
        <input type="number" id="hrVotes" name="hrVotes" class="input-field" value="<?= isset($_SESSION['hrVotes']) ? $_SESSION['hrVotes'] : 19 ?>">
      </div>
      <div>
        <label for="marketingVotes">Marketing Votes:</label>
        <input type="number" id="marketingVotes" name="marketingVotes" class="input-field" value="<?= isset($_SESSION['marketingVotes']) ? $_SESSION['marketingVotes'] : 3 ?>">
      </div>
      <div>
        <label for="graphicDesignVotes">Graphic Design Votes:</label>
        <input type="number" id="graphicDesignVotes" name="graphicDesignVotes" class="input-field" value="<?= isset($_SESSION['graphicDesignVotes']) ? $_SESSION['graphicDesignVotes'] : 5 ?>">
      </div>
      <div>
        <label for="operationsVotes">Operations Votes:</label>
        <input type="number" id="operationsVotes" name="operationsVotes" class="input-field" value="<?= isset($_SESSION['operationsVotes']) ? $_SESSION['operationsVotes'] : 2 ?>">
      </div>
      <div>
        <label for="adminVotes">Admin Votes:</label>
        <input type="number" id="adminVotes" name="adminVotes" class="input-field" value="<?= isset($_SESSION['adminVotes']) ? $_SESSION['adminVotes'] : 3 ?>">
      </div>
      <button type="submit" class="submit-button">Update Graph</button>
    </form>
  </div>

</body>
</html>
