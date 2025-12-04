<?php
include 'confirm_admin.php';


// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "users1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch pending leaves
function getPendingLeaves($conn) {
    $sql = "SELECT users.name, leaves.start_date, leaves.end_date, leaves.reason FROM leaves INNER JOIN users ON leaves.user_id = users.id WHERE leaves.status = 'pending'";
    $result = $conn->query($sql);
    $leaves = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
    }
    return $leaves;
}

// Function to fetch approved leaves
function getApprovedLeaves($conn) {
    $sql = "SELECT users.name, leaves.start_date, leaves.end_date, leaves.reason FROM leaves INNER JOIN users ON leaves.user_id = users.id WHERE leaves.status = 'approved'";
    $result = $conn->query($sql);
    $leaves = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
    }
    return $leaves;
}

// Function to add employee
function addEmployee($conn, $name) {
    $sql = "INSERT INTO users (name) VALUES ('$name')";
    return $conn->query($sql);
}

// Handle form submission for adding employee
if (isset($_POST['add_employee'])) {
    $employeeName = $_POST['employee_name'];
    if (addEmployee($conn, $employeeName)) {
        echo "<script>alert('Employee added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding employee.');</script>";
    }
}


// Get the vote data from the session (or use default if not set)
$itVotes = isset($_SESSION['itVotes']) ? $_SESSION['itVotes'] : 12;
$hrVotes = isset($_SESSION['hrVotes']) ? $_SESSION['hrVotes'] : 19;
$marketingVotes = isset($_SESSION['marketingVotes']) ? $_SESSION['marketingVotes'] : 3;
$graphicDesignVotes = isset($_SESSION['graphicDesignVotes']) ? $_SESSION['graphicDesignVotes'] : 5;
$operationsVotes = isset($_SESSION['operationsVotes']) ? $_SESSION['operationsVotes'] : 2;
$adminVotes = isset($_SESSION['adminVotes']) ? $_SESSION['adminVotes'] : 3;

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resources/TTG-Logo.png">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="../../css/emp_dashboard.css">    
    <link rel="stylesheet" href="../../css/dashBstyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Dashboard</title>    
</head>
<body>


<style>
    .sidebar {
            width: 300px; /* Increased the sidebar width */
            background-color: #ff9500;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 30px;
            box-sizing: border-box;
            font-size: 18px;
        }
        .sidebar.active {
        left: 0;
    }

    /* Hamburger menu styles */
    .hamburger {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        background: #ff9500;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1000;
    }

    .hamburger i {
        font-size: 24px;
        color: white;
    }

    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .hamburger {
            display: block;
        }

        .sidebar {
            width: 250px;
            left: -250px;
        }

        .sidebar nav a {
            padding: 15px;
            font-size: 18px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
        }

        .profile-info p {
            font-size: 18px;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
        }
    }

    .layout {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 300px;
        background: rgba(255, 149, 0, 0.95);
        backdrop-filter: blur(5px);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding: 20px 18px;
        box-shadow: 2px 0 15px rgba(0,0,0,0.15);
        z-index: 1000;
        transition: transform 0.35s ease;
        overflow-y: auto;
    }

    /* Hide sidebar on mobile by default */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.active {
            transform: translateX(0);
        }
    }

    
   .main-content {
    margin-left: 300px;        
    width: calc(100% - 300px);
    min-height: 100vh;
    padding: 30px;
    padding-bottom: 100px;   
    overflow-y: auto;
    height: 100vh;          
}
    


   /* Hamburger menu button styles */
.hamburger-menu {
    display: none;
    position: absolute;
    top: 20px;
    left: 20px;
    cursor: pointer;
    z-index: 1100;
}

.hamburger-menu div {
    width: 35px;
    height: 5px;
    background-color: black;
    margin: 6px 0;
    transition: 0.4s;
}
    .overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 999;
    }
    .overlay.active { display: block; }

/* PROFILE */


.profile-section {
    text-align: center;          
    margin-bottom: 30px;
    padding: 20px 0;             
}


.profile-image {
    width: 120px;                 
    height: 120px;
    margin: 0 auto 15px auto;    
    border-radius: 50%;           
    overflow: hidden;             
    border: 4px solid rgba(255,255,255,0.8);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;            
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;            
    display: block;
}

.profile-btn {
    background: rgba(255,255,255,0.25);
    color: #fff;
    border: none;
    padding: 7px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.25s;
}

.profile-btn:hover {
    background: rgba(255,255,255,0.35);
}

/* NAV LINKS */
.nav-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nav-links a span {
    flex: 1;
    white-space: nowrap;       
    overflow: hidden;
    text-overflow: ellipsis;   
    text-align: left;
    padding-left: 5px;          
}


