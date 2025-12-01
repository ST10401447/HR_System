<?php
    include 'confirm_employee.php';

    require '../db.php';

    try {
        $employee_id = $_SESSION['employee_id'];
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $employee_id]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT * FROM timeoff WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $employee_id]);
        $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
?>
<script>
// Add this to your existing JavaScript
function setupMobileTables() {
    if (window.innerWidth <= 768) {
        // Tasks table
        document.querySelectorAll('#tasks td').forEach((td, index) => {
            const headers = ['Task Name', 'Due Date', 'Status', 'Start Date', 'Action'];
            td.setAttribute('data-label', headers[index]);
        });
        
        // Meetings table
        document.querySelectorAll('#meetings td').forEach((td, index) => {
            const headers = ['Time', 'Subject', 'Manager', 'Participants', 'Location'];
            td.setAttribute('data-label', headers[index]);
        });
        
        // Leave table
        document.querySelectorAll('#leave td').forEach((td, index) => {
            const headers = ['Type', 'Status', 'Manager', 'Action'];
            td.setAttribute('data-label', headers[index]);
        });
    }
}

// Run on load and resize
window.addEventListener('load', setupMobileTables);
window.addEventListener('resize', setupMobileTables);
</script>





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

<div class="dashboard-container">
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-image">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" id="profilePic" alt="User Profile">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="updateProfilePic()">
                    </div>
                    <button class="profile-button" onclick="document.getElementById('imageUpload').click()">Change Picture</button>
                    <input type="file" id="imageUpload" style="display: none;" accept="image/*" onchange="updateProfileImage(event)">
                    <div class="profile-info">
                        <p><?php echo htmlspecialchars($user_name); ?> </p>
                    </div>
                </div>
            </div>
            <nav>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="update_details.php"><i class="fas fa-user"></i> Update Details</a>
                <a href="daily_tasks.php"><i class="fas fa-tasks"></i> Daily Tasks</a>
                <a href="timeOff.php"><i class="fas fa-calendar-alt"></i> Time Off</a>
                <a href="leave_balance.php"><i class="fas fa-calculator"></i> Leave Balance</a>
                <a href="feedback.php"><i class="fas fa-comment-dots"></i> Feedback</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </nav>
        </div>

        <!-- Main Content -->
       <div class="main-content">
        <h1 class="circular-slide">
            <div class="card glass-card top">
  <i class="fa fa-user-secret"></i> Welcome to the Dashboard, <?php echo htmlspecialchars($user_name); ?>
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

 <div class="card-container">
    <div class="card glass-card top">
    <div class="left">

                      
    <?php                
                    // Fetch the latest 5 announcements for the employee dashboard using PDO
try {
    $stmt = $conn->prepare("SELECT * FROM announcements ORDER BY date DESC LIMIT 5");
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching announcements: " . $e->getMessage();
}
?>

