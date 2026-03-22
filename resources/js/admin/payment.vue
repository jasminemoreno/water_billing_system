<template>
  <div class="payment-page">
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

    <button class="add-btn" @click="openAdd">+</button>

    <PaymentForm
      v-if="popupVisible"
      :mode="popupMode"
      :payment="currentPayment"
      :customers="customers"
      :bills="bills"
      @saved="handleSaved"
      @close="popupVisible=false"
      @verify="verifyPayment"
      @reject="rejectPayment"
    />

    <!-- Delete confirmation popup -->
    <div v-if="confirmDeletePopup" class="payment-success-popup">
      <div class="payment-success-popup-content">
        <p>Do you really want to delete this payment?</p>
        <div class="popup-btns">
          <button class="payment-close-btn" @click="confirmDelete">Yes</button>
          <button class="payment-close-btn cancel-btn" @click="cancelDelete">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Success popup -->
    <SuccessPopup
      v-if="showSuccess"
      :message="successMessage"
      @close="showSuccess=false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/api'
import PaymentTable from '@/components/admin/Table.vue'
import PaymentForm from '@/components/admin/PaymentForm.vue'
import SuccessPopup from '@/components/admin/SuccessPopup.vue'

const payments = ref([])
const customers = ref([])
const bills = ref([])
const searchQuery = ref('')

const popupVisible = ref(false)
const popupMode = ref('add')
const showSuccess = ref(false)
const successMessage = ref('')

const confirmDeletePopup = ref(false)
const paymentToDelete = ref(null)

const newPaymentTemplate = {
  id: null,
  customer_id: null,
  bill_id: null,
  meter_no: '',
  amount: 0,
  payment_method: 'Cash',
  status: 'Pending'
}

const currentPayment = reactive({ ...newPaymentTemplate })

const columns = [
  { label: 'Payment ID', field: 'id' },
  { label: 'Customer Name', field: 'customer_name' },
  { label: 'Bill ID', field: 'bill_id' },
  { label: 'Meter No', field: 'meter_no' },
  { label: 'Amount (₱)', field: 'amount' },
  { label: 'Payment Method', field: 'payment_method' },
  { label: 'Status', field: 'status' },
]

onMounted(async () => {
  await loadCustomers()
  await loadBills()
  await loadPayments()
})

async function loadCustomers() { customers.value = (await api.get('/customers')).data }
async function loadBills() { bills.value = (await api.get('/bills')).data }
async function loadPayments() { payments.value = (await api.get('/payments')).data }

const formattedPayments = computed(() => payments.value.map(p => ({
  ...p,
  customer_name: customers.value.find(c => c.id === p.customer_id)?.customer_name || ''
})))

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

async function handleSaved(message, newPayment) {
  if(newPayment) payments.value.push(newPayment)
  successMessage.value = message
  showSuccess.value = true
  popupVisible.value = false
}

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

// Verify / Reject
async function verifyPayment(payment) {
  try {
    const res = await api.post(`/payments/${payment.id}/verify`)
    const index = payments.value.findIndex(p => p.id === payment.id)
    if (index !== -1) payments.value[index] = res.data.payment

    // Close the PaymentForm popup
    popupVisible.value = false

    // Show SuccessPopup
    successMessage.value = 'Payment verified successfully!'
    showSuccess.value = true
  } catch (err) {
    console.error(err)
  }
}

async function rejectPayment(payment) {
  try {
    const res = await api.post(`/payments/${payment.id}/reject`)
    const index = payments.value.findIndex(p => p.id === payment.id)
    if (index !== -1) payments.value[index] = res.data.payment

    // Close the PaymentForm popup
    popupVisible.value = false

    // Show SuccessPopup
    successMessage.value = 'Payment rejected successfully!'
    showSuccess.value = true
  } catch (err) {
    console.error(err)
  }
}
</script>

<style scoped>
.payment-page { margin-left: 250px; padding: 80px 30px 30px; min-height: 100vh; background-color: #f4f6f8; }
.add-btn {
  position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px;
  font-size: 26px; background-color: #007bff; color: white; border: none;
  border-radius: 50%; cursor: pointer; display: flex; justify-content:center; align-items:center;
}
.add-btn:hover { background-color: #00a0ff; transform: scale(1.05); transition: 0.2s; }
.payment-success-popup { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.4); display:flex; justify-content:center; align-items:center; z-index:9999; }
.payment-success-popup-content { background:white; padding:30px 40px; border-radius:15px; text-align:center; box-shadow:0 8px 25px rgba(0,0,0,0.2); min-width:280px; }
.payment-success-popup-content p { font-size:17px; color:#333; font-weight:500; }
.popup-btns { display:flex; justify-content:center; gap:15px; margin-top:15px; }
.payment-close-btn { padding:10px 20px; border:none; border-radius:8px; background-color:#007bff; color:white; cursor:pointer; }
.payment-close-btn:hover { background-color:#0069d9; }
.cancel-btn { background-color:#6c757d; }
.cancel-btn:hover { background-color:#5a6268; }
</style>