.nav-links a {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 13px 16px;         
    background: rgba(255,255,255,0.12);
    border-radius: 12px;
    color: white;
    text-decoration: none;
    font-size: 16px;
    transition: all 0.25s ease;
    overflow: hidden;         
}

.nav-links a i {
    font-size: 19px;
    width: 24px;
    text-align: center;
}

/* TEXT ALIGNMENT */
.nav-links a span {
    flex: 1;   
}

/* Hover */
.nav-links a:hover {
    background: rgba(255,255,255,0.22);
    transform: translateX(3px);
}

/* Logout item */
.nav-links a.logout {
    margin-top: auto;
    background: rgba(247, 247, 247, 0.88);
   color: red
}

.nav-links a.logout:hover {
    background: rgba(0,0,0,0.35);
}


    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .hamburger {
            display: block;
        }

        .sidebar {
            width: 250px;
            left: -250px;
        }

        .sidebar nav a {
            padding: 15px;
            font-size: 18px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
        }

        .profile-info p {
            font-size: 18px;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
        }
    }


html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow-x: hidden;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
        padding: 80px 15px 50px 15px !important;  
    }
    
    .hamburger {
        display: block !important;
    }
}
</style>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const hamburger = document.querySelector(".hamburger");
        const sidebar = document.querySelector(".sidebar");

        hamburger.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    });
</script> -->

<!-- NEW SIDEBAR -->
<div class="layout">

<!-- MOBILE MENU BUTTON -->
<div class="hamburger" id="hamburger">
    <i class="fas fa-bars"></i>
</div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">

            <div class="profile-section">
                <div class="profile-image">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic">
                    <input type="file" id="imageUpload" hidden accept="image/*" onchange="updateProfilePic()">
                </div>

                <p class="profile-name"><?php echo htmlspecialchars($user_name); ?> - Admin</p>

                <button class="profile-btn" onclick="document.getElementById('imageUpload').click()">
                    Change Picture
                </button>
            </div>

            <nav class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="manage_employee_tasks.php"><i class="fas fa-tasks"></i><span>Manage Employee Tasks</span></a>
                <a href="#"><i class="fas fa-calendar-alt"></i><span>Manage Leaves</span></a>
                <a href="view_employee_profiles.php"><i class="fas fa-users"></i><span>View Employee Profiles</span></a>
                <a href="manage_employees.php"><i class="fas fa-users-cog"></i><span>Manage Employees</span></a>
                <a href="admin_approve_registrations.php"><i class="fas fa-user-check"></i><span>Approve Registrations</span></a>
                <a href="../Employee/dashboard.php"><i class="fas fa-exchange-alt"></i><span>Switch to Employee</span></a>
                <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </nav>

        </div>
    </aside>


        <br>

        
        <!-- Main Content -->
        <div class="main-content">
          <div class="card glass-card top">
        <h1 class="circular-slide">
  <i class="fa fa-user-secret"></i> Welcome to the Admin Panel, <?php echo htmlspecialchars($user_name); ?>
</h1>
<!--the styling of the welcoming statement -->
<style>  
.circular-slide {
  font-family: Arial, sans-serif;
  color: black;
  font-size: 2.5em; /* Default size for desktop */
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  position: relative;
  animation: circularMotion 10s linear infinite;
}

.circular-slide i {
  margin-right: 10px;
  color: #333;
  animation: iconBounce 0.6s infinite alternate;
}

@keyframes circularMotion {
  0% {
    transform: translate(0, 0);
  }
  25% {
    transform: translate(20px, 20px);
  }
  50% {
    transform: translate(0, 40px);
  }
  75% {
    transform: translate(-20px, 20px);
  }
  100% {
    transform: translate(0, 0);
  }
}

@keyframes iconBounce {
  from {
    transform: translateY(0);
  }
  to {
    transform: translateY(-5px);
  }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .circular-slide {
    font-size: 1.8em; /* Smaller font for mobile */
  }
  
  @keyframes circularMotion {
    0% {
      transform: translate(0, 0);
    }
    25% {
      transform: translate(10px, 10px); /* Reduced movement */
    }
    50% {
      transform: translate(0, 20px); /* Reduced movement */
    }
    75% {
      transform: translate(-10px, 10px); /* Reduced movement */
    }
    100% {
      transform: translate(0, 0);
    }
  }
}

/* Extra small devices (phones, 600px and down) */
@media (max-width: 600px) {
  .circular-slide {
    font-size: 1.5em; /* Even smaller font for very small screens */
    animation: circularMotion 8s linear infinite; /* Slightly faster animation */
  }
  
  .circular-slide i {
    margin-right: 5px; /* Reduced spacing */
    animation: iconBounce 0.5s infinite alternate; /* Faster bounce */
  }
}
</style>


