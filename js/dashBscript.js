const buttons = document.querySelectorAll(".dashBtab-button");
const contents = document.querySelectorAll(".dashBtab-content");
const underline = document.getElementById("dashBunderline");

buttons.forEach((button) => {
    button.addEventListener("click", () => {
        // Remove active class from all buttons and contents
        buttons.forEach((btn) => btn.classList.remove("active"));
        contents.forEach((content) => content.classList.remove("active"));

        // Add active class to the clicked button and corresponding content
        button.classList.add("active");
        const tabId = button.getAttribute("data-tab");
        document.getElementById(tabId).classList.add("active");

        // Move the underline
        const buttonWidth = button.offsetWidth;
        const buttonOffset = button.offsetLeft - button.parentElement.offsetLeft; // Fix offset calculation
        underline.style.width = `${buttonWidth}px`;
        underline.style.transform = `translateX(${buttonOffset}px)`;
    });
});

// Initialize the first button as active
buttons[0].click();