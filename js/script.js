const sidebar = document.getElementById('sidebar');
const hamburger = document.querySelector('.hamburger');

console.log("entered");
console.log("entered 2");

hamburger.addEventListener('click', () => {
    toggleSidebar();
})

// Toggle sidebar
function toggleSidebar() {
    sidebar.classList.toggle('active');
}

// Close sidebar if clicked outside
document.addEventListener('click', function(event) {
    // Check if the click is outside the sidebar or hamburger
    if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});

// Update Profile Picture
function updateProfilePic() {
    console.log('Changing picture');
    const fileInput = document.getElementById('imageUpload');
    const profilePic = document.getElementById('profilePic');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            profilePic.src = e.target.result; // Show image preview
        };
        reader.readAsDataURL(fileInput.files[0]);

        // Create a FormData object to send the file
        const formData = new FormData();
        formData.append("image", fileInput.files[0]);

        console.log(formData);
        
        // Send the file to PHP using fetch()
        fetch("../../js/upload_profile.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) // Expect JSON response from PHP
        .then(data => {
            if (data.success) {
                alert("Profile picture updated successfully!");
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}