<script>
function updateProfilePic(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePic').src = e.target.result;
            // Optional: Auto-submit the form after preview
            event.target.form.submit();
        }
        reader.readAsDataURL(file);
    }
}
</script>


<br>
           

<div class="card-container">
    <div class="left">
        <div class="card glass-card">
            <?php
            // Fetch the latest 5 announcements for the dashboard
            $result = $conn->query("SELECT * FROM announcements ORDER BY date DESC LIMIT 5");
            ?>

            <div class="announcements">
                <div class="header">
                    <h2>Announcements</h2>
                    <div class="underline"></div>
                </div>
                <div class="container">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="item">
                            <p class="date"><?php echo $row['date']; ?></p>
                            <p class="description"><?php echo $row['text']; ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <br>
            <button onclick="window.location.href='announcement.php'" class="add-announcement-btn">View/Add Announcements</button>
            <br>
            <br>

            <!--Start of the events calendar and all its style and function -->
            <div class="line"></div>
            <div class="header">
                <h2>Events Calendar</h2>
                <div class="underline"></div>
            </div>
            <div class="event-calendar">
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

                <link href='./lib/fullcalendar.min.css' rel='stylesheet'/>
                <link href='./lib/fullcalendar.print.css' rel='stylesheet' media='print'/>
                <script src='./lib/jquery.min.js'></script>
                <script src='./lib/moment.min.js'></script>
                <script src='./lib/jquery-ui.custom.min.js'></script>
                <script src='./lib/fullcalendar.min.js'></script>
                <script>
                $(document).ready(function () {
                    function fmt(date) {
                        return date.format("YYYY-MM-DD HH:mm");
                    }

                    var calendar = $('#calendar').fullCalendar({
                        editable: true,
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay'
                        },
                        events: "event.php",
                        eventRender: function (event, element, view) {
                            if (event.allDay === 'true') {
                                event.allDay = true;
                            } else {
                                event.allDay = false;
                            }
                        },
                        selectable: true,
                        selectHelper: true,
                        select: function (start, end, allDay) {
                            var title = prompt('Event Title:');
                            if (title) {
                                var start = fmt(start);
                                var end = fmt(end);
                                $.ajax({
                                    url: 'add_event.php',
                                    data: 'title=' + title + '&start=' + start + '&end=' + end,
                                    type: "POST",
                                    success: function (json) {}
                                });
                                calendar.fullCalendar('renderEvent', {
                                    title: title,
                                    start: start,
                                    end: end,
                                    allDay: allDay
                                }, true);
                            }
                            calendar.fullCalendar('unselect');
                        },
                        eventDrop: function (event, delta) {
                            var start = fmt(event.start);
                            var end = fmt(event.end);
                            $.ajax({
                                url: 'update_event.php',
                                data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                                type: "POST",
                                success: function (json) {}
                            });
                        },
                        eventClick: function (event) {
                            var decision = confirm("Do you want to remove event?");
                            if (decision) {
                                $.ajax({
                                    type: "POST",
                                    url: "delete_event.php",
                                    data: "&id=" + event.id,
                                    success: function (json) {
                                        $('#calendar').fullCalendar('removeEvents', event.id);
                                    }
                                });
                            }
                        },
                        eventResize: function (event) {
                            var start = fmt(event.start);
                            var end = fmt(event.end);
                            $.ajax({
                                url: 'update_event.php',
                                data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                                type: "POST",
                                success: function (json) {}
                            });
                        }
                    });
                });
                </script>

                <style>
                    body {
                        
                        margin-top: 0px;
                        text-align: center;
                        font-size: 15px;
                        font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
                    }

                    #calendar {
                        width: 100%;
                        max-width: 350px;
                        margin: auto;
                        margin-left: 30px;
                        padding: 10px;
                        box-sizing: border-box;
                    }

                    .add-announcement-btn, .manage-events-btn {
                        background-color: #ff9500;
                        color: white;
                        padding: 12px 25px;
                        border: none;
                        border-radius: 5px;
                        font-size: 16px;
                        cursor: pointer;
                        text-decoration: none;
                        transition: background-color 0.3s ease;
                        display: inline-block;
                        text-align: center;
                        width: 100%;
                        max-width: 300px;
                        box-sizing: border-box;
                    }

                    .add-announcement-btn:hover, .manage-events-btn:hover {
                        background-color: #e68600;
                    }

                    .announcements .container {
                        width: 100%;
                        max-width: 350px;
                        margin: 0 auto;
                    }

                    .announcements .item {
                        padding: 10px;
                        margin-bottom: 10px;
                        text-align: left;
                        word-wrap: break-word;
                    }

                    .announcements .date {
                        font-weight: bold;
                        margin-bottom: 5px;
                    }

                    .card-container {
                        display: flex;
                        flex-direction: column;
                        width: 100%;
                    }

                    .left {
                        width: 100%;
                    }

                    /* Responsive styles */
                    @media screen and (min-width: 768px) {
                        .card-container {
                            flex-direction: row;
                        }
                        
                        .left {
                            width: 50%;
                        }
                        
                        #calendar {
                            width: 350px;
                        }
                        
                        .add-announcement-btn, .manage-events-btn {
                            width: auto;
                        }
                    }

                    @media screen and (max-width: 480px) {
                        #calendar {
                            font-size: 14px;
                        }
                        
                        .fc-header-toolbar {
                            flex-direction: column;
                        }
                        
                        .fc-header-toolbar .fc-center {
                            margin: 5px 0;
                        }
                    }
                </style>

                <div id='calendar'></div>
                <br>

                <!-- Add a button for managing events below the calendar -->
                <button class="manage-events-btn" onclick="manageEvents()">Manage Events</button>

                <script>
                    function manageEvents() {
                        window.location.href = "manage_events.php";
                    }
                </script>
            </div>
        </div>
    </div>

