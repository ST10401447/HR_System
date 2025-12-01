// Show confirmation message for 3 seconds
window.onload = function() {
    const confirmationMessage = document.querySelector('.confirmation-message');
    if (confirmationMessage) {
        setTimeout(function() {
            confirmationMessage.style.display = 'none';
        }, 3000); // Message disappears after 3 seconds
    }
};

function openModal() {
    document.getElementById('createTaskModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('createTaskModal').style.display = 'none';
}

function openEditModal(id, name, assignedTo, employee_id, date, status) {
    // Populate and show edit modal
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_task_name').value = name;
    document.getElementById('edit_assigned_to').value = assignedTo;
    document.getElementById('employee_id_hidden_edit').value = employee_id;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_status').value = status;
    document.getElementById('editTaskModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editTaskModal').style.display = 'none';
}

document.getElementById("assigned_to_create").addEventListener("change", function() {
    const selectedOption = this.options[this.selectedIndex]; 
    const employeeId = selectedOption.getAttribute("data-employee-id"); 
    document.getElementById("employee_id_hidden").value = employeeId; 
});

document.getElementById("edit_assigned_to").addEventListener("change", function() {
    const selectedOption = this.options[this.selectedIndex]; 
    const employeeId = selectedOption.getAttribute("data-employee-id"); 
    document.getElementById("employee_id_hidden_edit").value = employeeId; 
});