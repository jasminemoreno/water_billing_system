document.addEventListener("DOMContentLoaded", function() {
    // Only run on login page
    const loginPage = document.querySelector(".login-page");
    if(!loginPage) return;
  
    const togglePassword = loginPage.querySelector("#togglePassword");
    const password = loginPage.querySelector("#password");
  
    if(togglePassword && password) {
      togglePassword.addEventListener("click", function () {
        // Toggle password type
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
  
        // Toggle eye icon
        this.classList.toggle("fa-eye-slash");
      });
    }
  });

  
  document.addEventListener("DOMContentLoaded", () => {
    // --- Elements ---
    const addBtn = document.getElementById("addCustomerBtn");
    const addPopup = document.getElementById("addPopup");
    const closeAdd = document.getElementById("closeAddPopup");
  
    const editPopup = document.getElementById("editPopup");
    const closeEdit = document.getElementById("closeEditPopup");
  
    const successPopup = document.getElementById("successPopup");
    const notFoundPopup = document.getElementById("notFoundPopup");
  
    const searchInput = document.getElementById("searchInput");
    const customerTable = document.getElementById("customerTable").getElementsByTagName("tbody")[0];
    const emptyMessage = document.getElementById("emptyMessage");
  
    // --- Functions ---
    // Open/Close Add Popup
    addBtn.addEventListener("click", () => addPopup.style.display = "flex");
    closeAdd.addEventListener("click", () => addPopup.style.display = "none");
  
    // Open/Close Edit Popup
    closeEdit.addEventListener("click", () => editPopup.style.display = "none");
  
    // Close Success Popup after 2 seconds
    const showSuccess = (message = "Successfully saved!") => {
      document.getElementById("successMessage").textContent = message;
      successPopup.style.display = "flex";
      setTimeout(() => successPopup.style.display = "none", 2000);
    }
  
    // Show Not Found Popup for 2 seconds
    const showNotFound = (message = "Customer not found!") => {
      notFoundPopup.textContent = message;
      notFoundPopup.style.display = "flex";
      setTimeout(() => notFoundPopup.style.display = "none", 2000);
    }
  
    // Filter table based on search
    searchInput.addEventListener("input", () => {
      const filter = searchInput.value.toLowerCase();
      let visibleCount = 0;
      Array.from(customerTable.rows).forEach(row => {
        const nameCell = row.cells[0].textContent.toLowerCase();
        if(nameCell.indexOf(filter) > -1){
          row.style.display = "";
          visibleCount++;
        } else {
          row.style.display = "none";
        }
      });
  
      emptyMessage.style.display = visibleCount === 0 ? "block" : "none";
    });
  
    // Open Edit Popup when Edit button clicked
    customerTable.addEventListener("click", e => {
      if(e.target.classList.contains("edit-btn")){
        const row = e.target.closest("tr");
        document.getElementById("editName").value = row.cells[0].textContent;
        document.getElementById("editEmail").value = row.cells[1].textContent;
        document.getElementById("editMeter").value = row.cells[2].textContent;
        document.getElementById("editStatus").value = row.cells[3].textContent;
  
        editPopup.style.display = "flex";
      }
    });
  
    // Close popups when clicking outside
    [addPopup, editPopup].forEach(popup => {
      popup.addEventListener("click", e => {
        if(e.target === popup) popup.style.display = "none";
      });
    });
  
    // Optional: Press Escape key to close popups
    document.addEventListener("keydown", e => {
      if(e.key === "Escape"){
        addPopup.style.display = "none";
        editPopup.style.display = "none";
      }
    });
  });

  // BILL JS

  document.addEventListener("DOMContentLoaded", () => {
    const $ = id => document.getElementById(id);

    // Buttons & Popups
    const addBtn = $("addBillBtn");
    const addPopup = $("addPopup");
    const closeAddPopup = $("closeAddPopup");

    const editPopup = $("editPopup");
    const closeEditPopup = $("closeEditPopup");

    const saveBtn = $("saveBill");
    const updateBtn = $("updateBill");

    const billTable = document.querySelector("#billTable tbody");

    let editRow = null;

    // ---------------------------------------
    // Helper: ESCAPE HTML
    // ---------------------------------------
    function escapeHtml(text = "") {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // ---------------------------------------
    // ADD BILL POPUP
    // ---------------------------------------
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            addPopup.style.display = "flex";
        });
    }

    if (closeAddPopup) {
        closeAddPopup.addEventListener("click", () => {
            addPopup.style.display = "none";
        });
    }

    // ---------------------------------------
    // AUTO TOTAL COMPUTE (10 pesos per m³)
    // ---------------------------------------
    const addConsumptionInput = $("addConsumption");
    const addTotalInput = $("addTotal");
    const editConsumptionInput = $("editConsumption");
    const editTotalInput = $("editTotal");

    if (addConsumptionInput && addTotalInput) {
        addConsumptionInput.addEventListener("input", () => {
            addTotalInput.value = (parseFloat(addConsumptionInput.value) * 10 || 0).toFixed(2);
        });
    }

    if (editConsumptionInput && editTotalInput) {
        editConsumptionInput.addEventListener("input", () => {
            editTotalInput.value = (parseFloat(editConsumptionInput.value) * 10 || 0).toFixed(2);
        });
    }

    // ---------------------------------------
    // ADD NEW BILL (NO SAVE)
    // ---------------------------------------
    if (saveBtn) {
        saveBtn.addEventListener("click", () => {
            const billID = $("addBillID").value.trim();
            const name = $("addCustomerName").value.trim();
            const meter = $("addMeterNo").value.trim();
            const consumption = $("addConsumption").value.trim();
            const total = $("addTotal").value.trim();

            if (!billID || !name || !meter || !consumption || !total) {
                alert("Please fill all fields");
                return;
            }

            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${escapeHtml(billID)}</td>
                <td>${escapeHtml(name)}</td>
                <td>${escapeHtml(meter)}</td>
                <td>${escapeHtml(consumption)}</td>
                <td>${escapeHtml(total)}</td>
                <td>
                    <div class="action-icons">
                        <img src="/assets/css/edit.png" class="icon edit" title="Edit">
                    </div>
                </td>
            `;
            billTable.appendChild(row);

            // Attach edit handler
            row.querySelector(".edit").addEventListener("click", () => openEditPopup(row));

            addPopup.style.display = "none";

            ["addBillID", "addCustomerName", "addMeterNo", "addConsumption", "addTotal"]
                .forEach(id => $(id).value = "");
        });
    }

    // ---------------------------------------
    // OPEN EDIT POPUP
    // ---------------------------------------
    function openEditPopup(row) {
        editRow = row;
        const cells = row.querySelectorAll("td");

        $("editBillID").value = cells[0].textContent;
        $("editCustomerName").value = cells[1].textContent;
        $("editMeterNo").value = cells[2].textContent;
        $("editConsumption").value = cells[3].textContent;
        $("editTotal").value = cells[4].textContent;

        editPopup.style.display = "flex";
    }

    // Close edit popup
    if (closeEditPopup) {
        closeEditPopup.addEventListener("click", () => {
            editPopup.style.display = "none";
        });
    }

    // ---------------------------------------
    // UPDATE BILL (NO SAVE)
    // ---------------------------------------
    if (updateBtn) {
        updateBtn.addEventListener("click", () => {
            if (!editRow) return;

            const cells = editRow.querySelectorAll("td");

            cells[1].textContent = $("editCustomerName").value;
            cells[2].textContent = $("editMeterNo").value;
            cells[3].textContent = $("editConsumption").value;
            cells[4].textContent = $("editTotal").value;

            editPopup.style.display = "none";
        });
    }

});
 // BILL JS END

 // PAYMENT JS
// ================================
// DOM ELEMENTS
// ================================
const addBtn = document.getElementById('addPaymentBtn');
const addPopup = document.getElementById('addPaymentPopup');
const closeAddPopup = document.getElementById('closeAddPayment');

const editPopup = document.getElementById('editPaymentPopup');
const closeEditPopup = document.getElementById('closeEditPayment');
const updateBtn = document.getElementById('updatePayment');

const paymentTable = document.getElementById('paymentTable').querySelector('tbody');
const emptyMessage = document.getElementById('paymentEmpty');

const successPopup = document.getElementById('paymentSuccessPopup');
const successMessage = document.getElementById('paymentSuccessMessage');

const notFoundPopup = document.getElementById('paymentNotFound');

const searchInput = document.getElementById('paymentSearch');

let editRow = null;

// ================================
// SHOW SUCCESS POPUP
// ================================
function showPopup(message) {
    successMessage.textContent = message;
    successPopup.style.display = 'flex';
    setTimeout(() => successPopup.style.display = 'none', 2000);
}

// ================================
// ADD PAYMENT ROW
// ================================
function addPaymentRow(payment) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${payment.id}</td>
        <td>${payment.customer}</td>
        <td>₱${parseFloat(payment.amount).toFixed(2)}</td>
        <td>${payment.method}</td>
        <td>${payment.status || 'Paid'}</td>
        <td class="action-buttons">
            <button class="edit-btn" title="Edit">✎</button>
            <button class="delete-btn" title="Delete">🗑</button>
        </td>
    `;
    paymentTable.appendChild(row);
    attachRowEvents(row);
    emptyMessage.style.display = 'none';
}

// ================================
// ATTACH ROW EVENTS
// ================================
function attachRowEvents(row) {
    row.querySelector('.edit-btn').addEventListener('click', () => openEditPopup(row));
    row.querySelector('.delete-btn').addEventListener('click', () => {
        row.remove();
        if (paymentTable.children.length === 0) emptyMessage.style.display = 'block';
        showPopup('Payment deleted successfully!');
    });
}

// ================================
// ADD POPUP
// ================================
addBtn.addEventListener('click', () => addPopup.style.display = 'flex');
closeAddPopup.addEventListener('click', () => addPopup.style.display = 'none');

// ================================
// SAVE PAYMENT (UI ONLY)
// ================================
document.getElementById('savePayment').addEventListener('click', () => {
    const id = document.getElementById('addPaymentID').value.trim();
    const customer = document.getElementById('addCustomer').value.trim();
    const amount = document.getElementById('addAmount').value.trim();
    const method = document.getElementById('addMethod').value;

    if (!id || !customer || !amount || !method) {
        alert('Please fill all fields.');
        return;
    }

    addPaymentRow({ id, customer, amount, method, status: 'Paid' });
    addPopup.style.display = 'none';

    // Clear form
    document.getElementById('addPaymentID').value = '';
    document.getElementById('addCustomer').value = '';
    document.getElementById('addAmount').value = '';
    document.getElementById('addMethod').value = '';

    showPopup('Payment added!');
});

// ================================
// EDIT POPUP
// ================================
function openEditPopup(row) {
    editRow = row;
    const cells = row.querySelectorAll('td');
    document.getElementById('editPaymentID').value = cells[0].textContent;
    document.getElementById('editCustomer').value = cells[1].textContent;
    document.getElementById('editAmount').value = cells[2].textContent.replace('₱','');
    document.getElementById('editMethod').value = cells[3].textContent;
    editPopup.style.display = 'flex';
}

closeEditPopup.addEventListener('click', () => editPopup.style.display = 'none');

// ================================
// UPDATE PAYMENT (UI ONLY)
// ================================
updateBtn.addEventListener('click', () => {
    if (!editRow) return;
    const cells = editRow.querySelectorAll('td');
    cells[1].textContent = document.getElementById('editCustomer').value;
    cells[2].textContent = `₱${parseFloat(document.getElementById('editAmount').value).toFixed(2)}`;
    cells[3].textContent = document.getElementById('editMethod').value;

    editPopup.style.display = 'none';
    showPopup('Payment updated!');
});

// ================================
// SEARCH FUNCTION
// ================================
searchInput.addEventListener('keyup', () => {
    const searchValue = searchInput.value.trim().toLowerCase();
    let found = false;

    paymentTable.querySelectorAll('tr').forEach(row => {
        const customer = row.cells[1]?.textContent.toLowerCase();
        if (customer.includes(searchValue)) {
            row.classList.add('highlight');
            found = true;
        } else {
            row.classList.remove('highlight');
        }
    });

    if (!found && searchValue !== '') {
        notFoundPopup.style.display = 'block';
        setTimeout(() => notFoundPopup.style.display = 'none', 2000);
    }
});


//PAYMENT JS END

// REPORT JD 

// ================================
// DOM ELEMENTS
// ================================
const billingCard = document.getElementById('billingCard');
const paymentCard = document.getElementById('paymentCard');
const unpaidCard = document.getElementById('unpaidCard');
const revenueCard = document.getElementById('revenueCard');

// ================================
// EVENT LISTENERS (just trigger actions)
// ================================
billingCard.addEventListener('click', () => {
  console.log('Billing Reports clicked');
  // Add your controller logic here
});

paymentCard.addEventListener('click', () => {
  console.log('Payment Reports clicked');
  // Add your controller logic here
});

unpaidCard.addEventListener('click', () => {
  console.log('Unpaid Bills clicked');
  // Add your controller logic here
});

revenueCard.addEventListener('click', () => {
  console.log('Monthly Revenue clicked');
  // Add your controller logic here
});

// REPORT JS END

// USER JS 


    