<!-- End of the calendarand the start of the bar graph -->


<div class="right">
  <div class="card glass-card top">
    <div class="dashBheader">
      <h1>DEPARTMENTS PERFORMANCE MEASURE</h1>
      <div class="chart-container">
        <h2>Departments Performance</h2>
        <canvas id="myChart"></canvas>
      </div>

      <br>
      <!-- Add the button to link to the new page -->
      <button class="adjust-button" onclick="window.location.href='adjust_graph.php'">
        Adjust Department Performance
      </button>

      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['IT', 'HR', 'Marketing', 'Graphic design', 'Operations', 'Admin'],
            datasets: [{
              label: 'No of Votes',
              data: [<?= $itVotes ?>, <?= $hrVotes ?>, <?= $marketingVotes ?>, <?= $graphicDesignVotes ?>, <?= $operationsVotes ?>, <?= $adminVotes ?>],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true, // Make chart responsive
            maintainAspectRatio: false, // Allow chart to resize freely
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      </script>
    </div>
  </div>


<style>
/* Add these styles to your existing CSS */
.right {
  width: 100%;
  max-width: 1200px; /* Adjust as needed */
  margin: 0 auto;
}

.card.glass-card.top {
  padding: 15px;
  box-sizing: border-box;
}

.dashBheader h1 {
  font-size: 1.5rem;
  text-align: center;
  margin-bottom: 10px;
}

.chart-container {
  position: relative;
  height: 400px; /* Fixed height for chart */
  width: 100%;
}

.chart-container h2 {
  font-size: 1.2rem;
  text-align: center;
  margin-bottom: 10px;
}

.adjust-button {
  background-color: #ff9500;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  text-decoration: none;
  transition: background-color 0.3s ease;
  display: block;
  width: 100%;
  max-width: 300px;
  margin: 20px auto 0;
  text-align: center;
}


/* Mobile Responsive Styles for Right Section - Department Performance */
@media (max-width: 768px) {
    .right {
        width: 100% !important;
        margin-left: 0 !important;
        padding: 10px !important;
    }

    .right .glass-card.top {
        width: 100% !important;
        margin: 0 !important;
        padding: 15px !important;
        box-sizing: border-box;
    }

    .dashBheader h1 {
        font-size: 1.3rem !important;
        line-height: 1.4;
        margin-bottom: 15px !important;
        padding: 0 10px;
    }

    .chart-container {
        height: 280px !important;
        margin: 0 auto !important;
        padding: 0 5px !important;
    }

    .chart-container h2 {
        font-size: 1.1rem !important;
        margin-bottom: 8px !important;
    }

    .adjust-button {
        width: 90% !important;
        max-width: none !important;
        margin: 20px auto 10px !important;
        padding: 12px !important;
        font-size: 0.9rem !important;
    }

    /* Ensure chart is responsive */
    #myChart {
        width: 100% !important;
        height: auto !important;
        min-height: 250px;
    }
}

@media (max-width: 480px) {
    .dashBheader h1 {
        font-size: 1.1rem !important;
    }

    .chart-container {
        height: 240px !important;
    }

    .chart-container h2 {
        font-size: 1rem !important;
    }

    .adjust-button {
        width: 95% !important;
        padding: 10px !important;
        font-size: 0.85rem !important;
    }
}
</style>
</div>
                        
<br>
<br>
<br>
<br>
                        <script src="../../js/script.js"></script>
</body>
</html>