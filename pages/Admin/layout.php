<?php
?>
<p class="profile-name"><?= htmlspecialchars($user_name ?? 'Admin') ?></p>
<p class="profile-role">Admin</p>
</div>
</div>
</div>
<nav class="side-nav">
<a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
<a href="manage_employee_tasks.php" class="nav-item"><i class="fas fa-tasks"></i> Manage Employee Tasks</a>
<a href="manage_leaves.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Manage Leaves</a>
<a href="view_employee_profiles.php" class="nav-item"><i class="fas fa-users"></i> View Employee Profiles</a>
<a href="manage_employees.php" class="nav-item"><i class="fas fa-user-cog"></i> Manage Employees</a>
<a href="admin_approve_registrations.php" class="nav-item"><i class="fas fa-user-check"></i> Approve Registrations</a>
<a href="../logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
<a href="../Employee/dashboard.php" class="nav-item switch"><i class="fas fa-exchange-alt"></i> Switch to Employee</a>
</nav>
</aside>


<!-- Main content area -->
<div class="main-area">
<!-- Top bar -->
<header class="topbar">
<div class="topbar-left">
<button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu"><i class="fas fa-bars"></i></button>
<h1 class="page-title"><?= htmlspecialchars($page_title ?? '') ?></h1>
</div>
<div class="topbar-right">
<span class="welcome">Welcome, <strong><?= htmlspecialchars($user_name ?? 'Admin') ?></strong></span>
<a class="logout-btn" href="../logout.php">Logout</a>
</div>
</header>


<main class="main-content">
<?php
// Output the page content provided by the including file
if (isset($content)) {
echo $content;
} else {
// If the including file didn't set $content, include a simple placeholder
echo '<p>No page content. Make sure your page sets $content via output buffering before including layout.php.</p>';
}
?>
</main>


</div> <!-- /.main-area -->
</div> <!-- /.dashboard-container -->


<script>
// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function () {
const hamburger = document.getElementById('hamburgerBtn');
const sidebar = document.getElementById('sidebar');
hamburger.addEventListener('click', function () {
sidebar.classList.toggle('active');
});
});


function updateProfilePic(e) {
const file = e.target.files[0];
if (!file) return;
const reader = new FileReader();
reader.onload = function(ev) {
document.getElementById('profilePic').src = ev.target.result;
// optionally send the file to the server via fetch/ajax
}
reader.readAsDataURL(file);
}
</script>


<script src="../../js/script.js"></script>
</body>
</html>