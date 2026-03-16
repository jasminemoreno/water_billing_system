// login.js

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const loginBtn = document.getElementById("loginBtn");
    const formError = document.getElementById("formError");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault(); // prevent default form submission
        formError.textContent = ""; // clear previous errors

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        let errors = [];

        // Simple validation
        if (email === "") {
            errors.push("Email is required.");
        } else if (!validateEmail(email)) {
            errors.push("Please enter a valid email.");
        }

        if (password === "") {
            errors.push("Password is required.");
        } else if (password.length < 6) {
            errors.push("Password must be at least 6 characters.");
        }

        if (errors.length > 0) {
            formError.textContent = errors.join(" ");
            return;
        }

        // If valid, simulate login or redirect
        // Here we redirect (you can change to AJAX call if needed)
        window.location.href = loginBtn.getAttribute("href");
    });

    // Optional: Enable login button only if fields are filled
    function checkFields() {
        if (emailInput.value.trim() && passwordInput.value.trim()) {
            loginBtn.classList.add("btn-primary");
            loginBtn.removeAttribute("disabled");
        } else {
            loginBtn.classList.remove("btn-primary");
            loginBtn.setAttribute("disabled", "true");
        }
    }

    emailInput.addEventListener("input", checkFields);
    passwordInput.addEventListener("input", checkFields);

    function validateEmail(email) {
        // Basic email regex
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Initial check
    checkFields();
});

document.addEventListener("DOMContentLoaded", () => {

    // OPTIONAL: Popup for Update Bill button
    const updateBtn = document.getElementById("update-bill-btn");
    if (updateBtn) {
        updateBtn.addEventListener("click", () => {
            alert("To update your bill status, please contact the admin.");
        });
    }

});
// Get elements
const profileMenu = document.getElementById('profileMenu');
const profileDropdown = document.getElementById('profileDropdown');

// Toggle dropdown on profile click
profileMenu.addEventListener('click', function(e) {
    e.stopPropagation(); // Prevent click from bubbling up
    profileDropdown.classList.toggle('active');
});

// Close dropdown if clicking outside
document.addEventListener('click', function(e) {
    if (!profileMenu.contains(e.target)) {
        profileDropdown.classList.remove('active');
    }
});

// Optional: close dropdown on pressing ESC
document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") {
        profileDropdown.classList.remove('active');
    }
});

    document.addEventListener('DOMContentLoaded', function() {
        const payButtons = document.querySelectorAll('.pay-btn');

        payButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const billId = this.dataset.id;

                if(confirm('Are you sure you want to pay this bill?')) {
                    // Create a form dynamically to send POST request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/customer/pay/${billId}`;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('paymentModal');
        const form = document.getElementById('paymentProofForm');
        const closeBtn = document.querySelector('.pay-close');
    
        // Open modal with prefilled data
        document.querySelectorAll('.pay-btn').forEach(button => {
            button.addEventListener('click', function() {
                const billData = JSON.parse(this.dataset.bill);
    
                if (!billData || !billData.id) return;
    
                // Prefill form inputs
                document.getElementById('bill_id').value = billData.id;
                document.getElementById('customer_name').value = billData.customer_name;
                document.getElementById('meter_no').value = billData.meter_no;
                document.getElementById('amount').value = billData.amount;
    
                // Set form action dynamically
                form.action = `/customer/pay-bills/${billData.id}`;
    
                // Show modal
                modal.style.display = 'block';
            });
        });
    
        // Close modal when clicking X
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    
        // Close modal when clicking outside modal content
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const openEditBtn = document.getElementById('openEdit');
        const editPopup = document.getElementById('editPopup');
        const closePopupBtn = document.getElementById('closePopup');
        const previewImg = document.getElementById('previewImg');
        const editPhoto = document.getElementById('editPhoto');
    
        // Open popup
        openEditBtn.addEventListener('click', function() {
            editPopup.style.display = 'flex';
        });
    
        // Close popup
        closePopupBtn.addEventListener('click', function() {
            editPopup.style.display = 'none';
        });
    
        // Close when clicking outside
        editPopup.addEventListener('click', function(e) {
            if (e.target === editPopup) {
                editPopup.style.display = 'none';
            }
        });
    
        // Preview image when file is selected
        editPhoto.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
    