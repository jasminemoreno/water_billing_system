<template>
  <div class="bill-page">

    <!-- Bill Table -->
    <BillTable
      :bills="filteredBills"
      :searchQuery="searchQuery"
      @update:searchQuery="searchQuery = $event"
      @edit="openEditPopup"
      @add="openAddPopup"
      @delete="confirmDeleteBill"
    />

    <!-- Add Bill Popup -->
    <BillForm
      v-if="addPopup"
      :form="newBill"
      :customers="customers"
      mode="add"
      @save="storeBill"
      @close="addPopup = false"
    />

    <!-- Edit Bill Popup -->
    <BillForm
      v-if="editPopup"
      :form="editBill"
      :customers="customers"
      mode="edit"
      @save="updateBill"
      @close="editPopup = false"
    />

    <!-- Success / Confirm Popup -->
    <SuccessPopup
      v-if="successMessage"
      :message="successMessage"
      @close="successMessage = ''"
    />

    <SuccessPopup
      v-if="confirmDelete.show"
      :message="confirmDelete.message"
      :isConfirm="true"
      @confirm="deleteBill(confirmDelete.bill)"
      @close="confirmDelete.show = false"
    />

  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import api from '@/api.js'

import BillTable from '@/components/admin/bill/BillTable.vue'
import BillForm from '@/components/admin/bill/BillForm.vue'
import SuccessPopup from '@/components/admin/bill/SuccessPopup.vue'

// --- Reactive State ---
const bills = ref([])
const customers = ref([])
const searchQuery = ref('')
const addPopup = ref(false)
const editPopup = ref(false)
const successMessage = ref('')

// Confirm delete state
const confirmDelete = reactive({
  show: false,
  bill: null,
  message: ''
})

const newBill = reactive({
  customer_id: '',
  customer_name: '',
  meter_no: '',
  consumption: 0,
  total: 0,
  billing_date: '',
  due_date: '',
  disconnection_date: ''
})

const editBill = reactive({
  id: null,
  customer_id: '',
  customer_name: '',
  meter_no: '',
  consumption: 0,
  total: 0,
  billing_date: '',
  due_date: '',
  disconnection_date: ''
})

const errors = reactive({})

// --- Computed: Filtered Bills for Search ---
const filteredBills = computed(() => {
  if (!searchQuery.value) return bills.value
  return bills.value.filter(b =>
    (b.customer?.customer_name || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (b.meter_no || '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    b.id?.toString().includes(searchQuery.value)
  )
})

// --- Load bills and customers from API ---
onMounted(async () => {
  try {
    const billsRes = await api.get('/bills')
    bills.value = billsRes.data.map(b => ({
      ...b,
      customer: b.customer || customers.value.find(c => c.id === b.customer_id)
    }))

    const customersRes = await api.get('/customers')
    customers.value = customersRes.data
  } catch (err) {
    console.error('Failed to load bills or customers:', err)
  }
})

// --- Open Add/Edit Popup ---
const openAddPopup = () => {
  Object.assign(newBill, {
    customer_id: '',
    customer_name: '',
    meter_no: '',
    consumption: 0,
    total: 0,
    billing_date: '',
    due_date: '',
    disconnection_date: ''
  })
  addPopup.value = true
}

const openEditPopup = (bill) => {
  Object.assign(editBill, {
    ...bill,
    customer_name: bill.customer?.customer_name || ''
  })
  editPopup.value = true
}

// --- Store New Bill ---
const storeBill = async (formData) => {
  try {
    Object.keys(errors).forEach(k => delete errors[k])
    const res = await api.post('/bills', { ...formData })

    const newBillObj = {
      ...res.data.bill,
      customer: res.data.bill.customer || customers.value.find(c => c.id === res.data.bill.customer_id)
    }

    bills.value.unshift(newBillObj)
    addPopup.value = false
    successMessage.value = 'Bill added successfully!'
  } catch (err) {
    if (err.response?.status === 422) Object.assign(errors, err.response.data.errors)
    else console.error('Failed to add bill:', err)
  }
}

// --- Update Existing Bill ---
const updateBill = async (formData) => {
  try {
    Object.keys(errors).forEach(k => delete errors[k])
    const res = await api.put(`/bills/${formData.id}`, { ...formData })

    const updatedBillObj = {
      ...res.data.bill,
      customer: res.data.bill.customer || customers.value.find(c => c.id === res.data.bill.customer_id)
    }

    const idx = bills.value.findIndex(b => b.id === formData.id)
    if (idx !== -1) bills.value.splice(idx, 1, updatedBillObj)
    editPopup.value = false
    successMessage.value = 'Bill updated successfully!'
  } catch (err) {
    if (err.response?.status === 422) Object.assign(errors, err.response.data.errors)
    else console.error('Failed to update bill:', err)
  }
}

// --- Confirm Delete Bill ---
const confirmDeleteBill = (bill) => {
  confirmDelete.show = true
  confirmDelete.bill = bill
  confirmDelete.message = `Are you sure you want to delete bill #${bill.id}?`
}

// --- Delete Bill ---
const deleteBill = async (bill) => {
  try {
    await api.delete(`/bills/${bill.id}`)
    bills.value = bills.value.filter(b => b.id !== bill.id)
    successMessage.value = 'Bill deleted successfully!'
    confirmDelete.show = false
  } catch (err) {
    console.error('Failed to delete bill:', err)
  }
}
</script>

<style scoped>
.bill-page {
  margin-left: 250px;
  padding: 80px 30px 30px 30px;
  min-height: 100vh;
  background-color: #f4f6f8;
}
</style>