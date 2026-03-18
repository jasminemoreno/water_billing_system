<template>
  <div class="payment-page">

    <!-- PAYMENT TABLE -->
    <PaymentTable
      :rows="formattedPayments"
      :columns="columns"
      v-model:searchQuery="searchQuery"
      @delete="deletePayment"
      @view="viewPayment"
      :hasEdit="false"
      :hasDelete="true"
      :hasView="true"
      :actionOrder="['view','delete']" 
    />

    <!-- FLOATING ADD BUTTON -->
    <button class="add-btn" @click="openAdd">+</button>

    <!-- PAYMENT FORM POPUP -->
    <PaymentForm
  v-if="popupVisible"
  :mode="popupMode"
  :payment="currentPayment"
  :customers="customers"
  :bills="bills"
  @saved="handleSaved"   
  @close="popupVisible = false"
/>

    <!-- DELETE CONFIRMATION POPUP -->
    <div v-if="confirmDeletePopup" class="payment-success-popup">
      <div class="payment-success-popup-content">
        <p>Do you really want to delete this payment?</p>
        <div class="popup-btns">
          <button class="payment-close-btn" @click="confirmDelete">Yes</button>
          <button class="payment-close-btn cancel-btn" @click="cancelDelete">Cancel</button>
        </div>
      </div>
    </div>

    <!-- SUCCESS POPUP -->
    <SuccessPopup
      v-if="showSuccess"
      :message="successMessage"
      @close="showSuccess = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/api'
import PaymentTable from '@/components/admin/Table.vue'
import PaymentForm from '@/components/admin/PaymentForm.vue'
import SuccessPopup from '@/components/admin/SuccessPopup.vue'

// STATE
const payments = ref([])
const customers = ref([])
const bills = ref([])
const searchQuery = ref('')

const popupVisible = ref(false)
const popupMode = ref('add')
const showSuccess = ref(false)
const successMessage = ref('')

// DELETE CONFIRMATION
const confirmDeletePopup = ref(false)
const paymentToDelete = ref(null)

// NEW PAYMENT TEMPLATE
const newPaymentTemplate = {
  id: null,
  customer_id: null,
  bill_id: null,
  meter_no: '',
  amount: 0,
  payment_method: 'Cash',
  status: 'Paid'
}

const currentPayment = reactive({ ...newPaymentTemplate })

// TABLE COLUMNS
const columns = [
  { label: 'Payment ID', field: 'id' },
  { label: 'Customer Name', field: 'customer_name' },
  { label: 'Bill ID', field: 'bill_id' },
  { label: 'Meter No', field: 'meter_no' },
  { label: 'Amount (₱)', field: 'amount' },
  { label: 'Payment Method', field: 'payment_method' },
  { label: 'Status', field: 'status' },
]

// LOAD DATA
onMounted(async () => {
  await loadCustomers()
  await loadBills()
  await loadPayments()
})

async function loadCustomers() {
  const res = await api.get('/customers')
  customers.value = res.data
}

async function loadBills() {
  const res = await api.get('/bills')
  bills.value = res.data
}

async function loadPayments() {
  const res = await api.get('/payments')
  payments.value = res.data
}

// FORMAT PAYMENTS FOR TABLE
const formattedPayments = computed(() => {
  return payments.value.map(p => ({
    id: p.id,
    customer_name: getCustomerName(p.customer_id),
    bill_id: p.bill_id,
    meter_no: p.meter_no,
    amount: isNaN(Number(p.amount)) ? '0.00' : Number(p.amount).toFixed(2),
    payment_method: p.payment_method || '',
    status: p.status || ''
  }))
})

function getCustomerName(id) {
  const c = customers.value.find(c => c.id === id)
  return c ? c.customer_name : ''
}

// POPUPS
function openAdd() {
  Object.assign(currentPayment, { ...newPaymentTemplate })
  popupMode.value = 'add'
  popupVisible.value = true
}

function viewPayment(payment) {
  Object.assign(currentPayment, payment)
  popupMode.value = 'view'
  popupVisible.value = true
}

// SAVE / ADD PAYMENT
async function handleSaved(message, newPayment) {
  if(newPayment) {
    payments.value.push(newPayment)   // immediate table update
  }
  successMessage.value = message
  showSuccess.value = true
  popupVisible.value = false
}

// DELETE PAYMENT
function deletePayment(payment) {
  paymentToDelete.value = payment
  confirmDeletePopup.value = true
}

async function confirmDelete() {
  await api.delete(`/payments/${paymentToDelete.value.id}`)
  payments.value = payments.value.filter(p => p.id !== paymentToDelete.value.id)
  successMessage.value = 'Payment deleted successfully!'
  showSuccess.value = true
  confirmDeletePopup.value = false
  paymentToDelete.value = null
}

function cancelDelete() {
  confirmDeletePopup.value = false
  paymentToDelete.value = null
}
</script>

<style scoped>
.payment-page {
  margin-left: 250px;
  padding: 80px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
}

.add-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 55px;
  height: 55px;
  font-size: 26px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  display: flex;
  justify-content: center;
  align-items: center;
}

.add-btn:hover {
  background-color: #00a0ff;
  transform: scale(1.05);
  transition: 0.2s;
}

/* POPUP STYLING */
.payment-success-popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.payment-success-popup-content {
  background: white;
  padding: 30px 40px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  min-width: 280px;
}

.payment-success-popup-content p {
  font-size: 17px;
  color: #333;
  font-weight: 500;
}

.popup-btns {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 15px;
}

.payment-close-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
}

.payment-close-btn:hover {
  background-color: #0069d9;
}

.cancel-btn {
  background-color: #6c757d;
}

.cancel-btn:hover {
  background-color: #5a6268;
}
</style>