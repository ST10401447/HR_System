document.getElementById("employeeDropdown").addEventListener("change", function () {
    let employeeId = this.value;
    console.log("Entered eventsss handler");

    if (employeeId) {
        fetch("../../js/get_employee_details.php?employee_id=" + employeeId)
            .then(response => response.json())
            .then(data => {
                console.log(data.data.role);
                document.getElementById("name").value = data.data.name;
                document.getElementById("email").value = data.data.email;
                document.getElementById("employee__id").value = data.data.employee_id;
                document.getElementById("department").value = data.data.department;
                document.getElementById("role").value = data.data.role;
                document.getElementById("title").value = data.data.title;
                document.getElementById("dob").value = data.data.dob;
                document.getElementById("nationality").value = data.data.nationality;
                document.getElementById("gender").value = data.data.gender;
                document.getElementById("race").value = data.data.race;
                document.getElementById("start_date").value = data.data.start_date;
                document.getElementById("mobile").value = data.data.mobile;
                document.getElementById("emergency_name").value = data.data.emergency_name;
                document.getElementById("emergency_number").value = data.data.emergency_number;
            })
            .catch(error => console.error("Error fetching data:", error));
    }
});
