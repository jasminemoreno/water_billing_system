<template>
  <div class="payment-page">

    <!-- TABLE -->
    <PaymentTable
      :rows="formattedPayments"
      :columns="columns"
      v-model:searchQuery="searchQuery"
      :hasEdit="false"
      :hasDelete="true"
      :hasView="true"
      @delete="deletePayment"
      @view="viewPayment"
    />

    <!-- ADD BUTTON (optional hidden or removed if not needed) -->
    <button class="add-btn" @click="openAdd">+</button>

    <!-- FORM (VIEW ONLY / NO EDIT MODE HERE) -->
    <PaymentForm
      v-if="popupVisible"
      :mode="popupMode"
      :payment="currentPayment"
      :customers="customers"
      :bills="bills"
      @close="popupVisible = false"
      @verify="verifyPayment"
      @reject="rejectPayment"
    />

    <!-- DELETE CONFIRM POPUP -->
    <div v-if="confirmDeletePopup" class="popup-overlay">
      <div class="popup-box">

        <p class="popup-title">Delete this payment?</p>
        <p class="popup-sub">This action cannot be undone.</p>

        <div class="popup-actions">
          <button class="btn-danger" @click="confirmDelete">
            Yes, Delete
          </button>

          <button class="btn-secondary" @click="cancelDelete">
            Cancel
          </button>
        </div>

      </div>
    </div>

    <!-- SUCCESS -->
    <SuccessPopup
      v-if="showSuccess"
      :message="successMessage"
      @close="showSuccess = false"
    />

  </div>
</template>

<script setup>
import { ref, reactive, computed } from "vue"
import api from "@/api"
import { usePolling } from "@/polling"

import PaymentTable from "@/components/admin/Table.vue"
import PaymentForm from "@/components/admin/PaymentForm.vue"
import SuccessPopup from "@/components/admin/SuccessPopup.vue"

// DATA
const payments = ref([])
const customers = ref([])
const bills = ref([])

const searchQuery = ref("")
const popupVisible = ref(false)
const popupMode = ref("view")

const showSuccess = ref(false)
const successMessage = ref("")

const confirmDeletePopup = ref(false)
const paymentToDelete = ref(null)

// CURRENT ITEM
const currentPayment = reactive({
  id: null,
  customer_id: null,
  bill_id: null,
  meter_no: "",
  amount: 0,
  payment_method: "Cash",
  status: "Pending",
})

// TABLE COLUMNS (NO EDIT COLUMN)
const columns = [
  { label: "Payment ID", field: "id" },
  { label: "Customer Name", field: "customer_name" },
  { label: "Bill ID", field: "bill_id" },
  { label: "Meter No", field: "meter_no" },
  { label: "Amount", field: "amount" },
  { label: "Method", field: "payment_method" },
  { label: "Status", field: "status" },
]

// FETCH FUNCTIONS
const loadPayments = async () => {
  payments.value = (await api.get("/payments")).data
}

const loadCustomers = async () => {
  customers.value = (await api.get("/customers")).data
}

const loadBills = async () => {
  bills.value = (await api.get("/bills")).data
}

// POLLING (REAL TIME UPDATE)
const fetchAll = async () => {
  await loadPayments()
  await loadCustomers()
  await loadBills()
}

usePolling(fetchAll, 5000)

// FORMAT TABLE DATA
const formattedPayments = computed(() =>
  payments.value.map(p => ({
    ...p,
    customer_name:
      customers.value.find(c => c.id === p.customer_id)?.customer_name || ""
  }))
)

// VIEW ONLY
function viewPayment(payment) {
  Object.assign(currentPayment, payment)
  popupMode.value = "view"
  popupVisible.value = true
}

// DELETE FLOW
function deletePayment(payment) {
  paymentToDelete.value = payment
  confirmDeletePopup.value = true
}

async function confirmDelete() {
  await api.delete(`/payments/${paymentToDelete.value.id}`)

  successMessage.value = "Payment deleted successfully!"
  showSuccess.value = true

  confirmDeletePopup.value = false
  paymentToDelete.value = null
}

function cancelDelete() {
  confirmDeletePopup.value = false
  paymentToDelete.value = null
}

// OPTIONAL (if needed)
function openAdd() {
  popupMode.value = "view"
  popupVisible.value = false
}

// VERIFY / REJECT
async function verifyPayment(payment) {
  await api.post(`/payments/${payment.id}/verify`)
  popupVisible.value = false
  successMessage.value = "Payment verified!"
  showSuccess.value = true
}

async function rejectPayment(payment) {
  await api.post(`/payments/${payment.id}/reject`)
  popupVisible.value = false
  successMessage.value = "Payment rejected!"
  showSuccess.value = true
}
</script>

<style scoped>
.payment-page {
  margin-left: 250px;
  padding: 80px 30px;
  min-height: 100vh;
  background: #f4f6f8;
}

/* FLOAT BUTTON (OPTIONAL) */
.add-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 55px;
  height: 55px;
  border-radius: 50%;
  background: #007bff;
  color: white;
  font-size: 26px;
  border: none;
}

/* DELETE POPUP OVERLAY */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

/* POPUP BOX */
.popup-box {
  background: white;
  padding: 25px 30px;
  border-radius: 12px;
  width: 320px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.popup-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 5px;
}

.popup-sub {
  font-size: 13px;
  color: #666;
  margin-bottom: 20px;
}

/* BUTTONS */
.popup-actions {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.btn-danger {
  flex: 1;
  padding: 10px;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 8px;
}

.btn-secondary {
  flex: 1;
  padding: 10px;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 8px;
}
</style>