<div class="announcements" style="padding: 20px; background-color: #f4f4f9; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
    <div class="line" style="height: 1px; background-color: #ddd; margin-bottom: 10px;"></div>
    <div class="header" style="margin-bottom: 15px;">
        <h2 style="font-size: 24px; font-weight: bold; color: #333;">Announcements</h2>
        <div class="underline" style="height: 2px; background-color: #ff9500; width: 60px; margin-top: 5px;"></div>
    </div>
    <div class="container" style="font-size: 16px; color: #555;">
        <?php if (count($announcements) > 0): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="item" style="margin-bottom: 15px; padding: 15px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);">
                    <p class="date" style="font-size: 14px; color: #777; font-weight: bold;"><?php echo htmlspecialchars($announcement['date']); ?></p>
                    <p class="description" style="margin-top: 8px; font-size: 16px; color: #333;"><?php echo htmlspecialchars($announcement['text']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #777; font-style: italic;">No announcements available at the moment.</p>
        <?php endif; ?>
    </div>
</div>


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
<br>

                        </div>
                    </div>
                </div>


<div class="right">

    <div class="card glass-card top">
                        
        <div class="dashBheader">

                            <div class="dashBbuttons">
                                <button class="dashBtab-button active" data-tab="tasks">Tasks</button>
                                <button class="dashBtab-button" data-tab="meetings">Meetings</button>
                                <button class="dashBtab-button" data-tab="leave">Leave</button>
                            </div>
                            <div class="dashBunderline" id="dashBunderline"></div>
                        </div>
                        <div class="dashBcontent">
                                <div id="tasks" class="dashBtab-content active"> <!--Tasks Tab-->
                                    <?php if (count($tasks) > 0): ?>
                                        <table>

                                            <thead>
                                                <tr>
                                                    <th>Task Name</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Start Date</th>
                                                    <th></th> <!-- New column for the View button -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                <?php foreach ($tasks as $task): ?>
                                                    <tr>
                                                        <td><?= $task['task_name'] ?></td>
                                                        <td><?= $task['task_date'] ?></td>
                                                        <td class="status <?= strtolower($task['status']) ?>"><?= $task['status'] ?></td>
                                                        <td><?= $task['manager'] ?></td>
                                                        <td><a href="daily_tasks.php"><button class="view-button" onclick="viewTask('task1')">View</button></a></td>
                                                    </tr>
                                                <?php endforeach; ?>

                                            </tbody>
                                        </table>

                                    <?php else: ?>
                                        <p>No tasks found for today.</p>
                                    <?php endif; ?>
                                </div>
                                <div id="meetings" class="dashBtab-content"> <!--Meetings Tab-->
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Subject</th>
                                                <th>Manager</th>
                                                <th>Participants</th>
                                                <th>Location</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>10:00 AM</td>
                                                <td>Project Kickoff</td>
                                                <td>John Doe</td>
                                                <td>Team A, Team B</td>
                                                <td>Conference Room 1</td>
                                            </tr>
                                            <tr>
                                                <td>02:00 PM</td>
                                                <td>Client Presentation</td>
                                                <td>Jane Smith</td>
                                                <td>Client X, Marketing Team</td>
                                                <td>Virtual (Zoom)</td>
                                            </tr>
                                            <tr>
                                                <td>04:30 PM</td>
                                                <td>Code Review</td>
                                                <td>Mike Johnson</td>
                                                <td>Developers, QA Team</td>
                                                <td>Conference Room 2</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="leave" class="dashBtab-content">
                                    <?php if (count($leaves) > 0): ?>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Manager</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                <?php foreach ($leaves as $leave): ?>
                                                    <tr>
                                                        <td><?= $leave['leave_type'] ?></td>
                                                        <td class="status <?= strtolower($leave['status']) ?>"><?= $leave['status'] ?></td>
                                                        <td><?= $leave['start_date'] ?></td>
                                                        <td><a href="leave_balance.php"><button class="view-button" onclick="viewTask('task1')">View</button></a></td>
                                                    </tr>
                                                <?php endforeach; ?>

                                            </tbody>
                                        </table>

                                    <?php else: ?>
                                        <p>No leaves found.</p>
                                    <?php endif; ?>
                                  
                                </div>
                        </div>
                        
                    </div>
                   

                    </div>
                </div>
            </div>
        </div>
<style>
        /* Mobile Responsive Styles for Right Section */
@media (max-width: 768px) {
    .right {
        width: 100% !important;
        margin-left: 0 !important;
        padding: 10px !important;
    }

    .right .glass-card {
        width: 100% !important;
        margin: 0 !important;
        padding: 15px !important;
    }

    .dashBbuttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .dashBbuttons button {
        width: 100% !important;
        margin: 2px 0 !important;
    }

    .dashBunderline {
        display: none;
    }

    .dashBcontent table {
        width: 100% !important;
        display: block;
        overflow-x: auto;
    }

    .dashBcontent table thead {
        display: none;
    }

    .dashBcontent table tbody, 
    .dashBcontent table tr, 
    .dashBcontent table td {
        display: block;
        width: 100% !important;
    }

    .dashBcontent table tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .dashBcontent table td {
        padding: 8px 10px;
        border: none;
        position: relative;
        padding-left: 40% !important;
    }

    .dashBcontent table td:before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        width: 35%;
        padding-right: 10px;
        font-weight: bold;
        text-align: left;
    }

    /* Add data-label attributes to your table cells */
    #tasks td:nth-child(1):before { content: "Task Name"; }
    #tasks td:nth-child(2):before { content: "Due Date"; }
    #tasks td:nth-child(3):before { content: "Status"; }
    #tasks td:nth-child(4):before { content: "Start Date"; }
    
    #meetings td:nth-child(1):before { content: "Time"; }
    #meetings td:nth-child(2):before { content: "Subject"; }
    #meetings td:nth-child(3):before { content: "Manager"; }
    #meetings td:nth-child(4):before { content: "Participants"; }
    #meetings td:nth-child(5):before { content: "Location"; }
    
    #leave td:nth-child(1):before { content: "Type"; }
    #leave td:nth-child(2):before { content: "Status"; }
    #leave td:nth-child(3):before { content: "Manager"; }

    .view-button {
        width: 100% !important;
        margin-top: 5px !important;
    }
}
</style>
        
  

</div>
    <script src="../../js/script.js"></script>
    <script src="../../js/dashBscript.js"></script>
</body>
</html>

