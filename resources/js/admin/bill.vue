<template>
  <div class="bill-page">

    <!-- REUSABLE TABLE -->
    <Table
      :rows="formattedBills"
      :columns="columns"
      v-model:searchQuery="searchQuery"
      @edit="openEditPopup"
      @delete="deleteBill"
      :hasView="false"
    />

    <!-- FLOATING ADD BUTTON -->
    <button class="add-btn" @click="openAddPopup">+</button>

    <!-- BILL POPUP (used for both add & edit) -->
    <BillForm
      v-if="popupVisible"
      :form="currentBill"
      :customers="customers"
      :mode="popupMode"
      @save="handleSave"
      @close="popupVisible = false"
    />

    <!-- SUCCESS / CONFIRM POPUP -->
    <SuccessPopup
      v-if="successMessage || confirmDeletePopup"
      :message="popupMessage"
      :isConfirm="confirmDeletePopup"
      @confirm="confirmDelete"
      @close="closePopup"
    />

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/api'
import Table from '@/components/admin/Table.vue'
import BillForm from '@/components/admin/BillForm.vue'
import SuccessPopup from '@/components/admin/SuccessPopup.vue'

// ----------------------------
// STATE
// ----------------------------
const bills = ref([])
const customers = ref([])
const searchQuery = ref('')

const popupVisible = ref(false)
const popupMode = ref('add') // 'add' or 'edit'

// SUCCESS / CONFIRM POPUP STATE
const successMessage = ref('')           // success after add/edit/delete
const confirmDeletePopup = ref(false)    // whether confirmation is shown
const popupMessage = ref('')             // message shown in SuccessPopup
const billToDelete = ref(null)

// NEW BILL TEMPLATE
const newBillTemplate = {
  id: null,
  customer_id: null,
  customer_name: '',
  meter_no: '',
  consumption: 0,
  total: 0,
  billing_date: '',
  due_date: '',
  disconnection_date: '',
  status: 'Unpaid'
}

const currentBill = reactive({ ...newBillTemplate })

// ----------------------------
// TABLE COLUMNS
// ----------------------------
const columns = [
  { label: 'Bill ID', field: 'id' },
  { label: 'Customer Name', field: 'customer_name' },
  { label: 'Meter No.', field: 'meter_no' },
  { label: 'Consumption (m³)', field: 'consumption' },
  { label: 'Total (₱)', field: 'total' },
  { label: 'Billing Date', field: 'billing_date' },
  { label: 'Due Date', field: 'due_date' },
  { label: 'Disconnection Date', field: 'disconnection_date' },
  { label: 'Status', field: 'status' }
]

// ----------------------------
// FORMAT BILLS FOR TABLE
// ----------------------------
const formattedBills = computed(() => {
  return bills.value.map(b => ({
    ...b,
    customer_name: b.customer?.customer_name || '',
    meter_no: b.customer?.meter_no || '',
    billing_date: b.billing_date ? b.billing_date.split('T')[0] : '',
    due_date: b.due_date ? b.due_date.split('T')[0] : '',
    disconnection_date: b.disconnection_date ? b.disconnection_date.split('T')[0] : ''
  }))
})

// ----------------------------
// LOAD DATA
// ----------------------------
onMounted(async () => {
  await loadCustomers()
  await loadBills()
})

async function loadCustomers() {
  const res = await api.get('/customers')
  customers.value = res.data
}

async function loadBills() {
  const res = await api.get('/bills')
  bills.value = res.data
}

// ----------------------------
// POPUP HANDLERS
// ----------------------------
function openAddPopup() {
  Object.assign(currentBill, { ...newBillTemplate })
  popupMode.value = 'add'
  popupVisible.value = true
}

function openEditPopup(bill) {
  Object.assign(currentBill, bill)
  popupMode.value = 'edit'
  popupVisible.value = true
}

// ----------------------------
// SAVE / ADD / UPDATE BILL
// ----------------------------
async function handleSave(formData) {
  try {
    if (popupMode.value === 'add') {
      const res = await api.post('/bills', formData)
      bills.value.push(res.data.bill)   // update table immediately
      popupMessage.value = 'Bill added successfully!'
      successMessage.value = 'success'  // trigger popup
    } else {
      const res = await api.put(`/bills/${formData.id}`, formData)
      const idx = bills.value.findIndex(b => b.id === formData.id)
      if (idx !== -1) bills.value[idx] = res.data.bill
      popupMessage.value = 'Bill updated successfully!'
      successMessage.value = 'success'
    }
  } catch (err) {
    console.error(err)
  } finally {
    popupVisible.value = false
  }
}

// ----------------------------
// DELETE BILL (uses SuccessPopup as confirm)
// ----------------------------
function deleteBill(bill) {
  billToDelete.value = bill
  confirmDeletePopup.value = true
  popupMessage.value = 'Are you sure you want to delete this bill?'
}

// DELETE CONFIRM
async function confirmDelete() {
  try {
    await api.delete(`/bills/${billToDelete.value.id}`)
    bills.value = bills.value.filter(b => b.id !== billToDelete.value.id)
    popupMessage.value = 'Bill deleted successfully!'
  } catch (err) {
    console.error(err)
  } finally {
    confirmDeletePopup.value = false
    billToDelete.value = null
    successMessage.value = 'success' // trigger popup
  }
}

// CLOSE POPUP
function closePopup() {
  successMessage.value = ''
  confirmDeletePopup.value = false
  billToDelete.value = null
}
</script>

<style scoped>
.bill-page {
  margin-left: 250px;
  padding: 80px 30px 30px;
  background: #f4f6f8;
  min-height: 100vh;
}

/* FLOATING ADD BUTTON */
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
</style>